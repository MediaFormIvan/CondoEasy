<?php

namespace App\Controllers;

use App\Models\Contratto;
use App\Models\ContrattoDocumento;
use App\Models\TipoContratto;

class ContrattiController
{

    private $contrattoModel;

    public function __construct()
    {
        $this->contrattoModel = new Contratto();
    }

    // Elenco dei contratti
    public function index()
    {
        $titolo = $_GET['titolo'] ?? '';
        $contratti = $this->contrattoModel->getAll($titolo);

        // Dati di supporto: tipologie, condomini e fornitori
        $tipoModel = new TipoContratto();
        $tipiContratti = $tipoModel->getAll();

        // Assumiamo l'esistenza dei modelli Condominio e Fornitore
        $condominiModel = new \App\Models\Condominio();
        $condomini = $condominiModel->getAll();

        $fornitoriModel = new \App\Models\Fornitore();
        $fornitori = $fornitoriModel->getAll();

        // Paginazione (come in sinistri)
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $itemsPerPage = 30;
        $totalItems = count($contratti);
        $pages = ceil($totalItems / $itemsPerPage);
        $offset = ($page - 1) * $itemsPerPage;
        $contratti = array_slice($contratti, $offset, $itemsPerPage);

        include BASE_PATH . 'app/Views/adempimenti/contratti/index.php';
    }

    // Dettaglio di un contratto
    public function detail()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo "ID Contratto mancante.";
            exit;
        }
        $contratto = $this->contrattoModel->getById($id);
        if (!$contratto) {
            echo "Contratto non trovato.";
            exit;
        }
        // Recupera documenti associati
        $documentoModel = new ContrattoDocumento();
        $documenti = $documentoModel->getByContratto($id);

        // Dati di supporto per la view
        $tipoModel = new TipoContratto();
        $tipiContratti = $tipoModel->getAll();
        $condominiModel = new \App\Models\Condominio();
        $condomini = $condominiModel->getAll();
        $fornitoriModel = new \App\Models\Fornitore();
        $fornitori = $fornitoriModel->getAll();

        include BASE_PATH . 'app/Views/adempimenti/contratti/detail.php';

    }

    // Salva un nuovo contratto
    public function save()
    {
        $data = [
            'IDCondominio'    => $_POST['IDCondominio'] ?? null,
            'IDFornitore'     => $_POST['IDFornitore'] ?? null,
            'IDTipoContratto' => $_POST['IDTipoContratto'] ?? null,
            'Titolo'          => $_POST['Titolo'] ?? '',
            'DataInizio'      => $_POST['DataInizio'] ?? null,
            'DataFine'        => $_POST['DataFine'] ?? null,
            'Note'            => $_POST['Note'] ?? ''
        ];

        if (!$data['IDCondominio'] || !$data['IDFornitore'] || !$data['IDTipoContratto'] || empty($data['Titolo']) || !$data['DataInizio'] || !$data['DataFine']) {
            echo "Dati mancanti.";
            exit;
        }

        $result = $this->contrattoModel->create($data);
        if ($result) {
            header("Location: " . BASE_URL . "/contratti");
            exit;
        } else {
            echo "Errore durante il salvataggio del contratto.";
            exit;
        }
    }

    // Aggiorna un contratto esistente
    public function update()
    {
        $data = [
            'IDCondominioContratto' => $_POST['IDCondominioContratto'] ?? null,
            'IDCondominio'          => $_POST['IDCondominio'] ?? null,
            'IDFornitore'           => $_POST['IDFornitore'] ?? null,
            'IDTipoContratto'       => $_POST['IDTipoContratto'] ?? null,
            'Titolo'                => $_POST['Titolo'] ?? '',
            'DataInizio'            => $_POST['DataInizio'] ?? null,
            'DataFine'              => $_POST['DataFine'] ?? null,
            'Note'                  => $_POST['Note'] ?? ''
        ];

        if (!$data['IDCondominioContratto'] || !$data['IDCondominio'] || !$data['IDFornitore'] || !$data['IDTipoContratto'] || empty($data['Titolo']) || !$data['DataInizio'] || !$data['DataFine']) {
            echo "Dati mancanti per l'aggiornamento.";
            exit;
        }

        $result = $this->contrattoModel->update($data);
        if ($result) {
            header("Location: " . BASE_URL . "/contratti");
            exit;
        } else {
            echo "Errore durante l'aggiornamento del contratto.";
            exit;
        }
    }

    // Elimina un contratto definitivamente
    public function delete()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo "ID Contratto mancante.";
            exit;
        }
        $this->contrattoModel->delete($id);
        header("Location: " . BASE_URL . "/contratti");
        exit;
    }

    // Archivia un contratto (soft delete)
    public function archive()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo "ID Contratto mancante.";
            exit;
        }
        $this->contrattoModel->archive($id);
        header("Location: " . BASE_URL . "/contratti");
        exit;
    }

    // Upload di un documento per il contratto
    public function uploadDocumento()
    {
        $idContratto = $_POST['IDContratto'] ?? null;
        if (!$idContratto) {
            echo "ID Contratto mancante.";
            exit;
        }
        if (!isset($_FILES['File']) || $_FILES['File']['error'] != 0) {
            echo "Errore nell'upload del file.";
            exit;
        }
        // Percorso di upload (assicurati che la cartella esista o venga creata)
        $uploadDir = BASE_PATH . 'storage/uploads/contratti/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $filename = time() . '_' . basename($_FILES['File']['name']);
        $uploadFile = $uploadDir . $filename;
        if (!move_uploaded_file($_FILES['File']['tmp_name'], $uploadFile)) {
            echo "Errore nel salvataggio del file.";
            exit;
        }
        // Salva il record del documento nel database
        $data = [
            'IDContratto' => $idContratto,
            'Titolo'      => $_POST['DocumentoTitolo'] ?? 'Documento',
            'NomeFile'    => $filename
        ];
        $documentoModel = new ContrattoDocumento();
        $result = $documentoModel->create($data);
        if ($result) {
            header("Location: " . BASE_URL . "/contratti/detail?id=" . $idContratto);
            exit;
        } else {
            echo "Errore nel salvataggio del documento.";
            exit;
        }
    }
}
