<?php

namespace App\Models;

use PDO;

class Package
{
    private $con;   //$con is the data variable

    public function __construct($db)    //connect to database using constructor
    {
        $this->con = $db;
    }

    public function getAll()
    {
        $stmt = $this->con->prepare("SELECT * FROM packages ORDER BY name");
        if ($stmt->execute()) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return false;
    }
}
