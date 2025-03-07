<?php
namespace App\Models;

use PDO;

class Contratto extends Model {
    protected $table = 'contratti';

    // Recupera tutti i contratti, con filtro per titolo
    public function getAll($titolo = '') {
        $sql = "SELECT * FROM " . $this->table . " WHERE 1";
        $params = [];
        if ($titolo !== '') {
            $sql .= " AND Titolo LIKE :titolo";
            $params['titolo'] = "%$titolo%";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Recupera un contratto tramite ID
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE IDCondominioContratto = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Inserisce un nuovo contratto
    public function create($data) {
        $sql = "INSERT INTO " . $this->table . " 
            (IDCondominio, IDFornitore, IDTipoContratto, Titolo, DataInizio, DataFine, Note)
            VALUES (:IDCondominio, :IDFornitore, :IDTipoContratto, :Titolo, :DataInizio, :DataFine, :Note)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'IDCondominio'   => $data['IDCondominio'],
            'IDFornitore'    => $data['IDFornitore'],
            'IDTipoContratto'=> $data['IDTipoContratto'],
            'Titolo'         => $data['Titolo'],
            'DataInizio'     => $data['DataInizio'],
            'DataFine'       => $data['DataFine'],
            'Note'           => $data['Note'] ?? ''
        ]);
    }

    // Aggiorna un contratto esistente
    public function update($data) {
        $sql = "UPDATE " . $this->table . " SET 
            IDCondominio = :IDCondominio,
            IDFornitore = :IDFornitore,
            IDTipoContratto = :IDTipoContratto,
            Titolo = :Titolo,
            DataInizio = :DataInizio,
            DataFine = :DataFine,
            Note = :Note
            WHERE IDCondominioContratto = :IDCondominioContratto";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'IDCondominio'             => $data['IDCondominio'],
            'IDFornitore'              => $data['IDFornitore'],
            'IDTipoContratto'          => $data['IDTipoContratto'],
            'Titolo'                   => $data['Titolo'],
            'DataInizio'               => $data['DataInizio'],
            'DataFine'                 => $data['DataFine'],
            'Note'                     => $data['Note'] ?? '',
            'IDCondominioContratto'    => $data['IDCondominioContratto']
        ]);
    }

    // Elimina definitivamente un contratto
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM " . $this->table . " WHERE IDCondominioContratto = :id");
        return $stmt->execute(['id' => $id]);
    }

    // Archivia (soft delete) un contratto
    public function archive($id) {
        $stmt = $this->db->prepare("UPDATE " . $this->table . " SET Archiviato = 1 WHERE IDCondominioContratto = :id");
        return $stmt->execute(['id' => $id]);
    }
}
