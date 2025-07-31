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
}
