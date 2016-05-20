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
|	http://codeigniter.com/user_guide/general/routing.html
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
$route['default_controller'] = 'home';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

/*
| -------------------------------------------------------------------------
| REST API Routes
| -------------------------------------------------------------------------
*/
//$route['api/userAttributes/(:num)(\.)([a-zA-Z0-9_-]+)(.*)'] = 'api/admin/userAttributes/id/$1/format/$3$4'; 
//api routes
$route['api/admin/(:any)/(:num)'] = 'api/admin/$1/id/$2'; 
$route['api/admin/(:any)'] = 'api/admin/$1';
$route['api/(:any)/(:num)/(:any)'] = 'api/$1/$3/id/$2'; 
$route['api/(:any)/(:num)'] = 'api/$1/$1/id/$2';
$route['api/(:any)/(:any)'] = 'api/$1/$2';
$route['api/(:any)'] = 'api/$1/$1';


/*
 |------------------------------------------------------------------
 | END API Routes
 |------------------------------------------------------------------
 */


/*
| -------------------------------------------------------------------------
| Website Routes
| -------------------------------------------------------------------------
*/
//admin routes
//examples
//$route['admin/userAttributes/(:num)/edit'] = 'admin/userAttributes/edit/$1'
//$route['admin/userAttributes/(:num)'] = 'admin/userAttributes/view/1'
//$route['admin/userAttributes/create'] = 'admin/userAttributes/create'
//$route['admin/userAttributes'] = 'admin/userAttributes'
$route['admin/(:any)/(:num)/(:any)'] = 'web/admin/$1/$3/$2';
$route['admin/(:any)/(:num)'] = 'web/admin/$1/view/$2';
$route['admin/(:any)/(:any)'] = 'web/admin/$1/$2';
$route['admin/(:any)'] = 'web/admin/$1';

//site routes
//$route['(:any)/(:num)/(:any)'] = 'web/public/$1/$3/$2';
//$route['(:any)/(:num)'] = 'web/public/$1/view/$2';
//$route['(:any)/(:any)'] = 'web/public/$1/$2';
//$route['(:any)'] = 'web/public/$1';

//authentication routes


