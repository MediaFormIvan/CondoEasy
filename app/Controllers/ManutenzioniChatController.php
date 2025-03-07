<?php
namespace App\Controllers;

use App\Models\ManutenzioneChat;

class ManutenzioniChatController {

    private $chatModel;

    public function __construct() {
        $this->chatModel = new ManutenzioneChat();
    }

    /**
     * Salva un nuovo messaggio in chat per una manutenzione.
     */
    public function save() {
        // Assicurati che la sessione sia avviata e che l'utente loggato sia disponibile
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $data = [
            'IDManutenzione' => $_POST['IDManutenzione'] ?? null,
            'Testo'          => $_POST['Testo'] ?? '',
            'Data'           => date('Y-m-d'),
            'Orario'         => date('H:i:s'),
            'IDUser'         => $_SESSION['user']['IDUtente'] ?? null
        ];
        // Puoi aggiungere controlli sui dati se necessario
        if (!$data['IDManutenzione'] || !$data['Testo'] || !$data['IDUser']) {
            echo "Dati mancanti per il messaggio in chat.";
            exit;
        }
        $this->chatModel->create($data);
        // Dopo aver salvato, reindirizza alla pagina di dettaglio della manutenzione
        header("Location: " . BASE_URL . "/attivita/manutenzioni/detail?id=" . $data['IDManutenzione']);
        exit;
    }
}
