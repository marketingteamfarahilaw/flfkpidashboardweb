var token = JSON.parse(LocalStorage.get(STORAGE_ITEM.TOKEN));
axios.get(endPoints.QUERIES_COUNT.concat('?token=').concat(token), CONFIG.HEADER)
        .then( (response)  => {
            if(response.data.response > "0"){
                $('.notif').html(response.data.response);
            } else {
                $('.notif').hide();
            }
        })
        .catch( (error)  => {
            console.log(error);
        });