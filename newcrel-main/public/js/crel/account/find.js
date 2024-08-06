'use strict'

$(function(){

    // 1. email send for auth
    let cfmAuth = document.querySelector('.cfm-auth'),
    sendBtn = cfmAuth.querySelector('#find-send'),
    sendEmail = cfmAuth.querySelector('#find-email');
    // email 검증 정규식
    let emailRegExp = /^[0-9a-zA-Z]([-_.]?[0-9a-zA-Z])*@[0-9a-zA-Z]([-_.]?[0-9a-zA-Z])*.[a-zA-Z]{2,3}$/i;
    custom.click(sendBtn, function(){

        if(sendEmail.value.match(emailRegExp) != null){
            custom.errMsgRemove(cfmAuth);
            findSend(sendEmail.value, this);

        }else{
            custom.errMsgCreate(cfmAuth, '이메일 형식이 잘못되었습니다.');
        }
    });

    // find-cfm-btn
    // 2. eamil auth number check
    let cfmBtn = cfmAuth.querySelector('#find-cfm-btn'),
    cfmNumber = cfmAuth.querySelector('#find-string');
    custom.click(cfmBtn, function(){
        let len = cfmNumber.value.length;
        if(len != 8){
            custom.errMsgCreate(cfmAuth, '8자리 인증문자를 입력해주시길 바랍니다.');
            return;
        }

        let sendObject = {
            'email': sendEmail.value,
            'code': cfmNumber.value,
            'type': 'find'
        }

        let build = {
            param: sendObject,
            url: '/account/authVerification',
            method: 'post'
        }
        custom.ajax(build, function(data){
            console.log(data)
            if(data.error){
                custom.errMsgCreate(cfmAuth, data.msg);
                return;
            }
            layerCreate.alert(data);
            custom.errMsgRemove(cfmAuth);
        })
    });

    // 3. eamil auth number check
    const form = document.querySelector('#resetForm');
    let submit = form.querySelector('#reset-submit');
    custom.click(submit, function(){

        let email = form.querySelector('#find-email').value,
        pass = form.querySelector('#pass').value,
        pass_cfm = form.querySelector('#pass-cfm').value;

        let data = {
            'email': email,
            'pass': pass,
            'pass_confirmation': pass_cfm
        }

        let build = {
            param: data,
            url: '/account/reset',
            method: 'post'
        }
        custom.ajax(build, function(data){
            layerCreate.alert(data);
            custom.errMsgRemove(cfmAuth);
        })
    });
});
