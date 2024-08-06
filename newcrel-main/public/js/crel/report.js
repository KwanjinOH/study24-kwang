'use strict'
var repo = {
    coords: undefined,
    build: (p)=>{
        let build = {
            param: p,
            url: '/n/report',
            method: 'get'
        }
        custom.ajax(build, function(item){
            if(item.error){
                // error alert
                custom.errorProcess(item);
                return;
            }else{
                reportCreate.create(JSON.parse(item.data));
            }
        });
    },
    maps: (el, data)=>{
        repo.coords = _kakao.latlng(data.p.items[0].lat, data.p.items[0].lng);
        let roadbox = el.querySelector('.roadview'),
        cadastralbox = el.querySelector('.report-m.ca');
        mapEvent.roadview(roadbox, repo.coords);
        mapEvent.cadastral(cadastralbox, repo.coords, data);
    },
    modelObjects: (data)=>{

        var _pu = data.p.items,
        totalObjets = {},
        setter = (position, d)=>{
            totalObjets[position] = d;
        },
        _ll = data.ll;

        const repoSummary = {};
        // data.s.fg = 0;
        if(data.s.fg){
            /**
             *  1. 거래금액 && 직전거래 있음
             *  2. 직전거래 없음
             *  3. 거래금액 없음
             */
            let _su = data.s.items,
            su_prices = custom.reportSalesFormat(_su),
            size = Object.keys(su_prices).length,
            difference = {};

            // size = 1;

            if(size === 2){
                let riseOrigin = ((su_prices[0].origin/su_prices[1].origin)*100)-100;
                difference.rise = Math.trunc(riseOrigin * 100) / 100; // Math.floor -> 음수는 반올림되서 trunc 사용
                difference.origin = su_prices[0].origin-su_prices[1].origin;
                difference.format = custom.priceFormat(Math.abs((su_prices[0].origin-su_prices[1].origin)))
                difference.sign = ((su_prices[0].origin-su_prices[1].origin) < 0)? '-':'+';
                difference.style = ((su_prices[0].origin-su_prices[1].origin) < 0)? 'minus':'plus';

                repoSummary.b_gr = _su[1].sales_day;
                repoSummary.b_price = su_prices[1].format;

            }

            repoSummary.price = `${su_prices[0].format}원`;
            repoSummary.rise = (Object.keys(difference).length)? `${difference.sign} ${difference.format}원 (${difference.rise}%)`:0;
            repoSummary.style = difference.style; // undefined or value
            repoSummary.sales_day = _su[0].sales_day;
            repoSummary.daeji = custom.comma(_su[0].daeji) + '㎡';

            repoSummary.daeji_p = custom.comma(_su[0].daeji_p) + 'py';
            repoSummary.yeon = custom.comma(_su[0].yeon) + '㎡';
            repoSummary.yeon_p = custom.comma(_su[0].yeon_p) + 'py';
            repoSummary.dgp = custom.comma(_su[0].dgp) + '만원';
            repoSummary.ymp = custom.comma(_su[0].ymp); // 현재 카테고리 화면엔 없음  추후 수정 사항 있을시 적용

            repoSummary.yd = (_ll.items[0].yd)? _ll.items[0].yd:_su[0].yd;

        }else{
            repoSummary.daeji = custom.comma(_pu[0].daeji) + '㎡';
            repoSummary.daeji_p = custom.comma(_pu[0].daeji_p) + 'py';
            repoSummary.yeon = custom.comma(_pu[0].yeon) + '㎡';
            repoSummary.yeon_p = custom.comma(_pu[0].yeon_p) + 'py';
        }
        repoSummary.cal = (_ll.items[0].calPrice)? custom.comma(_ll.items[0].calPrice) + '억원':'추정 실패';

        repoSummary.ju = (_ll.items[0].ju)? _ll.items[0].ju:_pu[0].ju;
        repoSummary.jc = `기계 ${(_pu[0].jc_m)? _pu[0].jc_m:0} / 자주 ${(_pu[0].jc_j)? _pu[0].jc_j:0}`;
        repoSummary.lift = `승용 ${(_pu[0].lift_b)? _pu[0].lift_b:0} / 비상 ${(_pu[0].lift_s)? _pu[0].lift_s:0}`;
        repoSummary.gm = `${(_pu[0].jisang)? _pu[0].jisang:0}F / B${(_pu[0].jiha)? _pu[0].jiha:0}`;
        repoSummary.sy = _pu[0].sayong;

        setter('s', repoSummary);

        //
        return totalObjets;

    },
    bind: (el, data)=>{
        // #1 보고서 주소는 kakako api, 즐겨찾기는 매각 주소 (통일 필요)
        _kakao.geocoder.coord2Address(repo.coords.getLng(), repo.coords.getLat(), function(result, status){
            if(status === kakao.maps.services.Status.OK) {
                let obj = {};
                if(result[0].road_address){
                    obj.raddr = result[0].road_address.address_name;
                    obj.bd = result[0].road_address.building_name;
                }
                obj.addr = result[0].address.address_name;

                var ex = el.parentNode.querySelector('.example'),
                exbd = ex.querySelector('[data-category="example-0"][data-item="bdnm"]');
                exbd.value = (obj.bd)? obj.bd:''; // main 건물만 건물명 api로 활용


                if(obj.raddr){
                    let split = obj.raddr.split(' '),
                    splitStr = '';
                    split.forEach((str, idx)=>{
                        if(idx > 1) splitStr += str+' ';
                    });
                    obj.raddr = splitStr.trim();
                }
                for(let key in obj){
                    el.querySelector('[data-item="'+ key +'"]').innerHTML = obj[key];
                }
            }else{
                // error alert
                throw error('Address status error');
            }
        });
        console.log(data);
        let objects = repo.modelObjects(data);

        let categorys = el.querySelectorAll('[data-category]'),
        befireSalecnt = 0;
        categorys.forEach(el =>{
            // console.log(el)
            let ca = el.dataset.category,
            key = el.dataset.item;
            if(ca === 'api') return;

            switch(key){
                case 'price':
                    if(!objects[ca][key]){
                        console.log(el.parentNode)
                        el.parentNode.classList.add('hide');
                        el.parentNode.parentNode.parentNode.querySelector('.sales-empty').classList.remove('hide');
                    }else{
                        el.innerHTML = objects[ca][key];
                    }
                break;
                case 'rise':
                    el.parentNode.classList.add(objects[ca].style);
                    el.innerHTML = custom.bindValidationFormat(objects[ca][key], '-');
                break;
                case 'b_gr': case 'b_price':
                    if(!objects[ca][key]){
                        el.parentNode.classList.add('hide');
                        befireSalecnt++;
                    }else{
                        el.innerHTML = custom.bindValidationFormat(objects[ca][key], '정보 없음');
                    }
                    if(befireSalecnt === 2){
                        el.parentNode.parentNode.querySelector('.sales-empty').classList.remove('hide');
                    }

                break;
                default:
                    el.innerHTML = custom.bindValidationFormat(objects[ca][key], '정보 없음');
                break;
            }


            // salesType = (Object.keys(priceformatObject).length)? 1:0;
            // //
            // if(salesType){
            //     if(key === 'rise' || key === 'price' || key === 'cal'){
            //         if(priceformatObject[key]){
            //             c.parentNode.classList.add(priceformatObject.style);

            //         }else{
            //             c.parentNode.classList.add('empty');
            //         }
            //         c.innerHTML = (priceformatObject[key])? priceformatObject[key]:'-';
            //     }
            // }

        });

        return;
        let priceformatObject = {};
        if(data.s.fg){
            let su_prices = custom.reportSalesFormat(data.s.items),
            size = Object.keys(su_prices).length,
            difference = {};
            console.log(su_prices)
            if(size === 2){
                let riseOrigin = ((su_prices[0].origin/su_prices[1].origin)*100)-100;

                difference.rise = Math.trunc(riseOrigin * 100) / 100; // Math.floor -> 음수는 반올림되서 trunc 사용
                difference.origin = su_prices[0].origin-su_prices[1].origin;
                difference.format = custom.priceFormat(Math.abs((su_prices[0].origin-su_prices[1].origin)))
                difference.sign = ((su_prices[0].origin-su_prices[1].origin) < 0)? '-':'+';
                difference.style = ((su_prices[0].origin-su_prices[1].origin) < 0)? 'minus':'plus';
                data.s.items[1].b_gr = data.s.items[1].sales_day;
                data.s.items[1].b_price = su_prices[1].format;

                // difference.b_format_price = custom.priceFormat(Math.abs((u_prices[1].origin)));

            }

            priceformatObject.price = su_prices[0].format;
            priceformatObject.rise = (Object.keys(difference).length)? `${difference.sign} ${difference.format}원 (${difference.rise}%)`:0;
            priceformatObject.style = difference.style;
            // priceformatObject.b_price = difference.b_format_price;

            priceformatObject.cal = '9,999억 9,999만'; //temp

        }

        /**
         *  1. 거래금액 && 직전거래 있음
         *  2. 직전거래 없음
         *  3. 거래금액 없음
         */
        //  let categorys = el.querySelectorAll('[data-category]');
        categorys.forEach((c)=> {
            let category = c.dataset.category,
            key = c.dataset.item,
            salesType = (Object.keys(priceformatObject).length)? 1:0;

            //
            if(salesType){
                if(key === 'rise' || key === 'price' || key === 'cal'){
                    if(priceformatObject[key]){
                        c.parentNode.classList.add(priceformatObject.style);

                    }else{
                        c.parentNode.classList.add('empty');
                    }
                    c.innerHTML = (priceformatObject[key])? priceformatObject[key]:'-';
                }
            }
            //
            return;
            // salesType = 0;
            switch(category) {
                case 's':
                    if(salesType){
                        if(key === 'rise' || key === 'price' || key === 'cal'){
                            if(priceformatObject[key]){
                                c.parentNode.classList.add(priceformatObject.style);

                            }else{
                                c.parentNode.classList.add('empty');
                            }
                            c.innerHTML = (priceformatObject[key])? priceformatObject[key]:'-';
                        }else if(key === 'b_gr' || key === 'b_price'){
                            c.innerHTML = (data[cate].items[1][key])? data.s.items[1][key]:'-';
                        }else if(key === 'yeon' || key === 'daeji'){
                            c.innerHTML = (data.s.items[0][key])? custom.comma(data.s.items[0][key]) + '㎡':'-';
                        }else if(key === 'yeon_p' || key === 'daeji_p'){
                            c.innerHTML = (data.s.items[0][key])? `(${custom.comma(data.s.items[0][key])}py)`:'-';
                        }else{
                            c.innerHTML = (data.s.items[0][key])? data.s.items[0][key]:'-';
                        }

                    }else{
                        if(key === 'price'){
                            c.parentNode.classList.add('hide');
                            el.querySelector('.sales-empty').classList.remove('hide');
                        }
                        if(key === 'rise'){
                            c.parentNode.style.visibility = 'hidden';
                        }
                        if(key === 'b_gr'){
                            c.parentNode.classList.add('hide');
                            c.parentNode.parentNode.querySelector('.sales-empty').classList.remove('hide');
                        }

                    }
                break;
                case 'p':
                    if(salesType){

                    }
                    // c.querySelector(`[data-item="${key}"]`).innerHTML =  'l';
                break;
            }
            // if(category === 'price'){
            //     console.log(key)
            //     let type = (Object.keys(priceformatObject).length)? 1:0;
            //     if(type){
            //         if(key === 'rise'){
            //             if(priceformatObject[key]){
            //                 c.parentNode.classList.add(priceformatObject.style);
            //             }else{
            //                 c.parentNode.classList.add('empty');
            //             }
            //         }
            //         c.innerHTML = (priceformatObject[key])? priceformatObject[key]:'-';
            //     }else{
            //         if(key === 'price'){
            //             c.parentNode.classList.add('hide');
            //             el.querySelector('.sales-empty').classList.remove('hide');
            //         }
            //         if(key === 'rise'){
            //             c.parentNode.style.visibility = 'hidden';
            //         }
            //     }
            // }
        });
    },
    comment: (el)=>{
        console.log('comment')
        // let txtArea = el.querySelector('.txt-area').querySelector('textarea');
        let txtArea = el.querySelector('.txt-area');

        custom.click(txtArea, function(){
            console.log(this);
            this.classList.add('on');
            let _this = this.querySelector('textarea');
            _this.disabled = false;
            _this.focus();
            _this.style.cursor = 'revert';

            custom.maxTextLenth(_this, 180);

        })
    },
    exampleBind: (el, data)=> {
        console.log(el)
        console.log(data);
        let examParent = el.querySelector('.example-list');
        data.forEach((item, idx)=>{
            var points = examParent.querySelectorAll(`[data-category="example-${idx}"]`);
            points.forEach(point=>{
                // console.log(point)
                if(point.tagName === 'SPAN'){
                    if(idx){
                        point.textContent = idx;
                    }
                }else{
                    if(idx === 0 && point.dataset.item === 'bdnm'){
                        return;
                    }
                    point.value = item[point.dataset.item];
                }

            });

            // if(lists.dataset.category === `example-${idx}`){
            //     lists.querySelector(`[data-category="example-${idx}"][]`)
            // }
        });
        // examCategorys.forEach(el =>{
        //     // console.log(el)
        //     let ca = el.dataset.category,
        //     key = el.dataset.item;
        // })

        // data.forEach((item, idx)=>{
        //     console.log(item)
        // });
    }

}

