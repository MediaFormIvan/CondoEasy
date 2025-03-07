<?php
// Classe per la gestione delle view

class View {
    public static function render($template, $data = []) {
        extract($data);
        include '../app/Views/' . $template . '.php';
    }
}
