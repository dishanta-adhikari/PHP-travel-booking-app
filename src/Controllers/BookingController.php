<?php

namespace App\Controllers;

use App\Models\Booking;
use App\Models\Notification;
use Exception;

class BookingController
{
    private $Booking, $Notification;

    public function __construct($db)
    {
        $this->Booking = new Booking($db);
        $this->Notification = new Notification($db);
    }

    public function getBookingsByUser($id)
    {
        return $this->Booking->getBookingsByUser($id);
    }

    public function create(array $values)
    {
        try {
            $customer_id = trim($values['customer_id'] ?? '');
            $package_id  = trim($values['package_id'] ?? '');

            if (empty($customer_id) || empty($package_id)) {
                throw new Exception("Required fields are empty!");
            }

            // Prevent duplicate bookings
            if ($this->Booking->getByids(['customer_id' => $customer_id, 'package_id' => $package_id])) {
                throw new Exception("You have already booked this package.");
            }

            $booked = $this->Booking->create([
                'customer_id' => $customer_id,
                'package_id'  => $package_id,
                'pay_status'  => 'Pending'
            ]);

            if (!$booked) {
                throw new Exception("Failed to book the package!");
            }

            $message = "New booking by user ID $customer_id for package ID $package_id.";
            $this->Notification->create($message, $customer_id);

            $_SESSION['success'] = "Request Send. Waiting for confirmation...";
            header("Location:" . APP_URL . "/user/dashboard");
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location:" . APP_URL . "/user/booking/create");
            exit;
        }
    }

    public function delete(int $booking_id, int $customer_id)
    {
        try {
            // verify ownership
            $booking = $this->Booking->findById($booking_id);
            if (!$booking) {
                throw new Exception("Booking not found.");
            }

            if ($booking['customer_id'] !== $customer_id) {
                throw new Exception("Unauthorized action.");
            }

            $deleted = $this->Booking->delete($booking_id);
            if (!$deleted) {
                throw new Exception("Failed to delete booking.");
            }

            $_SESSION['success'] = "Booking deleted! Refund will be processed within 3 working days.";
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        header("Location:" . APP_URL . "/user/dashboard");
        exit;
    }
}
