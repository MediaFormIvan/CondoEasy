<?php
namespace App\Models;

use PDO;

class ManutenzioniLetture extends Model {
    protected $table = 'manutenzioni_letture';

    /**
     * Aggiorna (o inserisce) il record di ultimo accesso per una manutenzione e un utente.
     *
     * @param int $idManutenzione
     * @param int $idUtente
     * @return bool
     */
    public function updateLastAccess($idManutenzione, $idUtente)
    {
        $now = date('Y-m-d H:i:s');
        // Verifica se esiste già un record
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE IDManutenzione = :idManutenzione AND IDUtente = :idUtente");
        $stmt->execute([
            'idManutenzione' => $idManutenzione,
            'idUtente'       => $idUtente
        ]);
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($record) {
            // Aggiorna il record esistente
            $stmtUpdate = $this->db->prepare("UPDATE " . $this->table . " SET UltimoAccesso = :ultimoAccesso WHERE IDManutenzione = :idManutenzione AND IDUtente = :idUtente");
            return $stmtUpdate->execute([
                'ultimoAccesso'  => $now,
                'idManutenzione' => $idManutenzione,
                'idUtente'       => $idUtente
            ]);
        } else {
            // Inserisce un nuovo record
            $stmtInsert = $this->db->prepare("INSERT INTO " . $this->table . " (IDManutenzione, IDUtente, UltimoAccesso) VALUES (:idManutenzione, :idUtente, :ultimoAccesso)");
            return $stmtInsert->execute([
                'idManutenzione' => $idManutenzione,
                'idUtente'       => $idUtente,
                'ultimoAccesso'  => $now
            ]);
        }
    }
}
