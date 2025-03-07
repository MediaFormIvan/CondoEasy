<?php
namespace App\Models;

use PDO;

class Assicurazione extends Model {
    protected $table = 'assicurazioni';

    // Recupera tutte le assicurazioni, con filtri opzionali per condominio e fornitore
    public function getAll($filterCondominio = '', $filterFornitore = '') {
        $sql = "SELECT * FROM " . $this->table . " WHERE 1";
        $params = [];
        if ($filterCondominio !== '') {
            $sql .= " AND IDCondominio = :IDCondominio";
            $params['IDCondominio'] = $filterCondominio;
        }
        if ($filterFornitore !== '') {
            $sql .= " AND IDFornitore = :IDFornitore";
            $params['IDFornitore'] = $filterFornitore;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Recupera un'assicurazione in base al suo ID
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE IDAssicurazione = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Inserisce una nuova assicurazione
    public function create($data) {
        $sql = "INSERT INTO " . $this->table . " 
            (IDCondominio, IDFornitore, DataScadenza, Durata, Polizza)
            VALUES (:IDCondominio, :IDFornitore, :DataScadenza, :Durata, :Polizza)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'IDCondominio' => $data['IDCondominio'],
            'IDFornitore'  => $data['IDFornitore'],
            'DataScadenza' => $data['DataScadenza'],
            'Durata'       => $data['Durata'],
            'Polizza'      => $data['Polizza']
        ]);
    }

    // Aggiorna un'assicurazione esistente
    public function update($data) {
        $sql = "UPDATE " . $this->table . " SET 
            IDCondominio = :IDCondominio,
            IDFornitore = :IDFornitore,
            DataScadenza = :DataScadenza,
            Durata = :Durata,
            Polizza = :Polizza
            WHERE IDAssicurazione = :IDAssicurazione";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'IDCondominio'    => $data['IDCondominio'],
            'IDFornitore'     => $data['IDFornitore'],
            'DataScadenza'    => $data['DataScadenza'],
            'Durata'          => $data['Durata'],
            'Polizza'         => $data['Polizza'],
            'IDAssicurazione' => $data['IDAssicurazione']
        ]);
    }

    // Elimina definitivamente un'assicurazione
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM " . $this->table . " WHERE IDAssicurazione = :id");
        return $stmt->execute(['id' => $id]);
    }

    // Archivia (soft delete) un'assicurazione
    public function archive($id) {
        $stmt = $this->db->prepare("UPDATE " . $this->table . " SET Archiviato = 1 WHERE IDAssicurazione = :id");
        return $stmt->execute(['id' => $id]);
    }
}
