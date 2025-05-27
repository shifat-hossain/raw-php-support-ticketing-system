<?php
require 'validation/Login/LoginRules.php';
require 'config/JwtManager.php';
require_once 'middlewares/AuthMiddleware.php';
require_once 'models/UserModel.php';

class LoginController {
    private $conn;
    private $user_model;

    public function __construct($db) {
        $this->conn = $db;
        $this->user_model = new UserModel($db);
    }

    public function login() {
        $data = json_decode(file_get_contents("php://input"), true);
        (new LoginRules())->handle($data);

        $user = $this->user_model->getUserByEmail($data['email_address']);
        
        if ($user) {
            if (password_verify($data['password'], $user['password'])) {
                $payload = [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'role' => $user['role'],
                ];
                $token = (new JwtManager($this->conn))->generateToken($payload);
                echo json_encode([
                    'status' => 'success', 
                    'message' => 'Login successful', 
                    'user' => $user, 
                    'token' => $token,
                    'token_type' => 'Bearer'
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid password']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'User not found']);
        }
    }

    public function logout() {
        $auth = (new AuthMiddleware($this->conn))->handle();
        $query = "DELETE FROM access_tokens WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $auth['id']);
        $stmt->execute();
        // Implement logout logic here, such as clearing session data
        echo json_encode(['status' => 'success', 'message' => 'Logged out successfully']);
    }
}