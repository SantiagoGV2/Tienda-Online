<?php

   class Database {
    private $hostname ="localhost";
    private $username = "root";
    private $password = "";
    private $database = "tienda-online";

    function conectar(){
        try {
            $pdo = new PDO("mysql:host={$this->hostname};dbname={$this->database}", $this->username, $this->password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch(PDOException $e) {
            echo "Error al conectar con la base de datos: " . $e->getMessage();
            exit;
        }
    }
    
   }     


?>