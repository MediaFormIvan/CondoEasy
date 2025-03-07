<?php
namespace App\Models;

use PDO;

class ContrattoDocumento extends Model {
    protected $table = 'contratti_documenti';

    // Recupera i documenti relativi a un contratto
    public function getByContratto($id) {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE IDContratto = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Inserisce un nuovo documento
    public function create($data) {
        $sql = "INSERT INTO " . $this->table . " 
            (IDContratto, Titolo, NomeFile)
            VALUES (:IDContratto, :Titolo, :NomeFile)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'IDContratto' => $data['IDContratto'],
            'Titolo'      => $data['Titolo'],
            'NomeFile'    => $data['NomeFile']
        ]);
    }

    // Elimina un documento
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM " . $this->table . " WHERE IDContrattoDocumento = :id");
        return $stmt->execute(['id' => $id]);
    }
}
