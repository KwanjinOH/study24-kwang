<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \Carbon\Carbon;
use App\Helpers\HelperFunctions;
use DB;
use App\Classes\geoPHP\geoPHP;

class ReportController extends Controller
{
    public function info(Request $request)
    {
        DB::enableQueryLog();
        $pnu = $request->pnu;
        $basicAndSimilarList = $this->similarList($pnu);

        if(count($basicAndSimilarList) == 1){
            // 유사사례 없음. return error 안해고 foreach
        }
        $results = $this->getInfos($basicAndSimilarList);
        $addResult = $this->addRows($results);
        // dd($addResult);
        $example = $this->finalReportBindsData($addResult);

        $finalResult = [
            'main' => $addResult[0],
            'example' => $example
        ];
        // dd($finalResult);
        return response()->json([
            'error'=>false,
            'data'=>json_encode($finalResult)
        ]);

    }

    public function similarList($pnu)
    {

        $helpers = new HelperFunctions;
        // #0 기준 lat, lng, pnu, 년도, 공시지가, 용도
        $rs = DB::table('latlng_tb')
            ->join('crel_meagak', 'crel_meagak.MK_NO', '=', 'latlng_tb.ref_sales_no')
            ->select('crel_meagak.mk_type', 'latlng_tb.sido_id', 'latlng_tb.lat', 'latlng_tb.lng', 'latlng_tb.yd_code', 'latlng_tb.ju_code', 'latlng_tb.gongsi_price', 'latlng_tb.pnu')
            ->where('latlng_tb.pnu', '=', $pnu)
            ->first();

        // dd($rs);
        // #1 default query
        $whereGroup[] = 'SELECT CONVERT(m.pnu, char)as pnu, m.ref_sales_no, s.address, m.lat, m.lng, ( '.$rs->lat.' - m.lat)*('.$rs->lat.' - m.lat)+('.$rs->lng.' - m.lng)*('.$rs->lng.' - m.lng) AS order_,
                        (6371*acos(cos(radians('. $rs->lat .'))*cos(radians(m.lat))*cos(radians(m.lng)-radians('. $rs->lng .'))+sin(radians('. $rs->lat .'))*sin(radians(m.lat))))
                        AS distance
                        FROM latlng_tb AS m
                        INNER JOIN crel_meagak as s
                        ON m.ref_sales_no = s.MK_NO
                        WHERE m.lat IS NOT NULL
                        AND year(m.gr_date) >= (year(now())-5)'; // 현재년도 기준 3년 이내 거래 -> local test 에서는 5년으로 함.

        // #2 용도지역 주거가 있는 용도지역 코드 d3, d4, d5, d6 => 매각 데이터 있음
        $areaCheckArray = ['d3', 'd4', 'd5', 'd6'];
        // $param['yd_code'] = 'd3';
        $whereGroup[] = "AND m.yd_code ". ((in_array($rs->yd_code ,$areaCheckArray))? '':'NOT') ." IN ('d3', 'd4', 'd5', 'd6')"; // latlng table
        // #3 단독 다가구면 단독 다가구만 => 매각 데이터 있음
        $mkCheckArray = ['단독', '다가구'];
        // $param['mk_type'] = '단독';
        $whereGroup[] = (in_array($rs->mk_type ,$mkCheckArray))? "AND s.mk_type in ('단독', '다가구')":"";
        // #4 공시지가 30% 오차범위
        $whereGroup[] = $rs->gongsi_price? 'AND ('.$rs->gongsi_price.' - ('.$rs->gongsi_price.' *0.3)) <= m.gongsi_price AND ('.$rs->gongsi_price.' + ('.$rs->gongsi_price.' *0.3)) >= m.gongsi_price':'';
        // #5 etc order by
        $whereGroup[] = 'AND m.pnu <> '.$rs->pnu.' '. $helpers->adjoiningSido($rs->sido_id) .'group by m.idx HAVING distance <= 1 ORDER BY distance ASC LIMIT 10';
        // 정렬 orders ??, distance ?? 우선 distance
        $whereString = implode(' ',$whereGroup);
        // dd($whereString);

        $list = DB::select(DB::raw($whereString));
        // dd($list);
        $arr[] = $rs->pnu;
        if(count($list)){
            foreach ($list as $item){
                $arr[] = $item->pnu;
            }
            return $arr;
        }else{
            return $arr;
        }
    }

    public function getInfos($arrays)
    {

        $startCnt = 0;
        $limit = 5;

        foreach($arrays as $pnu) {

            if($startCnt == $limit){
                break;
            }

            $sido = substr($pnu, 0, 2);
            $resultArrays['p'] = $this->getPyo($pnu, $sido);
            $resultArrays['s'] = $this->getSale($pnu, $sido);
            $resultArrays['g'] = $this->getGeo($pnu, $sido);
            $resultArrays['b'] = $this->getBusok($pnu, $sido);
            $resultArrays['l'] = $this->getLend($pnu, $sido);
            $resultArrays['lp'] = $this->getLendPrice($pnu, $sido);
            $resultArrays['ll'] = $this->getLatlng($pnu, $sido);



            $returnArray[] = $resultArrays;
            $startCnt++;
        }
        return $returnArray;
    }

    private function getPyo($pnu, $sido)
    {

        $rs = DB::table('big_data.crel_pyo_'. $sido .' as val')
            ->selectRaw("cblp_daeji_lo as addr, cblp_doro_daeji_lo as doro, cblp_geonmul_name as nm
                        , cblp_ju_yongdo_no_name as ju, ifnull(cblp_jisang_cheung_ea, 0) as jisang, ifnull(cblp_jiha_cheung_ea, 0) as jiha
                        , IFNULL(TRIM(TRUNCATE(cblp_yeon_myeonjeog, 2))+0, 0) as yeon
                        , IFNULL(TRIM(TRUNCATE(cblp_yeon_myeonjeog*0.3025, 2))+0, 0) as yeon_p
                        , IFNULL(TRIM(TRUNCATE(cblp_daeji_myeonjeog, 2))+0, 0) as daeji
                        , IFNULL(TRIM(TRUNCATE(cblp_daeji_myeonjeog*0.3025, 2))+0, 0) as daeji_p
                        , IFNULL(TRIM(TRUNCATE(CBLP_GEONCHUG_MYEONJEOG, 2))+0, 0) as geonchug
                        , IFNULL(TRIM(TRUNCATE(CBLP_GEONCHUG_MYEONJEOG*0.3025, 2))+0, 0) as geonchug_p
                        , CBLP_GEONPYE_YUL as geonpye
                        , CBLP_YONGJEOGYUL as yongeog
                        , CBLP_SEUNGYONG_LIFT_EA as lift_s
                        , CBLP_BISEUNGYONG_LIFT_EA as lift_b
                        , (CBLP_OGNAE_MH_EA + CBLP_OGOE_MH_EA) as jc_m
                        , (CBLP_OGNAE_JJ_EA + CBLP_OGOE_JJ_EA) as jc_j
                        , IFNULL(DATE_FORMAT(CBLP_SAYONG_SEUNGIN_DAY, '%Y-%m-%d'), 0) as sayong
                        , IFNULL(DATE_FORMAT(CBLP_SAYONG_SEUNGIN_DAY, '%Y'), 0) as sayong_year
                        , pnu
                        , cblp_lat as lat
                        , cblp_long as lng")
            ->leftJoin(DB::raw('(
                SELECT MAX(CBLP_YEON_MYEONJEOG) AS _max
                FROM big_data.crel_pyo_'. $sido .'
                WHERE pnu ='. $pnu .') AS _p'), function($join){
                    $join->on( 'val.CBLP_YEON_MYEONJEOG', '=','_p._max' );
                })
            ->where('val.pnu', '=', $pnu)
            ->get();

        if(count($rs)){
            foreach ($rs as $key => $obj) {
                $arr['items'][] = (array) $obj;
            }
            $arr['fg'] = 1;
        }else{
            $arr['fg'] = 0;
        }
        return $arr;

    }
    private function getSale($pnu, $sido)
    {
        $rs = DB::table('crel.crel_meagak')
              // DB::table('lo_crel.crel_meagak')
            ->selectRaw("YONGDO_AREA as yd
                        , mk_yeon_area as yeon
                        , mk_yeon_area_py as yeon_p
                        , mk_deaji_area as daeji
                        , mk_deaji_area_py as daeji_p
                        , IFNULL(PRICE, 0)as ori_price
                        , IFNULL(TRIM(PRICE/10000)+0, 0)as price
                        , IFNULL(ympdg, 0)as ori_ymp
                        , TRUNCATE(IFNULL(TRIM(ympdg/10000)+0, 0), 0)as ymp
                        , IFNULL(DGPDG, 0)as ori_dgp
                        , TRUNCATE(IFNULL(TRIM(DGPDG/10000)+0, 0), 0)as dgp
                        , IFNULL(DATE_FORMAT(gurae_ym, '%Y-%m'), 0) as sales_day
                        , if(jubdo_fg = 'f2','대로변','이면') as jubdo
                        , lat, lng
                        , mk_type")
            ->where('pnu', '=', $pnu)
            ->orderBy('sales_day', 'DESC')
            ->limit(2)
            ->get();

        if(count($rs)){
            foreach ($rs as $key => $obj) {
                $arr['items'][] = (array) $obj;
            }
            $arr['fg'] = 1;
        }else{
            $arr['fg'] = 0;
        }
        return $arr;
    }
    private function getGeo($pnu, $sido)
    {
        $rs = DB::table('gis.gis_land_gongsi_'.$sido)
            ->selectRaw("CONVERT(a0,char) as pnu ,ASTEXT(SHAPE) AS coords")
            ->where('a0', '=', $pnu)->get();

        if(count($rs)){
            $w = $rs[0]->coords;
            $geom = geoPHP::load($w, 'wkt');
            $geometry = $geom->out('json');
            $rs[0]->coords = $geometry;
            foreach ($rs as $key => $obj) {
                $arr['items'][] = (array) $obj;
            }
            $arr['fg'] = 1;
        }else{
            $arr['fg'] = 0;
        }
        return $arr;
    }
    private function getBusok($pnu, $sido)
    {
        $busokPnuCheck = DB::select(DB::raw("SELECT a.ref_cblp_mgm, a.pnu, IFNULL(a.idx,'') AS idx
                                            FROM crel.latlng_tb AS a
                                            -- FROM lo_crel.latlng_tb AS a
                                            INNER JOIN big_data.crel_busok_jibun_tb AS b
                                            ON a.ref_cblp_mgm = b.cblp_mgm
                                            WHERE a.pnu = $pnu"));
        // dd(DB::getQueryLog()); // DB query 확인
        if (count($busokPnuCheck)) {
            // 부속 기준 있음
            foreach ($busokPnuCheck as $bu) {
                $b_asText = DB::table('gis.gis_land_gongsi_'.$sido)
                        ->selectRaw("CONVERT(a0,char) as pnu ,ASTEXT(SHAPE) AS coords")
                        ->where('a0', '=', $bu->pnu)->get();
                if ($b_asText) {
                    $w = $b_asText[0]->coords;
                    $geom = geoPHP::load($w, 'wkt');
                    $geometry = $geom->out('json');

                    $arr['items'][] = (array) $geometry;

                }else{
                    $arr['items'][] = 0;
                }
            }
            $arr['fg'] = 1;
        } else {
            // 부속 기준 없음.
           $arr['fg'] = 0;
        }
        return $arr;
    }

    private function getLend($pnu, $sido)
    {
        $rs = DB::select(DB::raw('SELECT TRUNCATE(lend_area,2)as lend_area, TRUNCATE(lend_area*0.3025,2)as lend_area_py, dj_nm, std_year, jm_nm, yd_nm1, use_nm, height_nm, shape_nm, load_nm, LEFT(create_dt,7)as create_dt
                                    FROM big_data.land_propertry_'.$sido.' WHERE pnu = '.$pnu.' AND std_year
                                    = (SELECT MAX(std_year) FROM big_data.land_propertry_'.$sido.' WHERE pnu = '.$pnu.')'));

        if(count($rs)){
            foreach ($rs as $key => $obj) {
                $arr['items'][] = (array) $obj;
            }
            $arr['fg'] = 1;
        }else{
            $arr['fg'] = 0;
        }
        return $arr;
    }

    private function getLatlng($pnu, $sido)
    {
        $helpers = new HelperFunctions;
        $rs = DB::table('crel.latlng_tb')
                // table('lo_crel.latlng_tb')
                    ->selectRaw('ju_code as ju, yd_code as yd, gongsi_price, lat, lng, sido_id')
                    ->wherePnu($pnu)->first();
        if($rs){
            $ll = [
                'ju'=> $helpers->simpleAreaNm($rs->ju),
                'yd'=> $helpers->simpleAreaNm($rs->yd),
                'yd_code' => $rs->yd,
                'gongsi_price' => $rs->gongsi_price,
                'sido'=> $sido,
            ];

            $arr['items'][0] = $ll;
            $arr['fg'] = 1;
        }else{
            $arr['fg'] = 0;
        }
        return $arr;
    }

    private function getLendPrice($pnu, $sido)
    {
        $rs = DB::table('big_data.lend_price_tb_'. $sido)
                ->select('year', 'lend_price')
                ->wherePnu($pnu)
                ->groupby('year')
                ->orderby('year', 'asc')
                ->limit(5)
                ->get()->toArray();
        if(count($rs)){
            foreach ($rs as $key => $obj) {
                $arr['items'][] = (array) $obj;
            }
            $arr['fg'] = 1;
        }else{
            $arr['fg'] = 0;
        }
        return $arr;
    }

    private function addRows($result)
    {
        $helpers = new HelperFunctions;
        // 공시지가대비율
        $cnt = count($result);
        $totalGyul = 0;
        foreach($result as $idx => $item) {
            // main 건물 공시지가대비율은 나머지 유사사례에 공시지가대비율들에 평균??
            if($idx > 0){
                $dgp = $item['s']['items'][0]['ori_dgp'];
                $gp = $item['ll']['items'][0]['gongsi_price'];
                $gYul = $helpers->gongsiYul($dgp, $gp);
                $result[$idx]['add']['items'][0]['gYul'] = $gYul;
                $totalGyul += $gYul;
            }
        }
        $mainGyul = $totalGyul / ($cnt-1);
        $result[0]['add']['items'][0]['gYul'] = $mainGyul;

        // dd($result[0]);
        // 추정가격
        $calPrice = $helpers->calculationPrice($result[0]);

        $result[0]['ll']['items'][0]['calPrice'] = $calPrice;

        return $result;
    }

    private function finalReportBindsData($result) // 최종 유사사례 바인드 데이터 정제
    {
        $helpers = new HelperFunctions;
        foreach($result as $idx => $item){

            $p = $item['p']['fg']? $item['p']['items'][0]:0;
            $s = $item['s']['fg']? $item['s']['items'][0]:0;
            $l = $item['l']['fg']? $item['l']['items'][0]:0;
            $ll = $item['ll']['fg']? $item['ll']['items'][0]:0;
            $lp = $item['lp']['fg']? $item['lp']['items']:0;
            $add = count($item['add'])? $item['add']['items'][0]:0;

            $formats[$idx]['pnu'] = $p? $p['pnu']:0;

            $formats[$idx]['bdnm'] = $p? $p['nm']:'-';
            $formats[$idx]['simple_addr'] = $p? $helpers->addrExplode($p['addr']):'-';
            $formats[$idx]['simple_raddr'] = $p? $helpers->addrExplode($p['doro']):'-';
            $formats[$idx]['format_price'] = $s? number_format($s['ori_price']).'원':'-';
            $formats[$idx]['sales_day'] = $s? $s['sales_day']:'-';
            $formats[$idx]['ju'] = $p? $p['ju']:'-';

            $formats[$idx]['yd'] = $s? $s['yd']:'';
            $formats[$idx]['jubdo'] = $s? $s['jubdo']:'-';
            $formats[$idx]['load_nm'] = $l? $l['load_nm']:'-';
            if($s){
                $formats[$idx]['yarea'] = $helpers->bracketStringJoin($s['yeon'], '㎡', $s['yeon_p'], 'py');
                $formats[$idx]['darea'] = $helpers->bracketStringJoin($s['daeji'], '㎡', $s['daeji_p'], 'py');
            }else if($p){
                $formats[$idx]['yarea'] = $helpers->bracketStringJoin($p['yeon'], '㎡', $p['yeon_p'], 'py'); // 실거래 없으면 표제부
                $formats[$idx]['darea'] = $helpers->bracketStringJoin($p['daeji'], '㎡', $p['daeji_p'], 'py'); // 실거래 없으면 표제부
            }else{
                $formats[$idx]['yarea'] = '-';
                $formats[$idx]['darea'] = '-';
            }
            $formats[$idx]['garea'] = $p? $helpers->bracketStringJoin($p['geonchug'], '㎡', $p['geonchug_p'], 'py'):'-';

            $formats[$idx]['gp_yj'] = $p? number_format($p['geonpye'], 2, '.', ','). '%/'. number_format($p['yongeog'], 2, '.', ',').'%':'-';
            $formats[$idx]['gm'] = $p? $p['jisang'].'F/B'.$p['jiha'].'F':'-';
            $formats[$idx]['ev'] = $p? $helpers->bracketStringJoin2($p['lift_s'], '승용', $p['lift_b'], '비상'):'-';
            $formats[$idx]['pk'] = $p? $helpers->bracketStringJoin2($p['jc_m'], '기계', $p['jc_j'], '자주'):'-';
            $formats[$idx]['sy'] = $p? $p['sayong']:'-';
            $formats[$idx]['bf'] = $p? Carbon::now()->year - $p['sayong_year']:'-';

            $formats[$idx]['dgp'] = $s? number_format($s['ori_dgp']).'원':'-';
            $formats[$idx]['yp'] = $s? number_format($s['ori_ymp']).'원':'-';
            $formats[$idx]['ori_dgp'] = $s? $s['ori_dgp']:0;
            $formats[$idx]['ori_yp'] = $s? $s['ori_ymp']:0;

            $formats[$idx]['gp'] = $ll? number_format($ll['gongsi_price']).'원':'-';
            if($lp){
                $cnt = count($lp);
                $formats[$idx]['re_gp'] = number_format($lp[$cnt-1]['lend_price']).'원';
            }else{
                $formats[$idx]['re_gp'] = '-';
            }

            $formats[$idx]['gp_yul'] = $add? number_format($add['gYul'], 2, '.', ',').'%':'-';


            $formats[$idx]['lat'] = $s? $s['lat']:0;
            $formats[$idx]['lng'] = $s? $s['lng']:0;
        }
        return $formats;


    }
}
