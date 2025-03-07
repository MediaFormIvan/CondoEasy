<?php
namespace App\Models;

use PDO;

class Fornitore extends Model {
    protected $table = 'fornitori';

    public function getAll($nome = '', $idTipoFornitore = '') {
        $sql = "SELECT * FROM " . $this->table . " WHERE Archiviato = 0";
        $params = [];
        if ($nome !== '') {
            $sql .= " AND Nome LIKE :nome";
            $params['nome'] = "%$nome%";
        }
        if ($idTipoFornitore !== '') {
            $sql .= " AND IDTipoFornitore = :idTipoFornitore";
            $params['idTipoFornitore'] = $idTipoFornitore;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $sql = "INSERT INTO " . $this->table . " 
                (Nome, IDTipoFornitore, Indirizzo, Cap, Citta, PartitaIva, CodiceFiscale, IBAN, Telefono, Mail, PEC, Note, CodiceRitenuta, Ritenuta) 
                VALUES 
                (:Nome, :IDTipoFornitore, :Indirizzo, :Cap, :Citta, :PartitaIva, :CodiceFiscale, :IBAN, :Telefono, :Mail, :PEC, :Note, :CodiceRitenuta, :Ritenuta)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'Nome'            => $data['Nome'],
            'IDTipoFornitore' => $data['IDTipoFornitore'],
            'Indirizzo'       => $data['Indirizzo'],
            'Cap'             => $data['Cap'],
            'Citta'           => $data['Citta'],
            'PartitaIva'      => $data['PartitaIva'],
            'CodiceFiscale'   => $data['CodiceFiscale'],
            'IBAN'            => $data['IBAN'],
            'Telefono'        => $data['Telefono'],
            'Mail'            => $data['Mail'],
            'PEC'             => $data['PEC'],
            'Note'            => $data['Note'],
            'CodiceRitenuta'  => $data['CodiceRitenuta'],
            'Ritenuta'        => $data['Ritenuta']
        ]);
    }

    public function update($data) {
        $sql = "UPDATE " . $this->table . " SET 
                Nome = :Nome,
                IDTipoFornitore = :IDTipoFornitore,
                Indirizzo = :Indirizzo,
                Cap = :Cap,
                Citta = :Citta,
                PartitaIva = :PartitaIva,
                CodiceFiscale = :CodiceFiscale,
                IBAN = :IBAN,
                Telefono = :Telefono,
                Mail = :Mail,
                PEC = :PEC,
                Note = :Note,
                CodiceRitenuta = :CodiceRitenuta,
                Ritenuta = :Ritenuta
                WHERE IDFornitore = :IDFornitore";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'Nome'            => $data['Nome'],
            'IDTipoFornitore' => $data['IDTipoFornitore'],
            'Indirizzo'       => $data['Indirizzo'],
            'Cap'             => $data['Cap'],
            'Citta'           => $data['Citta'],
            'PartitaIva'      => $data['PartitaIva'],
            'CodiceFiscale'   => $data['CodiceFiscale'],
            'IBAN'            => $data['IBAN'],
            'Telefono'        => $data['Telefono'],
            'Mail'            => $data['Mail'],
            'PEC'             => $data['PEC'],
            'Note'            => $data['Note'],
            'CodiceRitenuta'  => $data['CodiceRitenuta'],
            'Ritenuta'        => $data['Ritenuta'],
            'IDFornitore'     => $data['IDFornitore']
        ]);
    }

    public function delete($id) {
        $sql = "DELETE FROM " . $this->table . " WHERE IDFornitore = :IDFornitore";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['IDFornitore' => $id]);
    }

    public function archive($id) {
        $sql = "UPDATE " . $this->table . " SET Archiviato = 1 WHERE IDFornitore = :IDFornitore";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['IDFornitore' => $id]);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE IDFornitore = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
