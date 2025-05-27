<?php

class DepartmentRules
{
    private $errors;
    public function __construct()
    {
        $this->errors = [];
    }
    public function handle($data)
    {
        if (empty($data['name'])) {
            $this->errors[] = 'Name is required.';
        }
        if (!empty($data['name']) && strlen($data['name']) > 500) {
            $this->errors[] = 'Name cannot exceed 255 characters.';
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
