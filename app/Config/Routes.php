<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Authentication routes (frontend)
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attemptLogin');
$routes->get('register', 'Auth::register');
$routes->post('register', 'Auth::attemptRegister');
$routes->get('logout', 'Auth::logout');

// Dashboard
$routes->get('dashboard', 'Dashboard::index', ['filter' => 'auth']);

// Teams (frontend)
$routes->get('teams', 'Team::index', ['filter' => 'auth']);
$routes->get('teams/(:num)', 'Team::show/$1', ['filter' => 'auth']);

// Projects (frontend)
$routes->get('projects', 'Project::index', ['filter' => 'auth']);
$routes->get('projects/(:num)', 'Project::show/$1', ['filter' => 'auth']);

// API Routes (with auth filter)
$routes->group('api', ['filter' => 'auth'], function($routes) {
    // Team API
    $routes->get('teams', 'TeamController::index');
    $routes->post('teams', 'TeamController::create');
    $routes->get('teams/(:num)', 'TeamController::show/$1');
    $routes->post('teams/(:num)/invite', 'TeamController::invite/$1');
    
    // Project API
    $routes->get('projects', 'ProjectController::index');
    $routes->get('projects/team/(:num)', 'ProjectController::index/$1');
    $routes->post('projects', 'ProjectController::create');
    $routes->get('projects/(:num)', 'ProjectController::show/$1');
    $routes->put('projects/(:num)', 'ProjectController::update/$1');
    $routes->delete('projects/(:num)', 'ProjectController::delete/$1');
    
    // Task API
    $routes->get('projects/(:num)/tasks', 'TaskController::index/$1');
    $routes->post('tasks', 'TaskController::create');
    $routes->put('tasks/(:num)/status', 'TaskController::updateStatus/$1');
    $routes->put('tasks/(:num)/reassign', 'TaskController::reassign/$1');
    $routes->put('tasks/(:num)', 'TaskController::update/$1');
    $routes->delete('tasks/(:num)', 'TaskController::delete/$1');
});