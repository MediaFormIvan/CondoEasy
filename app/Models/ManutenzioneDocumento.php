<?php
namespace App\Models;

use PDO;

class ManutenzioneDocumento extends Model {
    protected $table = 'manutenzioni_documenti';

    // Recupera tutti i documenti per una data manutenzione
    public function getByManutenzione($idManutenzione) {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE IDManutenzione = :IDManutenzione");
        $stmt->execute(['IDManutenzione' => $idManutenzione]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Crea un nuovo documento
    public function create($data) {
        $sql = "INSERT INTO " . $this->table . " (IDManutenzione, Titolo, File) VALUES (:IDManutenzione, :Titolo, :File)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'IDManutenzione' => $data['IDManutenzione'],
            'Titolo'         => $data['Titolo'],
            'File'           => $data['File']
        ]);
    }

    // Aggiorna il titolo di un documento
    public function update($data) {
        $sql = "UPDATE " . $this->table . " SET Titolo = :Titolo WHERE IDManutenzioneDocumento = :IDManutenzioneDocumento";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'Titolo' => $data['Titolo'],
            'IDManutenzioneDocumento' => $data['IDManutenzioneDocumento']
        ]);
    }

    // Elimina un documento
    public function delete($id) {
        $sql = "DELETE FROM " . $this->table . " WHERE IDManutenzioneDocumento = :IDManutenzioneDocumento";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['IDManutenzioneDocumento' => $id]);
    }
}
