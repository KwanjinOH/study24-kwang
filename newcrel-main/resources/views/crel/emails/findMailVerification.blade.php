<!DOCTYPE html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>
    <div id="mailRead">
        <table align="center" border="0" cellpadding="0" cellspacing="0" style="width:100%;background-color:#e6e6e6">
            <tbody>
                <tr>
                    <td align="center">
                        <div style="max-width:650px;margin:0 auto">
                            <!-- header -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 auto;background:#ffb900">
                                <tbody>
                                    <tr>
                                        <td height="22" colspan="4"></td>
                                    </tr>
                                    <tr>
                                        <td width="20"></td>
                                        <td align="left" valign="top" width="556" height="33">
                                            <a href="{{ $details['host'] }}" target="_blank" rel="noreferrer noopener"> <!-- width="74" height="16" -->
                                            <img src="{{ $details['host'] }}/img/mail/logo_white.png" width="165" height="33" alt="부동산도서관" style="border:0">
                                            </a>
                                        </td>
                                        <td width="20"></td>
                                    </tr>
                                    <tr>
                                        <td height="21" colspan="4"></td>
                                    </tr>
                                </tbody>
                            </table>
                            <!-- //header -->
                            <!-- content -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 auto;background:#fff">
                                <tbody>
                                    <tr>
                                        <td>
                                            <table cellpadding="0" cellspacing="0" style="width:100%;margin:0;padding:0;background:#fff">
                                                <tbody>
                                                    <tr><td height="45" colspan="3"></td></tr>
                                                    <tr><td width="20"></td><td align="left" style="font-family:\'나눔고딕\',NanumGothic,\'맑은고딕\',Malgun Gothic,\'돋움\',Dotum,Helvetica,\'Apple SD Gothic Neo\',Sans-serif;font-size:29px;font-weight:bold;color:#666;line-height:35px"><span style="color:#6F6F6E">비밀번호 찾기</td><td width="20"></td></tr>
                                                    <tr><td height="22" colspan="3"></td></tr>
                                                    <tr><td width="20"></td><td align="left" style="font-family:\'나눔고딕\',NanumGothic,\'맑은고딕\',Malgun Gothic,\'돋움\',Dotum,Helvetica,\'Apple SD Gothic Neo\',Sans-serif;font-size:14px;color:#333;line-height:25px"><span style="color:#ff1414">{{ $details['email'] }}</span> 님.</td><td width="20"></td></tr>
                                                    <tr><td height="10" colspan="3"></td></tr>
                                                    <tr><td width="20"></td><td align="left" style="font-family:\'나눔고딕\',NanumGothic,\'맑은고딕\',Malgun Gothic,\'돋움\',Dotum,Helvetica,\'Apple SD Gothic Neo\',Sans-serif;font-size:14px;color:#333;line-height:25px">아래 인증 문자를 비밀번호 찾기 인증 문자란에 입력해 주시기 바랍니다.</td><td width="20"></td></tr>
                                                    <tr><td height="16" colspan="3"></td></tr>
                                                    <tr><td width="20"></td><td align="center" style="padding:10px 0 10px 0;font-family:\'나눔고딕\',NanumGothic,\'맑은고딕\',Malgun Gothic,\'돋움\',Dotum,Helvetica,\'Apple SD Gothic Neo\',Sans-serif;font-size:18px;color:#333;line-height:25px;background:#f7f7f7;font-size:24px">
                                                        {{-- <strong><a href="{{ route('account.confirm', $details['confirm_code']) }}" style="text-decoration: none; color:#fb9200" >인증하기</a></strong> --}}
                                                        <strong> {{ $details['code'] }} </strong>
                                                    </td><td width="20"></td></tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <tr>
                                            <td height="18" colspan="4"></td>
                                        </tr>
                                    </tr>
                                    <tr>
                                        <td height="1" style="background:#f2f2f2"></td>
                                    </tr>
                                </tbody>
                            </table>
                            <!-- //content -->
                            <!-- footer -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 auto;background:#fff">
                                <tbody>
                                    <tr><td align="left" style="padding:27px 20px 34px;font-family:\'나눔고딕\',NanumGothic,\'맑은고딕\',Malgun Gothic,\'돋움\',Dotum,Helvetica,\'Apple SD Gothic Neo\',Sans-serif;font-size:12px;color:#888;line-height:18px">이 메일은 발신전용입니다.</td></tr>
                                    <tr><td align="center" style="padding-bottom:72px;font-family:\'나눔고딕\',NanumGothic,\'맑은고딕\',Malgun Gothic,\'돋움\',Dotum,Helvetica,\'Apple SD Gothic Neo\',Sans-serif;font-size:13px;color:#333">© 2018. 부동산도서관 inc. all rights reserved.</td></tr>
                                </tbody>
                            </table>
                            <!-- //footer -->
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</html>

