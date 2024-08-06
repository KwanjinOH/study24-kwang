const directLogin = {
    action: (layer)=>{
        console.log(layer);
        let form = layer.querySelector('#direct-login'),
        warp = layer.querySelector('.box-warp');
        custom.submit(form, function(){
            console.log('submit');
            // let formData = new FormData(document.querySelector('#direct-login')); // new FormData는 cosole.log 시 빈 객체인것처럼 보임 foreach로 append하여 전송
            // formData.forEach(function(value, key){
            //     jsonObject[key] = value;
            //   });
            //   const jsonString = JSON.stringify(jsonObject);
            //   console.log(jsonString);
            let param = {
                email: warp.querySelector('#account-email').value,
                password: warp.querySelector('#account-password').value
            }
            let tempMsg = false;
            for(key in param){
                if(param[key].length === 0){
                    tempMsg = true;
                    break;
                }
            };
            if(tempMsg){
                custom.errMsgRemove(warp);
                custom.errMsgCreate(warp, '입력되지 않은 정보가 있습니다.'); return;
            }

            // return;
            let build = {
                param: param,
                url: '/auth/login',
                method: 'post'
            }
            custom.ajax(build, function(data){
                if(data.error){
                    if(data.lock === 'wait'){
                        return;
                    };
                    custom.errMsgRemove(warp);
                    custom.errMsgCreate(warp, data.msg);
                    if(data.lock){ // 실시간 잠금 시간 표시
                        let cnt = document.querySelector('.error-msg').querySelector('strong');
                        // console.log(cnt);
                        var interval = setInterval(function(){
                            data.lock--;
                            cnt.textContent = data.lock;
                            if(data.lock == 0){
                                clearInterval(interval);
                                custom.errMsgRemove(warp);
                            }
                        }, 1000);
                    }
                }else{
                    let remember = {
                        flag: form.parentNode.querySelector('#email-remember').checked,
                        email: param.email
                    }
                    localStorage.setItem('remember', JSON.stringify(remember));

                    let el = document.querySelector('.layer');
                    if(typeof public !== 'undefined'){
                        if(public.picFlag) public.picFlag = 0;
                    }
                    closing(el);
                    headerEvent.logedElementCreate(true);

                }


            });
        });

    }
}
