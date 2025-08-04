<?php

namespace App\Controllers;

use App\Models\Admin;
use App\Models\Package;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Notification;
use Exception;

class AdminController
{
    private $Admin, $Package, $Booking, $Customer, $Notification;

    public function __construct($con)
    {
        $this->Admin = new Admin($con);
        $this->Package = new Package($con);
        $this->Booking = new Booking($con);
        $this->Customer = new Customer($con);
        $this->Notification = new Notification($con);
    }

    public function handlePackageForm()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
            try {
                if (
                    empty($_POST['name']) ||
                    empty($_POST['price']) ||
                    empty($_POST['description'])
                ) {
                    throw new Exception("Required Fields are Empty !");
                }

                $id          = trim((int)($_POST['id'] ?? 0));
                $name        = trim($_POST['name']);
                $price       = trim((float)$_POST['price']);
                $description = trim($_POST['description']);

                $images = $this->handleImageUploads();

                if ($id > 0) {
                    $existing = $this->Package->find($id);
                    $existingImages = explode(',', $existing['image']);
                    $allImages = array_merge($existingImages, $images);
                    $imageString = implode(',', array_unique(array_map('trim', $allImages)));
                    $this->Package->update($id, compact('name', 'price', 'description'), $imageString);
                    $_SESSION['success'] = "Package updated.";
                } else {
                    $imageString = implode(',', $images);
                    $this->Package->save(compact('name', 'price', 'description'), $imageString);
                    $_SESSION['success'] = "Package added.";
                }

                header("Location: " . APP_URL . "/admin/dashboard");
                exit;
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                header("Location: " . APP_URL . "/admin/dashboard");
                exit;
            }
        }
    }

    public function deletePackage()
    {
        if (isset($_GET['delete'])) {
            try {
                $id = (int)$_GET['delete'];
                $deleted = $this->Package->delete($id);

                if (!$deleted) {
                    throw new Exception("Falied to Delete Package !");
                }

                $_SESSION['success'] = "Package deleted successfully.";
                header("Location: " . APP_URL . "/admin/dashboard");
                exit;
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                header("Location: " . APP_URL . "/admin/dashboard");
                exit;
            }
        }
    }

    public function deleteCustomer()
    {
        if (isset($_GET['delete_customer'])) {
            try {
                $id = (int)$_GET['delete_customer'];
                $deleted = $this->Customer->delete($id);

                if (!$deleted) {
                    throw new Exception("Falied to Delete Customer !");
                }

                $_SESSION['success'] = "Customer deleted successfully.";
                header("Location: " . APP_URL . "/admin/dashboard");
                exit;
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                header("Location: " . APP_URL . "/admin/dashboard");
                exit;
            }
        }
    }

    private function handleImageUploads(): array
    {
        // Absolute path to the upload directory
        $uploadDir = __DIR__ . '/../../public/uploads/images/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $uploaded = [];

        if (!empty($_FILES['images']['name'][0])) {
            foreach ($_FILES['images']['name'] as $key => $original) {
                $tmpName = $_FILES['images']['tmp_name'][$key];
                $ext     = pathinfo($original, PATHINFO_EXTENSION);
                $newName = uniqid("pkg_", true) . '.' . $ext;
                $target  = $uploadDir . $newName;

                if (move_uploaded_file($tmpName, $target)) {
                    // Public-facing URL for frontend use
                    $uploaded[] = '/uploads/images/' . $newName;
                }
            }
        }

        return $uploaded;
    }

    public function fetchData(): array
    {
        $data = [];

        // Validate and fetch packages
        if ($this->Package) {
            $data['packages'] = $this->Package->allWithPaymentStatus();
        } else {
            $data['packages'] = [];
        }

        // Validate and fetch bookings
        if ($this->Booking) {
            $data['bookings'] = $this->Booking->all();
        } else {
            $data['bookings'] = [];
        }

        // Validate and fetch customers
        if ($this->Customer) {
            $data['customers'] = $this->Customer->all();
        } else {
            $data['customers'] = [];
        }

        // Validate and fetch notifications
        if ($this->Notification) {
            $data['notifications'] = $this->Notification->unread();
        } else {
            $data['notifications'] = [];
        }

        return $data;
    }

    public function searchPackages($keyword)
    {
        try {
            $results = $this->Package->search($keyword);
            return is_array($results) ? $results : [];
        } catch (Exception $e) {

            return [];
        }
    }

    public function find($id)
    {
        return $this->Customer->find($id);
    }

    public function showProfile($id)
    {
        return $this->Admin->find($id);
    }

    public function updateProfile($id, $name, $email)
    {

        try {
            $updated = $this->Admin->updateProfile($id, $name, $email);

            if (!$updated) {
                throw new Exception("Failed to Update the profile !");
            }

            $_SESSION['success'] = "Profile Updated Succesfully.";
            header("Location: " . APP_URL . "/admin/dashboard");
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: " . APP_URL . "/admin/dashboard");
            exit;
        }
    }

    public function changePassword($id, $old, $new)
    {
        $stored = $this->Admin->getPassword($id);
        if (hash('sha256', $old) === $stored) {
            $hashedNew = hash('sha256', $new);
            return $this->Admin->updatePassword($id, $hashedNew);
        }
        return false;
    }
}
