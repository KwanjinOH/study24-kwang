var _kakao = {
    geocoder: new kakao.maps.services.Geocoder(),
    roadClient: new kakao.maps.RoadviewClient(),
    places: new kakao.maps.services.Places(),
    map: (el, o)=>{
        return new kakao.maps.Map(el, o)
    },
    latlng: (lat, lng)=>{
        return new kakao.maps.LatLng(lat, lng);
    },
    road: (el)=>{
        return new kakao.maps.Roadview(el);
    },
    customOverlay: (posi, con, xa, ya)=>{
        return new kakao.maps.CustomOverlay({
                    position: posi,
                    content: con,
                    xAnchor: xa, // 커스텀 오버레이의 x축 위치입니다. 1에 가까울수록 왼쪽에 위치합니다. 기본값은 0.5 입니다
                    yAnchor: ya // 커스텀 오버레이의 y축 위치입니다. 1에 가까울수록 위쪽에 위치합니다. 기본값은 0.5 입니다
                });
    },
    poly: (geos, we, color, sopa, sst, fcolor, fopa)=>{
      return new kakao.maps.Polygon({
                path: geos, // 그려질 다각형의 좌표 배열입니다
                strokeWeight: we, // 선의 두께입니다
                strokeColor: color, // 선의 색깔입니다
                strokeOpacity: sopa, // 선의 불투명도 입니다 1에서 0 사이의 값이며 0에 가까울수록 투명합니다
                strokeStyle: sst, // 선의 스타일입니다
                fillColor: fcolor, // 채우기 색깔입니다
                fillOpacity: fopa // 채우기 불투명도 입니다
            });
    },

    mapsEvent: (target, evt, callback)=>{
        kakao.maps.event.addListener(target, evt, callback);
    }
}
