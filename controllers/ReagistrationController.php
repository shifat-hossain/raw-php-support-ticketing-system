<?php
require_once 'validation/Registration/RegistrationRules.php';
require_once 'models/UserModel.php';

class RegistrationController
{
    private $conn;
    private $user_model;

    public function __construct($db)
    {
        $this->conn = $db;
        $this->user_model = new UserModel($db);
    }

    public function register()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        (new RegistrationRules($this->conn))->handle($data);

        $data['role'] = isset($data['role']) ? $data['role'] :'agent';
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $user_id = $this->user_model->create($data);
        
        if ($user_id) {
            echo json_encode(['status' => 'success', 'message' => 'Registration successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to Registration']);
        }
    }
}
