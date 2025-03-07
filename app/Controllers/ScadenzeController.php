<?php

namespace App\Controllers;

use App\Models\Scadenza;
use App\Models\ScadenzaDocumento;
use App\Models\TipoScadenza;

class ScadenzeController
{

    private $scadenzaModel;

    public function __construct()
    {
        $this->scadenzaModel = new Scadenza();
    }

    // Visualizza l'elenco delle scadenze con filtri e paginazione
    public function index()
    {
        $filterCondominio = $_GET['IDCondominio'] ?? '';
        $filterTipo = $_GET['IDTipoScadenza'] ?? '';
        $statusFilter = $_GET['status'] ?? 'tutti';

        // Recupera tutte le scadenze filtrate per condominio e tipo
        $scadenze = $this->scadenzaModel->getAll($filterCondominio, $filterTipo);

        // Applica il filtro per stato (attive, scadute, attive_scadute, archiviati, tutti)
        $today = date('Y-m-d');
        $scadenzeFiltrate = [];
        foreach ($scadenze as $s) {
            $include = true;
            $scaduta = ($s['DataScadenza'] < $today);
            if ($statusFilter == 'attive') {
                $include = (!$scaduta && $s['Archiviato'] == 0);
            } elseif ($statusFilter == 'scadute') {
                $include = ($scaduta && $s['Archiviato'] == 0);
            } elseif ($statusFilter == 'attive_scadute') {
                $include = ($s['Archiviato'] == 0);
            } elseif ($statusFilter == 'archiviati') {
                $include = ($s['Archiviato'] == 1);
            }
            if ($include) {
                $scadenzeFiltrate[] = $s;
            }
        }

        // Paginazione
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $itemsPerPage = 30;
        $totalItems = count($scadenzeFiltrate);
        $pages = ceil($totalItems / $itemsPerPage);
        $offset = ($page - 1) * $itemsPerPage;
        $scadenzePaginate = array_slice($scadenzeFiltrate, $offset, $itemsPerPage);

        // Dati di supporto per i filtri: tipi di scadenze, condomini e fornitori
        $tipoModel = new TipoScadenza();
        $tipiScadenze = $tipoModel->getAll();

        $condominiModel = new \App\Models\Condominio();
        $condomini = $condominiModel->getAll();

        $fornitoriModel = new \App\Models\Fornitore();
        $fornitori = $fornitoriModel->getAll();

        include BASE_PATH . 'app/Views/adempimenti/scadenze/index.php';
    }

    // Visualizza il dettaglio di una scadenza
    public function detail()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo "ID Scadenza mancante.";
            exit;
        }
        $scadenza = $this->scadenzaModel->getById($id);
        if (!$scadenza) {
            echo "Scadenza non trovata.";
            exit;
        }
        $documentoModel = new ScadenzaDocumento();
        $documenti = $documentoModel->getByScadenza($id);

        $tipoModel = new TipoScadenza();
        $tipiScadenze = $tipoModel->getAll();

        $condominiModel = new \App\Models\Condominio();
        $condomini = $condominiModel->getAll();

        $fornitoriModel = new \App\Models\Fornitore();
        $fornitori = $fornitoriModel->getAll();

        include BASE_PATH . 'app/Views/adempimenti/scadenze/detail.php';
    }

    // Salva una nuova scadenza (incluso il campo IDFornitore e Durata)
    public function save()
    {
        $data = [
            'IDCondominio'   => $_POST['IDCondominio'] ?? null,
            'IDTipoScadenza' => $_POST['IDTipoScadenza'] ?? null,
            'IDFornitore'    => $_POST['IDFornitore'] ?? null,
            'DataScadenza'   => $_POST['DataScadenza'] ?? null,
            'Durata'         => $_POST['Durata'] ?? null,
            'Note'           => $_POST['Note'] ?? ''
        ];
        if (!$data['IDCondominio'] || !$data['IDTipoScadenza'] || !$data['IDFornitore'] || !$data['DataScadenza'] || !$data['Durata']) {
            echo "Dati mancanti.";
            exit;
        }
        $result = $this->scadenzaModel->create($data);
        if ($result) {
            header("Location: " . BASE_URL . "/scadenze");
            exit;
        } else {
            echo "Errore durante il salvataggio della scadenza.";
            exit;
        }
    }

    // Aggiorna una scadenza esistente
    public function update()
    {
        $data = [
            'IDScadenza'     => $_POST['IDScadenza'] ?? null,
            'IDCondominio'   => $_POST['IDCondominio'] ?? null,
            'IDTipoScadenza' => $_POST['IDTipoScadenza'] ?? null,
            'IDFornitore'    => $_POST['IDFornitore'] ?? null,
            'DataScadenza'   => $_POST['DataScadenza'] ?? null,
            'Durata'         => $_POST['Durata'] ?? null,
            'Note'           => $_POST['Note'] ?? ''
        ];
        if (!$data['IDScadenza'] || !$data['IDCondominio'] || !$data['IDTipoScadenza'] || !$data['IDFornitore'] || !$data['DataScadenza'] || !$data['Durata']) {
            echo "Dati mancanti per l'aggiornamento.";
            exit;
        }
        $result = $this->scadenzaModel->update($data);
        if ($result) {
            header("Location: " . BASE_URL . "/scadenze");
            exit;
        } else {
            echo "Errore durante l'aggiornamento della scadenza.";
            exit;
        }
    }

    // Elimina definitivamente una scadenza
    public function delete()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo "ID Scadenza mancante.";
            exit;
        }
        $this->scadenzaModel->delete($id);
        header("Location: " . BASE_URL . "/scadenze");
        exit;
    }

    // Archivia (soft delete) una scadenza
    public function archive()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo "ID Scadenza mancante.";
            exit;
        }
        $this->scadenzaModel->archive($id);
        header("Location: " . BASE_URL . "/scadenze");
        exit;
    }

    // Upload di un documento associato ad una scadenza
    public function uploadDocumento()
    {
        $idScadenza = $_POST['IDScadenza'] ?? null;
        if (!$idScadenza) {
            echo "ID Scadenza mancante.";
            exit;
        }
        if (!isset($_FILES['File']) || $_FILES['File']['error'] != 0) {
            echo "Errore nell'upload del file.";
            exit;
        }
        $uploadDir = BASE_PATH . 'storage/uploads/scadenze/';
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
            'IDScadenza' => $idScadenza,
            'Titolo'     => $_POST['DocumentoTitolo'] ?? 'Documento',
            'File'       => $filename
        ];
        $documentoModel = new ScadenzaDocumento();
        $result = $documentoModel->create($data);
        if ($result) {
            header("Location: " . BASE_URL . "/scadenze/detail?id=" . $idScadenza);
            exit;
        } else {
            echo "Errore nel salvataggio del documento.";
            exit;
        }
    }

    public function griglia()
    {
        // Recupera tutti i condomini
        $condominiModel = new \App\Models\Condominio();
        $condomini = $condominiModel->getAll();

        // Recupera tutte le tipologie di scadenze
        $tipoModel = new \App\Models\TipoScadenza();
        $tipiScadenze = $tipoModel->getAll();

        // Recupera tutte le scadenze (senza filtri)
        $scadenze = $this->scadenzaModel->getAll('', '');

        // Recupera tutti i fornitori
        $fornitoriModel = new \App\Models\Fornitore();
        $fornitori = $fornitoriModel->getAll();

        // Costruisci una mappa: per ogni condominio e per ogni tipo di scadenza
        $grid = [];
        foreach ($scadenze as $s) {
            $grid[$s['IDCondominio']][$s['IDTipoScadenza']] = $s;
        }

        // Costruisci una mappa dei documenti associati per ogni scadenza
        $documentoModel = new \App\Models\ScadenzaDocumento();
        $documentCounts = [];
        foreach ($scadenze as $s) {
            $docs = $documentoModel->getByScadenza($s['IDScadenza']);
            $documentCounts[$s['IDScadenza']] = count($docs);
        }

        include BASE_PATH . 'app/Views/adempimenti/scadenze/griglia.php';
    }
}
