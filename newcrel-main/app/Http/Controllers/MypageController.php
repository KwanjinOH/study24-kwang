<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Detailes;
use App\Interests;
use App\Terms;
use \Carbon\Carbon;
use App\Helpers\HelperFunctions;
use Auth;
use DB;

class MypageController extends Controller
{
    public function read()
    {
        $user = Auth::guard('bduser')-> user();
        $mypage = DB::connection('mysql_ori')->table('terms')
                    ->leftJoin('user_interests', 'terms.user_id', '=', 'user_interests.user_id')
                    ->leftJoin('user_role', 'user_role.user_id', '=', 'terms.user_id')
                    ->leftJoin('role', 'role.id', '=', 'user_role.role_id')
                    ->select('role.authority', 'terms.marketing', DB::raw('count(user_interests.pnu)as cnt'))
                    ->where('terms.user_id', '=', $user->id)
                    ->first();

        if($mypage->authority == 'MEMBER'){
            $authority = '일반회원';
        }else if($mypage->authority == 'PREMIUM'){
            $authority = 'PREMIUM';
        }

        $data = [
            'email' => $user->email,
            'authority' => $authority,
            'interests' => $mypage->cnt,
            'marketing'=> $mypage->marketing,
        ];

        return response()-> json([
            'error'=> false,
            'data'=> (object)$data
        ]);
    }

    public function interestGet(request $request)
    {
        // auth check for middlewere
        //
        $user = Auth::guard('bduser')->user();
        try{
            $interestList = DB::connection('mysql_ori')->select(DB::raw(
                "SELECT c.MK_NO, DATE_FORMAT(a.updated_at, '%Y-%m-%d')as updated_at, a.unit, a.memo,c.mk_type, c.jubdo_fg
                , b.yd_code, b.ju_code, c.PRICE, c.ADDRESS, DATE_FORMAT(c.GURAE_YM, '%Y-%m')as GURAE_YM, a.pnu, d.path
                FROM user_interests AS a
                INNER JOIN crel.latlng_tb AS b ON a.pnu = b.pnu
                INNER JOIN crel.crel_meagak AS c ON b.ref_sales_no = c.MK_NO
                -- INNER JOIN lo_crel.latlng_tb AS b ON a.pnu = b.pnu
                -- INNER JOIN lo_crel.crel_meagak AS c ON b.ref_sales_no = c.MK_NO
                LEFT OUTER
                JOIN bd_img AS d ON a.pnu = d.pnu
                WHERE a.user_id = ". $user->id ."
                ORDER BY a.updated_at DESC"
            ));
        } catch (QueryException $e){
            return response()-> json([
                'error'=> 1064,
                'msg'=> '<div class="layer-msg" style="text-align: center;">예기치 않은 오류가 발생했습니다.</div><button class="close">확 인</button>'
            ]);
        }

        // dd($interestList);

        $helpers = new HelperFunctions;

        // dd($helpers->priceFormat(9999999999));
        $htmlString = '<div class="side-warp ir-lists"><div class="empty'. (count($interestList)>0? "":" on") .'"><span>저장된 관심 목록이 없습니다.</span></div>';
        foreach($interestList as $item){
            $path = $item->path? "<div class='s-ig'><img src='". $item->path ."' alt='building'/></div>":"";
            $regdate = $item->updated_at;
            $unit = $item->unit? "매물":"실거래";
            $gr = $item->GURAE_YM? "<li>". $item->GURAE_YM ."</li>":"";
            $mktype = $item->mk_type? "<li>". $item->mk_type ."</li>":"";
            $jubdo = $item->jubdo_fg? "<li>". ($item->jubdo_fg == "f1"? '이면':'대로변') .'</li>':"";
            $yd = $item->yd_code? "<li>". $helpers->simpleAreaNm($item->yd_code) ."</li>":"";
            $ju = $item->ju_code? "<li>". $helpers->simpleAreaNm($item->ju_code) ."</li>":"";
            $addr = '<a id="my-addr" href="javascript:;" data-pnu = "'.  $item->pnu .'">#'. $item->ADDRESS .'</a>';
            $price = '<span>'. $helpers->priceFormat($item->PRICE) .'억</span>';
            $memo = '<textarea '. ($item->memo? "":"class='txt-hide'") .' readonly>'. $item->memo .'</textarea>';
            // dd($helpers->simpleAreaNm($item->ju_code));
            $htmlString .= '
                <div class="ir-list" data-pnu="'. $item->pnu .'">
                    <div class="s-bage">
                        <span>'. $unit .'</span>
                    </div>
                    <div class="s-hd">
                        <span class="reg-date">'. $regdate .'</span>
                        <div class="del-btn">
                            <a href="javascript:;"><i class="fas fa-times"></i></a>
                        </div>
                    </div>
                    '. $path .'
                    <div class="s-info">
                        <div class="s-addr">
                            '. $addr .'
                        </div>
                        <div class="s-price">
                            '. $price .'
                        </div>
                        <ul class="s-etc">
                            '. $gr .''. $mktype .''. $jubdo .''. $yd .''. $ju .'
                        </ul>
                        <div class="memo">
                            '. $memo .'
                            <button class="m-btn" href="javascript:;" >메모 수정</button>
                        </div>
                    </div>
                </div>
            ';
        }
        return response()->json([
            'error' => false,
            'dom' => $htmlString
        ]);
    }
    public function interestDelete(request $request)
    {
        // dd($request->pnu);

        $user = Auth::guard('bduser')->user();
        try{
            Interests::whereUserId($user->id)->wherePnu($request->pnu)->delete();
        } catch (QueryException $e){
            return response()-> json([
                'error'=> 1064,
                'msg'=> '<div class="layer-msg" style="text-align: center;">예기치 않은 오류가 발생했습니다.</div><button class="close">확 인</button>'
            ]);
        }

        return response()->json([
            'error'=> false
        ]);

    }

    public function detailsGet(request $request)
    {

        // auth check for middlewere
        //
        $user = Auth::guard('bduser')->user();

        $details = Detailes::whereUserId($user->id)->first();

        try{
            $htmlString = '<div class="mm-warp"><div class="mm-category"><label class="mm-sbj">이메일</label><span>'.  $user->email .'</span></div><div class="mm-category"><label class="mm-sbj"for="nicknm">닉네임</label><input class="mm-ip"id="nicknm"name="nicknm"type="text"value="'. ($details->nick_name ? $details->nick_name:"") .'"/></div><div class="mm-category"><label class="mm-sbj"for="birth">생년월일</label><input class="mm-ip"id="birth"name="birth"type="date"value="'. ($details->birth ? $details->birth:"") .'"/></div><div class="mm-category"><label class="mm-sbj"for="">관심분야</label><div class="mm-coce"><div><input id="mm-c1"name="c1"type="checkbox"value="c1"'. ($details->concern_1 ? "checked":"") .'/><label class="chk-coce"for="mm-c1"><span>세미나(개발,세무)</span></label></div><div><input id="mm-c2"name="c2"type="checkbox"value="c2"'. ($details->concern_2 ? "checked":"") .'/><label class="chk-coce"for="mm-c2"><span>가치평가</span></label></div><div><input id="mm-c3"name="c3"type="checkbox"value="c3"'. ($details->concern_3? "checked":"") .'/><label class="chk-coce"for="mm-c3"><span>시장동향</span></label></div><div><input id="mm-c4"name="c4"type="checkbox"value="c4"'. ($details->concern_4 ? "checked":"") .'/><label class="chk-coce"for="mm-c4"><span>마케팅문서</span></label></div><div><input id="mm-c5"name="c5"type="checkbox"value="c5"'. ($details->concern_5 ? "checked":"") .'/><label class="chk-coce"for="mm-c5"><span>기타</span></label><input id="c5-ip"class="mm-ip"name="c5-txt"type="text"'. ($details->concern_5 ? "":"disabled") .' value="'. ($details->concern_5_txt ? $details->concern_5_txt:"") .'"/></div></div></div></div><div class="footer-btns"><a id="modi-btn" href="javascipt:;">수정</a></div>';
        } catch (QueryException $e){
            return response()-> json([
                'error'=> 1064,
                'msg'=> '<div class="layer-msg" style="text-align: center;">예기치 않은 오류가 발생했습니다.</div><button class="close">확 인</button>'
            ]);
        }
        return response()->json([
            'error' => false,
            'dom' => $htmlString
        ]);
    }
    public function modify(request $request)
    {

        $user = Auth::guard('bduser')->user();
        try{
            $rs = Detailes::whereUserId($user->id)
                ->update([
                    'nick_name' => ($request->nicknm)? $request->nicknm:'',
                    'birth' => ($request->birth)? $request->birth:null,
                    'concern_1' => ($request->coce['c1'])? 1:0,
                    'concern_2' => ($request->coce['c2'])? 1:0,
                    'concern_3' => ($request->coce['c3'])? 1:0,
                    'concern_4' => ($request->coce['c4'])? 1:0,
                    'concern_5' => ($request->coce['c5'])? 1:0,
                    'concern_5_txt' => ($request->c5txt)? $request->c5txt:'',
                ]);
            return response()-> json([
                'error'=> false,
                'msg' => '<div class="layer-msg" style="text-align: center;">회원 정보가 수정되었습니다.</div><button class="close">확 인</button>',
            ]);

        } catch (QueryException $e){
            return response()-> json([
                'error'=> 1064,
                'msg'=> '<div class="layer-msg" style="text-align: center;">예기치 않은 오류가 발생했습니다.</div><button class="close">확 인</button>'
            ]);
        }

    }

    public function marketingConcent(request $request)
    {
        $user = Auth::guard('bduser')->user();
        try{
            Terms::whereUserId($user->id)->update([
                'marketing' => (int)$request->type,
                'update_date' => Carbon::now()
            ]);
        }catch(QueryException $e){
            return response()-> json([
                'error'=> 1064,
                'msg'=> '<div class="layer-msg" style="text-align: center;">예기치 않은 오류가 발생했습니다.</div><button class="close">확 인</button>'
            ]);
        }
    }
}
