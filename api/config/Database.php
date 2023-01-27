<?php

// Используем подключение к БД + настройка

class Database
{

    private $host = "127.0.0.1:8889";
    private $db_name = "authentication_jwt";
    private $username = "root";
    private $password = "root";
    public $conn;

    public function getConnection()
    {

        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
        } catch (PDOException $exception) {
            echo "Error connection DB: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
