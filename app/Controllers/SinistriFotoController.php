<?php

namespace App\Controllers;

use App\Models\SinistroFoto;

class SinistriFotoController
{

    private $fotoModel;

    public function __construct()
    {
        $this->fotoModel = new SinistroFoto();
    }

    public function save()
    {
        if (!isset($_FILES['File'])) {
            echo "Errore nell'upload dei file.";
            exit;
        }

        $uploadDir = BASE_PATH . 'storage/uploads/sinistri/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $idSinistro = $_POST['IDSinistro'] ?? null;
        if (!$idSinistro) {
            echo "ID Sinistro mancante per le foto.";
            exit;
        }

        $files = $_FILES['File'];
        $allOk = true;
        // Itera su ogni file caricato
        for ($i = 0; $i < count($files['name']); $i++) {
            // Se si è verificato un errore in questo file, salta
            if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                continue;
            }

            $filename = time() . '_' . basename($files['name'][$i]);
            $targetFile = $uploadDir . $filename;

            if (!move_uploaded_file($files['tmp_name'][$i], $targetFile)) {
                $allOk = false;
                continue;
            }

            // Salva ciascuna foto nel database
            $data = [
                'IDSinistro' => $idSinistro,
                'File'       => 'sinistri/' . $filename
            ];
            $this->fotoModel->create($data);
        }

        if ($allOk) {
            header("Location: " . BASE_URL . "/attivita/sinistri/detail?id=" . $idSinistro);
            exit;
        } else {
            echo "Errore durante il salvataggio di una o più foto.";
            exit;
        }
    }

    public function delete()
    {
        $idFoto    = $_GET['id'] ?? null;
        $idSinistro = $_GET['idSinistro'] ?? null;
        if ($idFoto) {
            $this->fotoModel->delete($idFoto);
        }
        if ($idSinistro) {
            header("Location: " . BASE_URL . "/attivita/sinistri/detail?id=" . $idSinistro);
        } else {
            header("Location: " . BASE_URL . "/attivita/sinistri");
        }
        exit;
    }

    // Metodo downloadZip per generare e scaricare il file ZIP
    public function downloadZip()
    {
        $idSinistro = $_GET['id'] ?? null;
        if (!$idSinistro) {
            echo "ID Sinistro mancante.";
            exit;
        }

        $fotos = $this->fotoModel->getBySinistro($idSinistro);
        if (empty($fotos)) {
            echo "Nessuna foto trovata.";
            exit;
        }

        $zip = new \ZipArchive();
        // Crea un file temporaneo per il ZIP
        $zipFilename = tempnam(sys_get_temp_dir(), 'sinistri_') . '.zip';
        if ($zip->open($zipFilename, \ZipArchive::CREATE) !== TRUE) {
            exit("Impossibile creare il file ZIP");
        }

        foreach ($fotos as $f) {
            $filePath = BASE_PATH . 'storage/uploads/' . $f['File'];
            if (file_exists($filePath)) {
                // Aggiungi il file nel ZIP usando solo il nome base
                $zip->addFile($filePath, basename($filePath));
            }
        }
        $zip->close();

        // Imposta gli header per il download del file ZIP
        header('Content-Type: application/zip');
        header('Content-disposition: attachment; filename=sinistri_' . $idSinistro . '.zip');
        header('Content-Length: ' . filesize($zipFilename));
        readfile($zipFilename);
        unlink($zipFilename);
        exit;
    }
}
