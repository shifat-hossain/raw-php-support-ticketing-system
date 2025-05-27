<?php

require_once 'routes/Route.php';
require_once 'config/Database.php';
require_once 'controllers/DepartmentController.php';
require_once 'controllers/TicketController.php';
require_once 'controllers/TicketStatusController.php';
require_once 'controllers/TicketNoteController.php';
require_once 'controllers/ReagistrationController.php';
require_once 'controllers/LoginController.php';
// Get the request method
$requestMethod = $_SERVER["REQUEST_METHOD"];

// Get the ticket ID if provided
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/api/', $uri);
$group_uri = explode('/', $uri[1]);
$id = (isset($group_uri[1]) && is_numeric($group_uri[1])) ? $group_uri[1] : null;

$database = new Database();
$db = $database->connect();
// echo '<pre>';print_r($group_uri);die;
if($group_uri[0] === 'departments') {
    $controller = new DepartmentController($db, $id);
    $controller->processRequest();
}
else if($group_uri[0] === 'tickets') {
    if(isset($group_uri[2]) && $group_uri[2] === 'change-status') {
        $ticket_status_controller = new TicketStatusController($db);
        $ticket_status_controller(json_decode(file_get_contents("php://input"), true), $id);
    } else {
        $controller = new TicketController($db, $id);
        $controller->processRequest();
    }
}
else if($group_uri[0] === 'ticket-notes') {
    $controller = new TicketNoteController($db, $id);
    $controller->processRequest();
}
else if($group_uri[0] === 'registration') {
    $controller = new RegistrationController($db);
    $controller->register();
}
else if($group_uri[0] === 'login') {
    $controller = new LoginController($db);
    $controller->login();
}
else {
    echo json_encode(['status' => 'error', 'message' => 'Page not found']);
    http_response_code(404);
    exit();
}