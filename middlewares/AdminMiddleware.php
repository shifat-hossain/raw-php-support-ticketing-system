<?php

class AdminMiddleware
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function handle()
    {
        $headers = getallheaders();
        if (!isset($headers['Authorization'])) {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
            exit();
        }

        $token = str_replace('Bearer ', '', $headers['Authorization']);
        $jwtManager = new JwtManager($this->conn);
        $payload = $jwtManager->validateToken($token);

        if (!$payload || !isset($payload['role']) || $payload['role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'Access denied.']);
            exit();
        }
    }
}