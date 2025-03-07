<?php
namespace App\Controllers;

use App\Models\Legale;

class LegaleController {
    private $legaleModel;
    public function __construct() {
        $this->legaleModel = new Legale();
    }

    public function index() {
        $titolo = $_GET['titolo'] ?? '';
        $legali = $this->legaleModel->getAll($titolo);

        // Dati di supporto
        $condominiModel = new \App\Models\Condominio();
        $condomini = $condominiModel->getAll();

        $statiModel = new \App\Models\StatoManutenzione();
        $stati = $statiModel->getAll();

        $fornitoriModel = new \App\Models\Fornitore();
        $fornitori = $fornitoriModel->getAll();

        $utentiModel = new \App\Models\User();
        $utenti = $utentiModel->getAll();

        // Pagina e paginazione
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $itemsPerPage = 30;
        $totalItems = count($legali);
        $pages = ceil($totalItems / $itemsPerPage);
        $offset = ($page - 1) * $itemsPerPage;
        $legali = array_slice($legali, $offset, $itemsPerPage);

        include BASE_PATH . 'app/Views/attivita/legale/index.php';
    }

    public function detail() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo "ID Legale mancante.";
            exit;
        }
        $legale = $this->legaleModel->getById($id);
        if (!$legale) {
            echo "Legale non trovato.";
            exit;
        }
        $documentoModel = new \App\Models\LegaleDocumento();
        $chatModel = new \App\Models\LegaleChat();
        $documenti = $documentoModel->getByLegale($id);
        $chatMessages = $chatModel->getByLegale($id);

        // Dati di supporto
        $condominiModel = new \App\Models\Condominio();
        $condomini = $condominiModel->getAll();

        $statiModel = new \App\Models\StatoManutenzione();
        $stati = $statiModel->getAll();

        $fornitoriModel = new \App\Models\Fornitore();
        $fornitori = $fornitoriModel->getAll();

        $utentiModel = new \App\Models\User();
        $utenti = $utentiModel->getAll();

        include BASE_PATH . 'app/Views/attivita/legale/detail.php';
    }

    public function save() {
        $data = [
            'IDCondominio' => $_POST['IDCondominio'] ?? null,
            'IDFornitore'  => $_POST['IDFornitore'] ?? null,
            'IDStato'      => $_POST['IDStato'] ?? null,
            'DataApertura' => $_POST['DataApertura'] ?? null,
            'Titolo'       => $_POST['Titolo'] ?? '',
            'Descrizione'  => $_POST['Descrizione'] ?? ''
        ];
        if (!$data['IDCondominio'] || !$data['IDStato'] || !$data['DataApertura'] || empty($data['Titolo'])) {
            echo "Dati mancanti.";
            exit;
        }
        if (isset($_POST['IDLegale']) && !empty($_POST['IDLegale'])) {
            $data['IDLegale'] = $_POST['IDLegale'];
            $result = $this->legaleModel->update($data);
        } else {
            $result = $this->legaleModel->create($data);
        }
        if ($result) {
            header("Location: " . BASE_URL . "/attivita/legale");
            exit;
        } else {
            echo "Errore durante il salvataggio del legale.";
            exit;
        }
    }

    public function delete() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo "ID Legale mancante.";
            exit;
        }
        $this->legaleModel->delete($id);
        header("Location: " . BASE_URL . "/attivita/legale");
        exit;
    }
    
    // Metodo per inviare la mail (analogamente a sinistri)
    public function sendMail() {
        $idLegale = $_GET['id'] ?? null;
        if (!$idLegale) {
            echo "ID Legale mancante.";
            exit;
        }
        $legale = $this->legaleModel->getById($idLegale);
        if (!$legale) {
            echo "Legale non trovato.";
            exit;
        }
        
        $condominiModel = new \App\Models\Condominio();
        $condominio = $condominiModel->getById($legale['IDCondominio']);
        $nomeCondominio = $condominio ? $condominio['Nome'] : "Condominio sconosciuto";
        
        $emailBody = "Spett.le Agenzia,\n";
        $emailBody .= "in merito al condominio $nomeCondominio con polizza 12345, si richiede apertura pratica legale " . $legale['Titolo'] . " per il quale aggiungiamo i seguenti dettagli: " . $legale['Descrizione'] . ". Si richiede quando possibile di inviarci il numero della pratica. In allegato troverete i documenti utili alla gestione.\n";
        $emailBody .= "Cordiali saluti,\nGPI Srl";
        
        // Crea il file ZIP con i documenti (non ci sono foto in questa sezione)
        $zip = new \ZipArchive();
        $zipFilename = tempnam(sys_get_temp_dir(), 'legale_') . '.zip';
        if ($zip->open($zipFilename, \ZipArchive::CREATE) !== TRUE) {
            echo "Impossibile creare il file ZIP.";
            exit;
        }
        $documentoModel = new \App\Models\LegaleDocumento();
        $docs = $documentoModel->getByLegale($idLegale);
        foreach($docs as $doc) {
            $filePath = BASE_PATH . 'storage/uploads/' . $doc['File'];
            if (file_exists($filePath)) {
                $zip->addFile($filePath, 'documenti/' . basename($filePath));
            }
        }
        $zip->close();
        
        $mailConfig = include BASE_PATH . 'app/Config/mail.php';
        
        require_once BASE_PATH . 'vendor/autoload.php';
        $mail = new \PHPMailer\PHPMailer\PHPMailer();
        $mail->isSMTP();
        $mail->Host       = $mailConfig['host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $mailConfig['username'];
        $mail->Password   = $mailConfig['password'];
        $mail->SMTPSecure = $mailConfig['smtp_secure'];
        $mail->Port       = $mailConfig['port'];
        
        $mail->setFrom($mailConfig['from_email'], $mailConfig['from_name']);
        $mail->addAddress('ivanmediaform@gmail.com');
        $mail->Subject = "Richiesta Apertura Pratica Legale";
        $mail->Body    = $emailBody;
        
        $mail->addAttachment($zipFilename, 'legale_' . $idLegale . '.zip');
        
        if(!$mail->send()){
            $errorMessage = urlencode("Errore nell'invio dell'email: " . $mail->ErrorInfo);
            header("Location: " . BASE_URL . "/attivita/legale/detail?id=" . $idLegale . "&mailSent=0&mailError=" . $errorMessage);
            exit;
        } else {
            header("Location: " . BASE_URL . "/attivita/legale/detail?id=" . $idLegale . "&mailSent=1");
            exit;
        }
        
        unlink($zipFilename);
        exit;
    }

    public function changeState() {
        $id = $_POST['IDLegale'] ?? null;
        $newState = $_POST['newState'] ?? null;
        if (!$id || !$newState) {
             echo "Dati mancanti per il cambio stato.";
             exit;
        }
        $result = $this->legaleModel->changeState($id, $newState);
        if ($result) {
             header("Location: " . BASE_URL . "/attivita/legale/detail?id=" . $id);
             exit;
        } else {
             echo "Errore nel cambio di stato.";
             exit;
        }
    }
    
}
