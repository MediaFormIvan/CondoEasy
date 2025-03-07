<?php
namespace App\Models;

use PDO;

class TipiPersone extends Model {
    protected $table = 'tipi_persone';

    public function getAll() {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " ORDER BY Nome");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
