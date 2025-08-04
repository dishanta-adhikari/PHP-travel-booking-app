<?php
require_once __DIR__ . "/../Components/header.php";
require_once __DIR__ . "/../Components/footer.php";

use App\Middleware\SessionMiddleware;
use App\Controllers\AdminController;
use App\Controllers\PackageController;
use App\Helpers\Flash;

SessionMiddleware::validateAdminSession();

$pack = new PackageController($con);
$controller = new AdminController($con);

// Actions
$controller->handlePackageForm();
$controller->deletePackage();
$controller->deleteCustomer();

$data = $controller->fetchData();
$packages = $data['packages'] ?? [];
$bookings = $data['bookings'] ?? [];
$customers = $data['customers'] ?? [];
$notifications = $data['notifications'] ?? [];

$topPackages = $data['topPackages'] ?? [];
$totalBookings = $data['totalBookings'] ?? 0;
$paidBookings = $data['paidBookings'] ?? 0;
$pendingBookings = $data['pendingBookings'] ?? 0;
$totalRevenue = $data['totalRevenue'] ?? 0.0;

?>

<!DOCTYPE html>
<html>

<head>
    <title>Admin - Packages</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f1f5f9;
            color: #333;
        }

        .form-section,
        .card {
            border-radius: 12px;
        }

        .bg-header {
            background: linear-gradient(to right, #4facfe, #00f2fe);
            color: white;
            border-radius: 12px;
        }

        .dashboard-header {
            background: linear-gradient(to right, #667eea, #764ba2);
            color: white;
            border-radius: 12px;
        }

        .dashboard-header .btn-outline-light,
        .dashboard-header .btn-outline-danger {
            color: white;
            border-color: white;
        }

        .dashboard-header .btn-outline-light:hover,
        .dashboard-header .btn-outline-danger:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .table thead {
            background-color: #e2e8f0;
        }
    </style>
</head>

<body>
    <div class="container py-5">
        <div class="dashboard-header mb-4 p-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h3 class="mb-1">Admin
                        | Package Management</h3>
                    <p class="mb-0">Manage all travel packages, search and export.</p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <a href="<?= APP_URL ?>/admin/profile" class="btn btn-outline-light btn-sm me-2">My Profile</a>
                </div>
            </div>
        </div>

        <div class="alert alert-info">
            <h5>Notifications</h5>
            <?php if (count($notifications) == 0): ?>
                <p>No new notifications.</p>
            <?php else: ?>
                <ul>
                    <?php foreach ($notifications as $n): ?>
                        <li><?= htmlspecialchars($n['message']) ?> <small class="text-muted">(<?= $n['created_at'] ?>)</small></li>
                    <?php endforeach ?>
                </ul>
            <?php endif ?>
        </div>

        <?php Flash::render(); ?>

        <!-- Add/Edit Package Form -->
        <div class="form-section bg-white p-4 mb-4 shadow-sm">
            <h4><?= isset($_GET['edit']) ? 'Edit Package' : 'Create New Package' ?></h4>
            <?php
            $editPackage = ['package_id' => '', 'name' => '', 'price' => '', 'description' => '', 'image' => ''];

            if (isset($_GET['edit'])) {
                $id = (int)$_GET['edit'];
                $editPackage = $pack->getById($id);
            }
            ?>
            <form method="POST" enctype="multipart/form-data" class="row g-3">
                <input type="hidden" name="id" value="<?= htmlspecialchars($editPackage['package_id']) ?>">

                <div class="col-md-6">
                    <input name="name" class="form-control" value="<?= htmlspecialchars($editPackage['name']) ?>" placeholder="Package Name" required>
                </div>

                <div class="col-md-3">
                    <input name="price" type="number" step="0.01" class="form-control" value="<?= htmlspecialchars($editPackage['price']) ?>" placeholder="Price" required>
                </div>

                <div class="col-md-3">
                    <input name="images[]" type="file" class="form-control" multiple>
                    <small class="text-muted">You can select multiple images.</small>

                    <?php
                    if (!empty($editPackage['image'])):
                        $existingImages = explode(',', $editPackage['image']);
                        foreach ($existingImages as $img):
                            $img = trim($img);
                            $imagePath = APP_URL . '/public' . $img; // Correct relative path
                    ?>
                            <img src="<?= htmlspecialchars($imagePath) ?>" width="80" class="mt-2 me-2 rounded" alt="Package Image">
                    <?php
                        endforeach;
                    endif;
                    ?>
                </div>

                <div class="col-md-12">
                    <textarea name="description" class="form-control" placeholder="Description"><?= htmlspecialchars($editPackage['description']) ?></textarea>
                </div>

                <div class="col-md-12 text-end">
                    <button name="save" class="btn btn-success"><?= isset($_GET['edit']) ? 'Update' : 'Create' ?></button>
                </div>
            </form>
        </div>

        <!-- Export -->
        <div class="d-flex justify-content-between align-items-center mb-2">
            <a href="?export=csv" class="btn btn-outline-success">Export CSV</a>
        </div>

        <!-- Package List -->
        <div class="card p-3 shadow-sm" id="result">
            <h5 class="mb-3">Packages <span class="badge bg-secondary"><?= count($packages) ?></span></h5>
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
        </div>

        <!-- Booking Table -->
        <div class="card mt-5 p-3 shadow-sm">
            <h5 class="mb-3">Bookings <span class="badge bg-info"><?= count($bookings) ?></span></h5>
            <div class="table-responsive">
                <table class="table table-bordered text-center align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Package</th>
                            <th>Book Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $b): ?>
                            <tr>
                                <td><?= $b['booking_id'] ?></td>
                                <td><?= htmlspecialchars($b['customer_name']) ?></td>
                                <td><?= htmlspecialchars($b['package_name']) ?></td>
                                <td><?= $b['book_date'] ?></td>
                                <td>
                                    <?php if ($b['pay_status'] === 'Paid'): ?>
                                        <span class="text-success fw-bold">Paid</span>
                                    <?php else: ?>
                                        <span class="text-danger">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($b['pay_status'] === 'Pending'): ?>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="booking_id" value="<?= $b['booking_id'] ?>">
                                            <button type="submit" name="pay_now" class="btn btn-sm btn-success">Pay Now</button>
                                        </form>
                                    <?php elseif ($b['pay_status'] === 'Paid'): ?>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="booking_id" value="<?= $b['booking_id'] ?>">
                                            <button type="submit" name="cancel_booking" class="btn btn-sm btn-danger" onclick="return confirm('Cancel this booking?')">Cancel Booking</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Customers -->
        <div class="card mt-5 p-3 shadow-sm">
            <h5 class="mb-3">Customers <span class="badge bg-primary"><?= count($customers) ?></span></h5>
            <div class="table-responsive">
                <table class="table table-bordered text-center align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Username</th>
                            <th>Registered On</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($customers as $c): ?>
                            <tr>
                                <td><?= $c['id'] ?></td>
                                <td><?= htmlspecialchars($c['name']) ?></td>
                                <td><?= htmlspecialchars($c['email']) ?></td>
                                <td><?= htmlspecialchars($c['phone']) ?></td>
                                <td><?= $c['created_at'] ?? '-' ?></td>
                                <td>
                                    <a href="?delete_customer=<?= $c['id'] ?>" onclick="return confirm('Delete this customer?')" class="btn btn-sm btn-danger">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Top Packages -->
        <div class="card mt-4 p-3 shadow-sm">
            <h5 class="mb-3">Top 3 Most Booked Packages</h5>
            <ul class="list-group">
                <?php foreach ($topPackages as $pkg): ?>
                    <li class="list-group-item d-flex justify-content-between">
                        <?= htmlspecialchars($pkg['name']) ?>
                        <span class="badge bg-primary"><?= $pkg['count'] ?> bookings</span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Analytics -->
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card bg-header p-3 text-center">
                    <h6>Total Packages</h6>
                    <h2><?= count($packages) ?></h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-warning text-dark p-3 text-center">
                    <h6>Total Value</h6>
                    <h2>₹<?= number_format(array_sum(array_column($packages, 'price')), 2) ?></h2>
                </div>
            </div>
        </div>

        <div class="row mt-5 mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white p-3 text-center">
                    <h6>Total Bookings</h6>
                    <h2><?= $totalBookings ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white p-3 text-center">
                    <h6>Paid Bookings</h6>
                    <h2><?= $paidBookings ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-dark p-3 text-center">
                    <h6>Pending Bookings</h6>
                    <h2><?= $pendingBookings ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white p-3 text-center">
                    <h6>Total Revenue</h6>
                    <h2>₹<?= number_format($totalRevenue, 2) ?></h2>
                </div>
            </div>
        </div>
    </div>

</body>

</html>