<?php
namespace App\Models;

use PDO;

class Gestione extends Model {
    protected $table = 'gestioni';

    // Recupera le gestioni aperte per un dato condominio (Archiviato = 0)
    public function getAllByCondominio($IDCondominio) {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE IDCondominio = :IDCondominio AND Archiviato = 0 ORDER BY DataInizio DESC");
        $stmt->execute(['IDCondominio' => $IDCondominio]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Recupera le gestioni chiuse per un dato condominio (Archiviato = 1)
    public function getArchivedByCondominio($IDCondominio) {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE IDCondominio = :IDCondominio AND Archiviato = 1 ORDER BY DataInizio DESC");
        $stmt->execute(['IDCondominio' => $IDCondominio]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Recupera una gestione per ID
    public function getById($IDGestione) {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE IDGestione = :IDGestione");
        $stmt->execute(['IDGestione' => $IDGestione]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crea una nuova gestione
    public function create($data) {
        $sql = "INSERT INTO " . $this->table . " (IDCondominio, IDTipoGestione, Nome, DataInizio, DataFine)
                VALUES (:IDCondominio, :IDTipoGestione, :Nome, :DataInizio, :DataFine)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'IDCondominio'   => $data['IDCondominio'],
            'IDTipoGestione' => $data['IDTipoGestione'],
            'Nome'           => $data['Nome'],
            'DataInizio'     => $data['DataInizio'],
            'DataFine'       => $data['DataFine'] ?? null
        ]);
    }

    // Aggiorna una gestione esistente
    public function update($data) {
        $sql = "UPDATE " . $this->table . " SET 
                IDTipoGestione = :IDTipoGestione,
                Nome = :Nome,
                DataInizio = :DataInizio,
                DataFine = :DataFine
                WHERE IDGestione = :IDGestione";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'IDTipoGestione' => $data['IDTipoGestione'],
            'Nome'           => $data['Nome'],
            'DataInizio'     => $data['DataInizio'],
            'DataFine'       => $data['DataFine'] ?? null,
            'IDGestione'     => $data['IDGestione']
        ]);
    }

    // Archivia (chiude) una gestione
    public function archive($IDGestione) {
        $stmt = $this->db->prepare("UPDATE " . $this->table . " SET Archiviato = 1 WHERE IDGestione = :IDGestione");
        return $stmt->execute(['IDGestione' => $IDGestione]);
    }

    // Cancella definitivamente una gestione
    public function delete($IDGestione) {
        $stmt = $this->db->prepare("DELETE FROM " . $this->table . " WHERE IDGestione = :IDGestione");
        return $stmt->execute(['IDGestione' => $IDGestione]);
    }
}
