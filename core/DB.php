<?php

class DB {
  private static $connection;

  public static function getConnection() {
    if (!self::$connection) {
      self::$connection = new PDO(
        'mysql:host=localhost;dbname=gestion_academica;charset=utf8mb4',
        'root', // usuario de tu base de datos
        '',      // contraseña de tu base de datos (ajustar si es necesario)
        [
          PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
      );
    }

    return self::$connection;
  }
}
