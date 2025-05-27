<?php

class TicketAssignRules
{
    private $errors;

    public function __construct($db)
    {
        $this->errors = [];
    }

    public function handle($data)
    {
        $safe_data = [];

        if (empty($data['assign_to'])) {
            $this->errors[] = 'Assigned to is required';
        }

        if (!empty($this->errors)) {
            http_response_code(422);
            echo json_encode([
                'status' => 'error', 
                'message' => $this->errors
            ]);
            exit();
        }

        $safe_data['assign_to'] = htmlspecialchars(strip_tags($data['assign_to']));
        return $safe_data;
    }
}