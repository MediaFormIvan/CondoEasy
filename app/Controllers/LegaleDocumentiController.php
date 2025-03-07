<?php
namespace App\Controllers;

use App\Models\LegaleDocumento;

class LegaleDocumentiController {
    private $documentoModel;
    public function __construct() {
        $this->documentoModel = new LegaleDocumento();
    }
    public function save() {
        if (!isset($_FILES['File']) || $_FILES['File']['error'] !== UPLOAD_ERR_OK) {
            echo "Errore nell'upload del file.";
            exit;
        }
        $uploadDir = BASE_PATH . 'storage/uploads/legale/';
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
            'IDLegale' => $_POST['IDLegale'] ?? null,
            'Titolo'   => $_POST['Titolo'] ?? '',
            'File'     => 'legale/' . $filename
        ];
        if (!$data['IDLegale'] || empty($data['Titolo'])) {
            echo "Dati mancanti per il documento.";
            exit;
        }
        $result = $this->documentoModel->create($data);
        if ($result) {
            header("Location: " . BASE_URL . "/attivita/legale/detail?id=" . $data['IDLegale']);
            exit;
        } else {
            echo "Errore durante il salvataggio del documento.";
            exit;
        }
    }
    public function update() {
        $data = [
            'IDLegaleDocumento' => $_POST['IDLegaleDocumento'] ?? null,
            'Titolo'            => $_POST['Titolo'] ?? ''
        ];
        $idLegale = $_POST['IDLegale'] ?? null;
        if (!$data['IDLegaleDocumento'] || empty($data['Titolo'])) {
            echo "Dati mancanti per l'aggiornamento del documento.";
            exit;
        }
        $result = $this->documentoModel->update($data);
        if ($result) {
            if ($idLegale) {
                header("Location: " . BASE_URL . "/attivita/legale/detail?id=" . $idLegale);
            } else {
                header("Location: " . BASE_URL . "/attivita/legale");
            }
            exit;
        } else {
            echo "Errore durante l'aggiornamento del documento.";
            exit;
        }
    }
    public function delete() {
        $idDocumento = $_GET['id'] ?? null;
        $idLegale = $_GET['idLegale'] ?? null;
        if ($idDocumento) {
            $this->documentoModel->delete($idDocumento);
        }
        if ($idLegale) {
            header("Location: " . BASE_URL . "/attivita/legale/detail?id=" . $idLegale);
        } else {
            header("Location: " . BASE_URL . "/attivita/legale");
        }
        exit;
    }
}
