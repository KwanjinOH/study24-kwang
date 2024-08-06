var mypageEvent = {
    parentElement: document.querySelector('.side.parent'),
    childElement: document.querySelector('.side.child'),
    init: ()=>{
        mypageEvent.childPage();
        // mypageEvent.interest();
        mypageEvent.sideEvent();
    },
    // parentReSize: (flag)=>{
    //     if(flag){
    //         mypageEvent.parentElement.classList.add('on');
    //     }else{
    //         mypageEvent.parentElement.classList.remove('on');
    //     }
    // },
    logedElementBind: (item)=>{
        const mypageElement = mypageEvent.parentElement;
        let binds = mypageElement.querySelectorAll('[data-group=my-bind]');
        // console.log(binds);
        let marketingToggle;
        binds.forEach(bind=>{
            if(bind.dataset.key === 'marketing'){
                marketingToggle = bind;
                if(item[bind.dataset.key]){
                    bind.checked = true;
                }else{
                    bind.checked = false;
                };
            }else{
                bind.textContent = item[bind.dataset.key];
            }
        })

        custom.change(marketingToggle, function(e){
            e.preventDefault();

            let build = {
                param: {'type': (this.checked)? 1:0},
                url: '/u/concent',
                method: 'post'
            };

            custom.ajax(build, function(data){
                console.log(data);
                if(data.error){
                    custom.errorProcess(data.error);
                }
            });
        })


    },

    childPage: ()=>{
        let parent = mypageEvent.parentElement,
        childs = parent.querySelectorAll('.page');
        // console.log(childs);
        childs.forEach(child => {
            // console.log(child);
            custom.click(child, function(e){
                e.preventDefault();
                let id = this.getAttribute('id'),
                title = this.querySelector('.c-title').textContent;
                mypageEvent.childInit.title(title);
                if(id === 'ir-list'){
                    mypageEvent.interest();
                }else if(id === 'im-list'){
                    mypageEvent.userModify();
                }
                // mypageEvent.parentReSize(0);

            })
        });

    },
    childInit: {
        title: (title)=>{
            let child = document.querySelector('.side.child'),
            _title = child.querySelector('#cc-title');
            _title.textContent = title;
            child.classList.add('on');
        },
        empty: ()=>{
            let target = document.querySelector('.side-warp.child-title');
            let childElement = mypageEvent.childElement.firstElementChild;

            while (childElement){
                let nextSlibling = childElement.nextElementSibling;
                if(childElement !== target){
                    mypageEvent.childElement.removeChild(childElement);
                }
                childElement = nextSlibling;
            }
        }
    },
    interest: ()=>{
        // ajax bind
        let build = {
            param: '',
            url: '/u/interest',
            method: 'get'
        }
        custom.ajax(build, function(data){
            if(data.error){
                //errorprocess 나중에
                custom.errorProcess(data.error);

                return;
            }
            console.log(mypageEvent.childElement);
            mypageEvent.childElement.insertAdjacentHTML('beforeend', data.dom);
            mypageEvent.interestEvt();
        });
        //

    },
    interestEvt: ()=>{

        let lists = document.querySelectorAll('.ir-list');
        lists.forEach( list => {
            custom.click(list, function(e){
                e.preventDefault();
                let target = e.target.tagName;
                if(['BUTTON', 'A', 'I', 'TEXTAREA'].includes(target)){
                    if(target === 'BUTTON'){
                        memoModi(this, e.target);
                    }
                    if(target === 'I'){
                        delChild(this);
                    }
                    let readCheck = false;
                    if(target === 'TEXTAREA'){
                        readCheck = e.target.readOnly;
                    }
                    if(!readCheck){
                        return;
                    }
                }
                this.classList.toggle('pic');
            })

        })
        // memo modify
        function memoModi(_this, target) {
            console.log(target);
            let textarea = _this.querySelector('textarea');

            target.classList.toggle('on');
            if(target.classList.contains('on')){
                textarea.classList.remove('txt-hide');
                target.textContent = '메모 저장';
                textarea.readOnly = false;
                textarea.style.border = '1px solid #bbb';
                textarea.focus();

                custom.maxTextLenth(textarea, 45);
            }else{
                if(!textarea.value){textarea.classList.add('txt-hide')};
                target.textContent = '메모 수정';
                textarea.readOnly = true;
                textarea.style.border = '0';
            }



        }

        // delete
        function delChild(_this) {

            // confirm yes or no ??? 보류

            //

            let build = {
                'param': {pnu: _this.dataset.pnu},
                'url': '/u/interest/delete',
                'method': 'post',
            }
            custom.ajax(build, function(data){
                if(data.error){
                    custom.errorProcess(data.error);
                    return;
                }else{
                    let interestCnt = mypageEvent.parentElement.querySelector('[data-key=interests');
                    interestCnt.textContent -= 1;

                    _this.parentNode.removeChild(_this);
                    let lists = document.querySelector('.ir-lists'),
                    cnt = lists.querySelectorAll('.ir-list').length;
                    if(cnt === 0){
                        lists.querySelector('.empty').classList.add('on');
                    }
                }
            });


        };


        // compare

    },
    modifyEvtInit: ()=>{
        let etcChk = document.querySelector('#mm-c5');
        custom.change(etcChk, function(e){
            console.log('click');
            e.preventDefault();
            let ip = document.querySelector('#c5-ip');
            if(etcChk.checked){
                ip.disabled = false;
                ip.focus();
            }else{
                ip.disabled = true;
            }
        });

        // modify button
        let modiBtn = mypageEvent.childElement.querySelector('#modi-btn');
        custom.click(modiBtn, function(e){
            e.preventDefault();
            console.log('modi')
            let coce = mypageEvent.childElement.querySelector('.mm-coce'),
            chks = coce.querySelectorAll('[type=checkbox]');

            let chkObj = {};
            chks.forEach(chk=>{
                chkObj[chk.value] = (chk.checked)? 1:0;
            });

            let q = {
                nicknm: mypageEvent.childElement.querySelector('#nicknm').value,
                birth: mypageEvent.childElement.querySelector('#birth').value,
                coce: chkObj,
                c5txt: mypageEvent.childElement.querySelector('#c5-ip').value,
            }
            let build = {
                param: q,
                url: '/u/details/modify',
                method: 'post'
            }
            custom.ajax(build, function(data){
                if(data.error){
                    custom.errorProcess(data.error);
                }else{
                    layerCreate.alert(data);
                }
            })
        })
        //
    },
    userModify: ()=>{
        // console.log();
        let build = {
            param: '',
            url: '/u/details',
            method: 'get'
        }
        custom.ajax(build, function(data){
            if(data.error){
                custom.errorProcess(data.error);
                return;
            }
            mypageEvent.childElement.insertAdjacentHTML('beforeend', data.dom);
            mypageEvent.modifyEvtInit();


        });


    },
    sideEvent: ()=>{
        let sideBack = document.querySelector('#side-back'),
        child = document.querySelector('.side.child');
        custom.click(sideBack, function(e){
            e.preventDefault();
            child.classList.remove('on');
            // mypageEvent.parentReSize(1);
            mypageEvent.childInit.empty();
        })
    },


}


