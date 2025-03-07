<?php
namespace App\Controllers;

use App\Models\SinistroDocumento;

class SinistriDocumentiController {

    private $documentoModel;

    public function __construct() {
        $this->documentoModel = new SinistroDocumento();
    }

    public function save() {
        if (!isset($_FILES['File']) || $_FILES['File']['error'] !== UPLOAD_ERR_OK) {
            echo "Errore nell'upload del file.";
            exit;
        }
        
        $uploadDir = BASE_PATH . 'storage/uploads/sinistri/';
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
            'IDSinistro' => $_POST['IDSinistro'] ?? null,
            'Titolo'     => $_POST['Titolo'] ?? '',
            'File'       => 'sinistri/' . $filename
        ];
        
        if (!$data['IDSinistro'] || empty($data['Titolo'])) {
            echo "Dati mancanti per il documento.";
            exit;
        }
        
        $result = $this->documentoModel->create($data);
        if ($result) {
            header("Location: " . BASE_URL . "/attivita/sinistri/detail?id=" . $data['IDSinistro']);
            exit;
        } else {
            echo "Errore durante il salvataggio del documento.";
            exit;
        }
    }

    public function update() {
        $data = [
            'IDSinistroDocumento' => $_POST['IDSinistroDocumento'] ?? null,
            'Titolo'              => $_POST['Titolo'] ?? ''
        ];
        $idSinistro = $_POST['IDSinistro'] ?? null;
        
        if (!$data['IDSinistroDocumento'] || empty($data['Titolo'])) {
            echo "Dati mancanti per l'aggiornamento del documento.";
            exit;
        }
        
        $result = $this->documentoModel->update($data);
        if ($result) {
            if ($idSinistro) {
                header("Location: " . BASE_URL . "/attivita/sinistri/detail?id=" . $idSinistro);
            } else {
                header("Location: " . BASE_URL . "/attivita/sinistri");
            }
            exit;
        } else {
            echo "Errore durante l'aggiornamento del documento.";
            exit;
        }
    }

    public function delete() {
        $idDocumento = $_GET['id'] ?? null;
        $idSinistro  = $_GET['idSinistro'] ?? null;
        if ($idDocumento) {
            $this->documentoModel->delete($idDocumento);
        }
        if ($idSinistro) {
            header("Location: " . BASE_URL . "/attivita/sinistri/detail?id=" . $idSinistro);
        } else {
            header("Location: " . BASE_URL . "/attivita/sinistri");
        }
        exit;
    }
}
