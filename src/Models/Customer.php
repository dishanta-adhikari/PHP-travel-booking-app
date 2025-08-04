<?php

namespace App\Models;

use PDO;

class Customer
{
    private $con;

    public function __construct($db)
    {
        $this->con = $db;
    }

    public function all()
    {
        $stmt = $this->con->prepare("SELECT * FROM customers ORDER BY id DESC");
        if ($stmt->execute()) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return false;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->con->prepare("DELETE FROM customers WHERE id = :id");
        if ($stmt->execute([':id' => $id])) {
            return true;
        }
        return false;
    }

    public function find(int $id)
    {
        $stmt = $this->con->prepare("SELECT * FROM customers WHERE id = :id");
        if ($stmt->execute([':id' => $id])) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }
}
