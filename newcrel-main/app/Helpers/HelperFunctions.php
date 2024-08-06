<?php

namespace App\Helpers;
use DB;
use \Carbon\Carbon;

class HelperFunctions
{
    // 주용도, 용도지역 약어 모음
    public function simpleAreaNm($param)
    {
        // dd($param);
        $arrays = array(
            'd1' => '1종전용',
            'd2' => '2종전용',
            'd3' => '1종일반',
            'd4' => '2종일반',
            'd5' => '3종일반',

            'd6' => '준주거',
            'd7' => '중심산업',
            'd8' => '일반상업',
            'd9' => '근린상업',
            'd10' => '유통상업',

            'd11' => '전용공업',
            'd12' => '일반공업',
            'd13' => '준공업',
            'd14' => '보전녹지',
            'd15' => '생산녹지',

            'd16' => '자연녹지',
            'd17' => '개발제한',
            'd18' => '미정',
            'd19' => '관리지역',
            'd20' => '보전관리',

            'd21' => '생산관리',
            'd22' => '계획관리',
            'd23' => '농림지역',
            'd24' => '자연환경',

            'e1' => '근린생활',
            'e2' => '업무시설',
            'e3' => '교육연구',
            // 'e4' => '교육연고및복지시설',
            'e5' => '노유자',

            'e6' => '공장',
            'e7' => '종교',
            'e8' => '숙박',
            'e9' => '창고',
            'e10' => '자동차',

            'e11' => '판매',
            'e12' => '문화/집회',
            'e13' => '의료',
            'e14' => '위험물',
            'e15' => '운동',

            'e16' => '운수',
            'e17' => '위락',
            'e18' => '분뇨',
            'e19' => '동식물',
            'e20' => '관광',

            'e21' => '방송통신',
            'e22' => '교정/군사',
            'e23' => '수련',
            'e24' => '판매/영업',
            'e25' => '묘지',

            'e26' => '발전',
            'e27' => '자원순환',
            'e28' => '장례',
            'e29' => '단독주택',
            'e30' => '공동주택',

            'e31' => '기타',
            'e32' => '야영장'
        );
        // 배열에 값이 있는지 확인 in_array
        // $txt = (in_array($param, $arrays))? '':$arrays[$param];
        return (array_key_exists($param, $arrays))? $arrays[$param]:'';
    }

    public function priceFormat($num)
    {
        $num /= 100000000;
        $formatNum = floor($num * 100) / 100; // 소수점 아래 두 자리를 버림
        $formatNumValue = number_format($formatNum, 2, '.', ',');

        return $formatNumValue;
    }

    public function adjoiningSido($param)
    {
        $arrays = array(
            "11" => "and (m.pnu like '11%' OR m.pnu like '41%' OR m.pnu like '28%')",
            "26" => "and (m.pnu like '26%' OR m.pnu like '48%' OR m.pnu like '31%')",
            "27" => "and (m.pnu like '27%' OR m.pnu like '48%' OR m.pnu like '47%')",
            "28" => "and (m.pnu like '11%' OR m.pnu like '41%' OR m.pnu like '28%')",
            "29" => "and (m.pnu like '29%' OR m.pnu like '46%')",
            "30" => "and (m.pnu like '30%' OR m.pnu like '43%' OR m.pnu like '44%' OR m.pnu like '36%')",
            "31" => "and (m.pnu like '31%' OR m.pnu like '26%' OR m.pnu like '47%' OR m.pnu like '48%')",
            "36" => "and (m.pnu like '30%' OR m.pnu like '43%' OR m.pnu like '44%' OR m.pnu like '36%')",

            "41" => "and (m.pnu like '41%' OR m.pnu like '11%' OR m.pnu like '28%'OR m.pnu like '42%' OR m.pnu like '43%' OR m.pnu like '44%')",
            "42" => "and (m.pnu like '42%' OR m.pnu like '41%' OR m.pnu like '43%'OR m.pnu like '47%')",
            "43" => "and (m.pnu like '43%' OR m.pnu like '41%' OR m.pnu like '42%'OR m.pnu like '47%' OR m.pnu like '45%' OR m.pnu like '44%' OR m.pnu like '30%' OR m.pnu like '36%')",
            "44" => "and (m.pnu like '44%' OR m.pnu like '41%' OR m.pnu like '45%' OR m.pnu like '43%' OR m.pnu like '30%' OR m.pnu like '36%')",
            "45" => "and (m.pnu like '45%' OR m.pnu like '44%' OR m.pnu like '43%' OR m.pnu like '48%' OR m.pnu like '46%')",
            "46" => "and (m.pnu like '46%' OR m.pnu like '45%' OR m.pnu like '48%' OR m.pnu like '29%')",
            "47" => "and (m.pnu like '47%' OR m.pnu like '42%' OR m.pnu like '43%' OR m.pnu like '45%' OR m.pnu like '48%' OR m.pnu like '27%' OR m.pnu like '31%')",
            "48" => "and (m.pnu like '48%' OR m.pnu like '46%' OR m.pnu like '45%' OR m.pnu like '47%' OR m.pnu like '27%' OR m.pnu like '31%' OR m.pnu like '26%')",
            "50" => "and (m.pnu like '50%')"
        );

        return (array_key_exists($param, $arrays))? $arrays[$param]:'';

    }

    public function gongsiYul($dgp, $stdGongsi)
    {
        return ($dgp / ($stdGongsi / 0.3025)) * 100;
    }

    public function calculationPrice($main)
    {

        //  test 주소 : 서울특별시 강남구 역삼동 678-23번지
        // 토지가: 대지면적(평)*최근 공시지가*공시지가대비율*3.3058

        // 건물가: (5,000,000*연면적(평))-(((5,000,000*연면적(평))/50)*경과년수))

        // *서울 및 광역시만 5000000으로, 행정도는 300으로 진행

        if(!$main['s']['fg'] || !$main['p']['fg'] || !$main['lp']['fg'] || !$main['ll']['fg']){
            // 추정가격 계산식에 필요한 데이터 하나라도 없으면 0
            return 0;
        }

        $daeji_p = $main['s']['items'][0]['daeji_p'];
        $yeon_p = $main['s']['items'][0]['yeon_p'];
        $lendPrices = $main['lp']['items'];
        $lpDesc = count($lendPrices)-1;
        $descPrice = $lendPrices[$lpDesc]['lend_price'];
        $mainYul = $main['add']['items'][0]['gYul'];
        $bf = Carbon::now()->year - $main['p']['items'][0]['sayong_year'];
        // dd($daeji_p, $descPrice, $mainYul);
        $sidoPrice = 0;
        $flagArray = [11, 26, 27, 28, 29, 30, 31];
        if(in_array($main['ll']['items'][0]['sido'], $flagArray)){
            $sidoPrice = 5000000;
        }else{
            $sidoPrice = 3000000;
        }
        // $mainYul = 310.38;
        $t = ($daeji_p * $descPrice * ($mainYul/100) * 3.3058);
        $b = ($sidoPrice*$yeon_p)-((($sidoPrice*$yeon_p)/50)*$bf);
        // dd($t, $b, $t+$b, (int)(($t+$b)/100000000));

        // !! 매우 큰 숫자 계산 처리는 PHP 확장 라이브러리 필요함. 없을시 아래 처럼 DB에서 처리 할수도 있음.
        // $tString = "(CAST('{$daeji_p}' AS DECIMAL(30,10)) * CAST('{$descPrice}' AS DECIMAL(30,10)) * CAST('{$mainYul}' AS DECIMAL(30,10)) * CAST('3.3058' AS DECIMAL(30,10))) as t";
        // $bSub1 = "(CAST('5000000' AS DECIMAL(30,10)) * CAST({$yeon_p} AS DECIMAL(30,10)))";
        // $bSub2 = "(({$bSub1}/50)*  {$bf})";
        // $bString = "{$bSub1} - {$bSub2}";
        // $tt = DB::table(DB::raw('DUAL'))
        //             ->selectRaw($bString)->get();
        //

        return (int)(($t+$b)/100000000);

    }
    // report - address explode
    public function addrExplode($data){
        $e = explode(' ', $data);
        $t ='';
        for($i=2; $i<count($e); $i++){
            $t .= $e[$i].' ';
        }
        return trim($t);
    }
    // report - string(string)
    public function bracketStringJoin($a, $at, $b, $bt)
    {
        return number_format($a, 2, '.', ',').$at.' ('.number_format($b, 2, '.', ',').$bt.')';
    }
    public function bracketStringJoin2($a, $at, $b, $bt)
    {
        return (int)$a+(int)$b.'('.$at.': '.$a.'/'.$bt.': '.$b.')';
        // return number_format($a, 2, '.', ',').$at.' ('.number_format($b, 2, '.', ',').$bt.')';
    }
}
