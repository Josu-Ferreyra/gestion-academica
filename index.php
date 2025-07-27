<?php

session_start();

require_once 'core/Router.php';
require_once 'core/Auth.php';

$router = new Router();

// Default route
$router->get('/', 'AuthController@redirect');

// Admin routes
$router->get('/admin', 'AdminController@index', ['admin']);

// Authentication routes
$router->get('/login', 'AuthController@login');
$router->post('/login', 'AuthController@doLogin');
$router->get('/logout', 'AuthController@logout');

// Alumno routes
$router->get('/alumno', 'AlumnoController@index', ['alumno']);
$router->get('/alumno/create', 'AlumnoController@viewCreateAlumno', ['admin']);
$router->post('/alumno/create', 'AlumnoController@createAlumno', ['admin']);

// Profesor routes
$router->get('/profesor', 'ProfesorController@index', ['profesor']);
$router->get('/profesor/create', 'ProfesorController@viewCreateProfesor', ['admin']);
$router->post('/profesor/create', 'ProfesorController@createProfesor', ['admin']);

// Carrera routes
$router->get('/carrera/create', 'CarreraController@viewCreateCarrera', ['admin']);
$router->post('/carrera/create', 'CarreraController@createCarrera', ['admin']);

// Materia routes
$router->get('/materia/create', 'MateriaController@viewCreateMateria', ['admin']);
$router->post('/materia/create', 'MateriaController@createMateria', ['admin']);

$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
