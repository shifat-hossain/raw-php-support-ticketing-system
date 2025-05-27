<?php

class TicketNoteModel {
    private $conn;
    private $table;

    public function __construct($db) {
        $this->conn = $db;
        $this->table = 'ticket_notes';
    }

    public function getTicketNotes() {
        $query = "SELECT * FROM $this->table";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getNotesByTicketId($ticketId) {
        $query = "SELECT * FROM $this->table WHERE ticket_id = :ticket_id limit 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ticket_id', $ticketId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $query = "INSERT INTO $this->table (ticket_id, user_id, note) VALUES (:ticket_id, :user_id, :note)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ticket_id', $data['ticket_id']);
        $stmt->bindParam(':user_id', $data['user_id']);
        $stmt->bindParam(':note', $data['note']);
        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
    }

    public function delete($id) {
        $query = "DELETE FROM $this->table WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
    public function update($id, $data) {
        $query = "UPDATE $this->table SET ticket_id = :ticket_id, note = :note WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':note', $data['note']);
        $stmt->bindParam(':ticket_id', $data['ticket_id']);
        $stmt->bindParam(':id', $id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function getNoteById($id) {
        $query = "SELECT * FROM $this->table WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

}