<?php

require_once 'validation/Ticket/TicketRules.php';
require_once 'validation/Ticket/TicketAssignRules.php';
require_once 'middlewares/AuthMiddleware.php';
require_once 'models/TicketModel.php';
require_once 'config/RateLimit.php';

class TicketController {
    private $conn;
    private $id;
    private $ticket_model;
    private $auth;

    public function __construct($db, $id = null) {
        $this->conn = $db;
        $this->id = $id;
        $this->ticket_model = new TicketModel($db);
        
        $this->auth = (new AuthMiddleware($db))->handle();
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
                if ($this->id) {
                    $this->ticket_assign(json_decode(file_get_contents("php://input"), true), $this->id);
                } else {
                    (new RateLimit())->rateLimit();
                    $this->store(json_decode(file_get_contents("php://input"), true));
                }
                break;
            case 'PUT':
                $this->update(json_decode(file_get_contents("php://input"), true), $this->id);
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
        $tickets = $this->ticket_model->getTickets();
        echo json_encode($tickets);
    }

    public function store($data) {
        $safe_data = (new TicketRules($this->conn))->handle($data);
        $safe_data['user_id'] = $this->auth['id'];
        $this->ticket_model->create($safe_data);

        echo json_encode(['status' => 'success', 'message' => 'Ticket created successfully']);
    }

    public function edit($id) {
        $ticket = $this->ticket_model->getTicketById($id);
        echo json_encode($ticket);
    }

    public function update($data, $id) {
        $safe_data = (new TicketRules($this->conn))->handle($data);

        $ticket = $this->ticket_model->update($id, $safe_data);
        
        if ($ticket) {
            echo json_encode(['status' => 'success', 'message' => 'Ticket updated successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update Ticket']);
        }
    }

    public function ticket_assign($data, $id) {
        $safe_data = (new TicketAssignRules($this->conn))->handle($data);

        $ticket = $this->ticket_model->update($id, $safe_data);

        if ($ticket) {
            echo json_encode(['status' => 'success', 'message' => 'Ticket updated successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update Ticket']);
        }
    }

    public function delete($id) {
        $this->ticket_model->delete($id);
        
        echo json_encode(['status' => 'success', 'message' => 'Ticket deleted successfully']);
        
    }
}
