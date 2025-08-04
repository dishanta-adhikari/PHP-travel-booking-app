<?php
require_once __DIR__ . "/../Components/header.php";
require_once __DIR__ . "/../Components/footer.php";

use App\Controllers\AdminController;

$search = $_GET['search'] ?? '';
$controller = new AdminController($con);
$packages = $controller->searchPackages($search);
?>

<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle text-center">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Price (₹)</th>
                <th>Payment</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($packages as $pkg): ?>
                <tr>
                    <td><?= $pkg['package_id'] ?></td>
                    <td><?= htmlspecialchars($pkg['name']) ?></td>
                    <td><?= number_format($pkg['price'], 2) ?></td>
                    <td>
                        <?php if ($pkg['payment_status'] === 'Paid'): ?>
                            <span class='text-success'>Paid</span>
                        <?php elseif ($pkg['payment_status'] === null): ?>
                            <span class='text-muted'>No Booking</span>
                        <?php else: ?>
                            <span class='text-danger'>Pending</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="?edit=<?= $pkg['package_id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                        <a href="?delete=<?= $pkg['package_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this package?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>