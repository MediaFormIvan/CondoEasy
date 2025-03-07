<?php
// app/Models/Role.php

class Role {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Recupera tutti i ruoli non archiviati
    public function getAll() {
        $stmt = $this->db->prepare("SELECT * FROM ruoli WHERE Archiviato = 0 ORDER BY Nome ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
