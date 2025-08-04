<?php

namespace App\Models;

use PDO;

class User
{
    private $con;   //$con is the data variable

    public function __construct($db)    //connect to database using constructor
    {
        $this->con = $db;
    }

    public function create(array $values)
    {
        $stmt = $this->con->prepare("INSERT INTO customers (name, email, phone, address, password, role) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$values['name'], $values['email'], $values['phone'], $values['address'], $values['password'], $values['role']])) {
            return [
                'user_id' => $this->con->lastInsertId(),
                'name'    => $values['name'],
                'email'   => $values['email'],
                'role'    => $values['role']
            ];
        }
        return false;
    }

    public function getByEmail($email)
    {
        $stmt = $this->con->prepare("SELECT * FROM customers WHERE email = ?");
        if ($stmt->execute([$email])) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    public function getUserById(int $id)
    {
        $stmt = $this->con->prepare("SELECT * FROM customers WHERE id = ?");
        if ($stmt->execute([$id])) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    public function update(array $values)
    {
        $stmt = $this->con->prepare("UPDATE customers SET name=?, email=?, phone=? WHERE id=?");
        if ($stmt->execute([$values['name'], $values['email'], $values['phone'], $values['id']])) {
            return true;
        }
        return false;
    }

    public function updatePassword(array $values)
    {
        $stmt = $this->con->prepare("UPDATE customers SET password = ? WHERE id = ?");
        if ($stmt->execute([$values['password'], $values['id']])) {
            return true;
        }
        return false;
    }
}
