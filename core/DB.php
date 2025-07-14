<?php

class DB {
  /**
   * Conexión única a la base de datos.
   *
   * @var PDO|null
   */
  private static $connection;

  /**
   * Devuelve una instancia única de la conexión a la base de datos.
   * Si no existe una conexión activa, la crea.
   *
   * @return PDO Instancia de la conexión a la base de datos.
   */
  public static function getConnection() {
    if (!self::$connection) {
      self::$connection = new PDO(
        'mysql:host=localhost;dbname=gestion_academica;charset=utf8mb4',
        'root',
        '',
        [
          PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
      );
    }

    return self::$connection;
  }
}
