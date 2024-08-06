var tileEvent= {
    els: {
        babge: document.querySelectorAll('.babge'),
        // compare: document.querySelector('.compare-button'),
        compare: document.querySelector('.map-compare'),
    },
    control: function(){
        this.els.babge.forEach(bb =>{
            custom.click(bb, function(){
                this.parentNode.classList.toggle('op');
                // if(this.parentNode.classList.contains('op')){
                //     this.parentNode.parentnode.style(maxWidth, '250px');
                // }
            })
        });
        this.compare(this.els.compare);
    },
    compare: (el)=>{
        let menu = el.querySelector('.parent-warp'),
        select = el.querySelector('.select'),
        down = el.querySelector('.down'),
        re = el.querySelector('.re');

        let cnt = 0;


        custom.click(menu, function(){
            // let selectSave = (public.selectFlag)? 'sub-btn select open on':'sub-btn select open';
            if(cnt % 2 === 0){
                menu.classList.add('on');

                select.className = 'sub-btn select open';
                down.className = 'sub-btn down open';
                re.className = 'sub-btn re open';
                cnt++;
            }else{
                // 1. select 버튼이 활성화 되어있다면 비활성화 상태로 hide
                // 2. select 기능이 활성화 되어있다면 비활성화 상태로 hide
                // 3. 비교분석 대상을 선택한 상태라면 보존 상태로 hide

                public.selectFlag = 0;
                let selectDots = document.querySelectorAll('.comaper-cnt.on');
                selectDots.forEach(dot =>{
                    dot.classList.remove('on');
                })
                // backend - 작업과 병행 - ing..

                menu.classList.remove('on');

                selectCursorRemove();

                select.className = 'sub-btn select hide';
                down.className = 'sub-btn down hide';
                re.className = 'sub-btn re hide';
                cnt++;
            }
        });

        custom.click(select, function(e){
            e.preventDefault(); e.stopPropagation();
            this.classList.toggle('on')

            if(this.classList.contains('on')){
                layerCreate.comparePopup();

                let selectCursor = returnHTML('', 'select-cursor');
                layerAppend(selectCursor);

                custom.mousemove(document, function(e){
                    let cursorLocation = `top: ${e.pageY - 10}px; left: ${e.pageX - 10}px`;
                    selectCursor.setAttribute('style', cursorLocation);
                });

                // compare more..
                public.selectFlag = 1;
                markerEvent.comparePic();

            }else{
                public.selectFlag = 0;
                selectCursorRemove();
            }
        });
        custom.click(down, function(){
            console.log('down');
        });
        custom.click(re, function(){
            console.log('re');
            // public.selectFlag = 0;
            markerEvent.compareReset();
        });


        var selectCursorRemove = ()=> {
            var el = document.querySelector('.select-cursor');
            if(el) el.parentNode.removeChild(el);
        }

    },
    compareDownCnt: ()=>{
        var total = document.querySelector('.total-compare-cnt');
        total.innerHTML = public.selectCnt;
    },

}
