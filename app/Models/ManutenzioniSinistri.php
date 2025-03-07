<?php
namespace App\Models;

use PDO;

class ManutenzioniSinistri extends Model {
    protected $table = 'manutenzioni_sinistri';

    public function createAssociation($data) {
        $sql = "INSERT INTO " . $this->table . " (IDManutenzione, IDSinistro, Creato) VALUES (:IDManutenzione, :IDSinistro, NOW())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'IDManutenzione' => $data['IDManutenzione'],
            'IDSinistro'     => $data['IDSinistro']
        ]);
    }

    public function getByManutenzione($IDManutenzione) {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE IDManutenzione = :id LIMIT 1");
        $stmt->execute(['id' => $IDManutenzione]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
