<?php require_once BASE_PATH . 'app/Views/layout/header.php'; ?>

<?php
// Costruiamo le mappe per visualizzare i nomi
$condominiMap = [];
foreach ($condomini as $condominio) {
    $condominiMap[$condominio['IDCondominio']] = $condominio['Nome'];
}

$tipiMap = [];
foreach ($tipiScadenze as $tipo) {
    $tipiMap[$tipo['IDTipoScadenza']] = $tipo['Nome'];
}

$fornitoriMap = [];
foreach ($fornitori as $fornitore) {
    $fornitoriMap[$fornitore['IDFornitore']] = $fornitore['Nome'];
}

$filterCondominio = $_GET['IDCondominio'] ?? '';
$filterTipo       = $_GET['IDTipoScadenza'] ?? '';
$filterFornitore  = $_GET['IDFornitore'] ?? '';
$statusFilter     = $_GET['status'] ?? 'tutti';
?>

<div class="main-wrapper">
    <?php require_once BASE_PATH . 'app/Views/layout/sidebar.php'; ?>
    <main class="content-area p-3">
        <!-- Intestazione e pulsanti -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h5 mb-0">Elenco Scadenze</h1>
            <div>
                <a href="<?php echo BASE_URL; ?>/scadenze/griglia" class="btn btn-primary me-2" title="Visualizza Griglia">
                    <i class="bi bi-grid-3x3-gap"></i> Griglia
                </a>
                <button type="button" class="btn btn-neutral" data-bs-toggle="modal" data-bs-target="#newScadenzaModal" title="Nuova Scadenza" onclick="resetScadenzaModal();">
                    <i class="bi bi-plus-circle" style="font-size:1.5rem;"></i>
                </button>
            </div>
        </div>

        <p>Visualizza e gestisci l'elenco delle scadenze. Utilizza i filtri per cercare.</p>

        <form method="GET" action="<?php echo BASE_URL; ?>/scadenze" class="mb-3">
            <div class="row g-2 align-items-center">
                <!-- Filtro per Condominio -->
                <div class="col-auto">
                    <select name="IDCondominio" class="form-select">
                        <option value="">Tutti i Condomini</option>
                        <?php foreach ($condomini as $condominio): ?>
                            <option value="<?php echo $condominio['IDCondominio']; ?>" <?php echo ($filterCondominio == $condominio['IDCondominio']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($condominio['Nome']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <!-- Filtro per Tipo Scadenza -->
                <div class="col-auto">
                    <select name="IDTipoScadenza" class="form-select">
                        <option value="">Tutti i Tipi</option>
                        <?php foreach ($tipiScadenze as $tipo): ?>
                            <option value="<?php echo $tipo['IDTipoScadenza']; ?>" <?php echo ($filterTipo == $tipo['IDTipoScadenza']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($tipo['Nome']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <!-- Filtro per Fornitore -->
                <div class="col-auto">
                    <select name="IDFornitore" class="form-select">
                        <option value="">Tutti i Fornitori</option>
                        <?php foreach ($fornitori as $fornitore): ?>
                            <option value="<?php echo $fornitore['IDFornitore']; ?>" <?php echo ($filterFornitore == $fornitore['IDFornitore']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($fornitore['Nome']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <!-- Radio per Status -->
                <div class="col-auto">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="status" id="status-attive" value="attive" <?php echo ($statusFilter == 'attive') ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="status-attive">SOLO ATTIVE</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="status" id="status-scadute" value="scadute" <?php echo ($statusFilter == 'scadute') ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="status-scadute">SOLO SCADUTE</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="status" id="status-attive_scadute" value="attive_scadute" <?php echo ($statusFilter == 'attive_scadute') ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="status-attive_scadute">ATTIVE E SCADUTE</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="status" id="status-archiviati" value="archiviati" <?php echo ($statusFilter == 'archiviati') ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="status-archiviati">SOLO ARCHIVIATI</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="status" id="status-tutti" value="tutti" <?php echo ($statusFilter == 'tutti') ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="status-tutti">TUTTE</label>
                    </div>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-neutral" title="Filtra">
                        <i class="bi bi-search" style="font-size:1.25rem;"></i>
                    </button>
                </div>
            </div>
        </form>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Condominio</th>
                    <th>Tipo Scadenza</th>
                    <th>Data Scadenza</th>
                    <th class="text-center">Azioni</th>
                </tr>
            </thead>
            <tbody>
                <?php if (is_array($scadenzePaginate) && count($scadenzePaginate) > 0): ?>
                    <?php foreach ($scadenzePaginate as $s): ?>
                        <?php
                        $today = date('Y-m-d');
                        $scaduta = ($s['DataScadenza'] < $today);
                        ?>
                        <tr <?php if ($scaduta && $s['Archiviato'] == 0): ?> style="color: darkred; font-weight: bold; font-style: italic;" <?php endif; ?> data-scadenza="<?php echo htmlspecialchars(json_encode($s), ENT_QUOTES, 'UTF-8'); ?>">
                            <td><?php echo isset($condominiMap[$s['IDCondominio']]) ? $condominiMap[$s['IDCondominio']] : $s['IDCondominio']; ?></td>
                            <td><?php echo isset($tipiMap[$s['IDTipoScadenza']]) ? $tipiMap[$s['IDTipoScadenza']] : $s['IDTipoScadenza']; ?></td>
                            <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($s['DataScadenza']))); ?></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-edit" title="Modifica" data-bs-toggle="modal" data-bs-target="#newScadenzaModal" onclick="populateScadenzaModal(this);">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <a href="<?php echo BASE_URL; ?>/scadenze/detail?id=<?php echo $s['IDScadenza']; ?>" class="btn btn-sm btn-detail" title="Dettagli">
                                    <i class="bi bi-info-circle"></i>
                                </a>
                                <a href="<?php echo BASE_URL; ?>/scadenze/archive?id=<?php echo $s['IDScadenza']; ?>" class="btn btn-sm btn-warning" title="Archivia" onclick="return confirm('Sei sicuro di voler archiviare questa scadenza?');">
                                    <i class="bi bi-archive"></i>
                                </a>
                                <a href="<?php echo BASE_URL; ?>/scadenze/delete?id=<?php echo $s['IDScadenza']; ?>" class="btn btn-sm btn-delete-doc" title="Cancella" onclick="return confirm('Sei sicuro di voler cancellare questa scadenza?');">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">Nessuna scadenza trovata.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Paginazione -->
        <nav aria-label="Paginazione">
            <ul class="pagination">
                <?php for ($i = 1; $i <= $pages; $i++): ?>
                    <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                        <a class="page-link" href="<?php echo BASE_URL; ?>/scadenze?page=<?php echo $i; ?>&IDCondominio=<?php echo urlencode($filterCondominio); ?>&IDTipoScadenza=<?php echo urlencode($filterTipo); ?>&IDFornitore=<?php echo urlencode($filterFornitore); ?>&status=<?php echo urlencode($statusFilter); ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>

    </main>
</div>

<!-- Modal per Nuova/Modifica Scadenza -->
<div class="modal fade" id="newScadenzaModal" tabindex="-1" aria-labelledby="newScadenzaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="scadenzaForm" action="<?php echo BASE_URL; ?>/scadenze/save" method="POST" novalidate>
                <div class="modal-header">
                    <h5 class="modal-title" id="newScadenzaModalLabel">Nuova Scadenza</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi" onclick="resetScadenzaModal();"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="IDScadenza" id="modal-IDScadenza" value="">
                    <div class="mb-3">
                        <label for="modal-IDCondominio" class="form-label">Condominio</label>
                        <select id="modal-IDCondominio" name="IDCondominio" class="form-select" required>
                            <option value="">Seleziona Condominio</option>
                            <?php foreach ($condomini as $condominio): ?>
                                <option value="<?php echo $condominio['IDCondominio']; ?>"><?php echo htmlspecialchars($condominio['Nome']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="modal-IDTipoScadenza" class="form-label">Tipo Scadenza</label>
                        <select id="modal-IDTipoScadenza" name="IDTipoScadenza" class="form-select" required>
                            <option value="">Seleziona Tipo Scadenza</option>
                            <?php foreach ($tipiScadenze as $tipo): ?>
                                <option value="<?php echo $tipo['IDTipoScadenza']; ?>" data-durata="<?php echo $tipo['Durata']; ?>">
                                    <?php echo htmlspecialchars($tipo['Nome']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <!-- Campo per il Fornitore -->
                    <div class="mb-3">
                        <label for="modal-IDFornitore" class="form-label">Fornitore</label>
                        <select id="modal-IDFornitore" name="IDFornitore" class="form-select" required>
                            <option value="">Seleziona Fornitore</option>
                            <?php foreach ($fornitori as $fornitore): ?>
                                <option value="<?php echo $fornitore['IDFornitore']; ?>"><?php echo htmlspecialchars($fornitore['Nome']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="modal-DataScadenza" class="form-label">Data Scadenza</label>
                        <input type="date" id="modal-DataScadenza" name="DataScadenza" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="modal-Durata" class="form-label">Durata (in mesi)</label>
                        <input type="number" id="modal-Durata" name="Durata" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="modal-Note" class="form-label">Note</label>
                        <textarea id="modal-Note" name="Note" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="resetScadenzaModal();">Chiudi</button>
                    <button type="submit" class="btn btn-neutral">Salva</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var tipoSelect = document.getElementById('modal-IDTipoScadenza');
    var durataInput = document.getElementById('modal-Durata');

    // Al caricamento, se esiste già una selezione, imposta la durata
    if (tipoSelect && durataInput) {
        var selectedOption = tipoSelect.options[tipoSelect.selectedIndex];
        var durataDefault = selectedOption ? selectedOption.getAttribute('data-durata') : "";
        if (durataDefault) {
            durataInput.value = durataDefault;
        }
    }

    // Al cambiamento del tipo scadenza, aggiorna automaticamente il campo Durata
    if (tipoSelect && durataInput) {
        tipoSelect.addEventListener('change', function() {
            var selectedOption = this.options[this.selectedIndex];
            var durataDefault = selectedOption.getAttribute('data-durata');
            if (durataDefault) {
                durataInput.value = durataDefault;
            } else {
                durataInput.value = "";
            }
        });
    }
});

// Funzione per popolare il form con i dati della scadenza da modificare
function populateScadenzaModal(button) {
    var row = button.closest('tr');
    var dataStr = row.getAttribute('data-scadenza');
    if (!dataStr) {
        console.error("Nessun dato trovato nella riga.");
        return;
    }
    try {
        var data = JSON.parse(dataStr);
    } catch(e) {
        console.error("Errore nel parsing JSON:", e);
        return;
    }
    document.getElementById('modal-IDScadenza').value = data.IDScadenza || "";
    document.getElementById('modal-IDCondominio').value = data.IDCondominio || "";
    document.getElementById('modal-IDTipoScadenza').value = data.IDTipoScadenza || "";
    document.getElementById('modal-IDFornitore').value = data.IDFornitore || "";
    
    if (data.DataScadenza) {
        var d = new Date(data.DataScadenza);
        if (!isNaN(d)) {
            document.getElementById('modal-DataScadenza').value = d.toISOString().slice(0,10);
        } else {
            document.getElementById('modal-DataScadenza').value = "";
        }
    } else {
        document.getElementById('modal-DataScadenza').value = "";
    }
    document.getElementById('modal-Durata').value = data.Durata || "";
    document.getElementById('modal-Note').value = data.Note || "";
    document.getElementById('newScadenzaModalLabel').innerText = "Modifica Scadenza";
    document.getElementById('scadenzaForm').action = "<?php echo BASE_URL; ?>/scadenze/update";
}
</script>

<?php require_once BASE_PATH . 'app/Views/layout/footer.php'; ?>
