// add user
$('#add_user').click( (e) => {
    e.preventDefault();
    $('.alert').remove();
    var reqData = {};
    if( ($('#customer_first_name').val() === "") || ($('#customer_last_name').val() === "") || ($('#cust_email').val() === "")  || ($('#cust_address').val() === "")  || ($('#customer_mobile').val() === "")  || ($('#customer_birthday').val() === "") ) {
        setTimeout( () => {
            $('<div class="alert alert-danger mt-3">Missing required field(s).</div>').insertAfter('.title_login');
        }, 500);
    } 
    else if ($("#customer_password").val() != $("#customer_repassword").val()) {
        setTimeout(  () => {
            $('<div class="alert alert-danger mt-3">Password did not match</div>').insertAfter('.title_login');
        }, 500);
    } 
    else {
        reqData.ruleNo = $("#customer_lawyer_rule_no").val();
        reqData.firstname = $("#customer_first_name").val();
        reqData.lastname = $("#customer_last_name").val();
        reqData.middlename = $("#customer_middle_name").val();
        reqData.username = $("#customer_username").val();
        reqData.password = $("#customer_password").val();
        reqData.gender = $("#gender").val();
        reqData.email = $("#cust_email").val();
        reqData.address = $("#cust_address").val();
        reqData.mobile = $("#customer_mobile").val();
        reqData.birthday = $("#customer_birthday").val();

        var bodyFormData = new FormData();
        bodyFormData.set("ruleNo", reqData.ruleNo);
        bodyFormData.set("first_name", reqData.firstname);
        bodyFormData.set("last_name", reqData.lastname);
        bodyFormData.set("middle_name", reqData.middlename);
        bodyFormData.set("username", reqData.username);
        bodyFormData.set("password", reqData.password);
        bodyFormData.set("gender", reqData.gender);
        bodyFormData.set("address", reqData.address);
        bodyFormData.set("email", reqData.email);
        bodyFormData.set("mobile", reqData.mobile);
        bodyFormData.set("birthdate", reqData.birthday);

        axios.post(endPoints.ADDUSER, bodyFormData, CONFIG.HEADER)
            .then( (response) => {
                setTimeout( () => {
                    $('<div class="alert alert-success mt-3">Success Registration</div>').insertAfter(".title_login");
                }, 500);
                setTimeout( () => {
                    $(location).attr("href", viewRoutes.LOGIN);
                }, 1300);
            })
            .catch( (error) => {
                setTimeout( () => {
                    $('<div class="alert alert-danger mt-3">' + error.response.data.message + '</div>').insertAfter(".title_login");
                }, 500);
            });
    }
}); 