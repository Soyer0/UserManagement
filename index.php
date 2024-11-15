<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/config/config.php';
require __DIR__ . '/controllers/UserController.php';

$controller = new UserController();

$action = $_GET['action'] ?? 'showUsers';

switch ($action) {
    case 'showUsers':
        $controller->showUsers();
        break;
    case 'addUser':
        $controller->addUser();
        break;
    case 'editUser':
        $controller->editUser();
        break;
    case 'getUser':
        $controller->getUser();
        break;
    case 'deleteUsers':
        $controller->deleteUsers();
        break;
    case 'setStatus':
        $controller->setStatus();
        break;
    default:
        echo "404 Not Found";
        break;
}