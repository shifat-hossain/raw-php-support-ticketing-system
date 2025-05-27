<?php

class UserModel
{
    private $conn;
    private $table;

    public function __construct($db)
    {
        $this->conn = $db;
        $this->table = 'users';
    }

    public function getUserById($id)
    {
        $query = "SELECT * FROM $this->table WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserByEmail($email)
    {
        $query = "SELECT * FROM $this->table WHERE email_address = :email_address";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email_address', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $data)
    {
        $query = "UPDATE $this->table SET name = :name, email_address = :email_address WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':email_address', $data['email_address']);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function delete($id)
    {
        $query = "DELETE FROM $this->table WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function create($data)
    {
        $query = "INSERT INTO $this->table (name, email_address, password, role) VALUES (:name, :email_address, :password, :role)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':email_address', $data['email_address']);
        $stmt->bindParam(':password', $data['password']);
        $stmt->bindParam(':role', $data['role']);
        
        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
    }
}   