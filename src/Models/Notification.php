<?php

namespace App\Models;

use PDO;

class Notification
{
    private $con;

    public function __construct($db)
    {
        $this->con = $db;
    }

    public function unread()
    {
        $stmt = $this->con->prepare("SELECT * FROM notifications WHERE is_read = 0 ORDER BY created_at DESC");
        if ($stmt->execute()) {
            return  $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return false;
    }

    public function markAllAsRead()
    {
        $stmt = $this->con->prepare("UPDATE notifications SET is_read = 1 WHERE is_read = 0");
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function create(string $message, int $id)
    {
        $stmt = $this->con->prepare("INSERT INTO notifications (message, is_read, created_at, user_id) VALUES (?, 0, NOW(), ?)");
        return $stmt->execute([$message, $id]);
    }
}
