<?php

namespace App\Models;

use App\Core\Database;

class Model
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // Nel file Model.php (classe base dei modelli)
    public function getDb()
    {
        return $this->db;
    }
}
