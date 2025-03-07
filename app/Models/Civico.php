<?php
namespace App\Models;

use PDO;

class Civico extends Model {
    protected $table = 'civici';

    public function getByFabbricato($IDFabbricato) {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE IDFabbricato = :IDFabbricato ORDER BY Nome");
        $stmt->execute(['IDFabbricato' => $IDFabbricato]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function create($data) {
        $sql = "INSERT INTO " . $this->table . " (IDFabbricato, Nome) VALUES (:IDFabbricato, :Nome)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'IDFabbricato' => $data['IDFabbricato'],
            'Nome' => $data['Nome']
        ]);
    }
    
    public function update($data) {
        $sql = "UPDATE " . $this->table . " SET Nome = :Nome WHERE IDCivico = :IDCivico";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'Nome' => $data['Nome'],
            'IDCivico' => $data['IDCivico']
        ]);
    }
    
    public function delete($IDCivico) {
        $stmt = $this->db->prepare("DELETE FROM " . $this->table . " WHERE IDCivico = :IDCivico");
        return $stmt->execute(['IDCivico' => $IDCivico]);
    }

    public function getById($IDCivico) {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE IDCivico = :IDCivico");
        $stmt->execute(['IDCivico' => $IDCivico]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
}
