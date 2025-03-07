<?php
namespace App\Controllers;

use App\Models\TipoFornitore;
use Mpdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class TipiFornitoriController {

    private $tipoFornitoreModel;

    public function __construct() {
        $this->tipoFornitoreModel = new TipoFornitore();
    }

    /**
     * Visualizza l'elenco dei tipi fornitore, con eventuale filtro sul nome.
     */
    public function index() {
        $nome = $_GET['nome'] ?? '';
        $tipi = $this->tipoFornitoreModel->getAll($nome);
        $pages = 1;
        $page = $_GET['page'] ?? 1;
        include BASE_PATH . 'app/Views/anagrafiche/tipifornitori/index.php';
    }

    /**
     * Salva un nuovo tipo fornitore o aggiorna uno esistente.
     */
    public function save() {
        $data = [
            'IDTipoFornitore' => $_POST['IDTipoFornitore'] ?? null,
            'Nome'            => $_POST['nome'] ?? ''
        ];

        if ($data['IDTipoFornitore']) {
            $result = $this->tipoFornitoreModel->update($data);
        } else {
            $result = $this->tipoFornitoreModel->create($data);
        }
        header("Location: " . BASE_URL . "/anagrafiche/tipifornitori");
        exit;
    }

    /**
     * Elimina definitivamente un tipo fornitore.
     */
    public function delete() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->tipoFornitoreModel->delete($id);
        }
        header("Location: " . BASE_URL . "/anagrafiche/tipifornitori");
        exit;
    }

    /**
     * Archivia un tipo fornitore.
     */
    public function archive() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->tipoFornitoreModel->archive($id);
        }
        header("Location: " . BASE_URL . "/anagrafiche/tipifornitori");
        exit;
    }

    /**
     * Esporta l'elenco dei tipi fornitore in PDF.
     */
    public function exportPdf() {
        $nome = $_GET['nome'] ?? '';
        $tipi = $this->tipoFornitoreModel->getAll($nome);

        try {
            $mpdf = new Mpdf();
            $html = '<h1>Elenco Tipi Fornitore</h1>';
            $html .= '<table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse; width: 100%;">';
            $html .= '<thead>
                        <tr>
                          <th>ID</th>
                          <th>Nome</th>
                        </tr>
                      </thead><tbody>';
            foreach ($tipi as $row) {
                $html .= '<tr>
                            <td>' . $row['IDTipoFornitore'] . '</td>
                            <td>' . htmlspecialchars($row['Nome']) . '</td>
                          </tr>';
            }
            $html .= '</tbody></table>';
            $mpdf->WriteHTML($html);
            $mpdf->Output('tipifornitori.pdf', \Mpdf\Output\Destination::DOWNLOAD);
            exit;
        } catch (\Exception $e) {
            echo "Errore nell'esportazione in PDF: " . $e->getMessage();
        }
    }

    /**
     * Esporta l'elenco dei tipi fornitore in Excel.
     */
    public function exportExcel() {
        $nome = $_GET['nome'] ?? '';
        $tipi = $this->tipoFornitoreModel->getAll($nome);

        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1', 'ID');
            $sheet->setCellValue('B1', 'Nome');

            $rowNumber = 2;
            foreach ($tipi as $row) {
                $sheet->setCellValue('A' . $rowNumber, $row['IDTipoFornitore']);
                $sheet->setCellValue('B' . $rowNumber, $row['Nome']);
                $rowNumber++;
            }
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="tipifornitori.xlsx"');
            header('Cache-Control: max-age=0');
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
        } catch (\Exception $e) {
            echo "Errore nell'esportazione in Excel: " . $e->getMessage();
        }
    }
}
