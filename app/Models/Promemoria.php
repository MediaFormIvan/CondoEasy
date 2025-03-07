<?php
namespace App\Models;

use PDO;

class Promemoria extends Model {
    protected $table = 'promemoria';

    public function getAll($filters = []) {
        $sql = "SELECT * FROM " . $this->table . " WHERE 1";
        $params = [];

        // Filtro testuale sul titolo
        if(isset($filters['titolo']) && $filters['titolo'] !== ''){
            $sql .= " AND Titolo LIKE :titolo";
            $params['titolo'] = "%" . $filters['titolo'] . "%";
        }
        // Filtro per Condominio
        if(isset($filters['condominio']) && $filters['condominio'] !== ''){
            $sql .= " AND IDCondominio = :condominio";
            $params['condominio'] = $filters['condominio'];
        }
        // Filtro per Utente
        if(isset($filters['utente']) && $filters['utente'] !== ''){
            $sql .= " AND IDUtente = :utente";
            $params['utente'] = $filters['utente'];
        }
        // Filtro per stato: "aperti" (esclude chiusi: 4,5), "chiusi" (in 4,5) o "tutti" (nessuna condizione)
        if(isset($filters['filter'])) {
            if($filters['filter'] === 'aperti'){
                $sql .= " AND IDStato NOT IN (4,5)";
            } elseif($filters['filter'] === 'chiusi'){
                $sql .= " AND IDStato IN (4,5)";
            }
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE IDPromemoria = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $sql = "INSERT INTO " . $this->table . " (IDCondominio, IDStato, DataScadenza, IDUtente, Titolo)
                VALUES (:IDCondominio, :IDStato, :DataScadenza, :IDUtente, :Titolo)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'IDCondominio' => $data['IDCondominio'],
            'IDStato' => $data['IDStato'],
            'DataScadenza' => $data['DataScadenza'],
            'IDUtente' => $data['IDUtente'],
            'Titolo' => $data['Titolo']
        ]);
    }

    public function update($data) {
        $sql = "UPDATE " . $this->table . " SET 
                    IDCondominio = :IDCondominio,
                    IDStato = :IDStato,
                    DataScadenza = :DataScadenza,
                    IDUtente = :IDUtente,
                    Titolo = :Titolo
                WHERE IDPromemoria = :IDPromemoria";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'IDCondominio' => $data['IDCondominio'],
            'IDStato' => $data['IDStato'],
            'DataScadenza' => $data['DataScadenza'],
            'IDUtente' => $data['IDUtente'],
            'Titolo' => $data['Titolo'],
            'IDPromemoria' => $data['IDPromemoria']
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM " . $this->table . " WHERE IDPromemoria = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function changeState($id, $newState) {
        $stmt = $this->db->prepare("UPDATE " . $this->table . " SET IDStato = :IDStato WHERE IDPromemoria = :IDPromemoria");
        return $stmt->execute([
            'IDStato' => $newState,
            'IDPromemoria' => $id
        ]);
    }
}
