<?php
namespace App\Controllers;

use App\Models\Condominio;
use Mpdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CondominiController {

    private $condominioModel;

    public function __construct() {
        $this->condominioModel = new Condominio();
    }

    /**
     * Mostra l'elenco dei condominii, con eventuali filtri e paginazione.
     */
    public function index() {
        $nome = $_GET['nome'] ?? '';
        $condomini = $this->condominioModel->getAll($nome);
        $pages = 1;
        $page = $_GET['page'] ?? 1;
        include BASE_PATH . 'app/Views/anagrafiche/condomini/index.php';
    }

    /**
     * Salva un nuovo condominio o aggiorna uno esistente.
     */
    public function save() {
        $data = [
            'IDCondominio'  => $_POST['IDCondominio'] ?? null,
            'Nome'          => $_POST['nome'] ?? '',
            'Indirizzo'     => $_POST['indirizzo'] ?? '',
            'Cap'           => $_POST['cap'] ?? '',
            'Citta'         => $_POST['citta'] ?? '',
            'CodiceFiscale' => $_POST['codiceFiscale'] ?? ''
        ];

        if ($data['IDCondominio']) {
            $result = $this->condominioModel->update($data);
        } else {
            $result = $this->condominioModel->create($data);
        }
        header("Location: " . BASE_URL . "/anagrafiche/condomini");
        exit;
    }

    /**
     * Elimina definitivamente un condominio.
     */
    public function delete() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->condominioModel->delete($id);
        }
        header("Location: " . BASE_URL . "/anagrafiche/condomini");
        exit;
    }

    /**
     * Archivia un condominio.
     */
    public function archive() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->condominioModel->archive($id);
        }
        header("Location: " . BASE_URL . "/anagrafiche/condomini");
        exit;
    }

    /**
     * Esporta l'elenco dei condominii in PDF.
     */
    public function exportPdf() {
        $nome = $_GET['nome'] ?? '';
        $condomini = $this->condominioModel->getAll($nome);

        try {
            $mpdf = new Mpdf();
            $html = '<h1>Elenco Condomini</h1>';
            $html .= '<table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse; width: 100%;">';
            $html .= '<thead>
                        <tr>
                          <th>ID</th>
                          <th>Nome</th>
                          <th>Indirizzo</th>
                          <th>CAP</th>
                          <th>Città</th>
                          <th>Codice Fiscale</th>
                        </tr>
                      </thead><tbody>';
            foreach ($condomini as $row) {
                $html .= '<tr>
                            <td>' . $row['IDCondominio'] . '</td>
                            <td>' . htmlspecialchars($row['Nome']) . '</td>
                            <td>' . htmlspecialchars($row['Indirizzo']) . '</td>
                            <td>' . htmlspecialchars($row['Cap']) . '</td>
                            <td>' . htmlspecialchars($row['Citta']) . '</td>
                            <td>' . htmlspecialchars($row['CodiceFiscale']) . '</td>
                          </tr>';
            }
            $html .= '</tbody></table>';

            $mpdf->WriteHTML($html);
            $mpdf->Output('condomini.pdf', \Mpdf\Output\Destination::DOWNLOAD);
            exit;
        } catch (\Exception $e) {
            echo "Errore nell'esportazione in PDF: " . $e->getMessage();
        }
    }

    /**
     * Esporta l'elenco dei condominii in Excel.
     */
    public function exportExcel() {
        $nome = $_GET['nome'] ?? '';
        $condomini = $this->condominioModel->getAll($nome);

        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1', 'ID');
            $sheet->setCellValue('B1', 'Nome');
            $sheet->setCellValue('C1', 'Indirizzo');
            $sheet->setCellValue('D1', 'CAP');
            $sheet->setCellValue('E1', 'Città');
            $sheet->setCellValue('F1', 'Codice Fiscale');

            $rowNumber = 2;
            foreach ($condomini as $row) {
                $sheet->setCellValue('A' . $rowNumber, $row['IDCondominio']);
                $sheet->setCellValue('B' . $rowNumber, $row['Nome']);
                $sheet->setCellValue('C' . $rowNumber, $row['Indirizzo']);
                $sheet->setCellValue('D' . $rowNumber, $row['Cap']);
                $sheet->setCellValue('E' . $rowNumber, $row['Citta']);
                $sheet->setCellValue('F' . $rowNumber, $row['CodiceFiscale']);
                $rowNumber++;
            }
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="condomini.xlsx"');
            header('Cache-Control: max-age=0');
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
        } catch (\Exception $e) {
            echo "Errore nell'esportazione in Excel: " . $e->getMessage();
        }
    }
}
