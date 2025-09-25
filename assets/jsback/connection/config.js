var CONFIG = {
    'HEADER'                      : {'headers': { "Authorization": "Basic " + btoa('FLF' + ":" + 'FLF@P!'), "X-API-KEY" : "4cww48g0cggc4kgggsckg8wo4kk8k8wowwgooo44" }},
};

var viewRoutes = {
    'HOME'               		  : 'https://lmthrp.com/web/',
    'LOGIN'                       : 'https://lmthrp.com/web/signin',
    // 'MAINTENANCE'                 : 'https://lmthrp.com/web/maintenance-page',
    'LOGOUT'                      : 'https://lmthrp.com/web/logout',
    'REGISTER'                    : 'https://lmthrp.com/web/registration',
    'PROFILE'                     : 'https://lmthrp.com/web/profile',
    'QUERIES'                     : 'https://lmthrp.com/web/queries',
    'COMPOSE'                     : 'https://lmthrp.com/web/compose/',
    'KPI'                        : 'https://lmthrp.com/web/digital-marketing-kpi/',
};

var endPoints = {
    'LOGIN'                       : 'https://lmthrp.com/api/site/login',
    'LOGOUT'                      : 'https://lmthrp.com/api/site/logout',
    'CUST_UPDATE'                 : 'https://lmthrp.com/api/customer/update',
    'ADDUSER'                     : 'https://lmthrp.com/api/site/register',
    'SITECONFIG'                  : 'https://lmthrp.com/api/site/config',
    'CONTACT'                     : 'https://lmthrp.com/api/contacts/directory_list',
    'LAWYERS'                     : 'https://lmthrp.com/api/lawyers/lawyer_list',
    'COMPOSE'                     : 'https://lmthrp.com/api/queries/compose_connect',
    'QUERIES'                     : 'https://lmthrp.com/api/queries/inbox_list_connect',
    'QUERIES_CLIENT'              : 'https://lmthrp.com/api/queries/inbox_list_client',
    'SENT_QUERIES'                : 'https://lmthrp.com/api/queries/sent_list',
    'SENT_QUERIES_CONNECT'        : 'https://lmthrp.com/api/queries/sent_list_connect',
    'ARCHIVE_QUERIES'             : 'https://lmthrp.com/api/queries/sent_archive',
    'ARCHIVE_QUERIES_CONNECT'     : 'https://lmthrp.com/api/queries/sent_archive_connect',
    'QUERIES_INFO'                : 'https://lmthrp.com/api/queries/query_info',
    'QUERIES_SENT_ARCHIVE_INFO'   : 'https://lmthrp.com/api/queries/query_info_sent_archive_get',
    'QUERIES_COUNT'               : 'https://lmthrp.com/api/queries/queries_number',
    'CONNECTION'                  : 'https://lmthrp.com/api/queries/lawyers_connect',
    'CONNECTION_CHAT'             : 'https://lmthrp.com/api/queries/lawyers_chat',
    'INTAKES'                     : 'https://lmthrp.com/api/intakes',
    'KPI'                         : 'https://lmthrp.com/api/kpi',
    'PORTFOLIO_INFO_LIST'         : 'https://lmthrp.com/api/portfolio/portfolio_info_list',
    'PORTFOLIO_INFO'              : 'https://lmthrp.com/api/portfolio/portfolio_info',
    'PORTFOLIO_COMPOSE'           : 'https://lmthrp.com/api/portfolio/portfolio_compose',
    'PORTFOLIO_ADD'               : 'https://lmthrp.com/api/portfolio/portfolio_add',
    'PORTFOLIO_CLIENT_ADD'        : 'https://lmthrp.com/api/portfolio/portfolio_add_client',
    'CONNECT'                     : 'https://lmthrp.com/api/connect/connect_add',
    'LAWYER_INFO'                 : 'https://lmthrp.com/api/lawyers/lawyer_info',

    'PORTALFEATURE'               : 'https://lmthrp.com/api/portal/portal_feature',
    'PORTALEDITORIAL'             : 'https://lmthrp.com/api/portal/portal_editorial',
    'PORTALPUBLISH'               : 'https://lmthrp.com/api/portal/portal_editorial',
    'PORTALADVERTORIAL'           : 'https://lmthrp.com/api/portal/portal_advertorial',
    'PORTALANNOUNCEMENT'          : 'https://lmthrp.com/api/portal/portal_announcement',
    'PORTAL_TYPE'                 : 'https://lmthrp.com/api/portal/portal_publish',
}; 

/** LOCAL WEB STORAGE ITEMS **/
var STORAGE_ITEM = {
    'TOKEN'                       : 'Token',
    'LOGIN'                       : 'Login',
};
