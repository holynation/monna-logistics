<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Logistic::index');

// The route for normal operations for the backend
$routes->group('vc/create', function ($routes) {
    $routes->get('(:any)', 'Viewcontroller::create/$1');
    $routes->get('(:any)/(:any)', 'Viewcontroller::create/$1/$2');
    $routes->get('(:any)/(:any)/(:any)', 'Viewcontroller::create/$1/$2/$3');
    $routes->get('(:any)/(:any)/(:any)/(:any)', 'Viewcontroller::create/$1/$2/$3/$4');
});

$routes->group('vc', function ($routes) {
    $routes->get('resetPassword/(:any)', 'Viewcontroller::resetPassword/$1');
    $routes->post('changePassword', 'Viewcontroller::changePassword');
    $routes->post('changePassword/(:any)', 'Viewcontroller::changePassword/$1');
    $routes->get('(:any)', 'Viewcontroller::view/$1');
    $routes->get('(:any)/(:any)', 'Viewcontroller::view/$1/$2');
    $routes->get('(:any)/(:any)/(:any)', 'Viewcontroller::view/$1/$2/$3');
});

$routes->get('edit/(:any)/(:any)', 'Viewcontroller::edit/$1/$2');

$routes->group('ajaxData', function($routes) {
    $routes->post('savePermission', 'Ajaxdata::savePermission');
});

$routes->group('mc', function ($routes) {
    $routes->post('add/(:any)/(:any)', 'Modelcontroller::add/$1/$2');
    $routes->post('add/(:any)/(:any)/(:any)/(:any)', 'Modelcontroller::add/$1/$2/$3/$4');
    $routes->post('update/(:any)/(:any)/(:any)', 'Modelcontroller::update/$1/$2/$3');
    $routes->post('update/(:any)/(:any)/(:any)/(:any)', 'Modelcontroller::update/$1/$2/$3/$4');
    $routes->post('delete/(:any)/(:any)', 'Modelcontroller::delete/$1/$2');
    $routes->add('template/(:any)', 'Modelcontroller::template/$1');
    $routes->add('export/(:any)', 'Modelcontroller::export/$1');
});

$routes->group('ac', function ($routes) {
    $routes->post('disable/(:any)/(:any)', 'Actioncontroller::disable/$1/$2');
    $routes->post('enable/(:any)/(:any)', 'Actioncontroller::enable/$1/$2');
});

$routes->post('delete/(:any)/(:any)', 'Actioncontroller::delete/$1/$2');
$routes->post('delete/(:any)/(:any)/(:any)', 'Actioncontroller::delete/$1/$2/$3');
$routes->post('truncate/(:any)', 'Actioncontroller::truncate/$1');
$routes->post('mail/(:any)/(:any)', 'Actioncontroller::mail/$1/$2');
$routes->post('changestatus/(:any)/(:any)/(:any)', 'Actioncontroller::changeStatus/$1/$2/$3');

$routes->get('account/verify/(:any)/(:any)/(:any)', 'Auth::verify/$1/$2/$3');
$routes->get('account/verifyTransaction/(:any)', 'Auth::verifyTransaction/$1');
$routes->get('register', 'Auth::signup');
$routes->get('forget_password', 'Auth::forget');

$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::web');
$routes->get('logout', 'Auth::logout');

$routes->get('admin/dashboard', 'Viewcontroller::view/admin/dashboard');
$routes->post('invoices/process', 'Viewcontroller::processInvoices');

$routes->cli('cron/cronJob/(:any)', 'Cron::cronJob/$1');