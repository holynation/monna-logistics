<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Logistic::index');

// The route for normal operations for the backend
$routes->group('vc/create', function ($routes) {
    $routes->add('(:any)', 'Viewcontroller::create/$1');
    $routes->add('(:any)/(:any)', 'Viewcontroller::create/$1/$2');
    $routes->add('(:any)/(:any)/(:any)', 'Viewcontroller::create/$1/$2/$3');
    $routes->add('(:any)/(:any)/(:any)/(:any)', 'Viewcontroller::create/$1/$2/$3/$4');
});

$routes->group('vc', function ($routes) {
    $routes->add('resetPassword/(:any)', 'Viewcontroller::resetPassword/$1');
    $routes->add('changePassword', 'Viewcontroller::changePassword');
    $routes->add('changePassword/(:any)', 'Viewcontroller::changePassword/$1');
    $routes->add('(:any)', 'Viewcontroller::view/$1');
    $routes->add('(:any)/(:any)', 'Viewcontroller::view/$1/$2');
    $routes->add('(:any)/(:any)/(:any)', 'Viewcontroller::view/$1/$2/$3');
});

$routes->add('edit/(:any)/(:any)', 'Viewcontroller::edit/$1/$2');

$routes->group('ajaxData', function($routes) {
    $routes->post('savePermission', 'Ajaxdata::savePermission');
});

$routes->group('mc', function ($routes) {
    $routes->add('add/(:any)/(:any)', 'Modelcontroller::add/$1/$2');
    $routes->add('add/(:any)/(:any)/(:any)/(:any)', 'Modelcontroller::add/$1/$2/$3/$4');
    $routes->add('update/(:any)/(:any)/(:any)', 'Modelcontroller::update/$1/$2/$3');
    $routes->add('update/(:any)/(:any)/(:any)/(:any)', 'Modelcontroller::update/$1/$2/$3/$4');
    $routes->add('delete/(:any)/(:any)', 'Modelcontroller::delete/$1/$2');
    $routes->add('template/(:any)', 'Modelcontroller::template/$1');
    $routes->add('export/(:any)', 'Modelcontroller::export/$1');
});

$routes->group('ac', function ($routes) {
    $routes->add('disable/(:any)/(:any)', 'Actioncontroller::disable/$1/$2');
    $routes->add('enable/(:any)/(:any)', 'Actioncontroller::enable/$1/$2');
});

$routes->add('delete/(:any)/(:any)', 'Actioncontroller::delete/$1/$2');
$routes->add('delete/(:any)/(:any)/(:any)', 'Actioncontroller::delete/$1/$2/$3');
$routes->add('truncate/(:any)', 'Actioncontroller::truncate/$1');
$routes->add('mail/(:any)/(:any)', 'Actioncontroller::mail/$1/$2');
$routes->add('changestatus/(:any)/(:any)/(:any)', 'Actioncontroller::changeStatus/$1/$2/$3');

$routes->add('account/verify/(:any)/(:any)/(:any)', 'Auth::verify/$1/$2/$3');
$routes->add('account/verifyTransaction/(:any)', 'Auth::verifyTransaction/$1');
$routes->get('register', 'Auth::signup');
$routes->get('forget_password', 'Auth::forget');

$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::web');
$routes->post('logout', 'Auth::logout');

$routes->get('admin/dashboard', 'Viewcontroller::view/admin/dashboard');

$routes->cli('cron/cronJob/(:any)', 'Cron::cronJob/$1');