const layerHtml = '<div class="blur"></div><div class="layer-content"><div class="content-warp"><div class="header"><div><h1 class="subject"></h1></div><button class="close"><i class="fas fa-times"></i></button></div><div class="content"> </div></div></div>';
const returnHTML = (str, nm)=>{
    var dom = document.createElement('div');
    if(nm) dom.className = nm;
    dom.innerHTML = str;
    return dom;
}
const layerSubject = (str, el)=>{
    if(el === undefined) return;
    el.querySelector('.subject').innerHTML = str;
}
const layerContentAppend = (str, el)=>{
    if(el === undefined) return;
    el.append(str)
}
const layerAppend = (el)=>{
    if(el === undefined) return;
    document.body.append(el);
}

const bindStringHTML = (tag, classNm, position, key)=>{
    // let class = (classNm)? `class="${classNm}"`:'';
    if(tag === 'input'){
        return `<${tag} ${(classNm)? `class="${classNm}"`:''} type="text" data-category="${position}" data-item="${key}"/>`;
    }else{
        return `<${tag} ${(classNm)? `class="${classNm}"`:''} data-category="${position}" data-item="${key}"></${tag}>`;
    }
}

const exampleTrInputBindHTML = (nm, key)=>{
    return '<tr class="row"><th class="ex-list-subject col"><span>'+ nm +'</span></th>'
        + `<td class="ex-list-this col">${bindStringHTML('input', '', 'example-0', key)}</td>`
        + `<td class="col">${bindStringHTML('input', '', 'example-1', key)}</td>`
        + `<td class="col">${bindStringHTML('input', '', 'example-2', key)}</td>`
        + `<td class="col">${bindStringHTML('input', '', 'example-3', key)}</td>`
        + `<td class="col">${bindStringHTML('input', '', 'example-4', key)}</td>`
        + '</tr>';
}

const closing = function(el, arg1){
    if(arg1){
        document.location.href = arg1;
    }else{
        el.parentNode.removeChild(el)
    }
}

const layerCloseEvent = function(el){ // function 간소화 시키면 arguments 못 읽음
    var arg1 = arguments[1],
    arg2 = arguments[2];

    // function closing(public, el, arg1){
    //     if(typeof public !== 'undefined'){
    //         if(public.picFlag) public.picFlag = 0;
    //     }
    //     if(arg1){
    //         document.location.href = arg1;
    //     }else{
    //         el.parentNode.removeChild(el)
    //     }
    // }
    el.querySelectorAll('.close').forEach(item=>{
        custom.click(item, function(){
            if(typeof public !== 'undefined'){
                if(public.picFlag) public.picFlag = 0;
            }
            closing(el, arg1, arg2)
        })
    });
    custom.click(el.querySelector('.blur'), function(){
        // if(typeof public !== 'undefined'){
        //     if(public.picFlag) public.picFlag = 0;
        // }
        // if(arg1){
        //     document.location.href = arg1;
        // }else{
        //     el.parentNode.removeChild(el)
        // }
        if(typeof public !== 'undefined'){
            if(public.picFlag) public.picFlag = 0;
        }
        closing(el, arg1, arg2)
    });

}



const layerTodayCloseEvent = {
    handleCookie: {
        setCookie: (nm, val, days)=> {
            // 만료 기한 지정 세션 쿠키
            let expires = '';
            // 형식의 유효성을 일관성 있게 유지하기 위해 내장 함수 encodeURIComponent를 사용하여 이름과 값을 이스케이프 처리해줍니다.
            // e.g. 'hello world' -> 'hello%20world', 'test?' -> 'test%3F'
            let updatedCookie = encodeURIComponent(nm) + '=' + encodeURIComponent(val);
                if (days) {
                    let date = new Date();
                    date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
                    // 쿠키의 유효 일자는 반드시 GMT(Greenwich Mean Time) 포맷으로 설정해야 합니다.
                    // date.toUTCString을 사용하면 해당 포맷으로 쉽게 변경할 수 있습니다.
                    expires = `; expires=${date.toUTCString()}`;
                }
              document.cookie = `${updatedCookie}${expires}; path=/`;

        },
        getCookie: function(nm) {
            // 조건에 맞는 쿠키가 없다면 undefined를 반환합니다.
            let encodeName = encodeURIComponent(nm);
            let matches = document.cookie.match(new RegExp(
                "(?:^|; )" + encodeName.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
            ));

            return matches ? decodeURIComponent(matches[1]) : undefined;
        }
    },
    todayClose: (el)=> {
        custom.click(el.querySelector('.today-again-btn'), function(){
            layerTodayCloseEvent.handleCookie.setCookie('c-layer', 'compare', 1);
            if(public.picFlag) public.picFlag = 0;
            el.parentNode.removeChild(el)
        });
    },
}

var layerCreate= {
    alert: (data)=> {

        // let _layer = document.querySelector('.layer');

        let layerContainer = returnHTML(layerHtml, 'layer');
        let subject = (data.error)? '<i style="color: #ed4545; font-size: 40px;" class="fa-solid fa-circle-exclamation"></i>':'알 림';
        link = (data.url)? data.url:0;
        layerSubject(subject, layerContainer);
        var html= returnHTML(data.msg, 'alert');
        layerContentAppend(html, layerContainer.querySelector('.content'));
        // if(_layer){
        //     blurTreat(_layer, 0);
        // }
        layerCloseEvent(layerContainer, link);
        layerAppend(layerContainer);



    },

    login: (subjectTxt)=> {
        let remember = localStorage.getItem('remember'),
        parse = false;

        if(remember){
            let re = JSON.parse(remember);
            parse = (re.flag)? re.email : false;
        }
        subjectTxt = (typeof subjectTxt == 'undefined')? '로그인':subjectTxt;
        let layerContainer = returnHTML(layerHtml, 'layer');
        layerSubject(subjectTxt, layerContainer);
        var innerTxt = '<form id="direct-login" action="javascript:;"><div class="login direct">'
        + '<div class="box-warp">'
        + '<div class="box-area unit-warp"><label for="account-email" tabindex="0">이메일</label>'
        + `<input id="account-email" type="email" name="email" placeholder="이메일 주소"/ value="${(parse)? parse : ''}" ></div>`
        + '<div class="box-area unit-warp"><label for="account-password">비밀번호</label>'
        + '<input id="account-password" type="password" name="pass" placeholder="비밀번호(8자 이상, 영문/숫자 혼합)" autoComplete="off"/></div>'
        + '</div>'
        + '<div class="etc unit-warp"><div class="remember">'
        + `<input id="email-remember" type="checkbox" name="remember" ${(parse)? 'checked' : ''}/>`
        + '<label for="email-remember">이메일 기억하기</label></div><div class="find"><a href="/account/find">비밀번호 찾기</a></div></div>'
        + '<div class="sign-up"><a href="/account/terms">회원가입</a></div>'
        + '<div class="btn unit-warp"><button type="submit">로그인</button></div></div></form><hr><div class="social"><div class="social-comment"><span>다른 방법으로 로그인하기</span></div><div class="social-btns"><a class="ic naver" href="#"> </a><a class="ic kakao" href="#"> </a></div></div>';
        var html= returnHTML(innerTxt, 0);
        layerContentAppend(html, layerContainer.querySelector('.content'));
        setTimeout(function() { // after bind focus event
            layerContainer.querySelector('#account-email').focus();
        },10)

        directLogin.action(layerContainer);
        layerCloseEvent(layerContainer);
        layerAppend(layerContainer);
    },
    comparePopup: ()=> {
        // console.log(layerTodayCloseEvent.handleCookie.getCookie('c-layer'));
        if(layerTodayCloseEvent.handleCookie.getCookie('c-layer')) return;

        let layerContainer = returnHTML(layerHtml, 'layer');
        layerSubject('알 림', layerContainer);
        var innerTxt = '<div class="layer-msg">비교할 대상 및 위치를 선택하여<br><strong>비교분석 보고서</strong>를<br>다운로드 받으실수 있습니다.</div><div class="inquiry">※문의전화 : 02-9999-9999</div><button class="today-again-btn">오늘 하루 보지 않기</button>';
        var html= returnHTML(innerTxt, 'alert');
        layerContentAppend(html, layerContainer.querySelector('.content'));
        layerTodayCloseEvent.todayClose(layerContainer);
        layerCloseEvent(layerContainer);
        layerAppend(layerContainer);

    }
}

var reportCreate= {
    layer: undefined,
    container: undefined,
    data: undefined,
    create: function(d){
        console.log(d);
        this.data = d;
        // const reportItems = JSON.parse(d.data);
        // if(!d) return; // undefined alert layer create
        var blur = '<div class="blur"></div>';
        this.layer = returnHTML(blur, 'layer');
        this.container = returnHTML('', 'report-container');

        this.header();
    },
    header: function(){
        const headerHTML = '<div class="report-functions"><ul data-id="'+ this.data.param +'"><li><i class="fa-solid fa-share-nodes"></i></li><li><i class="fa-solid fa-file-powerpoint"></i></li><li><i class="fa-solid fa-print"></i></li><li class="re-bookmark"><i class="fa-regular fa-bookmark"></i></li></ul></div><button class="close"><i class="fas fa-times"></i></button>';
        layerContentAppend(returnHTML(headerHTML, 'header'), this.container);
        this.babge();
    },
    babge: function(){
        const babgeHTML = '<div class="b-name">실거래</div>';
        layerContentAppend(returnHTML(babgeHTML, 'report-babge'), this.container);
        this.top();
    },
    top: function(){

        const topHTML = '<div class="top-map"><div class="report-m ro"><button id="add-img"><i class="fa-regular fa-image"></i></button>'
        + '<div class="roadview"></div></div><div class="report-m ca"></div></div>'
        + '<div class="info"><div class="s"><div class="addr-container">'
        + `${bindStringHTML('div', 'bd', 'api', 'bd')}`
        + `${bindStringHTML('div', 'addr', 'api', 'addr')}`
        + `${bindStringHTML('div', 'raddr', 'api', 'raddr')}`

        + '</div></div>'
        + '<div class="p"><div class="price-container"><div class="sold-price space"><div><span>거래금액</span></div>'
        + '<div><div class="sales-posi">'
        + `${bindStringHTML('span', '', 's', 'price')}`
        + '</div></div><div class="sales-empty hide">거래 없음</div></div><div class="rise-rate" for="sold-price"><i class="fa-solid fa-caret-up"></i>'
        + `${bindStringHTML('span', '', 's', 'rise')}`
        + '</div>'
        + '<div class="calculation-price space"><div><span>추정금액</span><span class="hint-ico" data-tooltip="!(-_-)!"><i class="fa-solid fa-circle-exclamation"></i></span>'
        + '</div><div>'
        + `${bindStringHTML('span', '', 's', 'cal')}`
        + '</div></div></div></div></div>'
        + '<div class="m"><ul><li><h4>거래일</h4>'
        + `${bindStringHTML('span', '', 's', 'sales_day')}`
        + '</li>'
        + '<li><h4>직전거래</h4>'
        + '<div class="li-div">'
        + `${bindStringHTML('span', '', 's', 'b_gr')}`
        + `${bindStringHTML('span', '', 's', 'b_price')}`
        + '</div>'
        + '<span class="sales-empty hide">거래 없음</span>'
        + '<li><h4>연면적</h4>'
        + `${bindStringHTML('span', '', 's', 'yeon')}`
        + `${bindStringHTML('span', '', 's', 'yeon_p')}`
        + '</li><li>'
        + '<h4>대지면적</h4>'
        + `${bindStringHTML('span', '', 's', 'daeji')}`
        + `${bindStringHTML('span', '', 's', 'daeji_p')}`
        + '</li><li>'
        + '<h4>주용도 / 용도</h4>'
        + `${bindStringHTML('span', '', 's', 'ju')}`
        + `${bindStringHTML('span', '', 's', 'yd')}`
        + '</li></ul><ul><li><h4>대지평당가</h4>'
        + `${bindStringHTML('span', '', 's', 'dgp')}`
        + '</li><li><h4>규모</h4>'
        + `${bindStringHTML('span', '', 's', 'gm')}`
        + '</li><li><h4>승강기</h4>'
        + `${bindStringHTML('span', '', 's', 'lift')}`
        + '</li><li><h4>주차</h4>'
        + `${bindStringHTML('span', '', 's', 'jc')}`
        + '</li><li><h4>사용승인</h4>'
        + `${bindStringHTML('span', '', 's', 'sy')}`
        + '</li></ul></div>';

        layerContentAppend(returnHTML(topHTML, 'summary f-warp'), this.container);

        this.middle();

    },
    middle: function(){
        const commentHTML = '<div class="depth-container"><div class="comment depth-sub"><span>Comment</span><span class="hint-ico" data-tooltip="히히히"><i class="fa-solid fa-circle-question"></i></span></div><div class="txt-area depth-content"><i class="fa-solid fa-quote-left"></i><textarea spellcheck="false" placeholder="※ 상세설명을 기재해주시기 바랍니다." disabled="disabled">'
        + '</textarea><i class="fa-solid fa-quote-right"></i></div></div>';
        layerContentAppend(returnHTML(commentHTML, 'comment f-warp'), this.container);

        const exampleHTML = '<div class="depth-container"><div class="example depth-sub"><span>유사사례</span><span class="hint-ico" data-tooltip="히히히2"><i class="fa-solid fa-circle-question"></i></span></div><div class="example-map"><div class="map-container"></div><div class="map-link"><a href="javascript:;"><button> 지도에서 확인하기 <i class="fa-solid fa-arrow-right-long"></i></button></a></div></div>'
        + '<section class="example-list">'
        + '<table><tbody>'
        + '<tr class="row head"><th class="ex-list-subject col"><span> 항목 </span></th>'
        + '<td class="ex-list-this col"><span><i class="fa-solid fa-building"></i></span></td>'

        + `<td class="col">${bindStringHTML('span', '', 'example-1', 'seq')}</td>`
        + `<td class="col">${bindStringHTML('span', '', 'example-2', 'seq')}</td>`
        + `<td class="col">${bindStringHTML('span', '', 'example-3', 'seq')}</td>`
        + `<td class="col">${bindStringHTML('span', '', 'example-4', 'seq')}</td>`
        + '</tr>'
        // + '<td class="col"><span><i class="fa-solid fa-2"></i></span></td><td class="col"><span><i class="fa-solid fa-3"></i></span></td><td class="col"><span><i class="fa-solid fa-4"></i></span></td></tr>'
        // + '<td class="col"><span><i class="fa-solid fa-1"></i></span></td><td class="col"><span><i class="fa-solid fa-2"></i></span></td><td class="col"><span><i class="fa-solid fa-3"></i></span></td><td class="col"><span><i class="fa-solid fa-4"></i></span></td></tr>'

        + exampleTrInputBindHTML('건물명', 'bdnm')
        // + '<tr class="row"><th class="ex-list-subject col"><span>건물명</span></th>'
        // + `<td class="ex-list-this col">${bindStringHTML('input', '', 'example-0', 'bdnm')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-1', 'bdnm')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-2', 'bdnm')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-3', 'bdnm')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-4', 'bdnm')}</td>`
        // + '</tr>'

        // + '<td class="ex-list-this col"> <input type="text" value="역삼빌딩0"/> </td>'
        // + '<td class="col"> <input type="text" value="역삼빌딩1"/> </td><td class="col"> <input type="text" value="역삼빌딩2"/> </td><td class="col"> <input type="text" value="역삼빌딩3"/> </td><td class="col"> <input type="text" value="역삼빌딩4"/> </td></tr>'

        + exampleTrInputBindHTML('지번', 'simple_addr')
        // + '<tr class="row"><th class="ex-list-subject col"><span>지번</span></th>'
        // + `<td class="ex-list-this col">${bindStringHTML('input', '', 'example-0', 'addr')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-1', 'addr')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-2', 'addr')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-3', 'addr')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-4', 'addr')}</td>`
        // + '</tr>'

        // + '<tr class="row"><th class="ex-list-subject col"><span>지번</span></th>'
        // + '<td class="ex-list-this col"> <input type="text" value="역삼동 777-777"/> </td><td class="col"> <input type="text" value="역삼동 777-778"/> </td><td class="col"> <input type="text" value="역삼동 777-779"/> </td><td class="col"> <input type="text" value="역삼동 777-780"/> </td><td class="col"> <input type="text" value="역삼동 777-781"/> </td></tr>'

        + exampleTrInputBindHTML('도로명', 'simple_raddr')
        // + '<tr class="row"><th class="ex-list-subject col"><span>도로명</span></th>'
        // + `<td class="ex-list-this col">${bindStringHTML('input', '', 'example-0', 'raddr')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-1', 'raddr')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-2', 'raddr')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-3', 'raddr')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-4', 'raddr')}</td>`
        // + '</tr>'

        // + '<tr class="row"><th class="ex-list-subject col"><span>도로명</span></th><td class="ex-list-this col"> <input type="text" value="테헤란로 0"/> </td><td class="col"> <input type="text" value="테헤란로 1"/> </td><td class="col"> <input type="text" value="테헤란로 2"/> </td>'
        // + '<td class="col"> <input type="text" value="테헤란로 3"/> </td><td class="col"> <input type="text" value="테헤란로 4"/> </td></tr>'

        + exampleTrInputBindHTML('거래금액', 'format_price')
        // + '<tr class="row"><th class="ex-list-subject col"><span>거래금액</span></th>'
        // + `<td class="ex-list-this col">${bindStringHTML('input', '', 'example-0', 'price')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-1', 'price')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-2', 'price')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-3', 'price')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-4', 'price')}</td>`
        // + '</tr>'

        // + '<tr class="row"><th class="ex-list-subject col"><span>거래금액</span></th><td class="ex-list-this col"> <input type="text" value="9,999조 9,999억"/> </td><td class="col"> <input type="text" value="9,999조 9,998억"/> </td><td class="col"> <input type="text" value="9,999조 9,997억"/> </td><td class="col"> <input type="text" value="9,999조 9,996억"/> </td><td class="col"> <input type="text" value="9,999조 9,995억"/> </td></tr>'
        // + '<tr class="row"><th class="ex-list-subject col"><span>추정금액</span></th><td class="ex-list-this col"> <input type="text" value="9,999억 9,999만"/> </td><td class="col"> <input type="text" value="9,999조 998억"/> </td><td class="col"> <input type="text" value="9,999조 997억"/> </td><td class="col"> <input type="text" value="9,999조 996억"/> </td><td class="col"> <input type="text" value="9,999조 995억"/> </td></tr>'

        + exampleTrInputBindHTML('거래일', 'sales_day')
        // + '<tr class="row"><th class="ex-list-subject col"><span>거래일</span></th>'
        // + `<td class="ex-list-this col">${bindStringHTML('input', '', 'example-0', 'grDate')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-1', 'grDate')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-2', 'grDate')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-3', 'grDate')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-4', 'grDate')}</td>`
        // + '</tr>'

        // + '<tr class="row"><th class="ex-list-subject col"><span>거래일</span></th><td class="ex-list-this col"> <input type="text" value="2000.10"/> </td><td class="col"> <input type="text" value="2000.11"/> </td><td class="col"> <input type="text" value="2000.12"/> </td><td class="col"> <input type="text" value="2000.01"/> </td><td class="col"> <input type="text" value="2000.02"/> </td></tr>'

        + exampleTrInputBindHTML('주용도', 'ju')
        // + '<tr class="row"><th class="ex-list-subject col"><span>주용도</span></th>'
        // + `<td class="ex-list-this col">${bindStringHTML('input', '', 'example-0', 'ju')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-1', 'ju')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-2', 'ju')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-3', 'ju')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-4', 'ju')}</td>`
        // + '</tr>'

        // + '<tr class="row"><th class="ex-list-subject col"><span>주용도</span></th>'
        // + '<td class="ex-list-this col"> <input type="text" value="업무시설"/> </td><td class="col"> <input type="text" value="업무시설"/> </td><td class="col"> <input type="text" value="업무과실"/> </td><td class="col"> <input type="text" value="업무시설"/> </td><td class="col"> <input type="text" value="업무시설"/> </td></tr>'

        + exampleTrInputBindHTML('용도', 'yd')
        // + '<tr class="row"><th class="ex-list-subject col"><span>용도</span></th>'
        // + `<td class="ex-list-this col">${bindStringHTML('input', '', 'example-0', 'yd')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-1', 'yd')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-2', 'yd')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-3', 'yd')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-4', 'yd')}</td>`
        // + '</tr>'

        // + '<tr class="row"><th class="ex-list-subject col"><span>용도</span></th><td class="ex-list-this col"> <input type="text" value="일반상업지역"/> </td><td class="col"> <input type="text" value="일반상업지역"/> </td><td class="col"> <input type="text" value="일반상업지역"/> </td><td class="col"> <input type="text" value="일반상업지역"/> </td><td class="col"> <input type="text" value="특수상업지역"/> </td></tr>'

        + exampleTrInputBindHTML('접도', 'jubdo')
        // + '<tr class="row"><th class="ex-list-subject col"><span>접도</span></th>'
        // + `<td class="ex-list-this col">${bindStringHTML('input', '', 'example-0', 'jubdo')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-1', 'jubdo')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-2', 'jubdo')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-3', 'jubdo')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-4', 'jubdo')}</td>`
        // + '</tr>'

        // + '<tr class="row"><th class="ex-list-subject col"><span>접도</span></th><td class="ex-list-this col"> <input type="text" value="대로변"/> </td><td class="col"> <input type="text" value="대변"/> </td><td class="col"> <input type="text" value="이면"/> </td><td class="col"> <input type="text" value="대로변"/> </td><td class="col"> <input type="text" value="대로변"/> </td></tr>'

        + exampleTrInputBindHTML('도로조건', 'load_nm')
        // + '<tr class="row"><th class="ex-list-subject col"><span>도로조건</span></th>'
        // + `<td class="ex-list-this col">${bindStringHTML('input', '', 'example-0', 'doro')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-1', 'doro')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-2', 'doro')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-3', 'doro')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-4', 'doro')}</td>`
        // + '</tr>'

        // + '<tr class="row"><th class="ex-list-subject col"><span>도로조건</span></th><td class="ex-list-this col"> <input type="text" value="광대소각"/> </td><td class="col"> <input type="text" value="광대소각"/> </td><td class="col"> <input type="text" value="광대소각"/> </td><td class="col"> <input type="text" value="광대세각"/> </td><td class="col"> <input type="text" value="광대소각"/> </td></tr>'

        + exampleTrInputBindHTML('연면적', 'yarea')
        // + '<tr class="row"><th class="ex-list-subject col"><span>연면적</span></th>'
        // + `<td class="ex-list-this col">${bindStringHTML('input', '', 'example-0', 'yarea')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-1', 'yarea')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-2', 'yarea')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-3', 'yarea')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-4', 'yarea')}</td>`
        // + '</tr>'

        // + '<tr class="row"><th class="ex-list-subject col"><span>연면적</span></th><td class="ex-list-this col"> <input type="text" value="9,999㎡(9,999py)"/> </td><td class="col"> <input type="text" value="9,999㎡(999py)"/> </td>'
        // + '<td class="col"> <input type="text" value="9,999㎡(9,999py)"/> </td><td class="col"> <input type="text" value="9,999㎡(9,999py)"/> </td><td class="col"> <input type="text" value="9,997㎡(9,997py)"/> </td></tr>'

        + exampleTrInputBindHTML('대지면적', 'darea')
        // + '<tr class="row"><th class="ex-list-subject col"><span>대지면적</span></th>'
        // + `<td class="ex-list-this col">${bindStringHTML('input', '', 'example-0', 'darea')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-1', 'darea')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-2', 'darea')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-3', 'darea')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-4', 'darea')}</td>`
        // + '</tr>'

        // + '<tr class="row"><th class="ex-list-subject col"><span>대지면적</span></th><td class="ex-list-this col"> <input type="text" value="9,999㎡(9,999py)"/> </td><td class="col"> <input type="text" value="9,999㎡(9,999py)"/> </td><td class="col"> <input type="text" value="9,999㎡(999py)"/> </td><td class="col"> <input type="text" value="9,993㎡(9,994py)"/> </td><td class="col"> <input type="text" value="9,999㎡(9,999py)"/> </td></tr>'

        + exampleTrInputBindHTML('건축면적', 'garea')
        // + '<tr class="row"><th class="ex-list-subject col"><span>건축면적</span></th>'
        // + `<td class="ex-list-this col">${bindStringHTML('input', '', 'example-0', 'garea')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-1', 'garea')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-2', 'garea')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-3', 'garea')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-4', 'garea')}</td>`
        // + '</tr>'

        // + '<tr class="row"><th class="ex-list-subject col"><span>건축면적</span></th><td class="ex-list-this col"> <input type="text" value="9,999㎡(9,999py)"/> </td><td class="col"> <input type="text" value="9,999㎡(9,999py)"/> </td><td class="col"> <input type="text" value="9,999㎡(9,999py)"/> </td><td class="col"> <input type="text" value="9,999㎡(9,999py)"/> </td><td class="col"> <input type="text" value="99㎡(9,999py)"/> </td></tr>'

        + exampleTrInputBindHTML('건폐/용적', 'gp_yj')
        // + '<tr class="row"><th class="ex-list-subject col"><span>건폐/용적</span></th>'
        // + `<td class="ex-list-this col">${bindStringHTML('input', '', 'example-0', 'gp_yj')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-1', 'gp_yj')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-2', 'gp_yj')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-3', 'gp_yj')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-4', 'gp_yj')}</td>`
        // + '</tr>'

        // + '<tr class="row"><th class="ex-list-subject col"><span>건폐/용적</span></th><td class="ex-list-this col"> <input type="text" value="9,999% / 9,999%"/> </td><td class="col"> <input type="text" value="9,999% / 9,999%"/> </td><td class="col"> <input type="text" value="99% / 9,999%"/> </td><td class="col"> <input type="text" value="9,999% / 99%"/> </td><td class="col"> <input type="text" value="9,999% / 9,993%"/> </td></tr>'

        + exampleTrInputBindHTML('규모', 'gm')
        // + '<tr class="row"><th class="ex-list-subject col"><span>규모</span></th>'
        // + `<td class="ex-list-this col">${bindStringHTML('input', '', 'example-0', 'gm')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-1', 'gm')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-2', 'gm')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-3', 'gm')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-4', 'gm')}</td>`
        // + '</tr>'

        // + '<tr class="row"><th class="ex-list-subject col"><span>규모</span></th><td class="ex-list-this col"> <input type="text" value="99F / B99"/> </td><td class="col"> <input type="text" value="99F / B99"/> </td><td class="col"> <input type="text" value="99F / B99"/> </td><td class="col"> <input type="text" value="99F / B98"/> </td><td class="col"> <input type="text" value="9F / B99"/> </td></tr>'

        + exampleTrInputBindHTML('승강기', 'ev')
        // + '<tr class="row"><th class="ex-list-subject col"><span>승강기</span></th>'
        // + `<td class="ex-list-this col">${bindStringHTML('input', '', 'example-0', 'ev')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-1', 'ev')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-2', 'ev')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-3', 'ev')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-4', 'ev')}</td>`
        // + '</tr>'

        // + '<tr class="row"><th class="ex-list-subject col"><span>승강기</span></th><td class="ex-list-this col"> <input type="text" value="승용 99 / 비상 99"/> </td><td class="col"> <input type="text" value="승용 99 / 비상 99"/> </td><td class="col"> <input type="text" value="승용 9 / 비상 99"/> </td><td class="col"> <input type="text" value="승용 99 / 비상 9"/> </td><td class="col"> <input type="text" value="승용 99 / 비상 99"/> </td></tr>'

        + exampleTrInputBindHTML('주차', 'pk')
        // + '<tr class="row"><th class="ex-list-subject col"><span>주차</span></th>'
        // + `<td class="ex-list-this col">${bindStringHTML('input', '', 'example-0', 'pk')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-1', 'pk')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-2', 'pk')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-3', 'pk')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-4', 'pk')}</td>`
        // + '</tr>'

        // + '<tr class="row"><th class="ex-list-subject col"><span>주차</span></th><td class="ex-list-this col"> <input type="text" value="기계 99 / 자주 9"/> </td><td class="col"> <input type="text" value="기계 99 / 자주 99"/> </td><td class="col"> <input type="text" value="기계 99 / 자주 99"/> </td><td class="col"> <input type="text" value="기계 9 / 자주 99"/> </td><td class="col"> <input type="text" value="기계 99 / 자주 99"/> </td></tr>'

        + exampleTrInputBindHTML('사용승인일', 'sy')
        // + '<tr class="row"><th class="ex-list-subject col"><span>사용승인일</span></th>'
        // + `<td class="ex-list-this col">${bindStringHTML('input', '', 'example-0', 'sy')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-1', 'sy')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-2', 'sy')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-3', 'sy')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-4', 'sy')}</td>`
        // + '</tr>'

        // + '<tr class="row"><th class="ex-list-subject col"><span>사용승인일</span></th><td class="ex-list-this col"> <input type="text" value="2000.09.30"/> </td><td class="col"> <input type="text" value="2000.01.30"/> </td><td class="col"> <input type="text" value="2000.09.30"/> </td><td class="col"> <input type="text" value="2000.09.30"/> </td><td class="col"> <input type="text" value="2000.06.30"/> </td></tr>'

        + exampleTrInputBindHTML('건물 노후', 'bf')
        // + '<tr class="row"><th class="ex-list-subject col"><span>건물 노후</span></th>'
        // + `<td class="ex-list-this col">${bindStringHTML('input', '', 'example-0', 'bf')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-1', 'bf')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-2', 'bf')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-3', 'bf')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-4', 'bf')}</td>`
        // + '</tr>'

        // + '<tr class="row"><th class="ex-list-subject col"><span>건물 노후</span></th><td class="ex-list-this col"> <input type="text" value="20년"/> </td><td class="col"> <input type="text" value="2000년"/> </td>'
        // + '<td class="col"> <input type="text" value="30년"/> </td><td class="col"> <input type="text" value="9년"/> </td><td class="col"> <input type="text" value="10년"/> </td></tr>'

        + exampleTrInputBindHTML('대지평당가', 'dgp')
        // + '<tr class="row"><th class="ex-list-subject col"><span>대지평당가</span></th>'
        // + `<td class="ex-list-this col">${bindStringHTML('input', '', 'example-0', 'dgp')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-1', 'dgp')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-2', 'dgp')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-3', 'dgp')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-4', 'dgp')}</td>`
        // + '</tr>'

        // + '<tr class="row"><th class="ex-list-subject col"><span>대지평당가</span></th><td class="ex-list-this col"> <input type="text" value="9,999만"/> </td><td class="col"> <input type="text" value="8,000만"/> </td><td class="col"> <input type="text" value="9,999만"/> </td><td class="col"> <input type="text" value="9,999만"/> </td><td class="col"> <input type="text" value="999만"/> </td></tr>'

        + exampleTrInputBindHTML('연면적평당가', 'yp')
        // + '<tr class="row"><th class="ex-list-subject col"><span>연면적평당가</span></th>'
        // + `<td class="ex-list-this col">${bindStringHTML('input', '', 'example-0', 'yp')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-1', 'yp')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-2', 'yp')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-3', 'yp')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-4', 'yp')}</td>`
        // + '</tr>'

        // + '<tr class="row"><th class="ex-list-subject col"><span>연면적평당가</span></th><td class="ex-list-this col"> <input type="text" value="9,999만"/> </td><td class="col"> <input type="text" value="8,000만"/> </td><td class="col"> <input type="text" value="9,999만"/> </td><td class="col"> <input type="text" value="9,999만"/> </td><td class="col"> <input type="text" value="999만"/> </td></tr>'

        + exampleTrInputBindHTML('거래시점 공시지가', 'gp')
        // + '<tr class="row"><th class="ex-list-subject col"><span>거래시점 공시지가</span></th>'
        // + `<td class="ex-list-this col">${bindStringHTML('input', '', 'example-0', 'gp')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-1', 'gp')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-2', 'gp')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-3', 'gp')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-4', 'gp')}</td>`
        // + '</tr>'

        // + '<tr class="row"><th class="ex-list-subject col"><span>거래시점 공시지가</span></th><td class="ex-list-this col"> <input type="text" value="9,999만"/> </td><td class="col"> <input type="text" value="2,222만"/> </td><td class="col"> <input type="text" value="2,222만"/> </td><td class="col"> <input type="text" value="222만"/> </td><td class="col"> <input type="text" value="2,222만"/> </td></tr>'

        + exampleTrInputBindHTML('최근 공시지가', 're_gp')
        // + '<tr class="row"><th class="ex-list-subject col"><span>최근 공시지가</span></th>'
        // + `<td class="ex-list-this col">${bindStringHTML('input', '', 'example-0', 're_gp')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-1', 're_gp')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-2', 're_gp')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-3', 're_gp')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-4', 're_gp')}</td>`
        // + '</tr>'

        // + '<tr class="row"><th class="ex-list-subject col"><span>최근 공시지가</span></th><td class="ex-list-this col"> <input type="text" value="999만"/> </td><td class="col"> <input type="text" value="222만"/> </td><td class="col"> <input type="text" value="2,222만"/> </td><td class="col"> <input type="text" value="222만"/> </td><td class="col"> <input type="text" value="2,222만"/> </td></tr>'

        + exampleTrInputBindHTML('공시지가대비율', 'gp_yul')
        // + '<tr class="row"><th class="ex-list-subject col"><span>공시지가대비율</span></th>'
        // + `<td class="ex-list-this col">${bindStringHTML('input', '', 'example-0', 'gp_yul')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-1', 'gp_yul')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-2', 'gp_yul')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-3', 'gp_yul')}</td>`
        // + `<td class="col">${bindStringHTML('input', '', 'example-4', 'gp_yul')}</td>`
        // + '</tr>'

        // + '<tr class="row"><th class="ex-list-subject col"><span>공시지가대비율</span></th><td class="ex-list-this col"> <input type="text" value="9,999%"/> </td><td class="col"> <input type="text" value="2,222%"/> </td><td class="col"> <input type="text" value="2,222%"/> </td><td class="col"> <input type="text" value="222%"/> </td><td class="col"> <input type="text" value="2,222%"/> </td></tr>'
        + '</tbody></table></section>'

        + '<section class="example-graph">'
        + '<div class="area">'
        + '<div id="re-chart-sales"></div></div>'
        + '<div class="area">'
        + '<div id="re-chart-gongsi"></div></div></section></div>';



        layerContentAppend(returnHTML(exampleHTML, 'example f-warp'), this.container);

        this.footer();
    },
    pageTop: function(){
        var upIcoHTML = '<button><i class="fa-solid fa-arrow-up"></i></button>';
        layerContentAppend(returnHTML(upIcoHTML, 'page-top'), this.layer);
    },

    footer: function(){
        const footerHTML = '<span>Copyright 2018. 주식회사부동산도서관. All right reserved</span>';
        layerContentAppend(returnHTML(footerHTML, 'footer'), this.container);

        this.success();
    },
    success: function(){
        console.log(this.data)
        let d = this.data;

        layerContentAppend(this.container, this.layer);
        layerCloseEvent(this.layer);
        layerAppend(this.layer);
        this.pageTop();

        /**
         * css 반응속도 이슈로 그래프 및 맵 바인딩에 오류가 있음.
         */

        repo.maps(this.container, d.main);
        repo.bind(this.container.querySelector('.summary'), d.main);


        // comment height
        var c_parent = this.container.querySelector('.txt-area');
        let textarea = c_parent.querySelector('textarea');
        textarea.style.height = textarea.scrollHeight + 'px';
        repo.comment(this.container);


        var e_parent = this.container.querySelector('.example');
        let exMap = e_parent.querySelector('.map-container');
        mapEvent.example(exMap, d.example);

        // list
        repo.exampleBind(this.container.querySelector('.example'), d.example);
        // chart
        var chartWarp = e_parent.querySelector('.example-graph');
        const elSales = chartWarp.querySelector('#re-chart-sales');
        const elGongsi = chartWarp.querySelector('#re-chart-gongsi');

        chartCreate.exampleChart(elSales, d.example);
        chartCreate.gongsiChart(elGongsi, d.main, d.example[0]);

        this.evt();

    },
    evt: function(){
        let containerEl = this.container;
        let pageTopEl = reportCreate.layer.querySelector('.page-top');
        custom.scroll(containerEl, function(){
            if(this.scrollTop > 0){
                pageTopEl.classList.add('on');
            }else{
                pageTopEl.classList.remove('on');
            }
        });
        custom.click(pageTopEl, function(){
            scrollTo(containerEl, 0, 200);
            function scrollTo(el, to, duration){
                if (duration <= 0) return;
                var difference = to - el.scrollTop;
                var perTick = difference / duration  * 10;

                setTimeout(function() {
                    el.scrollTop = el.scrollTop + perTick;
                    if (el.scrollTop == to) return;
                    scrollTo(el, to, duration - 10);
                }, 10);
            }
        })

    }
}
