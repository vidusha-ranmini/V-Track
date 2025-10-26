<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Pages::landing');

$routes->get('landing', 'Pages::landing');
$routes->get('login', 'Pages::login');
$routes->get('sidebar', 'Pages::sidebar');
$routes->get('add-details', 'Pages::add_details');

$routes->get('dashboard', 'Pages::dashboard');
$routes->get('view-details', 'Pages::view_details');
