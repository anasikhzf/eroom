<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'BoardController::index');
$routes->get('/boards', 'BoardController::index');
$routes->post('/boards/store', 'BoardController::store');
$routes->get('/sitemap.xml', 'BoardController::sitemap');
$routes->get('/boards/(:any)/lock', 'BoardController::lock/$1');
$routes->get('/boards/(:any)/messages', 'BoardController::getMessages/$1');
$routes->post('/boards/(:any)/messages', 'BoardController::sendMessage/$1');
$routes->post('/boards/(:any)/settings', 'BoardController::updateSettings/$1');
$routes->post('/boards/(:any)/unlock', 'BoardController::unlock/$1');
$routes->post('/boards/(:any)/delete', 'BoardController::delete/$1');
$routes->get('/boards/(:any)', 'BoardController::show/$1');

$routes->post('/lists/store', 'ListController::store');
$routes->post('/lists/(:any)/update', 'ListController::update/$1');
$routes->get('/lists/(:any)/delete', 'ListController::delete/$1');

$routes->post('/cards/store', 'CardController::store');
$routes->post('/cards/(:any)/update', 'CardController::update/$1');
$routes->get('/cards/(:any)/delete', 'CardController::delete/$1');
$routes->post('/cards/(:any)/move', 'CardController::move/$1');
$routes->post('/cards/(:any)/upload', 'CardController::uploadAttachment/$1');
$routes->get('/attachments/(:any)/delete', 'CardController::deleteAttachment/$1');
