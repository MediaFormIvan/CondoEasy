<?php
namespace App\Models;

use PDO;

class Fabbricato extends Model {
    protected $table = 'fabbricati';

    public function getByCondominio($IDCondominio) {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE IDCondominio = :IDCondominio ORDER BY Nome");
        $stmt->execute(['IDCondominio' => $IDCondominio]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function create($data) {
        $sql = "INSERT INTO " . $this->table . " (IDCondominio, Nome) VALUES (:IDCondominio, :Nome)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'IDCondominio' => $data['IDCondominio'],
            'Nome' => $data['Nome']
        ]);
    }
    
    public function update($data) {
        $sql = "UPDATE " . $this->table . " SET Nome = :Nome WHERE IDFabbricato = :IDFabbricato";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'Nome' => $data['Nome'],
            'IDFabbricato' => $data['IDFabbricato']
        ]);
    }
    
    public function delete($IDFabbricato) {
        $stmt = $this->db->prepare("DELETE FROM " . $this->table . " WHERE IDFabbricato = :IDFabbricato");
        return $stmt->execute(['IDFabbricato' => $IDFabbricato]);
    }

    public function getById($IDFabbricato) {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE IDFabbricato = :IDFabbricato");
        $stmt->execute(['IDFabbricato' => $IDFabbricato]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
}
