<?php
namespace App\Models;

use PDO;

class SinistroDocumento extends Model {
    protected $table = 'sinistri_documenti';

    // Recupera tutti i documenti per un sinistro
    public function getBySinistro($idSinistro) {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE IDSinistro = :IDSinistro");
        $stmt->execute(['IDSinistro' => $idSinistro]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Crea un nuovo documento
    public function create($data) {
        $sql = "INSERT INTO " . $this->table . " (IDSinistro, Titolo, File) VALUES (:IDSinistro, :Titolo, :File)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'IDSinistro' => $data['IDSinistro'],
            'Titolo'     => $data['Titolo'],
            'File'       => $data['File']
        ]);
    }

    // Aggiorna il titolo di un documento
    public function update($data) {
        $sql = "UPDATE " . $this->table . " SET Titolo = :Titolo WHERE IDSinistroDocumento = :IDSinistroDocumento";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'Titolo'              => $data['Titolo'],
            'IDSinistroDocumento' => $data['IDSinistroDocumento']
        ]);
    }

    // Elimina un documento
    public function delete($id) {
        $sql = "DELETE FROM " . $this->table . " WHERE IDSinistroDocumento = :IDSinistroDocumento";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['IDSinistroDocumento' => $id]);
    }
}
