<?php
namespace App\Models;

use PDO;

class Condominio extends Model {
    protected $table = 'condomini';

    /**
     * Recupera tutti i condominii non archiviati applicando eventuali filtri.
     *
     * @param string $nome
     * @return array
     */
    public function getAll($nome = '') {
        $sql = "SELECT * FROM " . $this->table . " WHERE Archiviato = 0";
        $params = [];
        if ($nome !== '') {
            $sql .= " AND Nome LIKE :nome";
            $params['nome'] = "%$nome%";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Crea un nuovo condominio.
     *
     * @param array $data
     * @return bool
     */
    public function create($data) {
        $sql = "INSERT INTO " . $this->table . " 
                (Nome, Indirizzo, Cap, Citta, CodiceFiscale) 
                VALUES 
                (:Nome, :Indirizzo, :Cap, :Citta, :CodiceFiscale)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'Nome'          => $data['Nome'],
            'Indirizzo'     => $data['Indirizzo'],
            'Cap'           => $data['Cap'],
            'Citta'         => $data['Citta'],
            'CodiceFiscale' => $data['CodiceFiscale']
        ]);
    }

    /**
     * Aggiorna i dati di un condominio esistente.
     *
     * @param array $data
     * @return bool
     */
    public function update($data) {
        $sql = "UPDATE " . $this->table . " SET 
                Nome = :Nome,
                Indirizzo = :Indirizzo,
                Cap = :Cap,
                Citta = :Citta,
                CodiceFiscale = :CodiceFiscale
                WHERE IDCondominio = :IDCondominio";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'Nome'          => $data['Nome'],
            'Indirizzo'     => $data['Indirizzo'],
            'Cap'           => $data['Cap'],
            'Citta'         => $data['Citta'],
            'CodiceFiscale' => $data['CodiceFiscale'],
            'IDCondominio'  => $data['IDCondominio']
        ]);
    }

    /**
     * Elimina definitivamente un condominio.
     *
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $sql = "DELETE FROM " . $this->table . " WHERE IDCondominio = :IDCondominio";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['IDCondominio' => $id]);
    }

    /**
     * Archivia (soft delete) un condominio: imposta il campo Archiviato a 1.
     *
     * @param int $id
     * @return bool
     */
    public function archive($id) {
        $sql = "UPDATE " . $this->table . " SET Archiviato = 1 WHERE IDCondominio = :IDCondominio";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['IDCondominio' => $id]);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE IDCondominio = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
}
