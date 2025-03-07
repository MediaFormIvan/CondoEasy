<?php

namespace App\Models;

use PDO;

class Scadenza extends Model
{
    protected $table = 'scadenze';

    /**
     * Recupera tutte le scadenze applicando i filtri per condominio e tipo scadenza.
     * Il filtro sul titolo non è applicabile in quanto non esiste una colonna "Titolo"; 
     * qui si filtra per condominio e per tipologia.
     */
    public function getAll($filtroCondominio = '', $filtroTipo = '')
    {
        $sql = "SELECT * FROM " . $this->table . " WHERE 1";
        $params = [];
        if ($filtroCondominio !== '') {
            $sql .= " AND IDCondominio = :filtroCondominio";
            $params['filtroCondominio'] = $filtroCondominio;
        }
        if ($filtroTipo !== '') {
            $sql .= " AND IDTipoScadenza = :filtroTipo";
            $params['filtroTipo'] = $filtroTipo;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Recupera una scadenza tramite ID
    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE IDScadenza = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Inserisce una nuova scadenza
    // Inserisce una nuova scadenza
    public function create($data)
    {
        $sql = "INSERT INTO " . $this->table . " (IDCondominio, IDTipoScadenza, DataScadenza, Durata, Note)
            VALUES (:IDCondominio, :IDTipoScadenza, :DataScadenza, :Durata, :Note)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'IDCondominio'   => $data['IDCondominio'],
            'IDTipoScadenza' => $data['IDTipoScadenza'],
            'DataScadenza'   => $data['DataScadenza'],
            'Durata'         => $data['Durata'] ?? null,
            'Note'           => $data['Note'] ?? ''
        ]);
    }

    // Aggiorna una scadenza esistente
    public function update($data)
    {
        $sql = "UPDATE " . $this->table . " SET 
                IDCondominio = :IDCondominio,
                IDTipoScadenza = :IDTipoScadenza,
                DataScadenza = :DataScadenza,
                Durata = :Durata,
                Note = :Note
            WHERE IDScadenza = :IDScadenza";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'IDCondominio'   => $data['IDCondominio'],
            'IDTipoScadenza' => $data['IDTipoScadenza'],
            'DataScadenza'   => $data['DataScadenza'],
            'Durata'         => $data['Durata'] ?? null,
            'Note'           => $data['Note'] ?? '',
            'IDScadenza'     => $data['IDScadenza']
        ]);
    }


    // Elimina definitivamente una scadenza
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM " . $this->table . " WHERE IDScadenza = :id");
        return $stmt->execute(['id' => $id]);
    }

    // Archivia (soft delete) una scadenza
    public function archive($id)
    {
        $stmt = $this->db->prepare("UPDATE " . $this->table . " SET Archiviato = 1 WHERE IDScadenza = :id");
        return $stmt->execute(['id' => $id]);
    }
}
