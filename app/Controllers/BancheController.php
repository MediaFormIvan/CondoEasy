<?php
namespace App\Controllers;

use App\Models\Banca;
use Mpdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class BancheController {

    private $bancaModel;

    public function __construct() {
        $this->bancaModel = new Banca();
    }

    /**
     * Visualizza l'elenco delle banche, con eventuale filtro per nome.
     */
    public function index() {
        $nome = $_GET['nome'] ?? '';
        $banche = $this->bancaModel->getAll($nome);
        $pages = 1;
        $page = $_GET['page'] ?? 1;
        include BASE_PATH . 'app/Views/anagrafiche/banche/index.php';
    }

    /**
     * Salva una nuova banca o aggiorna una esistente.
     */
    public function save() {
        $data = [
            'IDBanca' => $_POST['IDBanca'] ?? null,
            'Nome'    => $_POST['nome'] ?? ''
        ];

        if ($data['IDBanca']) {
            $result = $this->bancaModel->update($data);
        } else {
            $result = $this->bancaModel->create($data);
        }
        header("Location: " . BASE_URL . "/anagrafiche/banche");
        exit;
    }

    /**
     * Elimina definitivamente una banca.
     */
    public function delete() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->bancaModel->delete($id);
        }
        header("Location: " . BASE_URL . "/anagrafiche/banche");
        exit;
    }

    /**
     * Archivia una banca.
     */
    public function archive() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->bancaModel->archive($id);
        }
        header("Location: " . BASE_URL . "/anagrafiche/banche");
        exit;
    }

    /**
     * Esporta l'elenco delle banche in PDF.
     */
    public function exportPdf() {
        $nome = $_GET['nome'] ?? '';
        $banche = $this->bancaModel->getAll($nome);

        try {
            $mpdf = new Mpdf();
            $html = '<h1>Elenco Banche</h1>';
            $html .= '<table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse; width: 100%;">';
            $html .= '<thead>
                        <tr>
                          <th>ID</th>
                          <th>Nome</th>
                        </tr>
                      </thead><tbody>';
            foreach ($banche as $row) {
                $html .= '<tr>
                            <td>' . $row['IDBanca'] . '</td>
                            <td>' . htmlspecialchars($row['Nome']) . '</td>
                          </tr>';
            }
            $html .= '</tbody></table>';
            $mpdf->WriteHTML($html);
            $mpdf->Output('banche.pdf', \Mpdf\Output\Destination::DOWNLOAD);
            exit;
        } catch (\Exception $e) {
            echo "Errore nell'esportazione in PDF: " . $e->getMessage();
        }
    }

    /**
     * Esporta l'elenco delle banche in Excel.
     */
    public function exportExcel() {
        $nome = $_GET['nome'] ?? '';
        $banche = $this->bancaModel->getAll($nome);

        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1', 'ID');
            $sheet->setCellValue('B1', 'Nome');

            $rowNumber = 2;
            foreach ($banche as $row) {
                $sheet->setCellValue('A' . $rowNumber, $row['IDBanca']);
                $sheet->setCellValue('B' . $rowNumber, $row['Nome']);
                $rowNumber++;
            }
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="banche.xlsx"');
            header('Cache-Control: max-age=0');
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
        } catch (\Exception $e) {
            echo "Errore nell'esportazione in Excel: " . $e->getMessage();
        }
    }
}
