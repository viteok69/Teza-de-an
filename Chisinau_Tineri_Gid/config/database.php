<?php


class Database {
    private $host = "localhost";
    private $db_name = "places_db";
    private $username = "root";
    private $password = ""; // Asigură-te că aceasta este parola corectă pentru user-ul 'root'. Adesea este goală la instalarea XAMPP.
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            // Adaugă sau asigură-te că ai această linie:
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            // Aici ar trebui să se logheze erorile de conexiune
            error_log("DB Connection Error: " . $exception->getMessage());
            // Poți afișa și o eroare generală utilizatorului sau să oprești scriptul
            die("Eroare de conectare la baza de date."); 
        }
        return $this->conn;
    }
}
?>