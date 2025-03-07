<?php
namespace App\Models;

use PDO;

class ManutenzioneChat extends Model {
    protected $table = 'manutenzioni_chat';

    // Recupera i messaggi della chat per una manutenzione, ordinati cronologicamente
    public function getByManutenzione($idManutenzione) {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE IDManutenzione = :IDManutenzione ORDER BY Data, Orario ASC");
        $stmt->execute(['IDManutenzione' => $idManutenzione]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Inserisce un nuovo messaggio in chat
    public function create($data) {
        $sql = "INSERT INTO " . $this->table . " (IDManutenzione, Testo, Data, Orario, IDUser) VALUES (:IDManutenzione, :Testo, :Data, :Orario, :IDUser)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'IDManutenzione' => $data['IDManutenzione'],
            'Testo'          => $data['Testo'],
            'Data'           => $data['Data'],   // formato YYYY-MM-DD
            'Orario'         => $data['Orario'], // formato HH:MM:SS
            'IDUser'         => $data['IDUser']
        ]);
    }
}
