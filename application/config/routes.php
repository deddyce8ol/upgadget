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
|	https://codeigniter.com/userguide3/general/routing.html
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
| REST API Routes v1
| -------------------------------------------------------------------------
| Routes for RESTful API endpoints
*/
$route['api/v1/products'] = 'api/v1/products/index';
$route['api/v1/categories'] = 'api/v1/categories/index';
$route['api/v1/brands'] = 'api/v1/brands/index';
$route['api/v1/store'] = 'api/v1/store/index';

/*
| -------------------------------------------------------------------------
| Category Management Routes
| -------------------------------------------------------------------------
| Routes for product category CRUD operations (admin only)
*/
$route['admin/category'] = 'admin/category/index';
$route['admin/category/get_data'] = 'admin/category/get_data';
$route['admin/category/create'] = 'admin/category/create';
$route['admin/category/get_by_id/(:num)'] = 'admin/category/get_by_id/$1';
$route['admin/category/update'] = 'admin/category/update';
$route['admin/category/delete/(:num)'] = 'admin/category/delete/$1';
$route['admin/category/toggle_status/(:num)'] = 'admin/category/toggle_status/$1';

/*
| -------------------------------------------------------------------------
| Admin E-commerce Routes
| -------------------------------------------------------------------------
| Routes for admin panel e-commerce management
*/

// Brand Management
$route['admin/brand'] = 'admin/brand/index';
$route['admin/brand/get_data'] = 'admin/brand/get_data';
$route['admin/brand/create'] = 'admin/brand/create';
$route['admin/brand/get_by_id/(:num)'] = 'admin/brand/get_by_id/$1';
$route['admin/brand/update'] = 'admin/brand/update';
$route['admin/brand/delete/(:num)'] = 'admin/brand/delete/$1';
$route['admin/brand/toggle_status/(:num)'] = 'admin/brand/toggle_status/$1';

// Product Management
$route['admin/product'] = 'admin/product/index';
$route['admin/product/get_data'] = 'admin/product/get_data';
$route['admin/product/create'] = 'admin/product/create';
$route['admin/product/store'] = 'admin/product/store';
$route['admin/product/edit/(:num)'] = 'admin/product/edit/$1';
$route['admin/product/update/(:num)'] = 'admin/product/update/$1';
$route['admin/product/delete/(:num)'] = 'admin/product/delete/$1';
$route['admin/product/toggle_status/(:num)'] = 'admin/product/toggle_status/$1';
$route['admin/product/delete_image/(:num)'] = 'admin/product/delete_image/$1';

// Product Import
$route['admin/product_import'] = 'admin/product_import/index';
$route['admin/product_import/upload'] = 'admin/product_import/upload';
$route['admin/product_import/get_preview_data'] = 'admin/product_import/get_preview_data';
$route['admin/product_import/confirm_import'] = 'admin/product_import/confirm_import';
$route['admin/product_import/download_template'] = 'admin/product_import/download_template';

// Order Management
$route['admin/order'] = 'admin/order/index';
$route['admin/order/detail/(:num)'] = 'admin/order/detail/$1';
$route['admin/order/update_status'] = 'admin/order/update_status';
$route['admin/order/update_payment_status'] = 'admin/order/update_payment_status';
$route['admin/order/delete/(:num)'] = 'admin/order/delete/$1';

// Customer Management
$route['admin/customer'] = 'admin/customer/index';
$route['admin/customer/get_data'] = 'admin/customer/get_data';
$route['admin/customer/detail/(:num)'] = 'admin/customer/detail/$1';
$route['admin/customer/toggle_status/(:num)'] = 'admin/customer/toggle_status/$1';
$route['admin/customer/delete/(:num)'] = 'admin/customer/delete/$1';

// Banner Management
$route['admin/banner'] = 'admin/banner/index';
$route['admin/banner/get_data'] = 'admin/banner/get_data';
$route['admin/banner/create'] = 'admin/banner/create';
$route['admin/banner/get_by_id/(:num)'] = 'admin/banner/get_by_id/$1';
$route['admin/banner/update'] = 'admin/banner/update';
$route['admin/banner/delete/(:num)'] = 'admin/banner/delete/$1';
$route['admin/banner/toggle_status/(:num)'] = 'admin/banner/toggle_status/$1';

// Site Settings
$route['admin/settings'] = 'admin/settings/index';
$route['admin/settings/update'] = 'admin/settings/update';

// Admin Dashboard & User Management (must be at the end of admin routes)
$route['admin/role'] = 'dashboard/role';
$route['admin/role_access/(:any)'] = 'dashboard/role_access/$1';
$route['admin/change_role_access'] = 'dashboard/change_role_access';
$route['admin/change_role_by_id'] = 'dashboard/change_role_by_id';
$route['admin/delete_role_by_id/(:num)'] = 'dashboard/delete_role_by_id/$1';
$route['admin/get_role_by_id/(:num)'] = 'dashboard/get_role_by_id/$1';
$route['admin/user_data'] = 'dashboard/user_data';
$route['admin/get_user_by_username/(:any)'] = 'dashboard/get_user_by_username/$1';
$route['admin/delete_user_by_username/(:any)'] = 'dashboard/delete_user_by_username/$1';
$route['admin'] = 'dashboard/index';

/*
| -------------------------------------------------------------------------
| Frontend Product Routes
| -------------------------------------------------------------------------
| Routes for public product pages
*/
$route['product'] = 'product/index';
$route['product/search'] = 'product/search';
$route['product/category/(:any)'] = 'product/category/$1';
$route['product/detail/(:any)'] = 'product/detail/$1';
