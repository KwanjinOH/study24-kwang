'use strict'

$(function(){

    // 1. email send for auth
    let cfmAuth = document.querySelector('.cfm-auth'),
    sendBtn = cfmAuth.querySelector('#auth-send'),
    sendEmail = cfmAuth.querySelector('#sign-email');
    // email 검증 정규식
    let emailRegExp = /^[0-9a-zA-Z]([-_.]?[0-9a-zA-Z])*@[0-9a-zA-Z]([-_.]?[0-9a-zA-Z])*.[a-zA-Z]{2,3}$/i;

    custom.click(sendBtn, function(){

        if(sendEmail.value.match(emailRegExp) != null){
            custom.errMsgRemove(cfmAuth);
            // signSend error -> alert later create
            signSend(sendEmail.value, cfmAuth.querySelector('#auth-num'), this);

        }else{
            custom.errMsgCreate(cfmAuth, '이메일 형식이 잘못되었습니다.');
        }
    });

    // 2. eamil auth number check
    let cfmBtn = cfmAuth.querySelector('#cfm-btn'),
    cfmNumber = cfmAuth.querySelector('#sign-number');
    custom.click(cfmBtn, function(){
        let len = cfmNumber.value.length;
        if(len != 4){
            custom.errMsgCreate(cfmAuth, '4자리 인증번호를 입력해주시길 바랍니다.');
            return;
        }

        let sendObject = {
            'email': sendEmail.value,
            'code': cfmNumber.value,
            'type': 'sign'
        }

        let build = {
            param: sendObject,
            url: '/account/authVerification',
            method: 'post'
        }
        custom.ajax(build, function(data){
            if(data.error){
                custom.errMsgCreate(cfmAuth, data.msg);
                return;
            }
            layerCreate.alert(data);
            custom.errMsgRemove(cfmAuth);
        })
    });

    // 3. eamil auth number check
    const form = document.querySelector('#signForm');
    let submit = form.querySelector('#sign-submit');

    custom.click(submit, function(){
        let email = form.querySelector('#sign-email').value,
        pass = form.querySelector('#pass').value,
        pass_cfm = form.querySelector('#pass-cfm').value;

        // console.log(JSON.stringify({ email, pass, pass_cfm}));
        // let data = JSON.stringify({ email, pass, pass_cfm});
        let data = {
            'email': email,
            'pass': pass,
            'pass_confirmation': pass_cfm
        }

        let build = {
            param: data,
            url: '/account/sign',
            method: 'post'
        }
        custom.ajax(build, function(data){
            layerCreate.alert(data);
            custom.errMsgRemove(cfmAuth);
        })


    });


});


