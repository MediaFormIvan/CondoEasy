<?php
namespace App\Models;

use PDO;

class Sinistro extends Model {
    protected $table = 'sinistri';

    // Recupera tutti i sinistri, con eventuale filtro per titolo
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

    // Recupera un sinistro in base al suo ID
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE IDSinistro = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAperti() {
        $sql = "SELECT * FROM " . $this->table . " WHERE IDStato IN (1, 2, 3) AND Archiviato = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    // Inserisce un nuovo sinistro
    public function create($data) {
        // Converte i campi opzionali in null se vuoti
        $data['IDStudioPeritale'] = empty($data['IDStudioPeritale']) ? null : $data['IDStudioPeritale'];
        $data['DataChiusura']   = empty($data['DataChiusura']) ? null : $data['DataChiusura'];
        $data['Rimborso']       = empty($data['Rimborso']) ? null : $data['Rimborso'];
    
        $sql = "INSERT INTO " . $this->table . " 
            (IDCondominio, IDStato, DataApertura, Titolo, Descrizione, Numero, IDStudioPeritale, DataChiusura, Rimborso)
            VALUES (:IDCondominio, :IDStato, :DataApertura, :Titolo, :Descrizione, :Numero, :IDStudioPeritale, :DataChiusura, :Rimborso)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'IDCondominio'     => $data['IDCondominio'],
            'IDStato'          => $data['IDStato'],
            'DataApertura'     => $data['DataApertura'],
            'Titolo'           => $data['Titolo'],
            'Descrizione'      => $data['Descrizione'],
            'Numero'           => $data['Numero'],
            'IDStudioPeritale' => $data['IDStudioPeritale'],
            'DataChiusura'     => $data['DataChiusura'],
            'Rimborso'         => $data['Rimborso']
        ]);
    }
    

    // Aggiorna un sinistro esistente
    public function update($data) {
        // Converte i campi opzionali in null se vuoti
        $data['IDStudioPeritale'] = empty($data['IDStudioPeritale']) ? null : $data['IDStudioPeritale'];
        $data['DataChiusura']   = empty($data['DataChiusura']) ? null : $data['DataChiusura'];
        $data['Rimborso']       = empty($data['Rimborso']) ? null : $data['Rimborso'];
    
        $sql = "UPDATE " . $this->table . " SET 
            IDCondominio = :IDCondominio,
            IDStato = :IDStato,
            DataApertura = :DataApertura,
            Titolo = :Titolo,
            Descrizione = :Descrizione,
            Numero = :Numero,
            IDStudioPeritale = :IDStudioPeritale,
            DataChiusura = :DataChiusura,
            Rimborso = :Rimborso
            WHERE IDSinistro = :IDSinistro";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'IDCondominio'     => $data['IDCondominio'],
            'IDStato'          => $data['IDStato'],
            'DataApertura'     => $data['DataApertura'],
            'Titolo'           => $data['Titolo'],
            'Descrizione'      => $data['Descrizione'],
            'Numero'           => $data['Numero'],
            'IDStudioPeritale' => $data['IDStudioPeritale'],
            'DataChiusura'     => $data['DataChiusura'],
            'Rimborso'         => $data['Rimborso'],
            'IDSinistro'       => $data['IDSinistro']
        ]);
    }
    

    // Elimina definitivamente un sinistro
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM " . $this->table . " WHERE IDSinistro = :IDSinistro");
        return $stmt->execute(['IDSinistro' => $id]);
    }

    // Archivia (soft delete) un sinistro
    public function archive($id) {
        $stmt = $this->db->prepare("UPDATE " . $this->table . " SET Archiviato = 1 WHERE IDSinistro = :IDSinistro");
        return $stmt->execute(['IDSinistro' => $id]);
    }

    // Cambia lo stato di un sinistro
    public function changeState($id, $newState) {
        $stmt = $this->db->prepare("UPDATE " . $this->table . " SET IDStato = :IDStato WHERE IDSinistro = :IDSinistro");
        return $stmt->execute([
            'IDStato' => $newState,
            'IDSinistro' => $id
        ]);
    }
}
