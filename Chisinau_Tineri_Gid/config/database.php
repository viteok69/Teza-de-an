<?php

class Database {
    private $host = "localhost";
    private $db_name = "places_db";
    private $username = "root";
    private $password = ""; 
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            error_log("DB Connection Error: " . $exception->getMessage());
            die("Eroare de conectare la baza de date."); 
        }
        return $this->conn;
    }
}
?>
