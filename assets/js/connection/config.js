var origin   = window.location.origin;
var server = {
    'CISERVICE'                   : 'http://31.97.43.196/kpidashboardapi',
    'CISITE'                      : 'http://31.97.43.196/kpidashboardweb',
}

var viewRoutes = {
    'HOME'               		  : server.CISITE + '/',
    'LOGIN'                       : server.CISITE + '/signin',
    'MAINTENANCE'                 : server.CISITE + '/maintenance-page',
    'LOGOUT'                      : server.CISITE + '/logout',
    'REGISTER'                    : server.CISITE + '/registration',
    'DASHBOARD'                     : server.CISITE + '/digital-marketing-kpi',
    
};

var endPoints = {
    'LOGIN'                       : server.CISERVICE + '/site/login',
    'LOGOUT'                      : server.CISERVICE + '/site/logout',
    'CUST_UPDATE'                 : server.CISERVICE + '/customer/update',
    'ADDUSER'                     : server.CISERVICE + '/site/register',
    'SITECONFIG'                  : server.CISERVICE + '/site/config',
    'KPI'                         : server.CISERVICE + '/kpi/show',
    'ASANACYBER'                  : server.CISERVICE + '/kpi/asanaCyberTasks',
    'ASANAKC'                     : server.CISERVICE + '/kpi/asanaKCTasks',
    'INTAKE'                      : server.CISERVICE + '/intakes',
    'GBP'                         : server.CISERVICE + '/kpi/fetchGBP',
    'GBPTASKS'                    : server.CISERVICE + '/kpi/fetchGBPTask',
}; 

/** LOCAL WEB STORAGE ITEMS **/
var STORAGE_ITEM = {
    'TOKEN'                       : 'Token',
    'LOGIN'                       : 'Login',
};

var CONFIG = {
    'HEADER'                      : {'headers': { "Authorization": "Basic " + btoa('FLF' + ":" + 'FLF@P!'), "X-API-KEY" : "4cww48g0cggc4kgggsckg8wo4kk8k8wowwgooo44" }},
};