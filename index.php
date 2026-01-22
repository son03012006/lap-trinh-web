<?php
// ================= SESSION =================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ================= CONFIG =================
require_once 'app/Config/config.php';
require_once 'app/Config/database.php';

// ================= ROUTING =================
$c = $_GET['c'] ?? 'product';
$a = $_GET['a'] ?? 'index';


// ================= CONTROLLER =================
switch ($c) {

    case 'product':
        require_once 'app/Controller/ProductController.php';
        $controller = new ProductController();
        break;

    case 'cart':
        require_once 'app/Controller/CartController.php';
        $controller = new CartController();
        break;

    case 'auth':
        require_once 'app/Controller/AuthController.php';
        $controller = new AuthController();
        break;

    case 'user':
        require_once 'app/Controller/UserController.php';
        $controller = new UserController();
        break;

    case 'admin':
        require_once 'app/Controller/Admin/AdminController.php';
        $controller = new AdminController();
        break;
    case 'order':
        require_once 'app/Controller/OrderController.php';
        $controller = new OrderController();
        break;
    case 'chat':
        require_once 'app/Controller/ChatController.php';
        $controller = new ChatController();
        break;

    default:
        http_response_code(404);
        die('Controller không tồn tại');
}

// ================= ACTION =================
if (!method_exists($controller, $a)) {
    http_response_code(404);
    die('Action không tồn tại');
}

// ================= RUN =================
$controller->$a();
exit;
