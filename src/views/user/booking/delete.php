<?php
require_once __DIR__ . "/../../../_init_.php";

use App\Middleware\SessionMiddleware;
use App\Controllers\BookingController;

SessionMiddleware::validateUserSession();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["booking_id"])) {
    $booking_id = (int) $_POST["booking_id"];
    $customer_id = $_SESSION["user_id"];

    $controller = new BookingController($con);
    $controller->delete($booking_id, $customer_id);
} else {
    $_SESSION['error'] = "Invalid request.";
    header("Location:" . APP_URL . "/user/dashboard");
    exit;
}
