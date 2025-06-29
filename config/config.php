<?php
// config/config.php

// Datos de tu base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'gestion_academica');
define('DB_USER', 'root');
define('DB_PASS', '');

// DSN para PDO
define('DB_DSN', 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8');
