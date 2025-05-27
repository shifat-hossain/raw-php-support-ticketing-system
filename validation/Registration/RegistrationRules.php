<?php

class RegistrationRules
{
    private $errors;
    private $roles;
    private $conn;
    public function __construct($db)
    {
        $this->errors = [];
        $this->roles = [
            'agent',
            'client',
        ];
        $this->conn = $db;
    }
    public function handle($data)
    {
        if (empty($data['name'])) {
            $this->errors[] = 'Name is required.';
        }
        if (!empty($data['name']) && strlen($data['name']) > 500) {
            $this->errors[] = 'Name cannot exceed 255 characters.';
        }
        if (!empty($data['role']) && !in_array($data['role'], $this->roles)) {
            $this->errors[] = 'Invalid role provided';
        }
        
        if (empty($data['email_address'])) {
            $this->errors[] = 'Email is required.';
        }
        if (!empty($data['email_address']) && !filter_var($data['email_address'], FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = "Invalid email format";
        }
        if (!empty($data['email_address'])) {
            $query = "SELECT * FROM users WHERE email_address = :email_address";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':email_address', $data['email_address']);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                $this->errors[] = 'Email address must be unique.';
            }
        }
        if (empty($data['password'])) {
            $this->errors[] = 'Password is required.';
        }
        if (!empty($data['password']) && strlen($data['password']) < 6) {
            $this->errors[] = 'Minimum 6 characters are required.';
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
