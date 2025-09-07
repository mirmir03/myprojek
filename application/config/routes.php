<?php

defined('BASEPATH') or exit('No direct script access allowed');

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
// $route['default_controller'] = 'reg/parents/create';
// In your routes.php file, add these routes at the top:

// Direct routes without manage prefix for external access
$route['external_auth/login'] = 'manage/external_auth/login';
$route['external_auth/process_login'] = 'manage/external_auth/process_login';
$route['external_auth/logout'] = 'manage/external_auth/logout';
$route['external_auth/debug_process_login'] = 'manage/external_auth/debug_process_login';
$route['external_auth/test_form'] = 'manage/external_auth/test_form';
$route['external_auth/clear_session'] = 'manage/external_auth/clear_session'; // Add this line

// External auth routes - include 'manage' subdirectory (keep these as backup)
$route['external_login'] = 'manage/external_auth/login';

// Keep your existing default route unchanged
$route['default_controller'] = 'manage/Role/system_start';

// Your existing routes remain here
$route['manage/Role/system_start'] = 'manage/Role/system_start';


$route['404_override'] = '';
$route['translate_uri_dashes'] = false;


$route['qr1/(:any)'] = 'pelajar/admin/index/$1';
$route['qr/(:any)'] = 'qr/utama';
$route['qr_kelas/(:any)'] = 'qr_kelas/utama';
$route['attendance/(:any)'] = 'attendance/utama';

$route['api/(:any)'] = 'pensyarah/report/index/$1';
$route['login'] = 'supplier/access/login';
$route['logmasuk'] = 'pensyarah/qr/login';
$route['logpelajar'] = 'pelajar/qr/login';
$route['logother'] = 'pensyarah/qr/loginemo';

$route['vhadir/(:any)'] = 'pensyarah/qr/vhadir';
$route['logout'] = 'supplier/access/logout';
$route['nopage'] = 'pensyarah/qr/nolanding';
$route['expired'] = 'pensyarah/qr/expired';
$route['test'] = 'pensyarah/report/testapi';

$route['scanner/(:any)'] = 'pensyarah/qr/vhadir/$1';

$route['addloc'] = 'supplier/ora/create';
$route['testoracle'] = 'pensyarah/qr/loginemo';

$route['admin'] = 'reg/ora/testoracle';
$route['lokasi-x'] = 'reg/parents/create';
$route['main'] = 'reg/sys/index';
$route['update'] = 'reg/profil/create';
$route['pg_direct'] = 'rhm/ccc/index'; //direct
$route['pg_return'] = 'kursusv1/bayar/view_transaction'; //indirect

$route['manage/auth/login'] = 'manage/auth/login';
$route['manage/auth/do_login'] = 'manage/auth/do_login';



