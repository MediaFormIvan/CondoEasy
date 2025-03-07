<?php
namespace App\Models;

use PDO;

class AssicurazioneDocumento extends Model {
    protected $table = 'assicurazioni_documenti';

    // Recupera tutti i documenti per un'assicurazione
    public function getByAssicurazione($id) {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE IDAssicurazione = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Inserisce un nuovo documento
    public function create($data) {
        $sql = "INSERT INTO " . $this->table . " 
            (IDAssicurazione, Titolo, File)
            VALUES (:IDAssicurazione, :Titolo, :File)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'IDAssicurazione' => $data['IDAssicurazione'],
            'Titolo'          => $data['Titolo'],
            'File'            => $data['File']
        ]);
    }

    // Aggiorna un documento esistente
    public function update($data) {
        $sql = "UPDATE " . $this->table . " SET 
            Titolo = :Titolo,
            File = :File
            WHERE IDAssicurazioneDocumento = :IDAssicurazioneDocumento";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'Titolo'                   => $data['Titolo'],
            'File'                     => $data['File'],
            'IDAssicurazioneDocumento' => $data['IDAssicurazioneDocumento']
        ]);
    }

    // Elimina un documento
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM " . $this->table . " WHERE IDAssicurazioneDocumento = :id");
        return $stmt->execute(['id' => $id]);
    }
}
