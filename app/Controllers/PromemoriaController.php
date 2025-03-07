<?php
namespace App\Controllers;

use App\Models\Promemoria;

class PromemoriaController {
    private $promemoriaModel;

    public function __construct() {
        $this->promemoriaModel = new Promemoria();
    }

    public function index() {
        // Raccoglie i filtri dal GET
        $filters = [];
        $filters['titolo'] = $_GET['titolo'] ?? '';
        $filters['condominio'] = $_GET['condominio'] ?? '';
        $filters['utente'] = $_GET['utente'] ?? '';
        $filters['filter'] = $_GET['filter'] ?? 'aperti'; // 'aperti', 'chiusi' o 'tutti'

        $promemoria = $this->promemoriaModel->getAll($filters);

        // Dati di supporto per i dropdown
        $condominiModel = new \App\Models\Condominio();
        $condomini = $condominiModel->getAll();

        $utentiModel = new \App\Models\User();
        $utenti = $utentiModel->getAll();

        $statiModel = new \App\Models\StatoManutenzione(); // Se usi lo stesso modello per gli stati
        $stati = $statiModel->getAll();

        // Pagina e paginazione
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $itemsPerPage = 30;
        $totalItems = count($promemoria);
        $pages = ceil($totalItems / $itemsPerPage);
        $offset = ($page - 1) * $itemsPerPage;
        $promemoria = array_slice($promemoria, $offset, $itemsPerPage);

        include BASE_PATH . 'app/Views/adempimenti/promemoria/index.php';
    }

    public function save() {
        $data = [
            'IDCondominio' => $_POST['IDCondominio'] ?? null,
            'IDStato' => $_POST['IDStato'] ?? null,
            'DataScadenza' => $_POST['DataScadenza'] ?? null,
            'IDUtente' => $_POST['IDUtente'] ?? null,
            'Titolo' => $_POST['Titolo'] ?? ''
        ];
        if (!$data['IDCondominio'] || !$data['IDStato'] || !$data['DataScadenza'] || !$data['IDUtente'] || empty($data['Titolo'])) {
            echo "Dati mancanti.";
            exit;
        }
        $result = $this->promemoriaModel->create($data);
        if ($result) {
            header("Location: " . BASE_URL . "/promemoria");
            exit;
        } else {
            echo "Errore durante il salvataggio del promemoria.";
            exit;
        }
    }

    public function update() {
        $data = [
            'IDPromemoria' => $_POST['IDPromemoria'] ?? null,
            'IDCondominio' => $_POST['IDCondominio'] ?? null,
            'IDStato' => $_POST['IDStato'] ?? null,
            'DataScadenza' => $_POST['DataScadenza'] ?? null,
            'IDUtente' => $_POST['IDUtente'] ?? null,
            'Titolo' => $_POST['Titolo'] ?? ''
        ];
        if (!$data['IDPromemoria'] || !$data['IDCondominio'] || !$data['IDStato'] || !$data['DataScadenza'] || !$data['IDUtente'] || empty($data['Titolo'])) {
            echo "Dati mancanti.";
            exit;
        }
        $result = $this->promemoriaModel->update($data);
        if ($result) {
            header("Location: " . BASE_URL . "/promemoria");
            exit;
        } else {
            echo "Errore durante l'aggiornamento del promemoria.";
            exit;
        }
    }

    public function delete() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo "ID Promemoria mancante.";
            exit;
        }
        $this->promemoriaModel->delete($id);
        header("Location: " . BASE_URL . "/promemoria");
        exit;
    }

    public function changeState() {
        $id = $_POST['IDPromemoria'] ?? null;
        $newState = $_POST['newState'] ?? null;
        if (!$id || !$newState) {
            echo "Dati mancanti per il cambio stato.";
            exit;
        }
        $result = $this->promemoriaModel->changeState($id, $newState);
        if ($result) {
            header("Location: " . BASE_URL . "/promemoria");
            exit;
        } else {
            echo "Errore nel cambio di stato.";
            exit;
        }
    }
}
