<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Route par défaut vers l'authentification
$routes->get('/', 'Auth::index');
$routes->get('/auth', 'Auth::index');
$routes->get('/auth/login', 'Auth::index'); // Ajout de la route GET manquante

// Routes d'authentification
$routes->post('/auth/login', 'Auth::login');
$routes->get('/auth/logout', 'Auth::logout');

// Routes du tableau de bord
$routes->get('/dashboard', 'Dashboard::index');

// Route de test
$routes->get('/test', 'TestController::test');
$routes->post('/test', 'TestController::test');

// Routes admin (gestion des utilisateurs)
$routes->group('admin', ['filter' => 'auth'], function($routes) {
    $routes->get('users', 'Admin::index');  // CORRIGÉ: index au lieu de users
    $routes->get('users/create', 'Admin::create');
    $routes->post('users/create', 'Admin::create');
    $routes->get('users/edit/(:num)', 'Admin::edit/$1');
    $routes->post('users/edit/(:num)', 'Admin::edit/$1');
    $routes->get('users/delete/(:num)', 'Admin::delete/$1');
});
