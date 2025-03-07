<?php
namespace App\Models;

use PDO;

class Persona extends Model {
    protected $table = 'persone';

    /**
     * Recupera tutte le persone non archiviate, con eventuale filtro sul nome e cognome.
     *
     * @param string $nome
     * @param string $cognome
     * @return array
     */
    public function getAll($nome = '', $cognome = '') {
        $sql = "SELECT * FROM " . $this->table . " WHERE Archiviato = 0";
        $params = [];
        if ($nome !== '') {
            $sql .= " AND Nome LIKE :nome";
            $params['nome'] = "%$nome%";
        }
        if ($cognome !== '') {
            $sql .= " AND Cognome LIKE :cognome";
            $params['cognome'] = "%$cognome%";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Crea una nuova persona.
     *
     * @param array $data
     * @return bool
     */
    public function create($data) {
        $sql = "INSERT INTO " . $this->table . " 
                (Nome, Cognome, CodiceFiscale, Indirizzo, Cap, Citta, Provincia, Telefono, Telefono2, Mail, Pec, Note)
                VALUES 
                (:Nome, :Cognome, :CodiceFiscale, :Indirizzo, :Cap, :Citta, :Provincia, :Telefono, :Telefono2, :Mail, :Pec, :Note)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'Nome'          => $data['Nome'],
            'Cognome'       => $data['Cognome'],
            'CodiceFiscale' => $data['CodiceFiscale'],
            'Indirizzo'     => $data['Indirizzo'],
            'Cap'           => $data['Cap'],
            'Citta'         => $data['Citta'],
            'Provincia'     => $data['Provincia'],
            'Telefono'      => $data['Telefono'],
            'Telefono2'     => $data['Telefono2'],
            'Mail'          => $data['Mail'],
            'Pec'           => $data['Pec'],
            'Note'          => $data['Note']
        ]);
    }

    /**
     * Aggiorna i dati di una persona esistente.
     *
     * @param array $data
     * @return bool
     */
    public function update($data) {
        $sql = "UPDATE " . $this->table . " SET 
                Nome = :Nome,
                Cognome = :Cognome,
                CodiceFiscale = :CodiceFiscale,
                Indirizzo = :Indirizzo,
                Cap = :Cap,
                Citta = :Citta,
                Provincia = :Provincia,
                Telefono = :Telefono,
                Telefono2 = :Telefono2,
                Mail = :Mail,
                Pec = :Pec,
                Note = :Note
                WHERE IDPersona = :IDPersona";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'Nome'          => $data['Nome'],
            'Cognome'       => $data['Cognome'],
            'CodiceFiscale' => $data['CodiceFiscale'],
            'Indirizzo'     => $data['Indirizzo'],
            'Cap'           => $data['Cap'],
            'Citta'         => $data['Citta'],
            'Provincia'     => $data['Provincia'],
            'Telefono'      => $data['Telefono'],
            'Telefono2'     => $data['Telefono2'],
            'Mail'          => $data['Mail'],
            'Pec'           => $data['Pec'],
            'Note'          => $data['Note'],
            'IDPersona'     => $data['IDPersona']
        ]);
    }

    /**
     * Elimina definitivamente una persona.
     *
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $sql = "DELETE FROM " . $this->table . " WHERE IDPersona = :IDPersona";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['IDPersona' => $id]);
    }

    /**
     * Archivia (soft delete) una persona: imposta il campo Archiviato a 1.
     *
     * @param int $id
     * @return bool
     */
    public function archive($id) {
        $sql = "UPDATE " . $this->table . " SET Archiviato = 1 WHERE IDPersona = :IDPersona";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['IDPersona' => $id]);
    }
    public function getById($IDPersona) {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE IDPersona = :IDPersona");
        $stmt->execute(['IDPersona' => $IDPersona]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    
}
