var token = JSON.parse(LocalStorage.get(STORAGE_ITEM.TOKEN));
if(LocalStorage.get(STORAGE_ITEM.LOGIN) === "lawdger"){
  if (LocalStorage.get(STORAGE_ITEM.TOKEN)) {
      axios.get(endPoints.LOGIN.concat('?token=').concat(token), CONFIG.HEADER)
           .then( (response) => {
            setTimeout(() => {
              $('li #button_checker').html('My Account');
              $("li #button_checker").attr("href", viewRoutes.PROFILE);
              $('a#register').hide();
            }, 500);
           })
           .catch( (error) => {
              // handle error
              LocalStorage.delete(STORAGE_ITEM.TOKEN);
           })
  }
}
else {
  setTimeout(() => {
  	$('li #button_checker').html('SIGN IN');
    $("li #button_checker").attr("href", viewRoutes.LOGIN);
    $("li #register").html("REGISTER");
    $("li #register").attr("href", viewRoutes.REGISTER);
  }, 500);
}