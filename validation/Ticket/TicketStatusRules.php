<?php

class TicketStatusRules
{
    private $conn;
    private $status;

    public function __construct($db)
    {
        $this->conn = $db;
        $this->status = [
            'open',
            'in_progress',
            'closed'
        ];
    }

    public function handle($data)
    {
        $errors = [];

        if (empty($data['status'])) {
            $errors[] = 'Status is required';
        }
        if (!empty($data['status']) && !in_array($data['status'], $this->status)) {
            $errors[] = 'Invalid status provided';
        }
        
        if (!empty($errors)) {
            http_response_code(422);
            echo json_encode(['status' => 'error', 'message' => $errors]);
            exit();
        }

        $safe_data = [];
        $safe_data['status'] = htmlspecialchars(strip_tags($data['status']));

        return $safe_data;
    }
}