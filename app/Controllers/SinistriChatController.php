<?php
namespace App\Controllers;

use App\Models\SinistroChat;

class SinistriChatController {

    private $chatModel;

    public function __construct() {
        $this->chatModel = new SinistroChat();
    }

    public function save() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $data = [
            'IDSinistro' => $_POST['IDSinistro'] ?? null,
            'Testo'      => $_POST['Testo'] ?? '',
            'Data'       => date('Y-m-d'),
            'Orario'     => date('H:i:s'),
            'IDUser'     => $_SESSION['user']['IDUtente'] ?? null
        ];
        // Debug
        // var_dump($data); exit;
    
        if (!$data['IDSinistro'] || !$data['Testo'] || !$data['IDUser']) {
            echo "Dati mancanti per il messaggio in chat.";
            exit;
        }
        $this->chatModel->create($data);
        header("Location: " . BASE_URL . "/attivita/sinistri/detail?id=" . $data['IDSinistro']);
        exit;
    }
    
}
