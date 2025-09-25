var token = JSON.parse(LocalStorage.get(STORAGE_ITEM.TOKEN));
var base_url = window.location.host;

if (LocalStorage.get(STORAGE_ITEM.TOKEN)) {
    axios.get(endPoints.LOGIN.concat('id' + id + '?token=' + token), CONFIG.HEADER)
         .then( (response)  => {
            $(location).attr('href', viewRoutes.PROFILE);
         })
         .catch( (error)  => {
            $(location).attr('href', viewRoutes.HOME);
      	 })
}
else {
	$(location).attr('href', viewRoutes.HOME);
}