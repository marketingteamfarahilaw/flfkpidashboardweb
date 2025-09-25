var token = JSON.parse(LocalStorage.get(STORAGE_ITEM.TOKEN));
var url = $(location).attr("href"),
parts = url.split("/"),
last_part = parts[parts.length - 1];
// alert(last_part);
axios.get(endPoints.QUERIES_INFO.concat('/'+ last_part + '?token=' + token), CONFIG.HEADER)
    .then( (response)  => {
    	console.log(response.data.response);
       	$('#subject_query').html(response.data.response.subject);
        $('#sender_query').html(response.data.response.sender);
        $('#message_body_query').html(response.data.response.message);
        $('#date_query').html(response.data.response.datesend);
        $('#composeReply').attr("href", viewRoutes.COMPOSE + response.data.response.sender_id);
    })
    .catch( (error)  => {
    	toastr.error('Error communicating on server');
    });