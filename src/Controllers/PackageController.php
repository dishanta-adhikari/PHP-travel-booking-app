<?php

namespace App\Controllers;

use App\Models\Package;
use Exception;

class PackageController
{
    private $Package;

    public function __construct($db)
    {
        $this->Package = new Package($db);
    }

    public function getAll()
    {
        return $this->Package->getAll();
    }

    public function getById($id)
    {
        return $this->Package->getById($id);
    }
}
