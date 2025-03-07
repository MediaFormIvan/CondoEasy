<?php
namespace App\Models;

use PDO;

class Unita extends Model {
    protected $table = 'unita';

    public function getByScala($IDScala) {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE IDScala = :IDScala ORDER BY Interno");
        $stmt->execute(['IDScala' => $IDScala]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function create($data) {
        $sql = "INSERT INTO " . $this->table . " 
                (IDScala, Interno, Piano, Sezione, Foglio, Subalterno, Categoria) 
                VALUES (:IDScala, :Interno, :Piano, :Sezione, :Foglio, :Subalterno, :Categoria)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'IDScala' => $data['IDScala'],
            'Interno' => $data['Interno'] ?? null,
            'Piano' => $data['Piano'] ?? null,
            'Sezione' => $data['Sezione'] ?? null,
            'Foglio' => $data['Foglio'] ?? null,
            'Subalterno' => $data['Subalterno'] ?? null,
            'Categoria' => $data['Categoria'] ?? null
        ]);
    }
    
    public function update($data) {
        $sql = "UPDATE " . $this->table . " SET 
                Interno = :Interno, Piano = :Piano, Sezione = :Sezione, Foglio = :Foglio, 
                Subalterno = :Subalterno, Categoria = :Categoria 
                WHERE IDUnita = :IDUnita";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'Interno' => $data['Interno'] ?? null,
            'Piano' => $data['Piano'] ?? null,
            'Sezione' => $data['Sezione'] ?? null,
            'Foglio' => $data['Foglio'] ?? null,
            'Subalterno' => $data['Subalterno'] ?? null,
            'Categoria' => $data['Categoria'] ?? null,
            'IDUnita' => $data['IDUnita']
        ]);
    }
    
    public function delete($IDUnita) {
        $stmt = $this->db->prepare("DELETE FROM " . $this->table . " WHERE IDUnita = :IDUnita");
        return $stmt->execute(['IDUnita' => $IDUnita]);
    }

    public function getById($IDUnita) {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE IDUnita = :IDUnita");
        $stmt->execute(['IDUnita' => $IDUnita]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
}
