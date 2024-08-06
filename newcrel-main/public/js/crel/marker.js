OBJECT: {
    var public = {
        customOverlays: [],
        picFlag: 0,
        selectFlag: 0,
        selectCnt: 0,
        p_poly: [],
    }
}

var markerEvent= {
    els: {
        // dot: document.querySelector('.ndot'),
        // reportLayer: document.querySelector('.report')
    },
    control: {
        // // marker click layer create, layer event call
        // custom.click(this.els.dot, function(){
        //     reportCreate.create('marker data');
        //     // markerEvent.els.reportLayer.style.display = 'flex';
        // });
        // reportCallFn: (dot)=>{
        //     reportCreate.create('marker data');
        //     // console.log(dot)
        //     // markerEvent.picHandle('type', dot, 'overlay');
        // }

    },
    picHandle: (type, el, ov, idx)=>{

        switch (type) {
            case 'over':
                if(!el.classList.contains('over')) el.classList.add('over');
                // if(el.querySelector('.compare-cnt.on')) return;
                ov[idx].setZIndex(99);
                // temp 0504
                // if(!el.classList.contains('pic')) el.classList.add('pic');
                // // if(el.querySelector('.compare-cnt.on')) return;
                // ov[idx].setZIndex(99);
            break;
            case 'out':
                if(el.querySelector('.compare-cnt.on')){
                    // 2. select pic move - "el.classList.remove('pic');" remove
                    el.classList.remove('pic');
                    ov[idx].setZIndex(3 + public.selectCnt);
                    return;
                }
                if(el.classList.contains('over')) el.classList.remove('over');
                // temp 0504
                // if(el.classList.contains('pic')) el.classList.remove('pic');

                ov[idx].setZIndex(2);
            break;
            case 'pic':
                var reDot = document.querySelectorAll('.ndot');

                reDot.forEach((rd, r_idx) => {
                    if(rd.classList.contains('pic')){
                        rd.classList.remove('pic');
                        ov[r_idx].setZIndex(2);
                    }
                })

                el.classList.add('pic');
                ov[idx].setZIndex(3);

            break;
            case 'select':
                var selectDot = el.querySelector('.compare-cnt');
                // 1. select pic move - "el.classList.add('pic');" add
                // el.classList.add('pic');

                if(selectDot.textContent){ // duplcate click -> cancel work
                    var beforeNum = selectDot.textContent;
                    selectDot.classList.remove('on');
                    selectDot.innerHTML = '';
                    var selectDots = document.querySelectorAll('.compare-cnt.on');
                    selectDots.forEach(dot =>{
                        if(parseInt(dot.textContent) > parseInt(beforeNum)){
                            console.log(dot)
                            dot.innerHTML = dot.textContent - 1;
                        }
                    })
                    public.selectCnt--;
                    tileEvent.compareDownCnt(public.selectCnt);
                    break;
                }

                public.selectCnt++;
                selectDot.classList.add('on');
                selectDot.innerHTML = public.selectCnt;

                tileEvent.compareDownCnt(public.selectCnt);


            break;
            case 'reset':
                let target = el.querySelector('.compare-cnt.on');
                if(target){
                    target.classList.remove('on');
                    el.classList.remove('pic');
                    target.innerHTML = '';
                }
            break;
            case 'compareSave':

            break;
        }
    },
    bounds: (map, bound)=>{
        if(bound.m_level > 4) return;

        let sw = bound.m_bounds.getSouthWest(),
        ne = bound.m_bounds.getNorthEast(),
        bounds = {swLng: sw.getLng(), swLat: sw.getLat(), neLng: ne.getLng(), neLat: ne.getLat()},
        q = {bound: bounds, filter: false};
        // filter more.. for q object

        let build = {
            param: q,
            url: '/n/bounds',
            method: 'post'
        }
        custom.ajax(build, function(data){
            if(data.error){
                // layer more..
                return;
            }

            // marker & cluster init more..
            if(public.customOverlays.length){
                public.customOverlays.forEach(overlay =>{
                    overlay.setMap(null);
                });
                public.customOverlays.length = 0;
            }
            let positions = [];

            data.markers.forEach(item=> {

                // marker dgpdg format..
                // var unit = '만';
                //     if(item.dgpdg.length > 4){
                //         item.dgpdg = Math.floor((parseInt(item.dgpdg)/10000) * 100) / 100; // 억단위로 소수점 두자리에서 자름
                //         unit = '억';
                //     }

                let contentArray = [
                    `<div class='ndot d' data-uid=${item.pnu}>`,
                    "<div class='dcontainer'>",
                    "<div class='compare-cnt'></div>",
                    "<div class='subject'>매매</div>",
                    "<div class='sold-price'>",
                    `<span>${custom.comma(item._price)} 억</span>`,
                    "</div>",
                    "<div class='unit-price'>",
                    `<span>평 ${custom.comma(item._dgp)} 만</span>`,
                    "</div></div></div>"
                ];

                let contentTxt = contentArray.join('');
                let marker = {
                    latlng: new kakao.maps.LatLng(item.lat, item.lng),
                    contents: contentTxt
                }

                positions.push(marker);

            });

            let overlays = [];
            positions.forEach(item=> {
                let psi = item.latlng, cot = item.contents;
                let customOverlay = new kakao.maps.CustomOverlay({
                    position: psi,
                    content: cot,
                    xAnchor: 0.5,
                    yAnchor: 1,
                    zIndex: 2,
                    clickable: false // true : 클릭했을때 지도 이벤트를 막아준다.
                })

                public.customOverlays.push(customOverlay);
                overlays.push(customOverlay);
                customOverlay.setMap(map);
            });

            let dots = document.querySelectorAll('.ndot');
            // console.log(overlays)
            // let _overlays = [];
            // _overlays.push(public.customOverlays);
            dots.forEach((dot, idx)=> {

                custom.click(dot, function(){
                    if(!public.selectFlag){

                        let p = {pnu: this.dataset.uid};
                        repo.build(p);


                        public.picFlag = 1;
                        markerEvent.picHandle('pic', dot, overlays, idx);
                    }
                });

                custom.mouseover(dot, function(){
                    markerEvent.picHandle('over', dot, overlays, idx);
                });
                custom.mouseout(dot, function(){
                    if(!public.picFlag) markerEvent.picHandle('out', dot, overlays, idx);
                });
            });

            if(public.selectFlag) markerEvent.comparePic();

        });



    },
    comparePic: ()=> {
        console.log('pic')
        let c_dots = document.querySelectorAll('.ndot'),
        s_overlays = [];

        s_overlays.push(public.customOverlays);

        c_dots.forEach((dot, idx)=>{

            custom.click(dot, function(e){

                e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();

                if(public.selectFlag){
                    markerEvent.picHandle('select', dot, s_overlays, idx);
                }
            });
        });
        console.log(public.selectCnt)
        tileEvent.compareDownCnt(public.selectCnt);
    },
    compareReset: ()=>{
        public.selectCnt = 0;
        let dots = document.querySelectorAll('.ndot'),
        r_overlays = [];

        r_overlays.push(public.customOverlays);

        dots.forEach((dot, idx)=> {
            markerEvent.picHandle('reset', dot, r_overlays, idx);
        });

        tileEvent.compareDownCnt(public.selectCnt);

    },

}
