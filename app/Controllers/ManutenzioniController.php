<?php

namespace App\Controllers;

use App\Models\Manutenzione;
use App\Models\StatoManutenzione;
use App\Models\Condominio;
use App\Models\Fornitore;
use App\Models\User;
use App\Models\ManutenzioneDocumento;
use App\Models\ManutenzioneChat;
use Mpdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ManutenzioniController
{

    private $manutenzioneModel;
    private $statoModel;

    public function __construct()
    {
        $this->manutenzioneModel = new Manutenzione();
        $this->statoModel = new StatoManutenzione();
    }

    /**
     * Visualizza l'elenco delle manutenzioni con filtri e paginazione.
     * Oltre alle manutenzioni, recupera anche condominii, fornitori e utenti
     * per popolare i menu a tendina e per visualizzare i nomi anziché gli ID.
     */
    public function index()
    {
        $titolo = $_GET['titolo'] ?? '';
        $manutenzioni = $this->manutenzioneModel->getAll($titolo);
        $stati = $this->statoModel->getAll();

        // Recupero dinamico per i campi referenziati
        $condomini = (new \App\Models\Condominio())->getAll();
        $fornitori = (new \App\Models\Fornitore())->getAll();
        $utenti = (new \App\Models\User())->getAll(); // Supponendo esista

        // Dummy per la paginazione
        $pages = 1;
        $page = $_GET['page'] ?? 1;

        include BASE_PATH . 'app/Views/attivita/manutenzioni/index.php';
    }

    public function save()
    {
        $data = [
            'IDManutenzione' => $_POST['IDManutenzione'] ?? null,
            'IDCondominio'   => $_POST['IDCondominio'] ?? '',
            'DataApertura'   => $_POST['dataApertura'] ?? '',
            'IDFornitore'    => $_POST['IDFornitore'] ?? null,
            'Titolo'         => $_POST['titolo'] ?? '',
            'Descrizione'    => $_POST['descrizione'] ?? '',
            'IDStato'        => $_POST['IDStato'] ?? '',
            'IDUser'         => $_POST['IDUser'] ?? ''
        ];

        if ($data['IDManutenzione']) {
            $result = $this->manutenzioneModel->update($data);
        } else {
            $result = $this->manutenzioneModel->create($data);
        }
        header("Location: " . BASE_URL . "/attivita/manutenzioni");
        exit;
    }

    public function delete()
    {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->manutenzioneModel->delete($id);
        }
        header("Location: " . BASE_URL . "/attivita/manutenzioni");
        exit;
    }

    public function archive()
    {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->manutenzioneModel->archive($id);
        }
        header("Location: " . BASE_URL . "/attivita/manutenzioni");
        exit;
    }

    public function changeState()
    {
        $id = $_POST['IDManutenzione'] ?? null;
        $newState = $_POST['newState'] ?? null;
        if ($id && $newState) {
            $this->manutenzioneModel->changeState($id, $newState);
        }
        header("Location: " . BASE_URL . "/attivita/manutenzioni");
        exit;
    }

    public function exportPdf()
    {
        $titolo = $_GET['titolo'] ?? '';
        $manutenzioni = $this->manutenzioneModel->getAll($titolo);

        try {
            $mpdf = new Mpdf();
            $html = '<h1>Elenco Manutenzioni</h1>';
            $html .= '<table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse; width: 100%;">';
            $html .= '<thead>
                        <tr>
                          <th>ID</th>
                          <th>Condominio</th>
                          <th>Data Apertura</th>
                          <th>Fornitore</th>
                          <th>Titolo</th>
                          <th>Descrizione</th>
                          <th>Stato</th>
                          <th>Assegnato a</th>
                        </tr>
                      </thead><tbody>';
            // Per esportare, si potrebbero usare join oppure mappare con array PHP
            // Qui ipotizziamo di visualizzare i valori grezzi
            foreach ($manutenzioni as $row) {
                $html .= '<tr>
                            <td>' . $row['IDManutenzione'] . '</td>
                            <td>' . htmlspecialchars($row['IDCondominio']) . '</td>
                            <td>' . htmlspecialchars($row['DataApertura']) . '</td>
                            <td>' . htmlspecialchars($row['IDFornitore']) . '</td>
                            <td>' . htmlspecialchars($row['Titolo']) . '</td>
                            <td>' . htmlspecialchars($row['Descrizione']) . '</td>
                            <td>' . htmlspecialchars($row['IDStato']) . '</td>
                            <td>' . htmlspecialchars($row['IDUser']) . '</td>
                          </tr>';
            }
            $html .= '</tbody></table>';
            $mpdf->WriteHTML($html);
            $mpdf->Output('manutenzioni.pdf', \Mpdf\Output\Destination::DOWNLOAD);
            exit;
        } catch (\Exception $e) {
            echo "Errore nell'esportazione in PDF: " . $e->getMessage();
        }
    }

    public function exportExcel()
    {
        $titolo = $_GET['titolo'] ?? '';
        $manutenzioni = $this->manutenzioneModel->getAll($titolo);

        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1', 'ID');
            $sheet->setCellValue('B1', 'Condominio');
            $sheet->setCellValue('C1', 'Data Apertura');
            $sheet->setCellValue('D1', 'Fornitore');
            $sheet->setCellValue('E1', 'Titolo');
            $sheet->setCellValue('F1', 'Descrizione');
            $sheet->setCellValue('G1', 'Stato');
            $sheet->setCellValue('H1', 'Assegnato a');

            $rowNumber = 2;
            foreach ($manutenzioni as $row) {
                $sheet->setCellValue('A' . $rowNumber, $row['IDManutenzione']);
                $sheet->setCellValue('B' . $rowNumber, $row['IDCondominio']);
                $sheet->setCellValue('C' . $rowNumber, $row['DataApertura']);
                $sheet->setCellValue('D' . $rowNumber, $row['IDFornitore']);
                $sheet->setCellValue('E' . $rowNumber, $row['Titolo']);
                $sheet->setCellValue('F' . $rowNumber, $row['Descrizione']);
                $sheet->setCellValue('G' . $rowNumber, $row['IDStato']);
                $sheet->setCellValue('H' . $rowNumber, $row['IDUser']);
                $rowNumber++;
            }
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="manutenzioni.xlsx"');
            header('Cache-Control: max-age=0');
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
        } catch (\Exception $e) {
            echo "Errore nell'esportazione in Excel: " . $e->getMessage();
        }
    }

    public function detail()
    {
        // Avvia la sessione se non già attiva
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $currentUserId = $_SESSION['user']['IDUtente'] ?? null;
        if (!$currentUserId) {
            echo "Utente non autenticato.";
            exit;
        }
    
        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo "ID manutenzione non specificato.";
            exit;
        }
        $manutenzione = $this->manutenzioneModel->getById($id);
        if (!$manutenzione) {
            echo "Manutenzione non trovata.";
            exit;
        }
    
        // Utilizza il nuovo modello per aggiornare il record di ultimo accesso
        $lettureModel = new \App\Models\ManutenzioniLetture();
        $lettureModel->updateLastAccess($id, $currentUserId);
    
        // Recupera i documenti allegati
        $docModel = new \App\Models\ManutenzioneDocumento();
        $documenti = $docModel->getByManutenzione($id);
    
        // Recupera i messaggi della chat per questa manutenzione
        $chatModel = new \App\Models\ManutenzioneChat();
        $chatMessages = $chatModel->getByManutenzione($id);
    
        // Recupera gli elenchi per il mapping
        $condomini = (new \App\Models\Condominio())->getAll();
        $fornitori = (new \App\Models\Fornitore())->getAll();
        $utenti = (new \App\Models\User())->getAll();
        $stati = $this->statoModel->getAll();
    
        // Crea le mappe per la visualizzazione
        $condominiMap = [];
        foreach ($condomini as $condominio) {
            $condominiMap[$condominio['IDCondominio']] = $condominio['Nome'];
        }
        $fornitoriMap = [];
        foreach ($fornitori as $fornitore) {
            $fornitoriMap[$fornitore['IDFornitore']] = $fornitore['Nome'];
        }
        $utentiMap = [];
        foreach ($utenti as $utente) {
            $utentiMap[$utente['IDUtente']] = $utente['Nome'];
        }
        $statiMap = [];
        foreach ($stati as $stato) {
            $statiMap[$stato['IDStato']] = $stato['Nome'];
        }
    
        // Recupera l'associazione eventuale del sinistro
        $assocModel = new \App\Models\ManutenzioniSinistri();
        $manutenzioneSinistro = $assocModel->getByManutenzione($id);
    
        // Recupera l'elenco dei sinistri aperti
        $sinistriAperti = (new \App\Models\Sinistro())->getAperti();
    
        include BASE_PATH . 'app/Views/attivita/manutenzioni/detail.php';
    }


    public function associaSinistro()
    {
        $IDManutenzione = $_POST['IDManutenzione'] ?? null;
        $IDSinistro = $_POST['IDSinistro'] ?? null;
        if (!$IDManutenzione || !$IDSinistro) {
            die("Dati mancanti per l'associazione.");
        }

        $assocModel = new \App\Models\ManutenzioniSinistri();
        $result = $assocModel->createAssociation([
            'IDManutenzione' => $IDManutenzione,
            'IDSinistro'     => $IDSinistro
        ]);

        header("Location: " . BASE_URL . "/attivita/manutenzioni/detail?id=" . $IDManutenzione);
        exit;
    }

    public function sendMail()
    {
        $idManutenzione = $_GET['id'] ?? null;
        if (!$idManutenzione) {
            echo "ID Manutenzione mancante.";
            exit;
        }

        // Recupera la manutenzione
        $manutenzione = $this->manutenzioneModel->getById($idManutenzione);
        if (!$manutenzione) {
            echo "Manutenzione non trovata.";
            exit;
        }

        // Recupera il condominio per ottenere il nome
        $condominio = (new \App\Models\Condominio())->getById($manutenzione['IDCondominio']);
        $nomeCondominio = $condominio ? $condominio['Nome'] : "Condominio sconosciuto";

        // Recupera il fornitore per ottenere il nome
        $fornitore = (new \App\Models\Fornitore())->getById($manutenzione['IDFornitore']);
        $nomeFornitore = $fornitore ? $fornitore['Nome'] : "Fornitore sconosciuto";

        // Componi l'oggetto e il corpo della mail
        $subject = "$nomeCondominio - Richiesta di intervento";
        $emailBody = "Spett.le $nomeFornitore,\n";
        $emailBody .= "con la presente si richiede intervento nel condominio in oggetto in merito alla problematica: " . $manutenzione['Titolo'] . ". ";
        $emailBody .= "Per questa situazione si dettaglia che: " . $manutenzione['Descrizione'] . ".\n";
        $emailBody .= "Restiamo in attesa di cortese riscontro su questa mail o su Whatsapp al numero 3515160645.\n";
        $emailBody .= "Cordiali saluti\nGPI SRL";

        // Carica la configurazione email (assicurati che il file app/Config/mail.php esista e contenga i parametri necessari)
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
        // Destinatario statico
        $mail->addAddress('ivanmediaform@gmail.com');
        $mail->Subject = $subject;
        $mail->Body    = $emailBody;

        if (!$mail->send()) {
            echo "Errore nell'invio dell'email: " . $mail->ErrorInfo;
        } else {
            header("Location: " . BASE_URL . "/attivita/manutenzioni/detail?id=" . $idManutenzione . "&mailSent=1");
        }

        exit;
    }
}
