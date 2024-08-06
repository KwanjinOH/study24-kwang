var custom = {
    click: (el, fn)=> {
        el.addEventListener('click', fn);
    },
    submit: (el, fn)=> {
        el.addEventListener('submit', fn);
    },
    scroll: (el, fn)=> {
        el.addEventListener('scroll', fn);
    },
    mouseover: (el, fn)=> {
        el.addEventListener('mouseover', fn);
    },
    mouseout: (el, fn)=> {
        el.addEventListener('mouseout', fn);
    },
    mousemove: (el, fn)=> {
        el.addEventListener('mousemove', fn);
    },
    keyup: (el, fn)=> {
        el.addEventListener('keyup', fn);
    },
    change: (el, fn)=> {
        el.addEventListener('change', fn);
    },
    ajax: (build, callback)=> {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            beforeSend: function() {Loader.open()},
            complete: function() {Loader.off()},
            type: build.method,
            url: build.url,
            data: build.param,
            success: function(data){

                if(typeof callback == 'function'){
                    callback(data);
                }
            }
        });
    },
    maxTextLenth: (el, length) =>{
        console.log(el, length);
        let max = length;
        custom.keyup(el, function(e){
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            let currentLength = el.value.length;
            if(currentLength > max){
                console.log('asd');
                alert(`${max}자 이내로 입력해주시길 바랍니다.`);
            }
        })
        el.setAttribute('maxlength', max);
    },
    comma: (obj) =>{

        var obj_ = obj;
        if(typeof obj_ == 'number')obj_ = obj.toString();

        var regx = new RegExp(/(-?\d+)(\d{3})/);
        var bExists = obj_.indexOf(".", 0);
        var strArr = obj_.split('.');
        while (regx.test(strArr[0])) {
            strArr[0] = strArr[0].replace(regx, "$1,$2");
        }
        if (bExists > -1) {
            obj_ = strArr[0] + "." + strArr[1];
        } else {
            obj_ = strArr[0];
        }
        return obj_;
    },
    // account error messege bind
    errMsgCreate: (reference, msg)=>{
        custom.errMsgRemove(reference);

        let txt = `<div><i class="fa-solid fa-circle-exclamation"></i><span>${msg}</span></div>`,
        returnDom = returnHTML(txt, 'error-msg');
        reference.parentNode.insertBefore(returnDom, reference.nextSibling);
    },
    errMsgRemove: (reference)=>{
        if(reference.parentNode.querySelector('.error-msg')){
            let before = reference.parentNode.querySelector('.error-msg');
            reference.parentNode.removeChild(before);
         }
    },
    reportSalesFormat: (items) =>{

        let priceObject = {};
        items.forEach((item, idx) =>{
            let obj = {
                origin: item.price,
                format: custom.priceFormat(item.price),
            };
            priceObject[idx] = obj;
        });
        return priceObject;
    },
    priceFormat: (price)=>{
        let priceStr;
            if(price > 4){
                let billion = Math.floor((parseInt(price)/10000)), // 억단위
                billionStr = Math.floor((parseInt(price)/10000))? `${ custom.comma(billion)}억`:'',
                tenMillion = parseInt(price) - billion*10000,
                tenMillionStr = `${ custom.comma(tenMillion) }만`;

                priceStr = tenMillion? `${billionStr} ${tenMillionStr}` : `${billionStr}`;

                // price = Math.floor((parseInt(price)/10000) * 100) / 100; // 억단위로 소수점 두자리에서 자름
                // unit = '억';
            }
            return priceStr.trim();
    },
    bindValidationFormat: (item, not)=> {
        return (item)? item:not;
    },
    errorProcess: (item)=>{
        if(item.error === 401){
            // 사용자 인증
            layerCreate.login(item.msg);
        }else if(item.error === 1064){
            // DB 오류
        }
    },
    // backBtnNhome: ()=>{
    //     history.pushState(null, null, location.herf);
    //     window.onpopstate = function(e){
    //         history.go(1);
    //         var error = false,
    //         msg = '<div class="layer-msg">홈으로 돌아가시겠습니다?.</div><a href="/"><button style="background-color: #fbb900; color: #fff" font-size: 14px>홈으로</button></a>',
    //         url = 'close';

    //         let data = JSON.stringify({ error, msg, url});
    //         console.log(data);
    //         // layerCreate.alert(data);
    //     }
    // },


}

LOADER: {
    var Loader = {
        open: function(){
            this.off();
            // var parentBlurElement = document.querySelector('.blur-all');
            var body = document.querySelector('body');
            var div = document.createElement('div');
            div.className = 'Nloader';

            var array = [
                '<svg version="1.1" id="loader-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"',
                'width="40px" height="40px" viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;" xml:space="preserve">',
                '<path fill="#000" d="M43.935,25.145c0-10.318-8.364-18.683-18.683-18.683c-10.318,0-18.683,8.365-18.683,18.683h4.068c0-8.071,6.543-14.615,14.615-14.615c8.072,0,14.615,6.543,14.615,14.615H43.935z">',
                '<animateTransform attributeType="xml" attributeName="transform" type="rotate" from="0 25 25" to="360 25 25" dur="0.6s" repeatCount="indefinite"/>',
                '</path></svg>'
            ];
            var txt = array.join('');
            div.innerHTML = txt;
            body.appendChild(div);
        },
        off: function(){
            var Nloader = document.querySelector('.Nloader');
            if(Nloader) Nloader.parentNode.removeChild(Nloader);

        }
    }
}
