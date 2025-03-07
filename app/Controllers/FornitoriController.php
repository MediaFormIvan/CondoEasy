<?php
namespace App\Controllers;

use App\Models\Fornitore;
use App\Models\TipoFornitore;
use Mpdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class FornitoriController {

    private $fornitoreModel;
    private $tipoFornitoreModel;

    public function __construct() {
        $this->fornitoreModel = new Fornitore();
        $this->tipoFornitoreModel = new TipoFornitore();
    }

    public function index() {
        $nome = $_GET['nome'] ?? '';
        $idTipoFornitore = $_GET['idTipoFornitore'] ?? '';

        $fornitori = $this->fornitoreModel->getAll($nome, $idTipoFornitore);
        $tipiFornitori = $this->tipoFornitoreModel->getAll();

        $pages = 1;
        $page = $_GET['page'] ?? 1;

        include BASE_PATH . 'app/Views/anagrafiche/fornitori/index.php';
    }

    public function save() {
        $data = [
            'IDFornitore'     => $_POST['IDFornitore'] ?? null,
            'Nome'            => $_POST['nome'] ?? '',
            'IDTipoFornitore' => $_POST['idTipoFornitore'] ?? '',
            'Indirizzo'       => $_POST['indirizzo'] ?? '',
            'Cap'             => $_POST['cap'] ?? '',
            'Citta'           => $_POST['citta'] ?? '',
            'PartitaIva'      => $_POST['partitaIva'] ?? '',
            'CodiceFiscale'   => $_POST['codiceFiscale'] ?? '',
            'IBAN'            => $_POST['iban'] ?? '',
            'Telefono'        => $_POST['telefono'] ?? '',
            'Mail'            => $_POST['mail'] ?? '',
            'PEC'             => $_POST['pec'] ?? '',
            'Note'            => $_POST['note'] ?? '',
            'CodiceRitenuta'  => $_POST['codiceRitenuta'] ?? '',
            'Ritenuta'        => isset($_POST['ritenuta']) ? 1 : 0
        ];

        if (empty($data['IDTipoFornitore'])) {
            die("Errore: il campo 'Tipo Fornitore' è obbligatorio. Seleziona una tipologia valida.");
        }

        if ($data['IDFornitore']) {
            $result = $this->fornitoreModel->update($data);
        } else {
            $result = $this->fornitoreModel->create($data);
        }

        header("Location: " . BASE_URL . "/anagrafiche/fornitori");
        exit;
    }

    public function delete() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->fornitoreModel->delete($id);
        }
        header("Location: " . BASE_URL . "/anagrafiche/fornitori");
        exit;
    }

    public function archive() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->fornitoreModel->archive($id);
        }
        header("Location: " . BASE_URL . "/anagrafiche/fornitori");
        exit;
    }

    public function exportPdf() {
        // Assicurati di non aver inviato output prima di questo punto
        $nome = $_GET['nome'] ?? '';
        $idTipoFornitore = $_GET['idTipoFornitore'] ?? '';
        $fornitori = $this->fornitoreModel->getAll($nome, $idTipoFornitore);

        try {
            $mpdf = new Mpdf();
            $html = '<h1>Elenco Fornitori</h1>';
            $html .= '<table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse; width: 100%;">';
            $html .= '<thead>
                        <tr>
                          <th>ID</th>
                          <th>Nome</th>
                          <th>Tipologia</th>
                          <th>Indirizzo</th>
                          <th>CAP</th>
                          <th>Città</th>
                          <th>Partita IVA</th>
                        </tr>
                      </thead><tbody>';
            foreach ($fornitori as $row) {
                // Se desideri sostituire l'ID del tipo con il nome, potresti fare una lookup qui (o gestirlo in un metodo)
                $html .= '<tr>
                            <td>' . $row['IDFornitore'] . '</td>
                            <td>' . htmlspecialchars($row['Nome']) . '</td>
                            <td>' . htmlspecialchars($row['IDTipoFornitore']) . '</td>
                            <td>' . htmlspecialchars($row['Indirizzo']) . '</td>
                            <td>' . htmlspecialchars($row['Cap']) . '</td>
                            <td>' . htmlspecialchars($row['Citta']) . '</td>
                            <td>' . htmlspecialchars($row['PartitaIva']) . '</td>
                          </tr>';
            }
            $html .= '</tbody></table>';

            $mpdf->WriteHTML($html);
            // Utilizza la costante per la destinazione DOWNLOAD
            $mpdf->Output('fornitori.pdf', \Mpdf\Output\Destination::DOWNLOAD);
            exit;
        } catch (\Exception $e) {
            echo "Errore nell'esportazione in PDF: " . $e->getMessage();
        }
    }

    public function exportExcel() {
        $nome = $_GET['nome'] ?? '';
        $idTipoFornitore = $_GET['idTipoFornitore'] ?? '';
        $fornitori = $this->fornitoreModel->getAll($nome, $idTipoFornitore);

        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            // Header della tabella
            $sheet->setCellValue('A1', 'ID');
            $sheet->setCellValue('B1', 'Nome');
            $sheet->setCellValue('C1', 'Tipologia');
            $sheet->setCellValue('D1', 'Indirizzo');
            $sheet->setCellValue('E1', 'CAP');
            $sheet->setCellValue('F1', 'Città');
            $sheet->setCellValue('G1', 'Partita IVA');

            $rowNumber = 2;
            foreach ($fornitori as $row) {
                $sheet->setCellValue('A' . $rowNumber, $row['IDFornitore']);
                $sheet->setCellValue('B' . $rowNumber, $row['Nome']);
                $sheet->setCellValue('C' . $rowNumber, $row['IDTipoFornitore']);
                $sheet->setCellValue('D' . $rowNumber, $row['Indirizzo']);
                $sheet->setCellValue('E' . $rowNumber, $row['Cap']);
                $sheet->setCellValue('F' . $rowNumber, $row['Citta']);
                $sheet->setCellValue('G' . $rowNumber, $row['PartitaIva']);
                $rowNumber++;
            }
            // Imposta gli header per il download dell'Excel
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="fornitori.xlsx"');
            header('Cache-Control: max-age=0');
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
        } catch (\Exception $e) {
            echo "Errore nell'esportazione in Excel: " . $e->getMessage();
        }
    }
}
