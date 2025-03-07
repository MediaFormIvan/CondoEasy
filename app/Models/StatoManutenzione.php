<?php
namespace App\Models;

use PDO;

class StatoManutenzione extends Model {
    protected $table = 'stati';

    // Recupera tutti gli stati non archiviati
    public function getAll() {
        $sql = "SELECT * FROM " . $this->table . " WHERE Archiviato = 0 ORDER BY Nome ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
