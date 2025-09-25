
$( window ).on('load',() => {
    $('.main-preloader').remove();
});

axios.get(endPoints.SITECONFIG, CONFIG.HEADER)
        .then( (response)  => {
            jQuery.map( response.data.response, ( n)  => {
                console.log('yeah');
                if( n.config_name === "site_title" ){
                $('.site_title').append(n.config_value);
                }

                if( n.config_name === "site_logo" ){
                $('#header-logo').attr("src", n.config_value);
                }
            });
        })
        .catch( (error)  => {
            $(location).attr('href', viewRoutes.MAINTENANCE);
        });

// update profile
$('#update_profile').click((e)  => {
    e.preventDefault();

    var reqData = {};
    if( ($('#customer_first_name').val() === "") || ($('#customer_last_name').val() === "") || ($('#cust_email').val() === "")  || ($('#cust_address').val() === "") || ($('#customer_birthday').val() === "") ) {
        toastr.error('Missing required field(s).');
    } else {
        reqData.token = token;
        reqData.firstname = $('#customer_first_name').val();
        reqData.lastname = $('#customer_last_name').val();
        reqData.middlename = $('#customer_middle_name').val();
        reqData.gender = $('#gender').val();
        reqData.email = $('#cust_email').val();
        reqData.address = $('#cust_address').val();
        reqData.birthday = $('#customer_birthday').val();
        reqData.skill = $('#skill').val();
        reqData.about = $('#about').val();
        reqData.education = $('#education').val();

        var bodyFormData = new FormData();
        bodyFormData.set('token', reqData.token );
        bodyFormData.set('first_name', reqData.firstname );
        bodyFormData.set('last_name', reqData.lastname );
        bodyFormData.set('middle_name', reqData.middlename );
        bodyFormData.set('gender', reqData.gender );
        bodyFormData.set('address', reqData.address );
        bodyFormData.set('email', reqData.email );
        bodyFormData.set('birthdate', reqData.birthday );
        bodyFormData.set('skill', reqData.skill );
        bodyFormData.set('education', reqData.education );
        bodyFormData.set('about', reqData.about );
        bodyFormData.set('customer_image_url', $('#profileimage').val() );
        
        axios.post(endPoints.CUST_UPDATE, bodyFormData, CONFIG.HEADER)
          .then( (response)  => {
            toastr.success('Success updating profile');
            setTimeout(()  => {
                $(location).attr('href', viewRoutes.PROFILE);
            }, 1000);
          })
          .catch( (error)  => {
            toastr.error(error.response.data.message);
          })
    }
}); 

// deactivate profile
$('.deact_btn').click(function() {
    axios.get(endPoints.SITECONFIG.concat('?token=').concat(token), CONFIG.HEADER)
          .then(function (response) {

            toastr.success('Succesfully deactivated your account');
            LocalStorage.delete(STORAGE_ITEM.TOKEN);
            LocalStorage.delete(STORAGE_ITEM.LOGIN);
            $(location).attr('href', viewRoutes.HOME);
          })
          .catch(function (error) {
            toastr.error(error.response.data.message);
          })
}); 