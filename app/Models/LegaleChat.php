<?php
namespace App\Models;

use PDO;

class LegaleChat extends Model {
    protected $table = 'legale_chat';

    public function getByLegale($idLegale) {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE IDLegale = :IDLegale ORDER BY Data, Orario ASC");
        $stmt->execute(['IDLegale' => $idLegale]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $sql = "INSERT INTO " . $this->table . " (IDLegale, Testo, Data, Orario, IDUser)
                VALUES (:IDLegale, :Testo, :Data, :Orario, :IDUser)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'IDLegale' => $data['IDLegale'],
            'Testo'    => $data['Testo'],
            'Data'     => $data['Data'],
            'Orario'   => $data['Orario'],
            'IDUser'   => $data['IDUser']
        ]);
    }
}
