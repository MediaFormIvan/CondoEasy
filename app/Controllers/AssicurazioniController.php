<?php

namespace App\Controllers;

use App\Models\Assicurazione;
use App\Models\AssicurazioneDocumento;
use DateTime;
use DateInterval;
use Exception;


class AssicurazioniController
{

    private $assicurazioneModel;

    public function __construct()
    {
        $this->assicurazioneModel = new Assicurazione();
    }

    // Elenco delle assicurazioni con filtri per condominio e fornitore
    public function index()
    {
        $filterCondominio = $_GET['IDCondominio'] ?? '';
        $filterFornitore  = $_GET['IDFornitore'] ?? '';

        $assicurazioni = $this->assicurazioneModel->getAll($filterCondominio, $filterFornitore);

        // Recupera i dati di supporto per le tendine: condomini e fornitori
        $condominiModel = new \App\Models\Condominio();
        $condomini = $condominiModel->getAll();

        $fornitoriModel = new \App\Models\Fornitore();
        $fornitori = $fornitoriModel->getAll();

        include BASE_PATH . 'app/Views/adempimenti/assicurazioni/index.php';
    }

    // Visualizza il dettaglio di un'assicurazione e permette il caricamento dei documenti
    public function detail()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo "ID Assicurazione mancante.";
            exit;
        }
        $assicurazione = $this->assicurazioneModel->getById($id);
        if (!$assicurazione) {
            echo "Assicurazione non trovata.";
            exit;
        }
        $documentoModel = new AssicurazioneDocumento();
        $documenti = $documentoModel->getByAssicurazione($id);

        // Dati di supporto per il form: condomini e fornitori
        $condominiModel = new \App\Models\Condominio();
        $condomini = $condominiModel->getAll();

        $fornitoriModel = new \App\Models\Fornitore();
        $fornitori = $fornitoriModel->getAll();

        include BASE_PATH . 'app/Views/adempimenti/assicurazioni/detail.php';
    }

    // Salva una nuova assicurazione
    public function save()
    {
        $data = [
            'IDCondominio' => $_POST['IDCondominio'] ?? null,
            'IDFornitore'  => $_POST['IDFornitore'] ?? null,
            'DataScadenza' => $_POST['DataScadenza'] ?? null,
            'Durata'       => $_POST['Durata'] ?? null,
            'Polizza'      => $_POST['Polizza'] ?? ''
        ];
        if (!$data['IDCondominio'] || !$data['IDFornitore'] || !$data['DataScadenza'] || !$data['Durata'] || empty($data['Polizza'])) {
            echo "Dati mancanti.";
            exit;
        }
        $result = $this->assicurazioneModel->create($data);
        if ($result) {
            header("Location: " . BASE_URL . "/assicurazioni");
            exit;
        } else {
            echo "Errore durante il salvataggio dell'assicurazione.";
            exit;
        }
    }

    // Aggiorna un'assicurazione esistente
    public function update()
    {
        $data = [
            'IDAssicurazione' => $_POST['IDAssicurazione'] ?? null,
            'IDCondominio'    => $_POST['IDCondominio'] ?? null,
            'IDFornitore'     => $_POST['IDFornitore'] ?? null,
            'DataScadenza'    => $_POST['DataScadenza'] ?? null,
            'Durata'          => $_POST['Durata'] ?? null,
            'Polizza'         => $_POST['Polizza'] ?? ''
        ];
        if (!$data['IDAssicurazione'] || !$data['IDCondominio'] || !$data['IDFornitore'] || !$data['DataScadenza'] || !$data['Durata'] || empty($data['Polizza'])) {
            echo "Dati mancanti per l'aggiornamento.";
            exit;
        }
        $result = $this->assicurazioneModel->update($data);
        if ($result) {
            header("Location: " . BASE_URL . "/assicurazioni");
            exit;
        } else {
            echo "Errore durante l'aggiornamento dell'assicurazione.";
            exit;
        }
    }

    // Elimina definitivamente un'assicurazione
    public function delete()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo "ID Assicurazione mancante.";
            exit;
        }
        $this->assicurazioneModel->delete($id);
        header("Location: " . BASE_URL . "/assicurazioni");
        exit;
    }

    // Archivia (soft delete) un'assicurazione
    public function archive()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo "ID Assicurazione mancante.";
            exit;
        }
        $this->assicurazioneModel->archive($id);
        header("Location: " . BASE_URL . "/assicurazioni");
        exit;
    }

    // Upload di un documento per un'assicurazione
    public function uploadDocumento()
    {
        $idAssicurazione = $_POST['IDAssicurazione'] ?? null;
        if (!$idAssicurazione) {
            echo "ID Assicurazione mancante.";
            exit;
        }
        if (!isset($_FILES['File']) || $_FILES['File']['error'] != 0) {
            echo "Errore nell'upload del file.";
            exit;
        }
        $uploadDir = BASE_PATH . 'storage/uploads/assicurazioni/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $filename = time() . '_' . basename($_FILES['File']['name']);
        $uploadFile = $uploadDir . $filename;
        if (!move_uploaded_file($_FILES['File']['tmp_name'], $uploadFile)) {
            echo "Errore nel salvataggio del file.";
            exit;
        }
        $data = [
            'IDAssicurazione' => $idAssicurazione,
            'Titolo'          => $_POST['DocumentoTitolo'] ?? 'Documento',
            'File'            => $filename
        ];
        $documentoModel = new AssicurazioneDocumento();
        $result = $documentoModel->create($data);
        if ($result) {
            header("Location: " . BASE_URL . "/assicurazioni/detail?id=" . $idAssicurazione);
            exit;
        } else {
            echo "Errore nel salvataggio del documento.";
            exit;
        }
    }
    public function renew()
    {
        // Recupera l'ID dell'assicurazione da rinnovare
        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo "ID Assicurazione mancante.";
            exit;
        }
        $assicurazione = $this->assicurazioneModel->getById($id);
        if (!$assicurazione) {
            echo "Assicurazione non trovata.";
            exit;
        }
        // Calcola la nuova data di scadenza aggiungendo i mesi indicati in "Durata"
        try {
            $dataScadenza = new DateTime($assicurazione['DataScadenza']);
        } catch (Exception $e) {
            echo "Errore nella data di scadenza.";
            exit;
        }
        $durata = (int)$assicurazione['Durata']; // durata in mesi
        // Aggiunge i mesi alla data di scadenza
        $dataScadenza->add(new DateInterval("P{$durata}M"));
        $newDate = $dataScadenza->format('Y-m-d');

        // Aggiorna la data di scadenza nel record
        $assicurazione['DataScadenza'] = $newDate;
        $result = $this->assicurazioneModel->update($assicurazione);
        if ($result) {
            header("Location: " . BASE_URL . "/assicurazioni");
            exit;
        } else {
            echo "Errore nel rinnovo della polizza.";
            exit;
        }
    }
}
