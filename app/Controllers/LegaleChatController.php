<?php
namespace App\Controllers;

use App\Models\LegaleChat;

class LegaleChatController {
    private $chatModel;
    public function __construct() {
        $this->chatModel = new LegaleChat();
    }
    public function save() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $data = [
            'IDLegale' => $_POST['IDLegale'] ?? null,
            'Testo'    => $_POST['Testo'] ?? '',
            'Data'     => date('Y-m-d'),
            'Orario'   => date('H:i:s'),
            'IDUser'   => $_SESSION['user']['IDUtente'] ?? null
        ];
        if (!$data['IDLegale'] || !$data['Testo'] || !$data['IDUser']) {
            echo "Dati mancanti per il messaggio in chat.";
            exit;
        }
        $this->chatModel->create($data);
        header("Location: " . BASE_URL . "/attivita/legale/detail?id=" . $data['IDLegale']);
        exit;
    }
}
