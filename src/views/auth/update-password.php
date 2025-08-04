<?php
require_once __DIR__ . "/../Components/header.php";
require_once __DIR__ . "/../Components/footer.php";

use App\Middleware\SessionMiddleware;
use App\Controllers\UserController;
use App\Helpers\Flash;

if (isset($_SESSION['role']) && $_SESSION['role'] === 'user') {
    SessionMiddleware::validateUserSession();
} else {
    SessionMiddleware::validateAdminSession();
}

$user_id = $_SESSION["user_id"];

// Handle password change
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['submit'])) {
    $userController = new UserController($con);
    $userController->updatePassword($_POST);
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Change Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: rgb(162, 179, 196);
            color: #333;
        }

        .gradient-header {
            background: linear-gradient(to right, #667eea, #764ba2);
            color: white;
            padding: 15px;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            margin: -24px -24px 24px -24px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="d-flex align-items-center justify-content-center min-vh-100 bg-light">
        <div class="card shadow-sm p-4 rounded-4 w-100" style="max-width: 450px;">
            <div class="gradient-header">
                <h4 class="mb-0">Change Password</h4>
            </div>

            <?php Flash::render(); ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Old Password</label>
                    <input type="password" name="old-password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">New Password</label>
                    <input type="password" name="new-password" class="form-control" required>
                </div>
                <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id); ?>" class="form-control" required>
                <div class="d-flex justify-content-between">
                    <a href="dashboard" class="btn btn-outline-secondary">Back</a>
                    <button type="submit" name="submit" class="btn btn-primary">Change</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>

<?php include 'includes/footer.php'; ?>