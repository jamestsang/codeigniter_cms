<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = "home";
$route['cms']="cms/index";
$route['cms/(:any)']="cms/$1";
$route['api']="api/index";
$route['api/(:any)']="api/$1";
$route['panel']="panel/home";
$route['panel/(:any)']="panel/$1";
$route['assets/(:any)'] = 'assets/$1';
// $route["([a-zA-Z0-9_-]+)"] =  "nms/index";
// $route["([a-zA-Z0-9_-]+)/(:any)"] =  "nms/$2";

$route['404_override'] = 'error/error_404';

// URI like '/en/about' -> use controller 'about'
$route['^zh-hant/(.+)$'] = "$1";
$route['^zh-hans/(.+)$'] = "$1";
$route['^en/(.+)$'] = "$1";

//$route['result\.php'] = "index/result";
 
// '/en' and '/fr' URIs -> use default controller
$route['^zh-hant$'] = $route['default_controller'];
$route['^zh-hans$'] = $route['default_controller'];
$route['^en$'] = $route['default_controller'];


/* End of file routes.php */
/* Location: ./application/config/routes.php */