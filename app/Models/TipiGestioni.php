<?php
namespace App\Models;

use PDO;

class TipiGestioni extends Model {
    protected $table = 'tipi_gestioni';

    // Recupera tutti i tipi di gestione ordinati per nome
    public function getAll() {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " ORDER BY Nome");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
