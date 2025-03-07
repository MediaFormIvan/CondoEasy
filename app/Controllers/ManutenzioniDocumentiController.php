<?php
namespace App\Controllers;

use App\Models\ManutenzioneDocumento;

class ManutenzioniDocumentiController {

    private $documentoModel;

    public function __construct() {
        $this->documentoModel = new ManutenzioneDocumento();
    }

    /**
     * Salva un nuovo documento per una manutenzione.
     */
    public function save() {
        if (!isset($_FILES['File']) || $_FILES['File']['error'] !== UPLOAD_ERR_OK) {
            echo "Errore nell'upload del file.";
            exit;
        }
        
        $uploadDir = BASE_PATH . 'storage/uploads/manutenzioni/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $filename = time() . '_' . basename($_FILES['File']['name']);
        $targetFile = $uploadDir . $filename;
        
        if (!move_uploaded_file($_FILES['File']['tmp_name'], $targetFile)) {
            echo "Errore nel salvataggio del file.";
            exit;
        }
        
        $data = [
            'IDManutenzione' => $_POST['IDManutenzione'] ?? null,
            'Titolo'         => $_POST['Titolo'] ?? '',
            'File'           => 'manutenzioni/' . $filename
        ];
        
        if (!$data['IDManutenzione'] || empty($data['Titolo'])) {
            echo "Dati mancanti per il documento.";
            exit;
        }
        
        $result = $this->documentoModel->create($data);
        if ($result) {
            header("Location: " . BASE_URL . "/attivita/manutenzioni/detail?id=" . $data['IDManutenzione']);
            exit;
        } else {
            echo "Errore durante il salvataggio del documento.";
            exit;
        }
    }

    /**
     * Aggiorna il titolo di un documento.
     */
    public function update() {
        $data = [
            'IDManutenzioneDocumento' => $_POST['IDManutenzioneDocumento'] ?? null,
            'Titolo' => $_POST['Titolo'] ?? ''
        ];
        // Recupera anche l'ID della manutenzione per il redirect
        $idManutenzione = $_POST['IDManutenzione'] ?? null;
        
        if (!$data['IDManutenzioneDocumento'] || empty($data['Titolo'])) {
            echo "Dati mancanti per l'aggiornamento del documento.";
            exit;
        }
        
        $result = $this->documentoModel->update($data);
        if ($result) {
            if ($idManutenzione) {
                header("Location: " . BASE_URL . "/attivita/manutenzioni/detail?id=" . $idManutenzione);
            } else {
                header("Location: " . BASE_URL . "/attivita/manutenzioni");
            }
            exit;
        } else {
            echo "Errore durante l'aggiornamento del documento.";
            exit;
        }
    }

    /**
     * Elimina un documento.
     */
    public function delete() {
        $idDocumento = $_GET['id'] ?? null;
        $idManutenzione = $_GET['idManutenzione'] ?? null;
        if ($idDocumento) {
            $this->documentoModel->delete($idDocumento);
        }
        if ($idManutenzione) {
            header("Location: " . BASE_URL . "/attivita/manutenzioni/detail?id=" . $idManutenzione);
        } else {
            header("Location: " . BASE_URL . "/attivita/manutenzioni");
        }
        exit;
    }
}
