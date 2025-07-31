<?php
require_once __DIR__ . "/../../_init_.php";

$role = $_SESSION["role"] ?? '';

if ($role === 'admin') {
    $userName = 'Admin';
} elseif ($role === 'user') {
    $userName = $_SESSION['user_name'] ?? 'Guest';
} else {
    $userName = 'Guest';
}


?>
<!-- Sticky Header Styles -->
<style>
    body {
        font-family: 'Inter', sans-serif;
        background: #f1f5f9;
        color: #333;
        font-family: 'Inter', sans-serif;
    }

    .travel-header {
        position: sticky;
        top: 0;
        width: 100%;
        background: linear-gradient(to right, #667eea, #764ba2);
        color: #fff;
        z-index: 1050;
        padding: 0.6rem 1rem;
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.25);
        /* <-- Updated shadow */
    }


    .travel-header .site-title {
        font-weight: 700;
        font-size: 1.4rem;
        color: #fff;
        text-decoration: none;
    }

    .travel-header .user-info {
        font-size: 0.95rem;
        margin-right: 1rem;
        color: #e0e0e0;
    }

    .travel-header .btn-logout {
        color: white;
        padding: 0.35rem 0.8rem;
        border: none;
        border-radius: 6px;
        font-size: 0.9rem;
        transition: background-color 0.2s ease-in-out;
    }



    @media (max-width: 768px) {
        .travel-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .travel-header .right-content {
            margin-top: 0.5rem;
        }
    }

    /* Dashboard box */
    .dashboard-header {
        background: linear-gradient(to right, #667eea, #764ba2);
        color: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
    }

    .logout-btn {
        float: right;
    }

    .table thead {
        background-color: #e2e8f0;
    }

    .card {
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .btn-outline-light,
    .btn-outline-warning {
        border-radius: 6px;
    }
</style>

<!-- Sticky Header HTML -->
<header class="travel-header d-flex justify-content-between align-items-center">

    <?php if (!empty($_SESSION['user_id']) && $role): ?>
        <a href="<?= APP_URL . '/' . ($role === 'admin' ? 'admin/dashboard' : 'user/dashboard') ?>" class="site-title">✈️ TravelBook</a>
    <?php else: ?>
        <a href="<?= APP_URL ?>" class="site-title">✈️ TravelBook</a>
    <?php endif; ?>

    <div class="d-flex align-items-center right-content">
        <div class="user-info me-2">
            User: <?= htmlspecialchars($userName) ?>
        </div>
        <?php if ($role): ?>
            <a href="<?= APP_URL ?>/logout" class="btn btn-light text-dark btn-logout">Logout</a>
        <?php endif; ?>
    </div>
</header>