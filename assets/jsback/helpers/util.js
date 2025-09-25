/** LOCAL STORAGE CONTROLLER **/
var LocalStorage = (function () {

/** FUNCTION getDataFromLocalStorage
 * 
 * GET [PARAMS]
 * item = STORAGE_ITEM.ITEMNAME
 * 
 * GET [HOW TO USE]
 * LocalStorage.get(STORAGE_ITEM.ITEMNAME);
 * 
 * **/

function getDataFromLocalStorage(item) {
    return localStorage.getItem(item);
}

/** FUNCTION addDataToLocalStorage
 * 
 * ADD [PARAMS]
 * item = STORAGE_ITEM.ITEMNAME
 * data = "testingdatatoaddinlocalstorage"
 * 
 * ADD [HOW TO USE]
 * LocalStorage.add(STORAGE_ITEM.ITEMNAME, stringData);
 * 
 * **/

function addDataToLocalStorage(item, data) {
    localStorage.setItem(item, data);
}

/** FUNCTION deleteDataFromLocalStorage
 * 
 * DELETE [PARAMS]
 * item = STORAGE_ITEM.ITEMNAME
 * 
 * DELETE [HOW TO USE]
 * LocalStorage.delete(STORAGE_ITEM.ITEMNAME);
 * 
 * **/

function deleteDataFromLocalStorage(item) {
    localStorage.removeItem(item);
}

/** FUNCTION updateDataFromLocalStorage
 * 
 * UPDATE [PARAMS]
 * item = STORAGE_ITEM.ITEMNAME
 * data = "testingdatatoupdateinlocalstorage"
 * 
 * UPDATE [HOW TO USE]
 * LocalStorage.update(STORAGE_ITEM.ITEMNAME, stringData);
 * 
 * **/

function updateDataFromLocalStorage(item, data) {
    localStorage.setItem(item, data);
}

return {
    get: getDataFromLocalStorage,
    add: addDataToLocalStorage,
    delete: deleteDataFromLocalStorage,
    update: updateDataFromLocalStorage
};

})();