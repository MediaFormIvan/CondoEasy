<?php
namespace App\Models;

use PDO;

class Legale extends Model {
    protected $table = 'legale';

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

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE IDLegale = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $sql = "INSERT INTO " . $this->table . " (IDCondominio, IDFornitore, IDStato, DataApertura, Titolo, Descrizione)
                VALUES (:IDCondominio, :IDFornitore, :IDStato, :DataApertura, :Titolo, :Descrizione)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'IDCondominio' => $data['IDCondominio'],
            'IDFornitore'  => $data['IDFornitore'],
            'IDStato'      => $data['IDStato'],
            'DataApertura' => $data['DataApertura'],
            'Titolo'       => $data['Titolo'],
            'Descrizione'  => $data['Descrizione']
        ]);
    }

    public function update($data) {
        $sql = "UPDATE " . $this->table . " SET 
                    IDCondominio = :IDCondominio,
                    IDFornitore  = :IDFornitore,
                    IDStato      = :IDStato,
                    DataApertura = :DataApertura,
                    Titolo       = :Titolo,
                    Descrizione  = :Descrizione
                WHERE IDLegale = :IDLegale";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'IDCondominio' => $data['IDCondominio'],
            'IDFornitore'  => $data['IDFornitore'],
            'IDStato'      => $data['IDStato'],
            'DataApertura' => $data['DataApertura'],
            'Titolo'       => $data['Titolo'],
            'Descrizione'  => $data['Descrizione'],
            'IDLegale'     => $data['IDLegale']
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM " . $this->table . " WHERE IDLegale = :IDLegale");
        return $stmt->execute(['IDLegale' => $id]);
    }

    public function changeState($id, $newState) {
        $stmt = $this->db->prepare("UPDATE " . $this->table . " SET IDStato = :IDStato WHERE IDLegale = :IDLegale");
        return $stmt->execute([
             'IDStato' => $newState,
             'IDLegale' => $id
        ]);
    }
    
}
