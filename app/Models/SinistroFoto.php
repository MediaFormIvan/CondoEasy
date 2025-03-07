<?php
namespace App\Models;

use PDO;

class SinistroFoto extends Model {
    protected $table = 'sinistri_foto';

    // Recupera tutte le foto per un sinistro
    public function getBySinistro($idSinistro) {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE IDSinistro = :IDSinistro");
        $stmt->execute(['IDSinistro' => $idSinistro]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Inserisce una nuova foto
    public function create($data) {
        $sql = "INSERT INTO " . $this->table . " (IDSinistro, File) VALUES (:IDSinistro, :File)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'IDSinistro' => $data['IDSinistro'],
            'File'       => $data['File']
        ]);
    }

    // Elimina una foto
    public function delete($id) {
        $sql = "DELETE FROM " . $this->table . " WHERE IDSinistroFoto = :IDSinistroFoto";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['IDSinistroFoto' => $id]);
    }
}
