<?php
namespace App\Models;

use PDO;

class SinistroChat extends Model {
    protected $table = 'sinistri_chat';

    // Recupera i messaggi della chat per un sinistro, ordinati cronologicamente
    public function getBySinistro($idSinistro) {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE IDSinistro = :IDSinistro ORDER BY Data, Orario ASC");
        $stmt->execute(['IDSinistro' => $idSinistro]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Inserisce un nuovo messaggio in chat
    public function create($data) {
        $sql = "INSERT INTO " . $this->table . " (IDSinistro, Testo, Data, Orario, IDUser) VALUES (:IDSinistro, :Testo, :Data, :Orario, :IDUser)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'IDSinistro' => $data['IDSinistro'],
            'Testo'      => $data['Testo'],
            'Data'       => $data['Data'],
            'Orario'     => $data['Orario'],
            'IDUser'     => $data['IDUser']
        ]);
    }
}
