<?php

require_once 'validation/TicketNote/TicketNoteRules.php';
require_once 'middlewares/AuthMiddleware.php';
require_once 'models/TicketNoteModel.php';

class TicketNoteController {
    private $conn;
    private $id;
    private $auth;
    private $ticket_note_model;

    public function __construct($db, $id = null) {
        $this->conn = $db;
        $this->id = $id;
        $this->ticket_note_model = new TicketNoteModel($db);

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
                $this->store(json_decode(file_get_contents("php://input"), true));
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
        $tickets = $this->ticket_note_model->getTicketNotes();
        echo json_encode($tickets);
    }

    public function store($data) {
        (new TicketNoteRules($this->conn))->handle($data);
        
        $data['user_id'] = $this->auth['id'];
        $ticket_note_id = $this->ticket_note_model->create($data);

        if ($ticket_note_id) {
            echo json_encode(['status' => 'success', 'message' => 'Ticket note created successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to create ticket note']);
        }
    }

    public function edit($id) {
        $ticket = $this->ticket_note_model->getNoteById($id);
        echo json_encode($ticket);
    }

    public function update($data, $id) {
        (new TicketNoteRules($this->conn))->handle($data);

        if ($this->ticket_note_model->update($id, $data)) {
            echo json_encode(['status' => 'success', 'message' => 'Ticket note updated successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update ticket note']);
        }
    }

    public function delete($id) {
        if ($this->ticket_note_model->delete($id)) {
            echo json_encode(['status' => 'success', 'message' => 'Ticket note deleted successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete ticket note']);
        }
    }
}
