<?php
namespace App\Models;

use PDO;

class TipoContratto extends Model {
    protected $table = 'tipi_contratti';

    // Recupera tutte le tipologie non archiviate
    public function getAll() {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE Archiviato = 0");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
