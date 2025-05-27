<?php

require_once 'validation/Department/DepartmentRules.php';
require_once 'middlewares/AdminMiddleware.php';

class DepartmentController {
    private $conn;
    private $id;
    public function __construct($db, $id = null) {
        $this->conn = $db;
        $this->id = $id;
        (new AdminMiddleware($db))->handle();
    }

    public function processRequest() {
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        switch ($requestMethod) {
            case 'GET':
                if (isset($_GET['id'])) {
                    $this->edit($_GET['id']);
                } else {
                    $this->index();
                }
                break;
            case 'POST':
                $this->store(json_decode(file_get_contents("php://input"), true));
                break;
            case 'PUT':
                $this->update(json_decode(file_get_contents("php://input"), true));
                break;
            case 'DELETE':
                $this->delete($this->id);
                break;
            default:
                echo json_encode(['status' => 'error', 'message' => 'Invalid Request Method']);
                break;
        }
    }

    public function index() {
        $query = "SELECT * FROM departments";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($departments);
    }

    public function store($data) {
        (new DepartmentRules())->handle($data);
        
        $query = "INSERT INTO departments (name) VALUES (:name)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $data['name']);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Department created successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to create department']);
        }
    }

    public function edit($id) {
        $query = "SELECT * FROM departments WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $department = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($department);
    }

    public function update($data) {
        $query = "UPDATE departments SET name = :name WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':id', $data['id']);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Department updated successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update department']);
        }
    }

    public function delete($id) {
        $query = "DELETE FROM departments WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Department deleted successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete department']);
        }
    }
    
}