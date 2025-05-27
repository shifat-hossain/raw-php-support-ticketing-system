<?php

require_once 'validation/Ticket/TicketRules.php';
require_once 'validation/Ticket/TicketStatusRules.php';
require_once 'middlewares/AuthMiddleware.php';
require_once 'models/TicketModel.php';

class TicketStatusController
{
    private $conn;
    private $ticket_model;
    private $auth;

    public function __construct($db, $id = null) {
        $this->conn = $db;
        $this->ticket_model = new TicketModel($db);
        
        $this->auth = (new AuthMiddleware($db))->handle();
    }

    public function __invoke($data, $id)
    {
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        if ($requestMethod === 'POST') {
            $safe_data = (new TicketStatusRules($this->conn))->handle($data);
            $ticket = $this->ticket_model->update($id, $safe_data);

            if ($ticket) {
                echo json_encode(['status' => 'success', 'message' => 'Ticket status changed successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to change status']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid Request Method']);
        }
    }
}
