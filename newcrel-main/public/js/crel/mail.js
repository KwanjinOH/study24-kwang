const signSend = (email, numEl, thisBtn)=>{
    let build = {
        param: {'email' : email},
        url: '/account/send',
        method: 'post'
    }

    custom.ajax(build, function(data){
        console.log(data)
        if(data.error){
            layerCreate.alert(data);
            return;
        };
        // success layer create
        layerCreate.alert(data);
        //

        thisBtn.innerHTML = '재전송';
        //
        numEl.classList.remove('hide');
    });
};
const findSend = (email, thisBtn)=>{
    let build = {
        param: {'email' : email},
        url: '/account/find',
        method: 'post'
    }

    custom.ajax(build, function(data){
        console.log(data)
        if(data.error){
            layerCreate.alert(data);
            return;
        };
        // success layer create
        layerCreate.alert(data);
        //

        thisBtn.innerHTML = '재전송';
        //
        // numEl.classList.remove('hide');
    });

};
