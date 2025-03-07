<?php

namespace App\Controllers;

use App\Models\Gestione;
use App\Models\Condominio;
use App\Models\TipiGestioni;

class GestioniController
{

    private $gestioneModel;
    private $condominioModel;
    private $tipiGestioniModel;

    public function __construct()
    {
        $this->gestioneModel = new Gestione();
        $this->condominioModel = new \App\Models\Condominio();
        $this->tipiGestioniModel = new TipiGestioni();
    }

    public function index() {
        // Se non esiste una gestione selezionata in sessione, prova a recuperarla dal record utente
        if (!isset($_SESSION['gestione_selezionata']) && isset($_SESSION['user']['IDGestione'])) {
            $gestioneSalvata = $this->gestioneModel->getById($_SESSION['user']['IDGestione']);
            if ($gestioneSalvata) {
                // Recupera i dati del condominio
                $condominio = $this->condominioModel->getById($gestioneSalvata['IDCondominio']);
                if ($condominio) {
                    $gestioneSalvata['CondominioNome'] = $condominio['Nome'];
                }
                $_SESSION['gestione_selezionata'] = $gestioneSalvata;
            }
        }
        $selectedGestione = $_SESSION['gestione_selezionata'] ?? null;
    
        $condomini = $this->condominioModel->getAll();
        foreach ($condomini as &$condominio) {
            $condominio['gestioni_aperti'] = $this->gestioneModel->getAllByCondominio($condominio['IDCondominio']);
            $condominio['gestioni_chiusi'] = $this->gestioneModel->getArchivedByCondominio($condominio['IDCondominio']);
        }
        include BASE_PATH . 'app/Views/gestioni/bilanci/index.php';
    }
    


    // Salvataggio (creazione o modifica) di una gestione
    public function save()
    {
        $data = [
            'IDGestione'     => $_POST['IDGestione'] ?? null,
            'IDCondominio'   => $_POST['IDCondominio'] ?? '',
            'IDTipoGestione' => $_POST['IDTipoGestione'] ?? '',
            'Nome'           => $_POST['Nome'] ?? '',
            'DataInizio'     => $_POST['DataInizio'] ?? '',
            'DataFine'       => $_POST['DataFine'] ?? null
        ];
        if ($data['IDGestione']) {
            $this->gestioneModel->update($data);
        } else {
            $this->gestioneModel->create($data);
        }
        header("Location: " . BASE_URL . "/gestioni/bilanci");
        exit;
    }

    public function edit()
    {
        $IDGestione = $_GET['id'] ?? null;
        if (!$IDGestione) {
            header("Location: " . BASE_URL . "/gestioni/bilanci");
            exit;
        }
        $gestione = $this->gestioneModel->getById($IDGestione);
        if (!$gestione) {
            echo "Gestione non trovata.";
            exit;
        }
        // Recupera i tipi di gestione per popolare il menu a tendina nel form
        $tipiGestioni = $this->tipiGestioniModel->getAll();

        // Carica la view di modifica (crea il file edit.php nella cartella app/Views/gestioni/bilanci/)
        include BASE_PATH . 'app/Views/gestioni/bilanci/edit.php';
    }


    // Archivia (chiude) una gestione
    public function archive()
    {
        $IDGestione = $_GET['id'] ?? null;
        if ($IDGestione) {
            $this->gestioneModel->archive($IDGestione);
        }
        header("Location: " . BASE_URL . "/gestioni/bilanci");
        exit;
    }

    // Cancella una gestione
    public function delete()
    {
        $IDGestione = $_GET['id'] ?? null;
        if ($IDGestione) {
            $this->gestioneModel->delete($IDGestione);
        }
        header("Location: " . BASE_URL . "/gestioni/bilanci");
        exit;
    }

    // Seleziona una gestione (salvata in sessione per essere portata in giro nell'app)
    public function select() {
        $IDGestione = $_GET['id'] ?? null;
        if (!$IDGestione) {
            header("Location: " . BASE_URL . "/gestioni/bilanci");
            exit;
        }
        
        $gestione = $this->gestioneModel->getById($IDGestione);
        if (!$gestione) {
            echo "Gestione non trovata.";
            exit;
        }
        
        // Recupera il condominio associato e aggiungi il nome alla gestione
        $condominio = $this->condominioModel->getById($gestione['IDCondominio']);
        if ($condominio) {
            $gestione['CondominioNome'] = $condominio['Nome'];
        }
        
        // Salva la gestione selezionata in sessione
        $_SESSION['gestione_selezionata'] = $gestione;
        
        // Se l'utente è loggato, aggiorna il record utente nel database
        if (isset($_SESSION['user']) && isset($_SESSION['user']['IDUtente'])) {
            $userId = $_SESSION['user']['IDUtente'];
            $userModel = new \App\Models\User();
            $userModel->updateGestioneSelection($userId, $IDGestione);
            $_SESSION['user']['IDGestione'] = $IDGestione;
        }
        
        header("Location: " . BASE_URL . "/gestioni/bilanci");
        exit;
    }
    
}
