<?php
require_once __DIR__ . "/../Components/header.php";
require_once __DIR__ . "/../Components/footer.php";

use App\Middleware\SessionMiddleware;
use App\Controllers\RegisterController;
use App\Helpers\Flash;

SessionMiddleware::verifyAdmin();
SessionMiddleware::verifyUser();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['submit'])) {
    $register = new RegisterController($con);
    $register->register($_POST);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register - Travel Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #eef2f7;
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            margin: 0;
            display: flex;
            flex-direction: column;
        }

        .register-wrapper {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            background-color: #ffffff;
            color: #333;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
            width: 100%;
        }

        .form-label {
            font-weight: 600;
        }

        .btn-primary {
            background-color: #667eea;
            border: none;
        }

        .btn-primary:hover {
            background-color: #5a67d8;
        }

        .text-primary {
            color: #667eea !important;
        }

        .text-primary:hover {
            color: #4c51bf !important;
        }

        a.text-primary {
            text-decoration: none;
        }

        a.text-primary:hover {
            text-decoration: underline;
        }

        footer {
            margin-top: auto;
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
    <div class="register-wrapper">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card p-4">
                        <div class="gradient-header">
                            <h2 class="text-center mb-4 text-white">Create Account</h2>
                        </div>

                        <?php Flash::render(); ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input name="name" class="form-control"
                                    value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input name="email" type="email" class="form-control"
                                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input name="phone" class="form-control"
                                    value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control"><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input name="password" type="password" class="form-control" required>
                            </div>

                            <button type="submit" name="submit" class="btn btn-primary w-100">Register</button>
                        </form>


                        <p class="text-center mt-3">
                            Already have an account? <a href="<?= APP_URL ?>/login" class="text-primary">Login here</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>