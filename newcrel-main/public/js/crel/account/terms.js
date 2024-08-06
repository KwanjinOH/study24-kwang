'use strict'
$(function(){
    var warp = document.querySelector('.acc-warp');

    const terms = warp.querySelector('.trm'),
    afterBtn = warp.querySelector('#afterBtn');

    let checker = terms.querySelector('#t-chk-all'),
    selecters = terms.querySelectorAll('.selecters');

    custom.click(checker, function(){
        selecters.forEach(n=>n.checked = this.checked)
    });
    selecters.forEach(n=>{
        custom.click(n, function(){
            if(!this.checked && checker.checked) checker.checked = false;
        })
    });
    // terms after button
    let esCheckBoxs = terms.querySelectorAll('input.es'),
    unCheckBoxs = terms.querySelectorAll('input.un');

    custom.click(afterBtn, function(){
        let esCnt = 0;
        esCheckBoxs.forEach(n=>{
            if(!n.checked) esCnt++;
        });
        if(esCnt){ // 필수 항목 체크 안함
            let data = {
                'error': true,
                'msg': '<div class="layer-msg" style="text-align: center;">필수 항목을 확인주시기 바랍니다.</div>'
            }
            layerCreate.alert(data);
            return;
        }
        let unObj = {};
        unCheckBoxs.forEach(n=>{
            unObj[n.value] = (n.checked)? 1:0;
        });

        let build = {
            param: unObj,
            url: '/account/terms',
            method: 'post'
        }
        custom.ajax(build, function(data){
            if(data.error){
                layerCreate.alert(data);
                return;
            };
            document.location.href = data.url;
        })
    });
});

