<?php

class TicketNoteRules
{
    private $errors;
    private $conn;
    public function __construct($db)
    {
        $this->errors = [];
        $this->conn = $db;
    }

    public function handle($data)
    {
        if (empty($data['note'])) {
            $this->errors[] = 'Note is required.';
        }
        if (empty($data['ticket_id'])) {
            $this->errors[] = 'Ticket ID is required.';
        }
        
        if (!empty($data['ticket_id'])) {
            $query = "SELECT * FROM tickets WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $data['ticket_id']);
            $stmt->execute();
            $ticket = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$ticket) {
                $this->errors[] = 'Invalid ticket ID.';
            }
        }

        if (!empty($this->errors)) {
            http_response_code(403);
            echo json_encode([
                'status' => 'error',
                'errors' => $this->errors
            ]);

            exit();
        }
    }
}
