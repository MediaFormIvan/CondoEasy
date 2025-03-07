<?php
namespace App\Models;

use PDO;

class LegaleDocumento extends Model {
    protected $table = 'legale_documenti';

    public function getByLegale($idLegale) {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE IDLegale = :IDLegale");
        $stmt->execute(['IDLegale' => $idLegale]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $sql = "INSERT INTO " . $this->table . " (IDLegale, Titolo, File)
                VALUES (:IDLegale, :Titolo, :File)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'IDLegale' => $data['IDLegale'],
            'Titolo'   => $data['Titolo'],
            'File'     => $data['File']
        ]);
    }

    public function update($data) {
        $sql = "UPDATE " . $this->table . " SET Titolo = :Titolo WHERE IDLegaleDocumento = :IDLegaleDocumento";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'Titolo' => $data['Titolo'],
            'IDLegaleDocumento' => $data['IDLegaleDocumento']
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM " . $this->table . " WHERE IDLegaleDocumento = :IDLegaleDocumento");
        return $stmt->execute(['IDLegaleDocumento' => $id]);
    }
}
