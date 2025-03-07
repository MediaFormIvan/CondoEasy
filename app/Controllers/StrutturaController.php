<?php

namespace App\Controllers;

use App\Models\Condominio;
use App\Models\Fabbricato;
use App\Models\Civico;
use App\Models\Scala;
use App\Models\Unita;
use App\Models\UnitaPersone; // Modello per la tabella unita_persone
use App\Models\TipiPersone;  // Per recuperare i ruoli

class StrutturaController
{

    private $condominioModel;
    private $fabbricatoModel;
    private $civicoModel;
    private $scalaModel;
    private $unitaModel;

    public function __construct()
    {
        $this->condominioModel = new Condominio();
        $this->fabbricatoModel = new Fabbricato();
        $this->civicoModel = new Civico();
        $this->scalaModel = new Scala();
        $this->unitaModel = new Unita();
    }

    /**
     * Visualizza la struttura del condominio selezionato (ottenuto dalla gestione in sessione)
     */
    public function index()
    {
        $IDCondominio = $_SESSION['gestione_selezionata']['IDCondominio'] ?? null;
        if (!$IDCondominio) {
            echo "Nessun condominio selezionato.";
            exit;
        }
        $condominio = $this->condominioModel->getById($IDCondominio);
        if (!$condominio) {
            echo "Condominio non trovato.";
            exit;
        }
        // Recupera fabbricati; se il risultato non è un array, lo imposta a un array vuoto
        $fabbricati = $this->fabbricatoModel->getByCondominio($IDCondominio);
        if (!is_array($fabbricati)) {
            $fabbricati = [];
        }
        $condominio['fabbricati'] = $fabbricati;

        foreach ($condominio['fabbricati'] as &$fabbricato) {
            $civici = $this->civicoModel->getByFabbricato($fabbricato['IDFabbricato']);
            if (!is_array($civici)) {
                $civici = [];
            }
            $fabbricato['civici'] = $civici;
            foreach ($fabbricato['civici'] as &$civico) {
                $scale = $this->scalaModel->getByCivico($civico['IDCivico']);
                if (!is_array($scale)) {
                    $scale = [];
                }
                $civico['scale'] = $scale;
                foreach ($civico['scale'] as &$scala) {
                    $unita = $this->unitaModel->getByScala($scala['IDScala']);
                    if (!is_array($unita)) {
                        $unita = [];
                    }
                    $scala['unita'] = $unita;
                }
                unset($scala);
            }
            unset($civico);
        }
        unset($fabbricato);

        include BASE_PATH . 'app/Views/gestioni/strutture/index.php';
    }


    /* ===========================
     * Metodi per FABBRICATI
     * ===========================
     */
    public function createFabbricato()
    {
        $IDCondominio = $_POST['IDCondominio'] ?? null;
        $Nome = $_POST['Nome'] ?? '';
        if ($IDCondominio && $Nome) {
            $data = [
                'IDCondominio' => $IDCondominio,
                'Nome'         => $Nome
            ];
            $this->fabbricatoModel->create($data);
        }
        header("Location: " . BASE_URL . "/gestioni/strutture");
        exit;
    }
    public function updateFabbricato()
    {
        $IDFabbricato = $_POST['IDFabbricato'] ?? null;
        $Nome = $_POST['Nome'] ?? '';
        if ($IDFabbricato && $Nome) {
            $data = [
                'IDFabbricato' => $IDFabbricato,
                'Nome'         => $Nome
            ];
            $this->fabbricatoModel->update($data);
        }
        header("Location: " . BASE_URL . "/gestioni/strutture");
        exit;
    }
    public function deleteFabbricato()
    {
        $IDFabbricato = $_GET['id'] ?? null;
        if ($IDFabbricato) {
            $this->fabbricatoModel->delete($IDFabbricato);
        }
        header("Location: " . BASE_URL . "/gestioni/strutture");
        exit;
    }

    /* ===========================
     * Metodi per CIVICI
     * ===========================
     */
    public function createCivico()
    {
        $IDFabbricato = $_POST['IDFabbricato'] ?? null;
        $Nome = $_POST['Nome'] ?? '';
        if ($IDFabbricato && $Nome) {
            $data = [
                'IDFabbricato' => $IDFabbricato,
                'Nome'         => $Nome
            ];
            $this->civicoModel->create($data);
        }
        header("Location: " . BASE_URL . "/gestioni/strutture");
        exit;
    }
    public function updateCivico()
    {
        $IDCivico = $_POST['IDCivico'] ?? null;
        $Nome = $_POST['Nome'] ?? '';
        if ($IDCivico && $Nome) {
            $data = [
                'IDCivico' => $IDCivico,
                'Nome'     => $Nome
            ];
            $this->civicoModel->update($data);
        }
        header("Location: " . BASE_URL . "/gestioni/strutture");
        exit;
    }
    public function deleteCivico()
    {
        $IDCivico = $_GET['id'] ?? null;
        if ($IDCivico) {
            $this->civicoModel->delete($IDCivico);
        }
        header("Location: " . BASE_URL . "/gestioni/strutture");
        exit;
    }

    /* ===========================
     * Metodi per SCALE
     * ===========================
     */
    public function createScala()
    {
        $IDCivico = $_POST['IDCivico'] ?? null;
        $Nome = $_POST['Nome'] ?? '';
        if ($IDCivico && $Nome) {
            $data = [
                'IDCivico' => $IDCivico,
                'Nome'     => $Nome
            ];
            $this->scalaModel->create($data);
        }
        header("Location: " . BASE_URL . "/gestioni/strutture");
        exit;
    }
    public function updateScala()
    {
        $IDScala = $_POST['IDScala'] ?? null;
        $Nome = $_POST['Nome'] ?? '';
        if ($IDScala && $Nome) {
            $data = [
                'IDScala' => $IDScala,
                'Nome'    => $Nome
            ];
            $this->scalaModel->update($data);
        }
        header("Location: " . BASE_URL . "/gestioni/strutture");
        exit;
    }
    public function deleteScala()
    {
        $IDScala = $_GET['id'] ?? null;
        if ($IDScala) {
            $this->scalaModel->delete($IDScala);
        }
        header("Location: " . BASE_URL . "/gestioni/strutture");
        exit;
    }

    /* ===========================
     * Metodi per UNITÀ
     * ===========================
     */
    public function createUnita()
    {
        $IDScala = $_POST['IDScala'] ?? null;
        $Interno = $_POST['Interno'] ?? null;
        $Piano = $_POST['Piano'] ?? null;
        $Sezione = $_POST['Sezione'] ?? null;
        $Foglio = $_POST['Foglio'] ?? null;
        $Subalterno = $_POST['Subalterno'] ?? null;
        $Categoria = $_POST['Categoria'] ?? null;
        if ($IDScala) {
            $data = [
                'IDScala'   => $IDScala,
                'Interno'   => $Interno,
                'Piano'     => $Piano,
                'Sezione'   => $Sezione,
                'Foglio'    => $Foglio,
                'Subalterno' => $Subalterno,
                'Categoria' => $Categoria
            ];
            $this->unitaModel->create($data);
        }
        header("Location: " . BASE_URL . "/gestioni/strutture");
        exit;
    }
    public function updateUnita()
    {
        $IDUnita = $_POST['IDUnita'] ?? null;
        $Interno = $_POST['Interno'] ?? null;
        $Piano = $_POST['Piano'] ?? null;
        $Sezione = $_POST['Sezione'] ?? null;
        $Foglio = $_POST['Foglio'] ?? null;
        $Subalterno = $_POST['Subalterno'] ?? null;
        $Categoria = $_POST['Categoria'] ?? null;
        if ($IDUnita) {
            $data = [
                'IDUnita'   => $IDUnita,
                'Interno'   => $Interno,
                'Piano'     => $Piano,
                'Sezione'   => $Sezione,
                'Foglio'    => $Foglio,
                'Subalterno' => $Subalterno,
                'Categoria' => $Categoria
            ];
            $this->unitaModel->update($data);
        }
        header("Location: " . BASE_URL . "/gestioni/strutture");
        exit;
    }
    public function deleteUnita()
    {
        $IDUnita = $_GET['id'] ?? null;
        if ($IDUnita) {
            $this->unitaModel->delete($IDUnita);
        }
        header("Location: " . BASE_URL . "/gestioni/strutture");
        exit;
    }

    /* ===========================
     * Gestione Associazioni Persone
     * ===========================
     */
    public function managePersone()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $IDUnita = $_POST['IDUnita'] ?? null;
            $IDPersona = $_POST['IDPersona'] ?? null;
            $IDTipoPersona = $_POST['IDTipoPersona'] ?? null;
            $Percentuale = $_POST['Percentuale'] ?? null;
            $DataInizio = $_POST['DataInizio'] ?? null;
            $DataFine = $_POST['DataFine'] ?? null;

            // Campi obbligatori: IDUnita, IDPersona, IDTipoPersona e DataInizio
            if ($IDUnita && $IDPersona && $IDTipoPersona && $DataInizio) {
                $data = [
                    'IDUnita' => $IDUnita,
                    'IDPersona' => $IDPersona,
                    'IDTipoPersona' => $IDTipoPersona,
                    'Percentuale' => $Percentuale,
                    'DataInizio' => $DataInizio,
                    'DataFine' => $DataFine
                ];
                $unitaPersoneModel = new UnitaPersone();
                $unitaPersoneModel->create($data);
            }
            header("Location: " . BASE_URL . "/gestioni/strutture");
            exit;
        } else {
            header("Location: " . BASE_URL . "/gestioni/strutture");
            exit;
        }
    }

    // Restituisce i dati di un fabbricato in JSON
    public function getFabbricato()
    {
        $IDFabbricato = $_GET['id'] ?? null;
        if (!$IDFabbricato) {
            echo json_encode(['error' => 'IDFabbricato non fornito']);
            exit;
        }
        // Assumiamo che il modello Fabbricato abbia un metodo getById()
        $fabbricato = $this->fabbricatoModel->getById($IDFabbricato);
        if (!$fabbricato) {
            echo json_encode(['error' => 'Fabbricato non trovato']);
            exit;
        }
        header('Content-Type: application/json');
        echo json_encode($fabbricato);
        exit;
    }

    // Restituisce i dati di un civico in JSON
    public function getCivico()
    {
        $IDCivico = $_GET['id'] ?? null;
        if (!$IDCivico) {
            echo json_encode(['error' => 'IDCivico non fornito']);
            exit;
        }
        // Assumiamo che il modello Civico abbia un metodo getById()
        $civico = $this->civicoModel->getById($IDCivico);
        if (!$civico) {
            echo json_encode(['error' => 'Civico non trovato']);
            exit;
        }
        header('Content-Type: application/json');
        echo json_encode($civico);
        exit;
    }

    // Restituisce i dati di una scala in JSON
    public function getScala()
    {
        $IDScala = $_GET['id'] ?? null;
        if (!$IDScala) {
            echo json_encode(['error' => 'IDScala non fornito']);
            exit;
        }
        // Assumiamo che il modello Scala abbia un metodo getById()
        $scala = $this->scalaModel->getById($IDScala);
        if (!$scala) {
            echo json_encode(['error' => 'Scala non trovata']);
            exit;
        }
        header('Content-Type: application/json');
        echo json_encode($scala);
        exit;
    }

    // Restituisce i dati di un'unità in JSON
    public function getUnita()
    {
        $IDUnita = $_GET['id'] ?? null;
        if (!$IDUnita) {
            echo json_encode(['error' => 'IDUnita non fornito']);
            exit;
        }
        // Assumiamo che il modello Unita abbia un metodo getById()
        $unita = $this->unitaModel->getById($IDUnita);
        if (!$unita) {
            echo json_encode(['error' => 'Unità non trovata']);
            exit;
        }
        header('Content-Type: application/json');
        echo json_encode($unita);
        exit;
    }
}
