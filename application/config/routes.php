<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller']        =       'public/public_page_c/login';
$route['404_override']              =       '';
$route['translate_uri_dashes']      =       FALSE;

$route['signin']                    =       'public/public_page_c/login';
$route['registration']              =       'public/public_page_c/register';
$route['about-us']                  =       'public/public_page_c/about';
$route['feature']                   =       'public/public_page_c/feature';

$route['maintenance-page']			=       'error/error_c/error_404';

$route['profile']                   =       'user/user_profile_c/profile';
$route['profile/(:num)']            =       'user/user_profile_c/profile_user/$1';
$route['update-profile']            =       'user/user_profile_c/profile_update';

$route['dashboard']                 =       'user/digikpi_c/dashboard';
$route['analytics-engagement']      =       'user/digikpi_c/analyticsengagement';
$route['analytics-acquisition']      =       'user/digikpi_c/analyticsacquisition';
$route['webdev']                 =       'user/digikpi_c/webdev';
$route['asana']                 =       'user/digikpi_c/asana';
$route['socmed']                 =       'user/digikpi_c/socmed';
$route['tlc']                 =       'user/digikpi_c/tlc';
$route['lead-docket-tracker']                 =       'user/digikpi_c/leaddocket';
$route['intakes']                 =       'user/intake_c/intakes';
$route['active-campaign']                 =       'user/activecampaign_c/activecampaign';
$route['gbpseo']                 =       'user/gbp_c/gbpmarketing';
$route['keyword-ranking']                 =       'user/gbp_c/keywordranking';
$route['citation']                 =       'user/gbp_c/citation';
$route['content']                 =       'user/digikpi_c/content';
// $route['eow-digital-marketing-kpi']                 =       'user/digikpi_c/eowmarketingkpi';
$route['digital-marketing-kpi']                 =       'user/digikpi_c/eommarketingkpi';
$route['kpi']                 =       'user/digikpi_c/kpi';

// settings
$route['customer-settings']                 =       'user/setting_c';