<?php

namespace App\Controllers;

use App\Models\Sinistro;

class SinistriController
{

    private $sinistroModel;

    public function __construct()
    {
        $this->sinistroModel = new Sinistro();
    }

    // Elenco sinistri
    public function index()
    {
        $titolo = $_GET['titolo'] ?? '';
        $sinistri = $this->sinistroModel->getAll($titolo);

        // Carica dati di supporto per la view
        $condominiModel = new \App\Models\Condominio();
        $condomini = $condominiModel->getAll();

        $statiModel = new \App\Models\StatoManutenzione();
        $stati = $statiModel->getAll();

        $utentiModel = new \App\Models\User();
        $utenti = $utentiModel->getAll();

        $studiPeritaliModel = new \App\Models\StudiPeritali();
        $studiPeritali = $studiPeritaliModel->getAll();

        // Pagina corrente
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $itemsPerPage = 30;
        $totalItems = count($sinistri);
        $pages = ceil($totalItems / $itemsPerPage);

        // Seleziona i sinistri per la pagina corrente
        $offset = ($page - 1) * $itemsPerPage;
        $sinistri = array_slice($sinistri, $offset, $itemsPerPage);

        include BASE_PATH . 'app/Views/attivita/sinistri/index.php';
    }



    // Dettaglio di un sinistro
    public function detail()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo "ID Sinistro mancante.";
            exit;
        }
        $sinistro = $this->sinistroModel->getById($id);
        if (!$sinistro) {
            echo "Sinistro non trovato.";
            exit;
        }
        // Recupera documenti, chat e foto
        $documentoModel = new \App\Models\SinistroDocumento();
        $chatModel      = new \App\Models\SinistroChat();
        $fotoModel      = new \App\Models\SinistroFoto();

        $documenti    = $documentoModel->getBySinistro($id);
        $chatMessages = $chatModel->getBySinistro($id);
        $foto         = $fotoModel->getBySinistro($id);

        include BASE_PATH . 'app/Views/attivita/sinistri/detail.php';
    }

    // Salva un nuovo sinistro
    public function save()
    {
        $data = [
            'IDCondominio'     => $_POST['IDCondominio'] ?? null,
            'IDStato'          => $_POST['IDStato'] ?? null,
            'DataApertura'     => $_POST['DataApertura'] ?? null,
            'Titolo'           => $_POST['Titolo'] ?? '',
            'Descrizione'      => $_POST['Descrizione'] ?? '',
            'Numero'           => $_POST['Numero'] ?? '',
            'IDStudioPeritale' => $_POST['IDStudioPeritale'] ?? null, // Campo opzionale
            'DataChiusura'     => $_POST['DataChiusura'] ?? null,
            'Rimborso'         => $_POST['Rimborso'] ?? null
        ];

        // Verifica solo i campi obbligatori
        if (!$data['IDCondominio'] || !$data['IDStato'] || !$data['DataApertura'] || empty($data['Titolo'])) {
            echo "Dati mancanti.";
            exit;
        }

        $result = $this->sinistroModel->create($data);
        if ($result) {
            header("Location: " . BASE_URL . "/attivita/sinistri");
            exit;
        } else {
            echo "Errore durante il salvataggio del sinistro.";
            exit;
        }
    }


    // Aggiorna un sinistro esistente
    public function update()
    {
        $data = [
            'IDCondominio'   => $_POST['IDCondominio'] ?? null,
            'IDStato'        => $_POST['IDStato'] ?? null,
            'DataApertura'   => $_POST['DataApertura'] ?? null,
            'Titolo'         => $_POST['Titolo'] ?? '',
            'Descrizione'    => $_POST['Descrizione'] ?? '',
            'Numero'         => $_POST['Numero'] ?? '',
            'IDStudioPeritale' => $_POST['IDStudioPeritale'] ?? null,
            'DataChiusura'   => $_POST['DataChiusura'] ?? null,
            'Rimborso'       => $_POST['Rimborso'] ?? null,
            'IDSinistro'     => $_POST['IDSinistro'] ?? null
        ];

        if (!$data['IDSinistro'] || !$data['IDCondominio'] || !$data['IDStato'] || !$data['DataApertura'] || empty($data['Titolo'])) {
            echo "Dati mancanti per l'aggiornamento.";
            exit;
        }

        $result = $this->sinistroModel->update($data);
        if ($result) {
            header("Location: " . BASE_URL . "/attivita/sinistri");
            exit;
        } else {
            echo "Errore durante l'aggiornamento del sinistro.";
            exit;
        }
    }

    // Elimina definitivamente un sinistro
    public function delete()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo "ID Sinistro mancante.";
            exit;
        }
        $this->sinistroModel->delete($id);
        header("Location: " . BASE_URL . "/attivita/sinistri");
        exit;
    }

    // Archivia un sinistro
    public function archive()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo "ID Sinistro mancante.";
            exit;
        }
        $this->sinistroModel->archive($id);
        header("Location: " . BASE_URL . "/attivita/sinistri");
        exit;
    }

    // Cambia lo stato di un sinistro
    public function changeState()
    {
        $id       = $_POST['IDSinistro'] ?? null;
        $newState = $_POST['newState'] ?? null;
        if (!$id || !$newState) {
            echo "Dati mancanti per il cambio stato.";
            exit;
        }
        $this->sinistroModel->changeState($id, $newState);
        header("Location: " . BASE_URL . "/attivita/sinistri/detail?id=" . $id);
        exit;
    }

    public function sendMail() {
        $idSinistro = $_GET['id'] ?? null;
        if (!$idSinistro) {
            echo "ID Sinistro mancante.";
            exit;
        }
        
        // Recupera il sinistro
        $sinistro = $this->sinistroModel->getById($idSinistro);
        if (!$sinistro) {
            echo "Sinistro non trovato.";
            exit;
        }
        
        // Recupera il condominio per ottenere il Nome
        $condominiModel = new \App\Models\Condominio();
        $condominio = $condominiModel->getById($sinistro['IDCondominio']);
        $nomeCondominio = $condominio ? $condominio['Nome'] : "Condominio sconosciuto";
        
        // Compone il corpo della mail
        $emailBody = "Spett.le Agenzia,\n";
        $emailBody .= "in merito al condominio $nomeCondominio con polizza 12345, si richiede apertura sinistro " . $sinistro['Titolo'] . " per il quale aggiungiamo i seguenti dettagli: " . $sinistro['Descrizione'] . ". Si richiede quando possibile di inviarci il numero sinistro. Si allegano le foto in nostro possesso e i documenti utili ad una positiva gestione della pratica.\n";
        $emailBody .= "Cordiali saluti,\nGPI Srl";
        
        // Crea il file ZIP con foto e documenti
        $zip = new \ZipArchive();
        $zipFilename = tempnam(sys_get_temp_dir(), 'sinistro_') . '.zip';
        if ($zip->open($zipFilename, \ZipArchive::CREATE) !== TRUE) {
            echo "Impossibile creare il file ZIP.";
            exit;
        }
        
        // Aggiungi le foto al file ZIP
        $fotoModel = new \App\Models\SinistroFoto();
        $fotos = $fotoModel->getBySinistro($idSinistro);
        foreach ($fotos as $foto) {
            $filePath = BASE_PATH . 'storage/uploads/' . $foto['File'];
            if (file_exists($filePath)) {
                $zip->addFile($filePath, 'foto/' . basename($filePath));
            }
        }
        
        // Aggiungi i documenti al file ZIP
        $documentoModel = new \App\Models\SinistroDocumento();
        $docs = $documentoModel->getBySinistro($idSinistro);
        foreach ($docs as $doc) {
            $filePath = BASE_PATH . 'storage/uploads/' . $doc['File'];
            if (file_exists($filePath)) {
                $zip->addFile($filePath, 'documenti/' . basename($filePath));
            }
        }
        $zip->close();
        
        // Carica la configurazione email dal file app/Config/mail.php
        $mailConfig = include BASE_PATH . 'app/Config/mail.php';
        
        // Configura PHPMailer
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
        $mail->addAddress('ivanmediaform@gmail.com');  // Destinatario statico
        $mail->Subject = "Richiesta Apertura Sinistro";
        $mail->Body    = $emailBody;
        
        // Allega il file ZIP
        $mail->addAttachment($zipFilename, 'sinistro_' . $idSinistro . '.zip');
        
        if (!$mail->send()) {
            echo "Errore nell'invio dell'email: " . $mail->ErrorInfo;
        } else {
            header("Location: " . BASE_URL . "/attivita/sinistri/detail?id=" . $idSinistro . "&mailSent=1");
        }
        
        // Elimina il file ZIP temporaneo
        unlink($zipFilename);
        exit;
    }
    
}
