var searchEvent = {
    status: false,
    parent: document.querySelector('.search-list'),
    target: false,
    timeoutId: undefined,
    input: function(el){
        // console.log(el)
        searchEvent.hide(el);
        custom.keyup(el, function(e){
            clearTimeout(searchEvent.timeoutId); // 기존의 타이머를 초기화합니다.
            let code = e.keyCode || e.width;
            // console.log(this.value)
            switch(code){
                case 13:
                    // $searchList.enter(filter);
                    searchEvent.enter(el.value);
                break;

                case 39: case 37: case 20:
                    // 39 : arrow right 키
                    // 37 : arrow left 키
                    // 37 : caps lock 키
                    // if(!$searchList.status && $searchList.target.length) $searchList.setTarget($());
                break;
                case 38: case 40:
                    if(searchEvent.status) searchEvent.listEvt.upDown(code);
                break;
                // case 37:
                    // if(!$searchList.status && $searchList.target.length) $searchList.setTarget($());
                // break;
                case 27:
                    // esc
                    // if(searchEvent.status)
                    searchEvent.listEvt.close(this);

                break;
                default:
                    console.log(code)
                    // var data = {keyword : filter};
                    // ajaxHandle.addressSearchHandle(data);
                    var val = this.value;
                    searchEvent.timeoutId = setTimeout(function() {
                        console.log('time')
                        if(val){
                            searchEvent.call(val);
                        }
                    }, 500); // 0.5초 후에 함수를 호출


                break;
            }
        })
    },
    call: function(val){
        // 최근검색기록 localstrage GET


        let places = _kakao.places;
        // let geocoder = _kakao.geocoder;
        let placesCallback = function(result, status) {
            if (status === kakao.maps.services.Status.OK) {
                // console.log(result)
                let lists = new Array();
                result.forEach((item, idx) => {
                    let addressNm = item.address_name.replace(val, '<b>'+val+'</b>'),
                    raddressNm_ = item.road_address_name.replace(val, '<b>'+val+'</b>'),
                    raddressNm = raddressNm_? '('+raddressNm_+')' : '';
                    placeNm = item.place_name.replace(val, '<b>'+val+'</b>');

                    let htmlString = `<li><div>${(placeNm)? `<strong>${placeNm}</strong>`:''}<span>${addressNm} ${raddressNm}</span></div></li>`;
                    lists.push(htmlString);
                });
                let liString = lists.join('');
                searchEvent.status = true;
                searchEvent.bind(liString);
            } else {
                let emptyString = `<li><div><span>검색결과가 없습니다.</span></div></li>`;
                searchEvent.status = true;
                searchEvent.bind(emptyString);
            }
        }
        places.keywordSearch(val, placesCallback);
        // geocoder.addressSearch(val, placesCallback)
    },
    bind: (places) => {
        clearTimeout(searchEvent.timeoutId);
        let parent = searchEvent.parent;
        parent.classList.add('on');

        let placesWarp = parent.querySelector('.places');
        searchEvent.reset(placesWarp);
        placesWarp.insertAdjacentHTML('beforeend', places);

    },

    reset: (el)=>{
        while (el.firstChild) {
            el.removeChild(el.firstChild);
        }
    },
    listEvt: {
        upDown: (type)=>{
            let parent = searchEvent.parent,
            placesWarp = parent.querySelector('.places');

            let lis = placesWarp.querySelectorAll('li');
            if(!searchEvent.target){
                searchEvent.target = lis[0];
            }else{
                if (type == 38) {
                    searchEvent.target = searchEvent.target.previousSibling;
                } else if(type == 40){
                    searchEvent.target = searchEvent.target.nextSibling;
                }

            }
            console.log(searchEvent.target);
            lis.forEach(li =>{
                if(searchEvent.target == li){
                    li.style.backgroundColor = '#F1F5FC';
                }else{
                    li.style.backgroundColor = '#fff';
                }
            })
        },
        close: (el)=>{

            el.value = '';
            el.blur(); // 포커스 해제
            let parent = searchEvent.parent;
            parent.classList.remove('on');

            let placesWarp = parent.querySelector('.places');
            searchEvent.reset(placesWarp);

        },



    },
    enter: (filter)=>{
        console.log(filter)
        let geo = _kakao.geocoder;
    },
    hide: (el)=>{

        custom.click(document.body, function(e){
            e.preventDefault();
            if(searchEvent.status){
                var isInput =  el.contains(e.target),
                isList = searchEvent.parent.contains(e.target);
                if(isInput || isList){
                    searchEvent.parent.classList.add('on');
                    // console.log('not hide');
                    return;
                }else{
                    // console.log('hide');
                    searchEvent.parent.classList.remove('on');
                }
                // console.log(e.target)
                // console.log(e.currentTarget)
                // console.log(e.target.parentNode)
            }

            // el.blur(); // 포커스 해제
        });




    },
}
