<?php
namespace App\Models;

use PDO;

class ScadenzaDocumento extends Model {
    protected $table = 'scadenze_documenti';

    // Recupera tutti i documenti associati a una scadenza
    public function getByScadenza($id) {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE IDScadenza = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Inserisce un nuovo documento per una scadenza
    public function create($data) {
        $sql = "INSERT INTO " . $this->table . " (IDScadenza, Titolo, File)
                VALUES (:IDScadenza, :Titolo, :File)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'IDScadenza' => $data['IDScadenza'],
            'Titolo'     => $data['Titolo'],
            'File'       => $data['File']
        ]);
    }

    // Elimina un documento
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM " . $this->table . " WHERE IDScadenzaDocumento = :id");
        return $stmt->execute(['id' => $id]);
    }
}
