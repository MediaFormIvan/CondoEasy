<?php
// public/index.php

// Carica la configurazione e l'autoload di Composer
require_once __DIR__ . '/../app/Config/app.php';
require_once BASE_PATH . 'vendor/autoload.php';

// Avvia la sessione se non già avviata
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Estrai la parte di URL
$url = $_GET['url'] ?? '';
$url = rtrim($url, '/');
$urlParts = explode('/', $url);

// (Opzionale: debug)
// echo "<pre>" . print_r($urlParts, true) . "</pre>";

switch ($urlParts[0] ?? '') {
    case 'login':
        $controller = new \App\Controllers\AuthController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->login();
        } else {
            include BASE_PATH . 'app/Views/auth/login.php';
        }
        break;

    case 'register':
        $controller = new \App\Controllers\AuthController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->register();
        } else {
            include BASE_PATH . 'app/Views/auth/register.php';
        }
        break;

    case 'logout':
        (new \App\Controllers\AuthController())->logout();
        break;

    case 'password_reset':
        $controller = new \App\Controllers\AuthController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->resetPasswordRequest();
        } else {
            include BASE_PATH . 'app/Views/auth/password_reset.php';
        }
        break;

    case 'reset_password':
        $controller = new \App\Controllers\AuthController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->resetPassword();
        } else {
            include BASE_PATH . 'app/Views/auth/reset_password.php';
        }
        break;

    case 'dashboard':
        include BASE_PATH . 'app/Views/dashboard.php';
        break;

        // Sezioni "piatte"
    case 'contratti':
        $controller = new \App\Controllers\ContrattiController();
        if (isset($urlParts[1])) {
            switch ($urlParts[1]) {
                case 'detail':
                    $controller->detail();
                    break;
                case 'save':
                    $controller->save();
                    break;
                case 'update':
                    $controller->update();
                    break;
                case 'delete':
                    $controller->delete();
                    break;
                case 'archive':
                    $controller->archive();
                    break;
                case 'uploadDocumento':
                    $controller->uploadDocumento();
                    break;
                default:
                    $controller->index();
                    break;
            }
        } else {
            $controller->index();
        }
        break;
    case 'promemoria':
        $controller = new \App\Controllers\PromemoriaController();
        if (isset($urlParts[1]) && trim($urlParts[1]) !== '') {
            $action = trim($urlParts[1]);
            if (in_array($action, ['save', 'update', 'delete', 'changeState', 'exportPdf', 'exportExcel'])) {
                $controller->$action();
            } else {
                $controller->index();
            }
        } else {
            $controller->index();
        }
        break;
    case 'scadenze':
        $controller = new \App\Controllers\ScadenzeController();
        if (isset($urlParts[1])) {
            switch ($urlParts[1]) {
                case 'griglia':
                    $controller->griglia();
                    break;
                case 'detail':
                    $controller->detail();
                    break;
                case 'save':
                    $controller->save();
                    break;
                case 'update':
                    $controller->update();
                    break;
                case 'delete':
                    $controller->delete();
                    break;
                case 'archive':
                    $controller->archive();
                    break;
                case 'uploadDocumento':
                    $controller->uploadDocumento();
                    break;
                default:
                    $controller->index();
                    break;
            }
        } else {
            $controller->index();
        }
        break;

    case 'assicurazioni':
        $controller = new \App\Controllers\AssicurazioniController();
        if (isset($urlParts[1])) {
            switch ($urlParts[1]) {
                case 'detail':
                    $controller->detail();
                    break;
                case 'save':
                    $controller->save();
                    break;
                case 'update':
                    $controller->update();
                    break;
                case 'delete':
                    $controller->delete();
                    break;
                case 'archive':
                    $controller->archive();
                    break;
                case 'uploadDocumento':
                    $controller->uploadDocumento();
                    break;
                case 'renew':
                    $controller->renew();
                    break;
                default:
                    $controller->index();
                    break;
            }
        } else {
            $controller->index();
        }
        break;

        // Sezioni con sottosezioni
    case 'anagrafiche':
        if (isset($urlParts[1])) {
            switch ($urlParts[1]) {
                case 'fornitori':
                    $controller = new \App\Controllers\FornitoriController();
                    if (isset($urlParts[2])) {
                        $action = $urlParts[2];
                        if (in_array($action, ['save', 'delete', 'exportPdf', 'exportExcel', 'archive'])) {
                            $controller->$action();
                        } else {
                            $controller->index();
                        }
                    } else {
                        $controller->index();
                    }
                    break;
                case 'condomini':
                    $controller = new \App\Controllers\CondominiController();
                    if (isset($urlParts[2])) {
                        $action = $urlParts[2];
                        if (in_array($action, ['save', 'delete', 'exportPdf', 'exportExcel', 'archive'])) {
                            $controller->$action();
                        } else {
                            $controller->index();
                        }
                    } else {
                        $controller->index();
                    }
                    break;
                case 'banche':
                    $controller = new \App\Controllers\BancheController();
                    if (isset($urlParts[2])) {
                        $action = $urlParts[2];
                        if (in_array($action, ['save', 'delete', 'exportPdf', 'exportExcel', 'archive'])) {
                            $controller->$action();
                        } else {
                            $controller->index();
                        }
                    } else {
                        $controller->index();
                    }
                    break;
                case 'persone':
                    $controller = new \App\Controllers\PersoneController();
                    // Qui l'azione è nel terzo segmento, perché l'URL è anagrafiche/persone/...
                    if (isset($urlParts[2]) && trim($urlParts[2]) !== '') {
                        $action = trim($urlParts[2]);
                        if ($action === 'detailAjax') {
                            $controller->detailAjax();
                        } elseif (in_array($action, ['save', 'delete', 'exportPdf', 'exportExcel', 'archive'])) {
                            $controller->$action();
                        } else {
                            $controller->index();
                        }
                    } else {
                        $controller->index();
                    }
                    break;
                case 'tipifornitori':
                    $controller = new \App\Controllers\TipiFornitoriController();
                    if (isset($urlParts[2])) {
                        $action = $urlParts[2];
                        if (in_array($action, ['save', 'delete', 'exportPdf', 'exportExcel', 'archive'])) {
                            $controller->$action();
                        } else {
                            $controller->index();
                        }
                    } else {
                        $controller->index();
                    }
                    break;
                default:
                    echo "Sezione anagrafiche non implementata.";
                    break;
            }
        } else {
            echo "Sezione anagrafiche non implementata.";
        }
        break;

    case 'attivita':
        if (isset($urlParts[1])) {
            switch ($urlParts[1]) {
                case 'manutenzioni':
                    $controller = new \App\Controllers\ManutenzioniController();
                    if (isset($urlParts[2])) {
                        $action = $urlParts[2];
                        if (in_array($action, ['save', 'delete', 'archive', 'exportPdf', 'exportExcel', 'changeState', 'detail', 'sendMail', 'associaSinistro'])) {
                            $controller->$action();
                        } else {
                            $controller->index();
                        }
                    } else {
                        $controller->index();
                    }
                    break;
                case 'manutenzioni_chat':
                    $controller = new \App\Controllers\ManutenzioniChatController();
                    if (isset($urlParts[2])) {
                        $action = $urlParts[2];
                        if (in_array($action, ['save'])) {
                            $controller->$action();
                        } else {
                            echo "Azione manutenzioni_chat non implementata.";
                        }
                    } else {
                        echo "Sezione manutenzioni_chat non implementata.";
                    }
                    break;
                case 'manutenzioni_documenti':
                    $controller = new \App\Controllers\ManutenzioniDocumentiController();
                    if (isset($urlParts[2])) {
                        $action = $urlParts[2];
                        if (in_array($action, ['save', 'update', 'delete'])) {
                            $controller->$action();
                        } else {
                            echo "Azione manutenzioni_documenti non implementata.";
                        }
                    } else {
                        echo "Sezione manutenzioni_documenti non implementata.";
                    }
                    break;
                case 'sinistri':
                    $controller = new \App\Controllers\SinistriController();
                    if (isset($urlParts[2])) {
                        $action = $urlParts[2];
                        if (in_array($action, ['save', 'delete', 'exportPdf', 'exportExcel', 'changeState', 'detail', 'update', 'sendMail'])) {
                            $controller->$action();
                        } else {
                            $controller->index();
                        }
                    } else {
                        $controller->index();
                    }
                    break;
                case 'sinistri_chat':
                    $controller = new \App\Controllers\SinistriChatController();
                    if (isset($urlParts[2])) {
                        $action = $urlParts[2];
                        if (in_array($action, ['save'])) {
                            $controller->$action();
                        } else {
                            echo "Azione sinistri_chat non implementata.";
                        }
                    } else {
                        echo "Sezione sinistri_chat non implementata.";
                    }
                    break;
                case 'sinistri_documenti':
                    $controller = new \App\Controllers\SinistriDocumentiController();
                    if (isset($urlParts[2])) {
                        $action = $urlParts[2];
                        if (in_array($action, ['save', 'update', 'delete'])) {
                            $controller->$action();
                        } else {
                            echo "Azione sinistri_documenti non implementata.";
                        }
                    } else {
                        echo "Sezione sinistri_documenti non implementata.";
                    }
                    break;
                case 'sinistri_foto':
                    $controller = new \App\Controllers\SinistriFotoController();
                    if (isset($urlParts[2])) {
                        $action = $urlParts[2];
                        if (in_array($action, ['save', 'delete', 'downloadZip'])) {
                            $controller->$action();
                        } else {
                            echo "Azione sinistri_foto non implementata.";
                        }
                    } else {
                        echo "Sezione sinistri_foto non implementata.";
                    }
                    break;
                case 'legale':
                    $controller = new \App\Controllers\LegaleController();
                    if (isset($urlParts[2])) {
                        $action = $urlParts[2];
                        if (in_array($action, ['save', 'delete', 'exportPdf', 'exportExcel', 'detail', 'update', 'changeState'])) {
                            $controller->$action();
                        } else {
                            $controller->index();
                        }
                    } else {
                        $controller->index();
                    }
                    break;
                case 'legale_documenti':
                    $controller = new \App\Controllers\LegaleDocumentiController();
                    if (isset($urlParts[2])) {
                        $action = $urlParts[2];
                        if (in_array($action, ['save', 'update', 'delete'])) {
                            $controller->$action();
                        } else {
                            echo "Azione legale_documenti non implementata.";
                        }
                    } else {
                        echo "Sezione legale_documenti non implementata.";
                    }
                    break;
                case 'legale_chat':
                    $controller = new \App\Controllers\LegaleChatController();
                    if (isset($urlParts[2])) {
                        $action = $urlParts[2];
                        if (in_array($action, ['save'])) {
                            $controller->$action();
                        } else {
                            echo "Azione legale_chat non implementata.";
                        }
                    } else {
                        echo "Sezione legale_chat non implementata.";
                    }
                    break;
                default:
                    echo "Sezione attività non implementata.";
                    break;
            }
        } else {
            echo "Sezione attività non implementata.";
        }
        break;

        // Sezione Gestioni
    case 'gestioni':
        if (isset($urlParts[1])) {
            switch ($urlParts[1]) {
                case 'bilanci':
                    $controller = new \App\Controllers\GestioniController();
                    if (isset($urlParts[2])) {
                        switch ($urlParts[2]) {
                            case 'create':
                                include BASE_PATH . 'app/Views/gestioni/bilanci/create.php';
                                break;
                            case 'edit':
                                $controller->edit();
                                break;
                            case 'save':
                                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                                    $controller->save();
                                }
                                break;
                            case 'archive':
                                $controller->archive();
                                break;
                            case 'delete':
                                $controller->delete();
                                break;
                            case 'select':
                                $controller->select();
                                break;
                            default:
                                $controller->index();
                                break;
                        }
                    } else {
                        $controller->index();
                    }
                    break;
                case 'strutture':
                    $controller = new \App\Controllers\StrutturaController();
                    if (isset($urlParts[2])) {
                        switch ($urlParts[2]) {
                            case 'createFabbricato':
                                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                                    $controller->createFabbricato();
                                }
                                break;
                            case 'updateFabbricato':
                                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                                    $controller->updateFabbricato();
                                }
                                break;
                            case 'deleteFabbricato':
                                $controller->deleteFabbricato();
                                break;
                            case 'createCivico':
                                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                                    $controller->createCivico();
                                }
                                break;
                            case 'updateCivico':
                                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                                    $controller->updateCivico();
                                }
                                break;
                            case 'deleteCivico':
                                $controller->deleteCivico();
                                break;
                            case 'createScala':
                                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                                    $controller->createScala();
                                }
                                break;
                            case 'updateScala':
                                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                                    $controller->updateScala();
                                }
                                break;
                            case 'deleteScala':
                                $controller->deleteScala();
                                break;
                            case 'createUnita':
                                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                                    $controller->createUnita();
                                }
                                break;
                            case 'updateUnita':
                                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                                    $controller->updateUnita();
                                }
                                break;
                            case 'deleteUnita':
                                $controller->deleteUnita();
                                break;
                            case 'managePersone':
                                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                                    $controller->managePersone();
                                }
                                break;
                                // Endpoint AJAX per il caricamento dei dati per la modifica
                            case 'getFabbricato':
                                $controller->getFabbricato();
                                break;
                            case 'getCivico':
                                $controller->getCivico();
                                break;
                            case 'getScala':
                                $controller->getScala();
                                break;
                            case 'getUnita':
                                $controller->getUnita();
                                break;
                            default:
                                $controller->index();
                                break;
                        }
                    } else {
                        $controller->index();
                    }
                    break;
                default:
                    echo "Sezione gestioni non implementata.";
                    break;
            }
        } else {
            echo "Sezione gestioni non implementata.";
        }
        break;

    default:
        echo "Pagina non trovata.";
        break;
}
