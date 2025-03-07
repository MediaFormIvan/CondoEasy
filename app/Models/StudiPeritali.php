<?php
namespace App\Models;

use PDO;

class StudiPeritali extends Model {
    protected $table = 'studi_peritali';

    /**
     * Recupera tutti gli studi peritali
     *
     * @return array
     */
    public function getAll() {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
