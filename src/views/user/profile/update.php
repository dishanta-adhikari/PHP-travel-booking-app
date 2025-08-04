<?php
require_once __DIR__ . "/../../Components/header.php";
require_once __DIR__ . "/../../Components/footer.php";

use App\Middleware\SessionMiddleware;
use App\Controllers\UserController;
use App\Helpers\Flash;

SessionMiddleware::validateUserSession();

$user_id = $_SESSION["user_id"];

$userController = new UserController($con);

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update"])) {
    $userController->update($_POST);
}

$user = $userController->getUserById($user_id);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Profile | Update</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f1f5f9;
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
    <div class="container min-vh-100 d-flex justify-content-center align-items-center">
        <div class="card py-4 px-4 rounded w-100" style="max-width: 500px;">
            <div class="gradient-header">
                <h3 class="mb-0">Profile | Edit</h3>
            </div>

            <?php Flash::render(); ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone']) ?>" required>
                </div>
                <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id) ?>" required>
                <div class="d-flex justify-content-between">
                    <a href="<?= APP_URL ?>/user/dashboard" class="btn btn-secondary">Back</a>
                    <button type="submit" name="update" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>