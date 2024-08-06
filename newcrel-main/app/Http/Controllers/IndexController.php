<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Helpers\UserSystemInfoHelper;
use DB;
use Auth;

class IndexController extends Controller
{
    public function index(Request $request) {
        $userBroser =  UserSystemInfoHelper::get_browsers();
        if($userBroser == 'Internet Explorer'){
            return $this -> moreNotices(true, false);
        }else if($userBroser == 'Safari Browser'){
            return $this -> moreNotices(false, true);
        }
        return $this-> moreNotices(true, true);
    }

    public function bounds(Request $request)
    {
        $res = $request-> input();

        if($res['filter'] == 'false'){
            // marker 가격 수정
            $markerData = DB::select(DB::raw("SELECT m.idx, m.lat, m.lng, if(year(m.gr_date) >= year(now())-5, 1, 0)as colorType, IFNULL(TRIM(s.PRICE/100000000)+0, 0)as _price, TRUNCATE(IFNULL(TRIM(s.DGPDG/10000)+0, 0), 0)as _dgp, RIGHT(YEAR(gr_date),2) AS Y, date_format(gr_date,'%m') AS m
                                            , CONVERT(m.pnu, char)as pnu
                                            , m.jisang_cheung_ea, m.yongjeogyul, m.sayong_seungin_day
                                            FROM latlng_tb as m
                                            INNER JOIN crel_meagak as s
                                            ON s.mk_no = m.ref_sales_no

                                            WHERE m.lng BETWEEN ".$res['bound']['swLng']." AND ".$res['bound']['neLng']." AND m.lat BETWEEN ".$res['bound']['swLat']." AND ".$res['bound']['neLat']));

        }else{

        }
        // dd($markerData);

        // $sss = "SELECT m.idx, m.lat, m.lng, if(year(m.gr_date) >= year(now())-5, 1, 0)as colorType, (s.PRICE/100000000)as _price, (s.DGPDG/10000)as _dgp, RIGHT(YEAR(gr_date),2) AS Y, date_format(gr_date,'%m') AS m
        // , CONVERT(m.pnu, char)as pnu
        // , m.jisang_cheung_ea, m.yongjeogyul, m.sayong_seungin_day
        // FROM latlng_tb as m
        // INNER JOIN crel_meagak as s
        // ON s.mk_no = m.ref_sales_no
        // WHERE m.lng BETWEEN ".$res['bound']['swLng']." AND ".$res['bound']['neLng']." AND m.lat BETWEEN ".$res['bound']['swLat']." AND ".$res['bound']['neLat'];

        // dd($markerData);

        return response()-> json([
            'error'=> false,
            'markers'=> $markerData
        ]);
    }

    private function moreNotices($flag, $browser) {
        // $notices = DB::select("select title, content from notices order by id desc;");
        // $faqs = DB::select("select title, content from faqs order by id desc;");
        // if(Auth::guard('newuser')->user()){
        //     $userId = Auth::guard('newuser')->user()->id;
        //     $adminFg = Auth::guard('newuser')->user()->admin_fg;
        //     $cnt = usersBookmark::whereUserid($userId)->count();

        //     if ($flag) {
        //         if(!$browser){
        //             return view('crel.index', ['notices' => $notices, 'faqs' => $faqs, 'bookMarkCnt' => $cnt, 'adminfg' => $adminFg, 'explorer' => 1]);
        //         }
        //         return view('crel.index', ['notices' => $notices, 'faqs' => $faqs, 'bookMarkCnt' => $cnt, 'adminfg' => $adminFg, 'explorer' => 0]);
        //     }else{
        //         return view('crel.mobile.agentIndex',['bookMarkCnt' => $cnt]);
        //     }

        // }else{
        //     if ($flag) {
        //         if(!$browser){
        //             return view('crel.index', ['notices' => $notices, 'faqs' => $faqs, 'explorer' => 1]);
        //         }
        //         return view('crel.index', ['notices' => $notices, 'faqs' => $faqs, 'explorer' => 0]);
        //     }else{
        //         return view('crel.mobile.agentIndex');
        //     }

        // }

        return view('crel.index');
    }

}
