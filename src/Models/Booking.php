<?php

namespace App\Models;

use PDO;

class Booking
{
    private $con;

    public function __construct($db)
    {
        $this->con = $db;
    }

    public function create(array $values)
    {
        $stmt = $this->con->prepare("INSERT INTO bookings (customer_id, package_id, book_date, pay_status) VALUES (?, ?, NOW(), ?)");
        if ($stmt->execute([$values['customer_id'], $values['package_id'], $values['pay_status']])) {
            return true;
        }
        return false;
    }

    public function all()
    {
        $stmt = $this->con->prepare("SELECT * FROM bookings");
        if ($stmt->execute()) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return false;
    }

    public function findById(int $id): array|false
    {
        $stmt = $this->con->prepare("SELECT * FROM bookings WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getBookingsByUser(int $userId)
    {
        $stmt = $this->con->prepare("SELECT b.id, b.package_id, b.pay_status, p.name AS package_name, b.book_date 
            FROM bookings b 
            JOIN packages p ON b.package_id = p.package_id 
            WHERE b.customer_id = ? 
            ORDER BY b.book_date DESC
        ");

        if ($stmt->execute([$userId])) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        };
        return false;
    }

    public function getByids(array $values)
    {
        $stmt = $this->con->prepare("SELECT * FROM bookings WHERE customer_id = ? AND package_id = ?");
        if ($stmt->execute([$values['customer_id'], $values['package_id']])) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->con->prepare("DELETE FROM bookings WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
