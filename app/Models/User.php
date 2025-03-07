<?php
// app/Models/User.php
namespace App\Models;

use App\Core\Database;
use PDO;

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Recupera un utente per email
    public function getByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM utenti WHERE Mail = :email AND Archiviato = 0");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Aggiunto: Recupera tutti gli utenti non archiviati, ordinati per Nome
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM utenti WHERE Archiviato = 0 ORDER BY Nome ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Crea un nuovo utente
    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO utenti (IDRuolo, Nome, Mail, Password, Creato) VALUES (:role, :name, :email, :password, NOW())");
        return $stmt->execute([
            'role'     => $data['role'],
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_BCRYPT)
        ]);
    }

    // Aggiorna il token per il reset password
    public function updateToken($email, $token) {
        $stmt = $this->db->prepare("UPDATE utenti SET Token = :token, Modificato = NOW() WHERE Mail = :email");
        return $stmt->execute(['token' => $token, 'email' => $email]);
    }

    // Recupera utente tramite token (per reset password)
    public function getByToken($token) {
        $stmt = $this->db->prepare("SELECT * FROM utenti WHERE Token = :token AND Archiviato = 0");
        $stmt->execute(['token' => $token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Aggiorna la password dell'utente (e resetta il token)
    public function updatePassword($userId, $newPassword) {
        $stmt = $this->db->prepare("UPDATE utenti SET Password = :password, Token = NULL, Modificato = NOW() WHERE IDUtente = :id");
        return $stmt->execute(['password' => password_hash($newPassword, PASSWORD_BCRYPT), 'id' => $userId]);
    }

    public function updateGestioneSelection($userId, $IDGestione) {
        $stmt = $this->db->prepare("UPDATE utenti SET IDGestione = :IDGestione WHERE IDUtente = :IDUtente");
        return $stmt->execute([
            'IDGestione' => $IDGestione,
            'IDUtente'   => $userId,
        ]);
    }
    
}
