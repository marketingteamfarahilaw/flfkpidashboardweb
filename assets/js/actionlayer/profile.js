alert('yeah');
var token = JSON.parse(LocalStorage.get(STORAGE_ITEM.TOKEN));

if (LocalStorage.get(STORAGE_ITEM.TOKEN)) {
    axios.get(endPoints.LOGIN.concat('id' + id '?token=' + token), CONFIG.HEADER)
         .then( (response)  => {
            
         })
         .catch( (error)  => {
            $(location).attr('href', viewRoutes.HOME);
      	 })
}
else {
	$(location).attr('href', viewRoutes.HOME);
}