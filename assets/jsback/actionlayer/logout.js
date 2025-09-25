
var token = JSON.parse(LocalStorage.get(STORAGE_ITEM.TOKEN));
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