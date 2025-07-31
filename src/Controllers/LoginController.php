<?php

namespace App\Controllers;

use App\Models\User;
use Exception;

class LoginController
{
    private $User;

    public function __construct($db)
    {
        $this->User = new User($db);
    }

    public function login(array $values)
    {
        try {
            if (
                empty($values["email"]) ||
                empty($values["password"])
            ) {
                throw new Exception("Required Fields are empty !");
            }

            $email    = trim($_POST["email"]);
            $password = trim($values["password"]);

            $user = $this->User->getByEmail($email);

            if (!$user) {
                throw new Exception("User Not Found !");
            }

            if (!password_verify($password, $user['password'])) {
                throw new Exception("Invalid Password !");
            }

            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            $_SESSION['success'] = "Welcome " . $user['name'];
            header("Location: " . APP_URL . "/" . $user['role'] . "/dashboard");
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: " . APP_URL . "/login");
            exit;
        }
    }
}
