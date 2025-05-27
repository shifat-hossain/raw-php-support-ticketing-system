<?php

class Database
{
    private $host = 'localhost';
    private $db_name = 'db_ticket_system';
    private $username = 'root';
    private $password = ''; 
    private $conn;
    public function connect()
    {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Database Connection Error: ' . $exception->getMessage()
            ]);
            exit();
        }

        return $this->conn;
    }
}