<?php
namespace App\Controllers;

use App\Models\Persona;
use Mpdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PersoneController {

    private $personaModel;

    public function __construct() {
        $this->personaModel = new Persona();
    }

    /**
     * Visualizza l'elenco delle persone, con eventuale filtro su Nome e Cognome.
     */
    public function index() {
        $nome = $_GET['nome'] ?? '';
        $cognome = $_GET['cognome'] ?? '';
        $persone = $this->personaModel->getAll($nome, $cognome);
        $pages = 1;
        $page = $_GET['page'] ?? 1;
        include BASE_PATH . 'app/Views/anagrafiche/persone/index.php';
    }

    /**
     * Salva una nuova persona o aggiorna una esistente.
     */
    public function save() {
        $data = [
            'IDPersona'     => $_POST['IDPersona'] ?? null,
            'Nome'          => $_POST['nome'] ?? '',
            'Cognome'       => $_POST['cognome'] ?? '',
            'CodiceFiscale' => $_POST['codiceFiscale'] ?? '',
            'Indirizzo'     => $_POST['indirizzo'] ?? '',
            'Cap'           => $_POST['cap'] ?? '',
            'Citta'         => $_POST['citta'] ?? '',
            'Provincia'     => $_POST['provincia'] ?? '',
            'Telefono'      => $_POST['telefono'] ?? '',
            'Telefono2'     => $_POST['telefono2'] ?? '',
            'Mail'          => $_POST['mail'] ?? '',
            'Pec'           => $_POST['pec'] ?? '',
            'Note'          => $_POST['note'] ?? ''
        ];

        if ($data['IDPersona']) {
            $result = $this->personaModel->update($data);
        } else {
            $result = $this->personaModel->create($data);
        }
        header("Location: " . BASE_URL . "/anagrafiche/persone");
        exit;
    }

    /**
     * Elimina definitivamente una persona.
     */
    public function delete() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->personaModel->delete($id);
        }
        header("Location: " . BASE_URL . "/anagrafiche/persone");
        exit;
    }

    /**
     * Archivia una persona.
     */
    public function archive() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->personaModel->archive($id);
        }
        header("Location: " . BASE_URL . "/anagrafiche/persone");
        exit;
    }

    /**
     * Esporta l'elenco delle persone in PDF.
     */
    public function exportPdf() {
        $nome = $_GET['nome'] ?? '';
        $cognome = $_GET['cognome'] ?? '';
        $persone = $this->personaModel->getAll($nome, $cognome);

        try {
            $mpdf = new Mpdf();
            $html = '<h1>Elenco Persone</h1>';
            $html .= '<table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse; width: 100%;">';
            $html .= '<thead>
                        <tr>
                          <th>ID</th>
                          <th>Nome</th>
                          <th>Cognome</th>
                          <th>Codice Fiscale</th>
                          <th>Indirizzo</th>
                          <th>CAP</th>
                          <th>Città</th>
                          <th>Provincia</th>
                          <th>Telefono</th>
                          <th>Telefono2</th>
                          <th>Mail</th>
                          <th>Pec</th>
                        </tr>
                      </thead><tbody>';
            foreach ($persone as $row) {
                $html .= '<tr>
                            <td>' . $row['IDPersona'] . '</td>
                            <td>' . htmlspecialchars($row['Nome']) . '</td>
                            <td>' . htmlspecialchars($row['Cognome']) . '</td>
                            <td>' . htmlspecialchars($row['CodiceFiscale']) . '</td>
                            <td>' . htmlspecialchars($row['Indirizzo']) . '</td>
                            <td>' . htmlspecialchars($row['Cap']) . '</td>
                            <td>' . htmlspecialchars($row['Citta']) . '</td>
                            <td>' . htmlspecialchars($row['Provincia']) . '</td>
                            <td>' . htmlspecialchars($row['Telefono']) . '</td>
                            <td>' . htmlspecialchars($row['Telefono2']) . '</td>
                            <td>' . htmlspecialchars($row['Mail']) . '</td>
                            <td>' . htmlspecialchars($row['Pec']) . '</td>
                          </tr>';
            }
            $html .= '</tbody></table>';
            $mpdf->WriteHTML($html);
            $mpdf->Output('persone.pdf', \Mpdf\Output\Destination::DOWNLOAD);
            exit;
        } catch (\Exception $e) {
            echo "Errore nell'esportazione in PDF: " . $e->getMessage();
        }
    }

    /**
     * Esporta l'elenco delle persone in Excel.
     */
    public function exportExcel() {
        $nome = $_GET['nome'] ?? '';
        $cognome = $_GET['cognome'] ?? '';
        $persone = $this->personaModel->getAll($nome, $cognome);

        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            // Header della tabella
            $sheet->setCellValue('A1', 'ID');
            $sheet->setCellValue('B1', 'Nome');
            $sheet->setCellValue('C1', 'Cognome');
            $sheet->setCellValue('D1', 'Codice Fiscale');
            $sheet->setCellValue('E1', 'Indirizzo');
            $sheet->setCellValue('F1', 'CAP');
            $sheet->setCellValue('G1', 'Città');
            $sheet->setCellValue('H1', 'Provincia');
            $sheet->setCellValue('I1', 'Telefono');
            $sheet->setCellValue('J1', 'Telefono2');
            $sheet->setCellValue('K1', 'Mail');
            $sheet->setCellValue('L1', 'Pec');

            $rowNumber = 2;
            foreach ($persone as $row) {
                $sheet->setCellValue('A' . $rowNumber, $row['IDPersona']);
                $sheet->setCellValue('B' . $rowNumber, $row['Nome']);
                $sheet->setCellValue('C' . $rowNumber, $row['Cognome']);
                $sheet->setCellValue('D' . $rowNumber, $row['CodiceFiscale']);
                $sheet->setCellValue('E' . $rowNumber, $row['Indirizzo']);
                $sheet->setCellValue('F' . $rowNumber, $row['Cap']);
                $sheet->setCellValue('G' . $rowNumber, $row['Citta']);
                $sheet->setCellValue('H' . $rowNumber, $row['Provincia']);
                $sheet->setCellValue('I' . $rowNumber, $row['Telefono']);
                $sheet->setCellValue('J' . $rowNumber, $row['Telefono2']);
                $sheet->setCellValue('K' . $rowNumber, $row['Mail']);
                $sheet->setCellValue('L' . $rowNumber, $row['Pec']);
                $rowNumber++;
            }
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="persone.xlsx"');
            header('Cache-Control: max-age=0');
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
        } catch (\Exception $e) {
            echo "Errore nell'esportazione in Excel: " . $e->getMessage();
        }
    }
    public function detailAjax() {
        error_log("detailAjax: Inizio esecuzione"); //AGGIUNGI QUESTO LOG
        $IDPersona = $_GET['id'] ?? null;
        if (!$IDPersona) {
            error_log("detailAjax: IDPersona non fornito");
            echo json_encode(['error' => 'IDPersona non fornito']);
            exit;
        }
        $persona = $this->personaModel->getById($IDPersona);
        if (!$persona) {
            error_log("detailAjax: Persona non trovata per ID $IDPersona");
            echo json_encode(['error' => 'Persona non trovata']);
            exit;
        }
        error_log("detailAjax: Persona trovata: " . print_r($persona, true));
        error_log("detailAjax: Prima di json_encode"); //AGGIUNGI QUESTO LOG
        header('Content-Type: application/json');
        echo json_encode($persona);
        error_log("detailAjax: Dopo json_encode");  //AGGIUNGI QUESTO LOG
        exit;
    }
    
    
    
}
