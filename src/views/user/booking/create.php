<?php
require_once __DIR__ . "/../../../_init_.php";

use App\Middleware\SessionMiddleware;
use App\Controllers\BookingController;

SessionMiddleware::validateUserSession();

// FIXED check
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location:" . APP_URL . "/user/dashboard");
    exit;
}

$package_id = (int) $_GET["id"];
$customer_id = $_SESSION["user_id"];

$values = [
    'customer_id' => $customer_id,
    'package_id'  => $package_id
];

$bookingController = new BookingController($con);
$bookingController->create($values);
