<?php

namespace App\Models;

use PDO;

class Package
{
    private $con;   //$con is the data variable

    public function __construct($db)    //connect to database using constructor
    {
        $this->con = $db;
    }

    public function getById($id)
    {
        $stmt = $this->con->prepare("SELECT * FROM packages WHERE package_id = ?");
        if ($stmt->execute([$id])) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    public function getAll()
    {
        $stmt = $this->con->prepare("SELECT * FROM packages ORDER BY name");
        if ($stmt->execute()) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return false;
    }

    public function allWithPaymentStatus()
    {
        $stmt = $this->con->prepare("
        SELECT p.*, MAX(b.pay_status) AS payment_status
        FROM packages p
        LEFT JOIN bookings b ON p.package_id = b.package_id
        GROUP BY p.package_id
        ORDER BY p.package_id DESC
    ");
        if ($stmt->execute()) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return false;
    }

    public function find($id)
    {
        $stmt = $this->con->prepare("SELECT * FROM packages WHERE package_id = ?");
        if ($stmt->execute([$id])) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    public function save($data, $imageString = "")
    {
        $stmt = $this->con->prepare("
            INSERT INTO packages (name, price, description, image)
            VALUES (:name, :price, :description, :image)
        ");
        if ($stmt->execute([
            ':name' => $data['name'],
            ':price' => $data['price'],
            ':description' => $data['description'],
            ':image' => $imageString
        ])) {
            return true;
        }
        return false;
    }

    public function update($id, $data, $imageString = null)
    {
        if ($imageString) {
            $sql = "UPDATE packages SET name = :name, price = :price, description = :description, image = :image WHERE package_id = :id";
        } else {
            $sql = "UPDATE packages SET name = :name, price = :price, description = :description WHERE package_id = :id";
        }

        $stmt = $this->con->prepare($sql);
        $params = [
            ':name' => $data['name'],
            ':price' => $data['price'],
            ':description' => $data['description'],
            ':id' => $id
        ];
        if ($imageString) $params[':image'] = $imageString;

        if ($stmt->execute($params)) {
            return true;
        }
        return false;
    }

    public function delete($id)
    {
        $stmt = $this->con->prepare("DELETE FROM packages WHERE package_id = ?");
        if ($stmt->execute([$id])) {
            return true;
        }
        return false;
    }

    public function exportCsv()
    {
        $stmt = $this->con->prepare("SELECT * FROM packages");
        if ($stmt->execute()) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return false;
    }

    public function search($keyword)
    {
        $stmt = $this->con->prepare("
        SELECT p.*, MAX(b.pay_status) AS payment_status
        FROM packages p
        LEFT JOIN bookings b ON p.package_id = b.package_id
        WHERE p.name LIKE ? OR p.price LIKE ?
        GROUP BY p.package_id
        ORDER BY p.package_id DESC
    ");
        $searchTerm = "%$keyword%";
        $stmt->execute([$searchTerm, $searchTerm]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
