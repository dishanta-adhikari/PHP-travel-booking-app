<?php

namespace App\Controllers;

use App\Models\User;
use Exception;

class UserController
{
    private $User;

    public function __construct($db)
    {
        $this->User = new User($db);
    }

    public function getUserById($id)
    {
        return $this->User->getUserById($id);
    }

    public function update(array $values)
    {
        try {
            if (
                empty($values['name']) ||
                empty($values['email']) ||
                empty($values['phone']) ||
                empty($values['user_id'])
            ) {
                throw new Exception("Please Enter Valid Data.");
            }

            $name    = trim($values["name"]);
            $email   = trim($values["email"]);
            $phone   = trim($values["phone"]);
            $user_id = trim($values['user_id']);

            $values = [
                'name'  => $name,
                'email' => $email,
                'phone' => $phone,
                'id'    => $user_id
            ];

            $updated = $this->User->update($values);

            if (!$updated) {
                throw new Exception("Failed to update user data.");
            }

            $_SESSION['success'] = "Profile Updated Successfully.";
            header("Location: " . APP_URL . "/user/dashboard");
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: " . APP_URL . "/user/profile/update");
            exit;
        }
    }

    public function updatePassword(array $values)
    {
        try {
            if (
                empty($values['old-password']) ||
                empty($values['new-password']) ||
                empty($values['user_id'])
            ) {
                throw new Exception("Required fields are empty!");
            }

            $old     = trim($values['old-password']);
            $new     = trim($values['new-password']);
            $user_id = trim($values['user_id']);

            if ($new === $old) {
                throw new Exception("New password must be different from old password!");
            }

            $user = $this->User->getUserById($user_id);
            if (!$user) {
                throw new Exception("User not found!");
            }

            $db_password = $user['password'];

            if (!password_verify($old, $db_password)) {
                throw new Exception("Invalid old password!");
            }

            $password_hash = password_hash($new, PASSWORD_DEFAULT);

            $values = [
                'password' => $password_hash,
                'id'       => $user_id
            ];

            $passwordUpdated = $this->User->updatePassword($values);
            if (!$passwordUpdated) {
                throw new Exception("Failed to update password!");
            }

            $_SESSION['success'] = "Password updated successfully.";
            header("Location: " . APP_URL . "/user/dashboard");
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: " . APP_URL . "/update-password");
            exit;
        }
    }
}
