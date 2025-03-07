<?php
// app/Controllers/AuthController.php
namespace App\Controllers;

use App\Models\User;
use App\Core\Database;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }
    
    // Login: verifica le credenziali e avvia la sessione
    public function login() {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        $user = $this->userModel->getByEmail($email);
        if ($user && password_verify($password, $user['Password'])) {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['user'] = $user;
            header("Location: " . BASE_URL . "/dashboard");
            exit;
        } else {
            echo "Credenziali non valide.";
        }
    }
    
    // Registrazione: crea un nuovo utente se i dati sono validi
    public function register() {
        $name = $_POST['nome'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $role = $_POST['ruolo'] ?? '';
        
        // Verifica se l'email è già registrata
        if ($this->userModel->getByEmail($email)) {
            echo "Email già registrata.";
            return;
        }
        
        // Verifica se le password coincidono
        if ($password !== $confirmPassword) {
            echo "Le password non coincidono.";
            return;
        }
        
        $data = [
            'name'     => $name,
            'email'    => $email,
            'password' => $password,
            'role'     => $role
        ];
        
        if ($this->userModel->create($data)) {
            header("Location: " . BASE_URL . "/login");
            exit;
        } else {
            echo "Errore durante la registrazione.";
        }
    }
    
    // Logout: distrugge la sessione utente
    public function logout() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
        header("Location: " . BASE_URL . "/login");
        exit;
    }
    
    // Reset Password Request: genera un token, lo salva e invia una email con il link per resettare la password
    public function resetPasswordRequest() {
        $email = $_POST['email'] ?? '';
        $user = $this->userModel->getByEmail($email);
        
        if (!$user) {
            echo "Email non trovata.";
            return;
        }
        
        // Genera un token casuale
        $token = bin2hex(random_bytes(16));
        if (!$this->userModel->updateToken($email, $token)) {
            echo "Errore nell'aggiornamento del token.";
            return;
        }
        
        $resetLink = BASE_URL . "/reset_password?token=" . $token;
        
        // Recupera la configurazione per la mail
        $mailConfig = require BASE_PATH . 'app/Config/mail.php';
        
        $mail = new PHPMailer(true);
        try {
            // Configurazione SMTP
            $mail->isSMTP();
            $mail->Host       = $mailConfig['host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $mailConfig['username'];
            $mail->Password   = $mailConfig['password'];
            $mail->SMTPSecure = $mailConfig['smtp_secure'] ?? 'tls';
            $mail->Port       = $mailConfig['port'];
            
            $mail->setFrom($mailConfig['from_email'], $mailConfig['from_name']);
            $mail->addAddress($email);
            
            $mail->isHTML(true);
            $mail->Subject = 'Reset della password - CONDOEASY';
            $mail->Body    = "Clicca sul seguente link per resettare la password: <a href='{$resetLink}'>Reset Password</a>";
            $mail->AltBody = "Visita questo link per resettare la password: {$resetLink}";
            
            $mail->send();
            echo "Email di reset inviata.";
        } catch (Exception $e) {
            echo "Errore nell'invio dell'email: " . $mail->ErrorInfo;
        }
    }
    
    // Reset Password: verifica il token e aggiorna la password
    public function resetPassword() {
        $token = $_GET['token'] ?? '';
        $newPassword = $_POST['password'] ?? '';
        
        $user = $this->userModel->getByToken($token);
        if (!$user) {
            echo "Token non valido o scaduto.";
            return;
        }
        
        if ($this->userModel->updatePassword($user['IDUtente'], $newPassword)) {
            echo "Password aggiornata con successo. <a href='" . BASE_URL . "/login'>Accedi</a>";
        } else {
            echo "Errore nell'aggiornamento della password.";
        }
    }
}
