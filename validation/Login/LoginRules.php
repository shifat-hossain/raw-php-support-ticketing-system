<?php

class LoginRules
{
    private $errors;
    public function __construct()
    {
        $this->errors = [];
    }
    public function handle($data)
    {
        if (empty($data['email_address'])) {
            $this->errors[] = 'Email is required.';
        }
        if (empty($data['password'])) {
            $this->errors[] = 'Password is required.';
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
