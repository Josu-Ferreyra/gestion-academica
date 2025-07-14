<?php

session_start();

require_once 'core/Router.php';
require_once 'core/Auth.php';

$router = new Router();

$router->get('/', 'HomeController@index');
$router->get('/admin', 'AdminController@index', ['admin']);
$router->get('/login', 'AuthController@login');
$router->post('/login', 'AuthController@doLogin');
$router->get('/logout', 'AuthController@logout');
$router->get('/alumno', 'AlumnoController@index', ['alumno']);
$router->get('/profesor', 'ProfesorController@index', ['profesor']);

$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
