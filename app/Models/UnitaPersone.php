<?php
namespace App\Models;

use PDO;

class UnitaPersone extends Model {
    protected $table = 'unita_persone';

    public function getByUnita($IDUnita) {
        $stmt = $this->db->prepare("SELECT up.*, p.Nome AS PersonaNome, p.Cognome, tp.Nome AS TipoPersona
                                    FROM " . $this->table . " up
                                    JOIN persone p ON up.IDPersona = p.IDPersona
                                    JOIN tipi_persone tp ON up.IDTipoPersona = tp.IDTipoPersona
                                    WHERE up.IDUnita = :IDUnita");
        $stmt->execute(['IDUnita' => $IDUnita]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function create($data) {
        $sql = "INSERT INTO " . $this->table . " 
                (IDUnita, IDPersona, IDTipoPersona, Percentuale, DataInizio, DataFine)
                VALUES (:IDUnita, :IDPersona, :IDTipoPersona, :Percentuale, :DataInizio, :DataFine)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'IDUnita' => $data['IDUnita'],
            'IDPersona' => $data['IDPersona'],
            'IDTipoPersona' => $data['IDTipoPersona'],
            'Percentuale' => $data['Percentuale'] ?? null,
            'DataInizio' => $data['DataInizio'],
            'DataFine' => $data['DataFine'] ?? null
        ]);
    }
    
    public function update($data) {
        $sql = "UPDATE " . $this->table . " SET 
                Percentuale = :Percentuale, DataInizio = :DataInizio, DataFine = :DataFine
                WHERE IDUnita = :IDUnita AND IDPersona = :IDPersona AND IDTipoPersona = :IDTipoPersona";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'Percentuale' => $data['Percentuale'] ?? null,
            'DataInizio' => $data['DataInizio'],
            'DataFine' => $data['DataFine'] ?? null,
            'IDUnita' => $data['IDUnita'],
            'IDPersona' => $data['IDPersona'],
            'IDTipoPersona' => $data['IDTipoPersona']
        ]);
    }
    
    public function delete($IDUnita, $IDPersona, $IDTipoPersona) {
        $stmt = $this->db->prepare("DELETE FROM " . $this->table . " WHERE IDUnita = :IDUnita AND IDPersona = :IDPersona AND IDTipoPersona = :IDTipoPersona");
        return $stmt->execute([
            'IDUnita' => $IDUnita,
            'IDPersona' => $IDPersona,
            'IDTipoPersona' => $IDTipoPersona
        ]);
    }
}
