<?php

class TicketModel
{
    private $conn;
    private $table;

    public function __construct($db)
    {
        $this->conn = $db;
        $this->table = 'tickets';
    }

    public function getTickets()
    {
        $query = "SELECT * FROM $this->table";
        $stmt = $this->conn->prepare($query);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTicketById($id)
    {
        $query = "SELECT * FROM $this->table WHERE id = :id limit 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $query = "INSERT INTO $this->table (title, description, status, department_id, user_id) VALUES (:title, :description, :status, :department_id, :user_id)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':status', $data['status']);
        $stmt->bindParam(':department_id', $data['department_id']);
        $stmt->bindParam(':user_id', $data['user_id']);
        return $stmt->execute();
    }

    public function update($id, $data)
    {
        $queryStr = "";
        
        foreach ($data as $key => $value) {
            if (!empty($value)) {
                $queryStr .= "`$key` = :$key ,";
            }
        }
        $queryStr = rtrim($queryStr, ",");
        $data['id'] = $id;

        $query = "UPDATE $this->table SET $queryStr WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute($data);
    }

    public function delete($id)
    {
        $query = "DELETE FROM $this->table WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
