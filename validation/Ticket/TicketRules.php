<?php

class TicketRules
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
        if (empty($data['title'])) {
            $this->errors[] = 'Title is required.';
        }
        if (!empty($data['title']) && strlen($data['title']) > 500) {
            $this->errors[] = 'Title cannot exceed 255 characters.';
        }
        if (empty($data['department_id'])) {
            $this->errors[] = 'Department ID is required.';
        }
        if (!empty($data['department_id'])) {
            $query = "SELECT * FROM departments WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $data['department_id']);
            $stmt->execute();
            $department = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$department) {
                $this->errors[] = 'Invalid Department ID.';
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

        $safe_data['title'] = htmlspecialchars(strip_tags($data['title']));
        $safe_data['description'] = $data['description'] ? htmlspecialchars(strip_tags($data['description'])) : '';
        $safe_data['department_id'] = htmlspecialchars(strip_tags($data['department_id']));
        if ($_SERVER["REQUEST_METHOD"] == 'POST') {
            $safe_data['status'] = 'open';
        }
        
        return $safe_data;
    }
}
