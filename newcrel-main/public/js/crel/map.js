var mapEvent= {
    default: {
        container: document.querySelector('#nmap'),
        center: new kakao.maps.LatLng(37.5012493916368, 127.038331573474),
        // zoomControl : new kakao.maps.ZoomControl(),
    },
    options: function(){
        var o= {
            center: this.default.center,
            level: 2,
            disableDoubleClickZoom: true
        }
        return o;
    },
    get: {
        info: (map)=> {
            return {
                m_center : map.getCenter(), // 현재 중심좌표
                m_level : map.getLevel(), // 현재 지도 레벨
                m_mapTypeId : map.getMapTypeId(), //지도 타입
                m_bounds : map.getBounds() // 현재 지도 영역
            }
        }
    },
    roadview: function(el, coords){
        // temp
        let roadview = _kakao.road(el),
        client = _kakao.roadClient;

        var position = _kakao.latlng(coords.getLat(), coords.getLng());

        client.getNearestPanoId(position, 50, function(panoId) {
            roadview.setPanoId(panoId, position);
        });

        let roadDot = returnHTML('<i class="fa-solid fa-building"></i>', 'roadDot');

        el.parentNode.append(roadDot);
        _kakao.mapsEvent(roadview, 'init', function(){

            let dotCustomOverlay = _kakao.customOverlay(position, roadDot, 0.5, 0.5);
            //rvCustomOverlay.setAltitude(2); //커스텀 오버레이의 고도값을 설정합니다.(로드뷰 화면 중앙이 0입니다)
            dotCustomOverlay.setMap(roadview);
            let projection = roadview.getProjection(); // viewpoint(화면좌표)값을 추출할 수 있는 projection 객체를 가져옵니다.
            // 커스텀오버레이의 position과 altitude값을 통해 viewpoint값(화면좌표)를 추출합니다.
            let viewpoint = projection.viewpointFromCoords(dotCustomOverlay.getPosition(), dotCustomOverlay.getAltitude());
            roadview.setViewpoint(viewpoint); //커스텀 오버레이를 로드뷰의 가운데에 오도록 로드뷰의 시점을 변화 시킵니다.

            roadDot.style.opacity = '0.7';
        });

    },
    cadastral: (el, coords, data)=>{

        var so = {
            center: _kakao.latlng(coords.getLat(), coords.getLng()),
            level: 1,
            scrollwheel: false
        }

        var cadastralMap = _kakao.map(el, so);
        cadastralMap.addOverlayMapTypeId(kakao.maps.MapTypeId.USE_DISTRICT);
        // 일반 지도와 스카이뷰로 지도 타입을 전환할 수 있는 지도타입 컨트롤을 생성합니다
        // var mapTypeControl = new kakao.maps.MapTypeControl();
        // cadastralMap.addControl(mapTypeControl, kakao.maps.ControlPosition.TOPMRIGHT);

        // 지도 확대 축소를 제어할 수 있는  줌 컨트롤을 생성합니다

        /**
         * 줌 컨트롤 같은 객체로 2개이상 사용할수 없음
         */

        var ca_zoomControl = new kakao.maps.ZoomControl();
        cadastralMap.addControl(ca_zoomControl, kakao.maps.ControlPosition.BOTTOMRIGHT);

        let pgeo = JSON.parse(data.g.items[0].coords),
        bgeo = false;
        mapEvent.polygon(cadastralMap, pgeo, bgeo);
    },
    polygon: (map, pgeo, bgeo)=>{

        if(pgeo){
            let p_poly = [];
            pgeo.coordinates[0].forEach(item => {
                p_poly.push(_kakao.latlng(item[1], item[0]));
            });
            console.log(p_poly);
            let poly = _kakao.poly(p_poly, 3, '#39DE2A', 0.8, 'solid', '#A2FF99', 0.7);
            console.log(poly)
            // generalObj.be_pPoly.push(p_polygon); // 배열의 의미가 없음
            public.p_poly.push(poly);

            poly.setMap(map);
        }


    },
    example: function(el, d){

        // static map -> 마커 하나밖에 표시가 안되서 유사사례 보기를 만족 하지 못함
        emakers = [];
        d.forEach((item, idx)=>{
            var ico = '';
            if(!idx){
                ico = '<i class="fa-solid fa-building"></i>';
            }else{
                ico = idx;
            }
            var ex_content = `<div class="report-marker">${ico}</div>`;
            emakers.push({
                latlng: _kakao.latlng(item.lat, item.lng),
                contents: ex_content
            });
        })
        var eo = {
            center: emakers[0].latlng,
            level: 5,
            scrollwheel: false,
            disableDoubleClickZoom: true
        }
        var exampleMap = new kakao.maps.Map(el, eo) ;
        var ex_zoomControl = new kakao.maps.ZoomControl();
        exampleMap.addControl(ex_zoomControl, kakao.maps.ControlPosition.BOTTOMRIGHT);

        emakers.forEach(item=> {
            let ex_customOverlay = new kakao.maps.CustomOverlay({
                position: item.latlng,
                content: item.contents,
                xAnchor: 0.5,
                yAnchor: 1,
                zIndex: 2,
                clickable: true, // 클릭했을때 지도 이벤트를 막아준다.
            })
            ex_customOverlay.setMap(exampleMap);
        });
        // 지도에서 확인하기 -> 카카오맵 연동할지 crel 에서 헨들링할지??????


    },
    handle: function(){
        var nMap = new kakao.maps.Map(this.default.container,this.options());
        // filter more..

        markerEvent.bounds(nMap, mapEvent.get.info(nMap));

        kakao.maps.event.addListener(nMap, 'idle', function() {
            if(nMap.getLevel() < 5) {

                markerEvent.bounds(nMap, mapEvent.get.info(nMap));
            }

        })

    }
}
