<?php

namespace App\Controllers;

use App\Models\User;
use Exception;

class RegisterController
{
    private $User;

    public function __construct($db)
    {
        $this->User = new User($db);
    }

    public function register(array $values)
    {
        try {
            if (
                empty($values["name"]) ||
                empty($values["email"]) ||
                empty($values["phone"]) ||
                empty($values["address"]) ||
                empty($values["password"])
            ) {
                throw new Exception("Required Fields are Empty !");
            }

            $name     = trim($values["name"]);
            $email    = trim($values["email"]);
            $phone    = trim($values["phone"]);
            $address  = trim($values["address"]);
            $password = trim($values["password"]);

            if ($this->User->getByEmail($email)) {
                throw new Exception('User Already Exists!');
            }

            if (strlen($password) < 4) {
                throw new Exception("Password must be at least 4 characters long.");
            }

            $password_Hash = password_hash($password, PASSWORD_DEFAULT);

            $values = [     //name, email, phone, address, password, role
                'name'     => $name,
                'email'    => $email,
                'phone'    => $phone,
                'address'  => $address,
                'role'     => 'user',
                'password' => $password_Hash
            ];

            $created = $this->User->create($values);

            if (!$created) {
                throw new Exception("Failed to create the user !");
            }

            session_regenerate_id(true);

            $_SESSION['user_id'] = $created['id'];
            $_SESSION['user_name'] = $created['name'];
            $_SESSION['user_email'] = $created['email'];
            $_SESSION['role'] = $created['role'];

            $_SESSION['success'] = "Registration Successfull. Welcome " . $created['name'];
            header("Location: " . APP_URL . "/user/dashboard");
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: " . APP_URL . "/register");
            exit;
        }
    }
}
