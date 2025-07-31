<?php

namespace App\Middleware;

class SessionMiddleware
{
    /**
     * Redirect authenticated users with 'user' role to their dashboard
     */
    public static function verifyUser(): void
    {
        if (
            isset($_SESSION['user_id']) &&
            $_SESSION['role'] === 'user'
        ) {
            header("Location: " . APP_URL . "/user/dashboard");
            exit;
        }
    }

    /**
     * Redirect authenticated users with 'admin' role to their dashboard
     */
    public static function verifyAdmin(): void
    {
        if (
            isset($_SESSION['user_id']) &&
            $_SESSION['role'] === 'admin'
        ) {
            header("Location: " . APP_URL . "/admin/dashboard");
            exit;
        }
    }

    /**
     * Ensure user is logged in and is a regular user
     */
    public static function validateUserSession(): void
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
            header("Location: " . APP_URL . "/login");
            exit;
        }
    }


    /**
     * Ensure user is logged in and is an admin
     */
    public static function validateAdminSession(): void
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header("Location: " . APP_URL . "/login");
            exit;
        }
    }
}
