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

// $route['admin/(:any)']         = 'admin/$1/$1';verma@435#

$route['default_controller']   = 'bank_statement';
$route['404_override']         = '';
$route['translate_uri_dashes'] = FALSE;

$route['assets/uploads/(:any)/(:any)/(:any)'] 	= "image/resize/";
$route['assets/uploads/(:any)/(:any)'] 	= "image/resize/";


$route['login'] = "registration/login";
$route['forget-password/(:any)'] = "registration/forgetPassword";
$route['update-new-password'] = "registration/updatePassword";
// $route['forgot-password'] = "registration/forgotPassword";
// $route['bank-statement'] = "bank_statement/index";
$route['convert-bank-statement'] = "bank_statement/convertBankStatement";
$route['create-template'] = "bank_statement/createTemplate";
$route['spreading'] = "bank_statement/spreading";
$route['download-excel'] = "bank_statement/createExcel";
$route['bulk-upload-spread/(:any)'] = "bulk_upload/bulkUploadSpread/$1";
$route['add-user'] = "users/addUser";
$route['edit-user'] = "users/editUser";
$route['update-user'] = "Users/updateUser";
$route['update-password'] = "Users/updatePassword";
$route['delete-user'] = "Users/deleteUser";
$route['spread-detail/(:any)'] = "result/index/$1";
$route['downstream-api/(:any)'] = "api/Downstream_api/$1";
$route['fs-dashboard'] = "fs_dashboard/index";
$route['fs-uploading'] = "Fs_uploading/index";
$route['fs-users'] = "Fs_users/index";
$route['fs-excelsheet/(:any)'] = "fs_dashboard/show_excelsheet/$1"; //fs-edit-user
$route['fs-precheckform/(:any)'] = "fs_dashboard/show_precheckFrom/$1"; //fs-edit-user
$route['fs-add-user'] = "Fs_users/addUser";
$route['fs-edit-user'] = "Fs_users/editUser";
$route['fs-update-user'] = "Fs_Users/updateUser";
$route['fs-update-password'] = "Fs_Users/updatePassword";
$route['fs-delete-user'] = "Fs_Users/deleteUser";
//$route['history'] = "History/index";
