<?php
namespace App\Models;

use PDO;

class Manutenzione extends Model {
    protected $table = 'manutenzioni';

    /**
     * Recupera tutte le manutenzioni, con eventuale filtro sul titolo.
     *
     * @param string $titolo
     * @return array
     */
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

    /**
     * Recupera una manutenzione in base al suo ID.
     *
     * @param int $id
     * @return array|false
     */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE IDManutenzione = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Inserisce una nuova manutenzione.
     *
     * @param array $data
     * @return bool
     */
    public function create($data) {
        $sql = "INSERT INTO " . $this->table . " 
            (IDCondominio, DataApertura, IDFornitore, Titolo, Descrizione, IDStato, IDUser)
            VALUES (:IDCondominio, :DataApertura, :IDFornitore, :Titolo, :Descrizione, :IDStato, :IDUser)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'IDCondominio' => $data['IDCondominio'],
            'DataApertura' => $data['DataApertura'],
            'IDFornitore'  => $data['IDFornitore'],
            'Titolo'       => $data['Titolo'],
            'Descrizione'  => $data['Descrizione'],
            'IDStato'      => $data['IDStato'],
            'IDUser'       => $data['IDUser']
        ]);
    }

    /**
     * Aggiorna una manutenzione esistente.
     *
     * @param array $data
     * @return bool
     */
    public function update($data) {
        $sql = "UPDATE " . $this->table . " SET 
            IDCondominio = :IDCondominio,
            DataApertura = :DataApertura,
            IDFornitore = :IDFornitore,
            Titolo = :Titolo,
            Descrizione = :Descrizione,
            IDStato = :IDStato,
            IDUser = :IDUser
            WHERE IDManutenzione = :IDManutenzione";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'IDCondominio'   => $data['IDCondominio'],
            'DataApertura'   => $data['DataApertura'],
            'IDFornitore'    => $data['IDFornitore'],
            'Titolo'         => $data['Titolo'],
            'Descrizione'    => $data['Descrizione'],
            'IDStato'        => $data['IDStato'],
            'IDUser'         => $data['IDUser'],
            'IDManutenzione' => $data['IDManutenzione']
        ]);
    }

    /**
     * Elimina definitivamente una manutenzione.
     *
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $sql = "DELETE FROM " . $this->table . " WHERE IDManutenzione = :IDManutenzione";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['IDManutenzione' => $id]);
    }

    /**
     * Archivia (soft delete) una manutenzione.
     *
     * @param int $id
     * @return bool
     */
    public function archive($id) {
        $sql = "UPDATE " . $this->table . " SET Archiviato = 1 WHERE IDManutenzione = :IDManutenzione";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['IDManutenzione' => $id]);
    }

    /**
     * Cambia lo stato di una manutenzione.
     *
     * @param int $id
     * @param int $newState
     * @return bool
     */
    public function changeState($id, $newState) {
        $sql = "UPDATE " . $this->table . " SET IDStato = :IDStato WHERE IDManutenzione = :IDManutenzione";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'IDStato' => $newState,
            'IDManutenzione' => $id
        ]);
    }
}
