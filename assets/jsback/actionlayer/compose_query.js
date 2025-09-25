var token = JSON.parse(LocalStorage.get(STORAGE_ITEM.TOKEN));
$('#sendBtn').click((e) => {
    e.preventDefault();   

    var bodyFormData = new FormData();
    bodyFormData.set('reciever',  $('#send_to').val());
    bodyFormData.set('subject',  $('#subject').val());
    bodyFormData.set('message_body',  $('#message_body').val());
    
    axios.post(endPoints.COMPOSE.concat('?token=' + token), bodyFormData, CONFIG.HEADER)
      .then( (response) => {
        toastr.success('Message Sent');
        setTimeout(function(){ 
          var token = JSON.parse(LocalStorage.get(STORAGE_ITEM.TOKEN));
          var bodyFormData = new FormData();
          bodyFormData.set("lawyer_connected", id);
          axios.post(endPoints.CONNECT.concat('/'+ id).concat('?token='+token), bodyFormData, CONFIG.HEADER)
                                  .then( (response) => {
                                      toastr.success('Successfully connected!');
                                      setTimeout(function() { 
                                          window.location = 'compose/'+id;
                                      }, 2000);
                                  })
                                  .catch( (error) => {
                                      toastr.error(error.response);
                                      console.log(error.response);
                                  });
            $(location).attr('href', viewRoutes.QUERIES);
        }, 1000);
      })
      .catch( (error) => {
        toastr.error('Error occured');
      })
}); 