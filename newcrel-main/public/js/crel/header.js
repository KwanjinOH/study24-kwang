var headerEvent= {
    els: {
        loginBtn: document.querySelector('#hd-btn-login'),
        mypageBtn: document.querySelector('#hd-btn-mypage'),
    },
    logedElementCreate: (flag)=>{

        let authWarp = document.querySelector('#user-auth');

        while (authWarp.firstChild){
            authWarp.removeChild(authWarp.firstChild);
        }

        let a = document.createElement('a');
        a.setAttribute('id', flag? 'hd-btn-mypage':'hd-btn-login');
        a.setAttribute('href', 'javascript:;');
        a.textContent = flag? '마이페이지':'로그인';

        authWarp.appendChild(a);

        headerEvent.control();

    },
    control: function(){
        let loginBtn = document.querySelector('#hd-btn-login');

        if(loginBtn){
            custom.click(loginBtn, function(){
                layerCreate.login();
            })
        }
        let mypageBtn = document.querySelector('#hd-btn-mypage');
        if(mypageBtn){
            let naming = 'side-on',
            page = document.querySelector('.side.parent'),
            tile = document.querySelector('.tilewarp'),
            compare = document.querySelector('.map-compare');

            custom.click(mypageBtn, function(){

                page.classList.toggle('on');
                tile.classList.toggle(naming);
                compare.classList.toggle(naming);

                if(page.classList.contains('on')){
                    let build = {
                        param: '',
                        url: '/u/mypage',
                        method: 'get'
                    };
                    custom.ajax(build, function(data){
                        mypageEvent.logedElementBind(data.data);
                    });
                }
            })
        }

        const searchWord = document.querySelector('#addr-search-box');
        searchEvent.input(searchWord);
    }
}
