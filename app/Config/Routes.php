<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Pages::landing');

$routes->get('landing', 'Pages::landing');
$routes->get('login', 'Login::index');
$routes->post('login', 'Login::authenticate');
$routes->get('sidebar', 'Pages::sidebar');
$routes->get('add-details', 'Details::create');
$routes->post('add-details', 'Details::store');

$routes->get('dashboard', 'Pages::dashboard');
$routes->get('view-details', 'ViewDetails::index');
$routes->get('logout', 'Login::logout');
// Dashboard report download
$routes->get('dashboard/report', 'Pages::generateReport');
// Member edit/delete endpoints
$routes->post('member/update', 'MemberController::update');
$routes->post('member/delete/(:num)', 'MemberController::delete/$1');
