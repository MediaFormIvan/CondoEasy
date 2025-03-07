<?php
namespace App\Models;

use PDO;

class TipoFornitore extends Model {
    protected $table = 'tipi_fornitori';

    public function getAll() {
        $sql = "SELECT * FROM " . $this->table . " WHERE Archiviato = 0 ORDER BY Nome ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Metodo per creare un nuovo record
    public function create($data) {
        $sql = "INSERT INTO " . $this->table . " (Nome) VALUES (:Nome)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'Nome' => $data['Nome']
        ]);
    }
    
    // Metodo per aggiornare un record esistente
    public function update($data) {
        $sql = "UPDATE " . $this->table . " SET Nome = :Nome WHERE IDTipoFornitore = :IDTipoFornitore";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'Nome' => $data['Nome'],
            'IDTipoFornitore' => $data['IDTipoFornitore']
        ]);
    }
    
    // Metodo per eliminare definitivamente un record
    public function delete($id) {
        $sql = "DELETE FROM " . $this->table . " WHERE IDTipoFornitore = :IDTipoFornitore";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['IDTipoFornitore' => $id]);
    }
    
    // Metodo per archiviare (soft delete) un record
    public function archive($id) {
        $sql = "UPDATE " . $this->table . " SET Archiviato = 1 WHERE IDTipoFornitore = :IDTipoFornitore";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['IDTipoFornitore' => $id]);
    }
}
