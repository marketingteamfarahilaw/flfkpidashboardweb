
$( window ).on('load',() => {
    $('.main-preloader').remove();
});

var token = JSON.parse(LocalStorage.get(STORAGE_ITEM.TOKEN));
var base_url = window.location.host;
var url = 'http://31.97.43.196/kpidashboardapi/';
if (LocalStorage.get(STORAGE_ITEM.TOKEN)) {
    axios.get(endPoints.LOGIN.concat('?token=').concat(token), CONFIG.HEADER)
         .then( (response)  => {

            $(".member_image").attr("src", url + 'uploads/' + response.data.response.customer_image_url + '.png');
            $("#profile_picture").attr("src", url + 'uploads/' + response.data.response.customer_image_url +'.png');
            $('#name_login').html(response.data.response.customer_first_name);
            $('.member_name').html(response.data.response.customer_first_name+' '+response.data.response.customer_middle_name+' '+response.data.response.customer_last_name);
            $('#gender').val(response.data.response.customer_gender);
            $('.gender').html(response.data.response.customer_gender);
            $('.cust_email').html(response.data.response.customer_email);
            $('.cust_about').html(response.data.response.customer_about);
            $('.cust_edu').html(response.data.response.customer_education);
            $('.cust_skill').html(response.data.response.customer_skills);
            $('.cust_address').html(response.data.response.customer_address);
            $('.customer_mobile').html(response.data.response.customer_mobile);
            $('.customer_birthday').html(response.data.response.customer_birthday);
            $('.customer_date_registered').html(response.data.response.customer_date_registered);

            $('.customer_first_name').val(response.data.response.customer_first_name);
            $('.customer_last_name').val(response.data.response.customer_last_name);
            $('.customer_middle_name').val(response.data.response.customer_middle_name);
            $('.cust_email').val(response.data.response.customer_email);
            $('.cust_address').val(response.data.response.customer_address);
            $('.customer_mobile').val(response.data.response.customer_mobile);
            $('.customer_birthday').val(response.data.response.customer_birthday);
            $('.customer_date_registered').val(response.data.response.customer_date_registered);
         })
         .catch( (error)  => {
            $(location).attr('href', viewRoutes.HOME);
      	 })
}
else {
	$(location).attr('href', viewRoutes.HOME);
}

$(".logout-btn").click( () => {
    var bodyFormData = new FormData();
    bodyFormData.set('token', token );
    axios.post(endPoints.LOGOUT, bodyFormData, CONFIG.HEADER)
          .then( (response) => {
            LocalStorage.delete(STORAGE_ITEM.TOKEN);
            LocalStorage.delete(STORAGE_ITEM.LOGIN);
            $(location).attr('href', viewRoutes.HOME);
          })
          .catch( (error) => {
            LocalStorage.delete(STORAGE_ITEM.TOKEN);
          })
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



// update profile
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