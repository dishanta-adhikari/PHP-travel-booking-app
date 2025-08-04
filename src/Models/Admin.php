<?php

namespace App\Models;

use PDO;

class Admin
{
    private $con;

    public function __construct($con)
    {
        $this->con = $con;
    }

    public function find($id)
    {
        $stmt = $this->con->prepare("SELECT id, name, email FROM customers WHERE id = ?");
        if ($stmt->execute([$id])) {
            return  $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    public function updateProfile($id, $name, $email)
    {
        $stmt = $this->con->prepare("UPDATE customers SET name = ?, email = ? WHERE id = ?");
        if ($stmt->execute([$id, $name, $email])) {
            return true;
        }
        return false;
    }

    public function getPassword($id)
    {
        $stmt = $this->con->prepare("SELECT password FROM customers WHERE id = ?");
        if ($stmt->execute()) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    public function updatePassword($id, $newHash)
    {
        $stmt = $this->con->prepare("UPDATE customers SET password = ? WHERE id = ?");
        if ($stmt->execute([$id, $newHash])) {
            return true;
        }
        return false;
    }
}
