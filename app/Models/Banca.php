<?php
namespace App\Models;

use PDO;

class Banca extends Model {
    protected $table = 'banche';

    /**
     * Recupera tutte le banche non archiviate.
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
     * Crea una nuova banca.
     *
     * @param array $data
     * @return bool
     */
    public function create($data) {
        $sql = "INSERT INTO " . $this->table . " (Nome) VALUES (:Nome)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'Nome' => $data['Nome']
        ]);
    }

    /**
     * Aggiorna i dati di una banca esistente.
     *
     * @param array $data
     * @return bool
     */
    public function update($data) {
        $sql = "UPDATE " . $this->table . " SET Nome = :Nome WHERE IDBanca = :IDBanca";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'Nome'    => $data['Nome'],
            'IDBanca' => $data['IDBanca']
        ]);
    }

    /**
     * Elimina definitivamente una banca.
     *
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $sql = "DELETE FROM " . $this->table . " WHERE IDBanca = :IDBanca";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['IDBanca' => $id]);
    }

    /**
     * Archivia (soft delete) una banca: imposta il campo Archiviato a 1.
     *
     * @param int $id
     * @return bool
     */
    public function archive($id) {
        $sql = "UPDATE " . $this->table . " SET Archiviato = 1 WHERE IDBanca = :IDBanca";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['IDBanca' => $id]);
    }
}
