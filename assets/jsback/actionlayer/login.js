var token = JSON.parse(LocalStorage.get(STORAGE_ITEM.TOKEN));

$('#login').click((e) => {
    e.preventDefault();
    $('.alert').remove();
    var reqData = {};
    if(($('#login_username').val() === "") || ($('#login_password').val() === "")) {
        setTimeout( () => {
            $('<div class="alert alert-danger mt-3">Username/Password is required.</div>').insertAfter('.title_login');
        }, 500);
    } else {
        reqData.username = $('#login_username').val();
        reqData.password = $('#login_password').val();

        var bodyFormData = new FormData();
        bodyFormData.set('username', reqData.username );
        bodyFormData.set('password', reqData.password );

        console.log($('#login_username').val());

        
        axios.post(endPoints.LOGIN, bodyFormData, CONFIG.HEADER)
          .then( (response) => {
            var TOKEN = JSON.stringify(response.data.token.access_token);
            var site_login = "FLF";
            
            LocalStorage.add(STORAGE_ITEM.TOKEN, TOKEN);
            LocalStorage.add(STORAGE_ITEM.LOGIN, site_login);
            $(location).attr('href', viewRoutes.KPI);
          })
          .catch( (error) => {
            console.log(error.response.data.message);
            setTimeout( () => {
                $('<div class="alert alert-danger mt-3">' + error.response.data.message + "</div>").insertAfter(".title_login");
            }, 500);
          })
    }
}); 