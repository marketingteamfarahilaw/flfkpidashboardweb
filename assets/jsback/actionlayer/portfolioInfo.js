var token = JSON.parse(LocalStorage.get(STORAGE_ITEM.TOKEN));
var url = $(location).attr("href"),
parts = url.split("/"),
last_part = parts[parts.length - 1];

axios.get(endPoints.PORTFOLIO_INFO.concat('/'+ last_part + '?token=' + token), CONFIG.HEADER)
    .then( (response)  => {
    	console.log(response);
        $('#invoice_id').html(response.data.response.customer_id);
        var date = response.data.response.portfolio_date;
        $('#invoice_date').html(response.data.response.portfolio_date);

        if(response.data.response.portfolio_status == '1'){
            var label = "Paid";
            var classLabel = "bc-primary"
        } else if(response.data.response.portfolio_status == '2'){
            var label = "Pending";
            var classLabel = "bc-green"
        }  else {
            var label = "Archived";
            var classLabel = "bc-red"
        }

        $('#invoice_status').html(label);
        $('.badgeType').addClass(classLabel);

        $('#invoice_email').html(response.data.response.email);
        $('#invoice_name').html(response.data.response.name);

        $('#invoice_made').html(response.data.userInfo.customer_first_name + ' ' + response.data.userInfo.customer_last_name);
        $('#invoice_address').html(response.data.userInfo.customer_address);
        $('#invoice_made_email').html(response.data.userInfo.customer_email);
        $('#invoice_made_number').html(response.data.userInfo.customer_mobile);


        $('#portfolio_price').html(response.data.response.portfolio_price);
        $('#portfolio_total').html(response.data.response.portfolio_total);
    })
    .catch( (error)  => {
    	console.log(error);
        toastr.error('Error communicating on server');
    });