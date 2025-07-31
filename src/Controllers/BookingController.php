<?php

namespace App\Controllers;

use App\Models\Booking;
use Exception;

class BookingController
{
    private $Booking;

    public function __construct($db)
    {
        $this->Booking = new Booking($db);
    }

    public function getBookingsByUser($id)
    {
        return $this->Booking->getBookingsByUser($id);
    }
}
