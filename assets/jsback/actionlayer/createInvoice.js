var token = JSON.parse(LocalStorage.get(STORAGE_ITEM.TOKEN));
var url = $(location).attr("href"),
parts = url.split("/"),
last_part = parts[parts.length - 1];
axios.get(endPoints.PORTFOLIO_COMPOSE.concat('/'+ last_part + '?token=' + token), CONFIG.HEADER)
    .then( (response)  => {
        $('#invoice_made').html(response.data.response.customer_first_name + ' ' + response.data.response.customer_last_name);
        $('#invoice_address').html(response.data.response.customer_address);
        $('#invoice_made_email').html(response.data.response.customer_email);
        $('#invoice_made_number').html(response.data.response.customer_mobile);
    })
    .catch( (error)  => {
        toastr.error('Error communicating on server');
    });