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
        // auth check
        $helpers = new HelperFunctions;

        if(!$request){ // error
            return response()->json([
                'error'=>true
            ]);
        }

        $pnu = $request->pnu;
        // 1111015200100600000

        $queryList = array(
            "pyo"=>"cblp_daeji_lo as addr, cblp_doro_daeji_lo as doro, cblp_geonmul_name as nm
                , cblp_ju_yongdo_no_name as ju, cblp_jisang_cheung_ea as jisang, cblp_jiha_cheung_ea as jiha
                , IFNULL(TRIM(TRUNCATE(cblp_yeon_myeonjeog, 2))+0, 0) as yeon
                , IFNULL(TRIM(TRUNCATE(cblp_yeon_myeonjeog*0.3025, 2))+0, 0) as yeon_p
                , IFNULL(TRIM(TRUNCATE(cblp_daeji_myeonjeog, 2))+0, 0) as daeji
                , IFNULL(TRIM(TRUNCATE(cblp_daeji_myeonjeog*0.3025, 2))+0, 0) as daeji_p
                , CBLP_SEUNGYONG_LIFT_EA as lift_s
                , CBLP_BISEUNGYONG_LIFT_EA as lift_b
                , (CBLP_OGNAE_MH_EA + CBLP_OGOE_MH_EA) as jc_m
                , (CBLP_OGNAE_JJ_EA + CBLP_OGOE_JJ_EA) as jc_j
                , IFNULL(DATE_FORMAT(CBLP_SAYONG_SEUNGIN_DAY, '%Y.%m.%d'), 0) as sayong
                , cblp_lat as lat
                , cblp_long as lng",

            "meagak"=>"YONGDO_AREA as yd
                , mk_yeon_area as yeon
                , mk_yeon_area_py as yeon_p
                , mk_deaji_area as daeji
                , mk_deaji_area_py as daeji_p
                , IFNULL(TRIM(PRICE/10000)+0, 0)as price
                , TRUNCATE(IFNULL(TRIM(ympdg/10000)+0, 0), 0)as ymp
                , TRUNCATE(IFNULL(TRIM(DGPDG/10000)+0, 0), 0)as dgp
                , IFNULL(DATE_FORMAT(gurae_ym, '%Y.%m'), 0) as sales_day
                , mk_type
                ",

            "geo"=>"CONVERT(a0,char) as pnu ,ASTEXT(SHAPE) AS coords",

            "lend"=>"TRUNCATE(lend_area,2)as lend_area, TRUNCATE(lend_area*0.3025,2)as lend_area_py, dj_nm, std_year, jm_nm, yd_nm1, use_nm, height_nm, shape_nm, load_nm, LEFT(create_dt,7)as create_dt",
            "latlng"=>"ju_code as ju, yd_code as yd, gongsi_price, lat, lng, sido_id",
        );

        // FROM crel_meagak WHERE pnu = 1111015200100600000
        //         ORDER BY gurae_ym DESC
        //         limit 2

        $subPnu = substr($pnu, 0, 2);
        $tableList = array (
            'pyo'=>'big_data.crel_pyo_'.$subPnu,
            'meagak'=>'crel.crel_meagak',
            // 'meagak'=>'lo_crel.crel_meagak',
            'lend'=>'big_data.land_propertry_'.$subPnu,
            'gis'=>'gis.gis_land_gongsi_'.$subPnu,
            'chong'=>'big_data.chong_pyo',
            'busok'=>'big_data.crel_busok_jibun_tb',
            'geo'=>'gis.gis_land_gongsi_'.$subPnu,
            // 0508
            'latlng' => 'crel.latlng_tb',
            // 'latlng' => 'lo_crel.latlng_tb',
        );

        $arrays = array(
            'param'=>$pnu
        );


        foreach($tableList as $key => $val)
        {
            switch ($key)
            {
                case 'pyo':
                    // dd($val);
                    $rs = DB::table($val. ' as val')
                        ->selectRaw($queryList[$key])
                        ->leftJoin(DB::raw('(
                            SELECT MAX(CBLP_YEON_MYEONJEOG) AS _max
                            FROM big_data.crel_pyo_'. $subPnu .'
                            WHERE pnu ='. $pnu .') AS _p'), function($join){
                                $join->on( 'val.CBLP_YEON_MYEONJEOG', '=','_p._max' );
                            })
                        ->where('val.pnu', '=', $pnu)
                        ->get();
                //    $rs = DB::table($val)
                //         ->selectRaw($queryList[$key])
                //         ->where('pnu', '=', $pnu)
                //         ->orderBy('created_at', 'DESC')
                //         ->limit(1)
                //         ->get();

                    if (count($rs)) {
                        foreach ($rs as $key => $obj) {
                            $arrays['p']['items'][$key] = (array) $obj;
                        }

                        $arrays['p']['fg'] = 1;
                    }else{
                        $arrays['p']['fg'] = 0;
                    }
                break;
                case 'meagak':
                    // multi data pnu = 1111010400100340003
                    // $pnu = '1111010400100340003';

                    $rs = DB::table($val)
                        ->selectRaw($queryList[$key])
                        ->where('pnu', '=', $pnu)
                        ->orderBy('sales_day', 'DESC')
                        ->limit(2)
                        ->get();

                    if (count($rs)) {
                        foreach ($rs as $key => $obj) {
                            $arrays['s']['items'][$key] = (array) $obj;
                        }

                        $arrays['s']['fg'] = 1;
                    }else{
                        $arrays['s']['fg'] = 0;
                    }
                break;
                case 'geo':
                    $rs = DB::table($val)
                        ->selectRaw($queryList[$key])
                        ->where('a0', '=', $pnu)->get();

                    if(count($rs)){
                        $w = $rs[0]->coords;
                        $geom = geoPHP::load($w, 'wkt');
                        $geometry = $geom->out('json');
                        $rs[0]->coords = $geometry;
                        foreach($rs as $key => $obj){
                            $arrays['g']['items'][$key] = (array) $obj;
                        }
                        $arrays['g']['fg'] = 1;
                    }else{
                        $arrays['g']['fg'] = 0;
                    }
                break;
                case 'lend':
                    $rs = DB::table($val)
                        ->selectRaw($queryList[$key])
                        ->where('pnu', '=', $pnu)
                        ->whereRaw('std_year = (SELECT MAX(std_year) FROM '. $val .' WHERE pnu = '. $pnu .')')
                        ->get();
                    if(count($rs)){
                        foreach($rs as $key => $obj){
                            $arrays['l']['items'][$key] = (array) $obj;
                        }

                        $grDate = $arrays['s']['fg']? $arrays['s']['items'][0]['sales_day']:0;

                        $arrays['l']['fg'] = 1;
                        $arrays['l']['gp'] = $this->lendPriceApiRequest($grDate, $pnu);
                    }else{
                        $arrays['l']['fg'] = 0;
                    }

                break;
                case 'latlng':
                    $rs = DB::table($val)
                        ->selectRaw($queryList[$key])
                        ->wherePnu($pnu)->first();
                    // if(count($rs)){
                    if($rs){
                        // ll -> latlng
                        // dd($helpers->simpleAreaNm($rs->ju));
                        $_ll = [
                            'ju'=> $helpers->simpleAreaNm($rs->ju),
                            'yd'=> $helpers->simpleAreaNm($rs->yd),
                            'yd_code' => $rs->yd,
                        ];
                        // dd($_ll);
                        $arrays['ll']['items'][0] = $_ll;

                        $arrays['ll']['fg'] = 1;
                    }else{
                        $arrays['ll']['fg'] = 0;
                    }
                break;
            }
        };
        // dd(DB::getQueryLog()); // DB query 확인
        // dd($arrays);
        $similarParameter = [
            'yd_code' => ($arrays['ll']['fg'])? $arrays['ll']['items'][0]['yd_code']:'',
            'mk_type' => ($arrays['s']['fg'])? $arrays['s']['items'][0]['mk_type']:'',
            'stdGp' => ($arrays['ll']['fg'])? $arrays['ll']['items'][0]['gongsi_priece']:'',
            'lat' => ($arrays['ll']['fg'])? $arrays['ll']['items'][0]['lat']:'',
            'lng' => ($arrays['ll']['fg'])? $arrays['ll']['items'][0]['lng']:'',
            'sido_id' => ($arrays['ll']['fg'])? $arrays['ll']['items'][0]['sido']:'',
        ];

        $this->similarList($similarParameter);

        return response()->json([
            'error'=>false,
            'data'=>json_encode($arrays)
        ]);

    }
    private function lendPriceApiRequest($gr, $pnu){
        $path = 'http://apis.data.go.kr/1611000/nsdi/IndvdLandPriceService/attr/getIndvdLandPriceAttr';
        $apiKey = 'Y9X8U%2BwfGfPdI7Kl6VhWW2wBkXFEpfwUD9sTSD1jDXlrvw2XpU817JyZA2SEsT%2Fq4bEh6%2F8DmcyZCczDmdFGnA%3D%3D';
        // $pnu = '1111010100100010002';
        $params = http_build_query(array(
            'ServiceKey' => urldecode($apiKey),
            'pnu' => $pnu,
            'format' => 'xml',
            'numOfRows' => 100,
            'pageNo' => 1
        ));


        function bindItems($items, $grYear){
            // 현재년도 -5년부터,  거래년도에 따른 기준년도 설정 (없으면 최신 년도)
            $carbon = Carbon::now()->timezone('Asia/Seoul');
            $now_year = $carbon ->year; // 현재 년도
            $now_month = $carbon -> month; // 현재 월

            $stdKey = 0;
            foreach($items as $key => $val){
                if($val->year > $now_year-5){
                    $arrs['items'][] = (array)$val;
                };

                if((int)$grYear === (int)$val->year){ // "===" : 엄격 비교연산자 (값 + 데이터유형)
                    $stdKey = $key;
                };
            }
            $arrs['std'] = $stdKey? (array)$items[$stdKey] : (array)$items[count($items)-1];
            $arrs['fg'] = 1;
            global $updateFlag;
            $updateFlag = updateStatus($items, $now_year, $now_month);
            return $arrs;
        };

        function updateStatus($items, $now_year, $now_month){
            if($now_year > $items[count($items)-1]->year && 7 <= $now_month && $now_month <= 8){ // 현재 년도보다 크고 현재가 6~8월이면 update
                return true;
            }else{
                return false;
            }
        };
        function updateApi($path, $params){ // 기존 DB에 있는 값이 있고 업데이트가 필요할경우
            $obj = new ReportController();
            $res = $obj -> apiOpts($path, $params);
            // $res = $this -> apiOpts($path, $params);
            if(!$res) return;
            $fields = (array)$res -> fields; // SimpleXMLElement 유형 array로 변환
            $total = $res -> totalCount;

            foreach($fields['field'] as $key => $val){ //
                    if((int)trim($val -> stdrYear) === Carbon::now()->timezone('Asia/Seoul')->year){
                    // if((int)trim($val -> stdrYear) === 2022){
                        $query[] = ['pnu' => trim($val -> pnu), 'year' => (int)trim($val -> stdrYear), 'lend_price' => (int)trim($val -> pblntfPclnd), 'created_dt' => Carbon::now()->timezone('Asia/Seoul')];
                        // dd($query);
                        $rs = DB::table('big_data.lend_price_tb_'.substr(trim($val -> pnu),0,2))
                        ->insert($query);
                        if(!$rs){
                            return;
                            // return error
                        };
                        unset($query);
                    }else{
                        return;
                    }
            };

        };

        $res = explode('.', $gr);
        $grYear = $res[0];

        $updateFlag = false;
        global $updateFlag;

        $checkLend = DB::table('big_data.lend_price_tb_'.substr($pnu,0,2))->wherePnu($pnu)->groupBy('year')->orderBy('year', 'asc')->get()->toArray();
        // dd(DB::getQueryLog()); // DB query 확인
        $checkLend = [];
        // dd($checkLend);
        if(count($checkLend)){
            // usort($checkLend, function ($a, $b){ // 내림차순 정렬
            //     if($a->year == $b->year){
            //         return 0;
            //     }
            //     return ($a->year > $b->year)? -1:1; // 내림차순
            // });
            $dataArray = bindItems($checkLend, $grYear);

            if($updateFlag){
                updateApi($path, $params);
                $lend = DB::table('big_data.lend_price_tb_'.substr($pnu,0,2))->wherePnu($pnu)->orderBy('year', 'asc')->get()->toArray();
                return (array)bindItems($checkLend, $grYear);
            }else{
                return (array)$dataArray;
            }
            unset($checkLend); // 변수 제거

            // dd(DB::getQueryLog()); // DB query 확인

            // [추정가격]
            // 1. 거래년도 공시지가: 공시지가상승율 적용_최근 3년 상승율_공식: (2021년 기준) (2018년공시지가/2021년공시지가)^(1/3)-1

        }else{
            // DB에 없는 토지 공시지가 api 활용
            $res = $this -> apiOpts($path, $params);
            if(!$res) return ['fg'=>0];

            $fields = (array)$res -> fields; // SimpleXMLElement 유형 array로 변환
            $total = $res -> totalCount;

            foreach($fields['field'] as $key => $val){ //
                $data[] = (object)[
                    'pnu'=> $pnu,
                    'year'=> (int)trim($val -> stdrYear),
                    'lend_price'=> (int)trim($val -> pblntfPclnd)
                ];
            };

            $dataArray = bindItems($data, $grYear);
            foreach($dataArray['items'] as $k => $i){
                $query[] = ['pnu' => $i['pnu'], 'year' => $i['year'], 'lend_price' => $i['lend_price'], 'created_dt' => Carbon::now()->timezone('Asia/Seoul')];
            };
            // DB insert
            $rs = DB::table('big_data.lend_price_tb_'.substr($pnu,0,2))
            ->insert($query);
            if(!$rs){
                return ['fg'=>0];
            };
            return (array)$dataArray;
            unset($data);

        }
    }

    // 0509 - 기준 pnu 으로 유사사례 pnu 들을 조회
    private function similarList($param){
        // #0 기준 lat, lng, pnu, 년도, 공시지가, 용도

        // dd($param);
        $whereGroup = [];
        // #1 default query
        $whereGroup[] = 'SELECT m.idx, CONVERT(m.pnu, char)as pnu, m.ref_sales_no, s.address, s.PRICE, date_format(s.GURAE_YM, "%Y-%m")AS GURAE_YM, s.YONGDO_AREA, IFNULL(i.CI_IMG_PATH,"")as CI_IMG_PATH, m.lat, m.lng, if(s.jubdo_fg = "f2","대로변","이면") as jubdo, ( '.$param['lat'].' - m.lat)*('.$param['lat'].' - m.lat)+('.$param['lng'].' - m.lng)*('.$param['lng'].' - m.lng) AS order_,
                        (6371*acos(cos(radians('.$param['lat'].'))*cos(radians(m.lat))*cos(radians(m.lng)-radians('.$param['lng'].'))+sin(radians('.$param['lat'].'))*sin(radians(m.lat))))
                        AS distance
                        FROM latlng_tb AS m
                        INNER JOIN crel_meagak as s
                        ON m.ref_sales_no = s.MK_NO
                        LEFT OUTER JOIN crel_img_list as i
                        ON m.pnu = i.pnu AND i.CI_IMG_FLAG = "Y"
                        WHERE m.lat IS NOT NULL';

        // #2 용도지역 주거가 있는 용도지역 코드 d3, d4, d5, d6 => 매각 데이터 있음
        $areaCheckArray = ['d3', 'd4', 'd5', 'd6'];
        // $param['yd_code'] = 'd3';
        $whereGroup[] = "AND yd_code ". ((in_array($param['yd_code'] ,$areaCheckArray))? '':'NOT') ." IN ('d3', 'd4', 'd5', 'd6')"; // latlng table
        // #3 단독 다가구면 단독 다가구만 => 매각 데이터 있음
        $mkCheckArray = ['단독', '다가구'];
        // $param['mk_type'] = '단독';
        $whereGroup[] = (in_array($param['mk_type'] ,$mkCheckArray))? "AND mk_type in ('단독', '다가구')":"";

        dd($whereGroup);
        //
        if(count($arr)){ return ['fg'=>0]; }
        $pnu = $arr['param'];

        // 주거가 있는 용도지역 코드 d3, d4, d5, d6
        if(strpos($res['yd_nm'],'주거')){ // 주거가  있으면 주거가 있는 조건 or 없으면 주거가 없는 조건
            $yongdoSql = 'AND s.YONGDO_AREA like "%주거%"';
        }else{
            $yongdoSql = 'AND s.YONGDO_AREA not like "%주거%"';
        }
        // 선택한 주소 매각 유형을 가져온다
        $mkSql = DB::table('crel_meagak')->selectRaw('YONGDO_AREA, mk_type')->where('pnu','=',$res['pnu'])->get();
        // 단독 또는 다가구면 조건 -> 매각 유형 조건 단독 or 다가구로 검색 (mk_type 이상함, 상업, 일반으로 수정)

        // 실거래가 있는 건물중 선택 건물에 용도 유형(주거 O, 주거X), 주용도 유형(단독, 다가구)가 같은걸 거리순으로..
        // 공시지가 비슷, 현재 3년 전까지
        $queryString = '';

    }
    public function apiOpts($path, $param){
        $opts = array(
            CURLOPT_URL => $path . '?' . $param,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSLVERSION => 1,
            CURLOPT_HEADER => false
        );
        $curl_session = curl_init();
        curl_setopt_array($curl_session, $opts);
        $return_data = curl_exec($curl_session);
        if (curl_errno($curl_session)) {
            return 0;
            // throw new Exception(curl_error($curl_session));
        } else {
            curl_close($curl_session);
            return simplexml_load_string($return_data);
        }
    }
}
