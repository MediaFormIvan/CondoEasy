<?php
namespace App\Models;

use PDO;

class Scala extends Model {
    protected $table = 'scale';

    public function getByCivico($IDCivico) {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE IDCivico = :IDCivico ORDER BY Nome");
        $stmt->execute(['IDCivico' => $IDCivico]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function create($data) {
        $sql = "INSERT INTO " . $this->table . " (IDCivico, Nome) VALUES (:IDCivico, :Nome)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'IDCivico' => $data['IDCivico'],
            'Nome' => $data['Nome']
        ]);
    }
    
    public function update($data) {
        $sql = "UPDATE " . $this->table . " SET Nome = :Nome WHERE IDScala = :IDScala";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'Nome' => $data['Nome'],
            'IDScala' => $data['IDScala']
        ]);
    }
    
    public function delete($IDScala) {
        $stmt = $this->db->prepare("DELETE FROM " . $this->table . " WHERE IDScala = :IDScala");
        return $stmt->execute(['IDScala' => $IDScala]);
    }
    public function getById($IDScala) {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE IDScala = :IDScala");
        $stmt->execute(['IDScala' => $IDScala]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
       
}
