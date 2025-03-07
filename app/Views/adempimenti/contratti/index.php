<?php require_once BASE_PATH . 'app/Views/layout/header.php'; ?>

<?php
// Mappe per visualizzare i nomi a partire dagli ID
$condominiMap = [];
foreach ($condomini as $condominio) {
    $condominiMap[$condominio['IDCondominio']] = $condominio['Nome'];
}
$fornitoriMap = [];
foreach ($fornitori as $fornitore) {
    $fornitoriMap[$fornitore['IDFornitore']] = $fornitore['Nome'];
}
$tipiMap = [];
foreach ($tipiContratti as $tipo) {
    $tipiMap[$tipo['IDTipoContratto']] = $tipo['Nome'];
}

// Filtro per titolo
$filterTitolo = $_GET['titolo'] ?? '';
// Filtro per condominio
$filterCondominio = $_GET['IDCondominio'] ?? '';
// Filtro per tipo contratto
$filterTipo = $_GET['IDTipoContratto'] ?? '';
// Filtro per status (attivi, scaduti, attivi_scaduti, archiviati, tutti)
$statusFilter = $_GET['status'] ?? 'tutti';
?>

<div class="main-wrapper">
    <?php require_once BASE_PATH . 'app/Views/layout/sidebar.php'; ?>
    <main class="content-area p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h5 mb-0">Elenco Contratti</h1>
            <button type="button" class="btn btn-neutral" data-bs-toggle="modal" data-bs-target="#newContrattoModal" title="Nuovo Contratto" onclick="resetContrattoModal();">
                <i class="bi bi-plus-circle" style="font-size:1.5rem;"></i>
            </button>
        </div>

        <p>Visualizza e gestisci l'elenco dei contratti. Utilizza i filtri per cercare.</p>

        <form method="GET" action="<?php echo BASE_URL; ?>/contratti" class="mb-3">
            <div class="row g-2 align-items-center">
                <!-- Filtro per titolo -->
                <div class="col-auto">
                    <input type="text" name="titolo" class="form-control" placeholder="Cerca per titolo" value="<?php echo htmlspecialchars($filterTitolo); ?>">
                </div>
                <!-- Filtro per condominio -->
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
                <!-- Filtro per tipo contratto -->
                <div class="col-auto">
                    <select name="IDTipoContratto" class="form-select">
                        <option value="">Tutti i Tipi di Contratto</option>
                        <?php foreach ($tipiContratti as $tipo): ?>
                            <option value="<?php echo $tipo['IDTipoContratto']; ?>" <?php echo ($filterTipo == $tipo['IDTipoContratto']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($tipo['Nome']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <!-- Radio per status -->
                <div class="col-auto">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="status" id="status-attivi" value="attivi" <?php echo ($statusFilter == 'attivi') ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="status-attivi">SOLO ATTIVI</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="status" id="status-scaduti" value="scaduti" <?php echo ($statusFilter == 'scaduti') ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="status-scaduti">SOLO SCADUTI</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="status" id="status-attivi_scaduti" value="attivi_scaduti" <?php echo ($statusFilter == 'attivi_scaduti') ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="status-attivi_scaduti">ATTIVI E SCADUTI</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="status" id="status-archiviati" value="archiviati" <?php echo ($statusFilter == 'archiviati') ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="status-archiviati">SOLO ARCHIVIATI</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="status" id="status-tutti" value="tutti" <?php echo ($statusFilter == 'tutti') ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="status-tutti">TUTTI I CONTRATTI</label>
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
                    <th>Fornitore</th>
                    <th>Tipo Contratto</th>
                    <th>Titolo</th>
                    <th>Data Inizio</th>
                    <th>Data Fine</th>
                    <th class="text-center">Azioni</th>
                </tr>
            </thead>
            <tbody>
                <?php if (is_array($contratti) && count($contratti) > 0): ?>
                    <?php foreach ($contratti as $c): ?>
                        <?php 
                            // Calcola se il contratto è scaduto: consideriamo scaduto se DataFine < oggi (e non archiviato)
                            $today = date('Y-m-d');
                            $scaduto = ($c['DataFine'] < $today);
                            
                            // Applica il filtro per status in base al parametro inviato
                            $include = true;
                            if($statusFilter == 'attivi') {
                                $include = (!$scaduto && $c['Archiviato'] == 0);
                            } elseif($statusFilter == 'scaduti') {
                                $include = ($scaduto && $c['Archiviato'] == 0);
                            } elseif($statusFilter == 'attivi_scaduti') {
                                $include = ($c['Archiviato'] == 0);
                            } elseif($statusFilter == 'archiviati') {
                                $include = ($c['Archiviato'] == 1);
                            }
                            // "tutti" include tutti i record
                            if(!$include) continue;
                        ?>
                        <tr <?php if($scaduto && $c['Archiviato'] == 0): ?> style="color: darkred; font-weight: bold; font-style: italic;" <?php endif; ?> data-contratto="<?php echo htmlspecialchars(json_encode($c), ENT_QUOTES, 'UTF-8'); ?>">
                            <td><?php echo isset($condominiMap[$c['IDCondominio']]) ? $condominiMap[$c['IDCondominio']] : $c['IDCondominio']; ?></td>
                            <td><?php echo isset($fornitoriMap[$c['IDFornitore']]) ? $fornitoriMap[$c['IDFornitore']] : $c['IDFornitore']; ?></td>
                            <td><?php echo isset($tipiMap[$c['IDTipoContratto']]) ? $tipiMap[$c['IDTipoContratto']] : $c['IDTipoContratto']; ?></td>
                            <td><?php echo htmlspecialchars($c['Titolo']); ?></td>
                            <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($c['DataInizio']))); ?></td>
                            <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($c['DataFine']))); ?></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-edit" title="Modifica" data-bs-toggle="modal" data-bs-target="#newContrattoModal">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <a href="<?php echo BASE_URL; ?>/contratti/detail?id=<?php echo $c['IDCondominioContratto']; ?>" class="btn btn-sm btn-detail" title="Dettagli">
                                    <i class="bi bi-info-circle"></i>
                                </a>
                                <a href="<?php echo BASE_URL; ?>/contratti/archive?id=<?php echo $c['IDCondominioContratto']; ?>" class="btn btn-sm btn-warning" title="Archivia" onclick="return confirm('Sei sicuro di voler archiviare questo contratto?');">
                                    <i class="bi bi-archive"></i>
                                </a>
                                <a href="<?php echo BASE_URL; ?>/contratti/delete?id=<?php echo $c['IDCondominioContratto']; ?>" class="btn btn-sm btn-delete-doc" title="Cancella" onclick="return confirm('Sei sicuro di voler cancellare questo contratto?');">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">Nessun contratto trovato.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Paginazione -->
        <nav aria-label="Paginazione">
            <ul class="pagination">
                <?php for ($i = 1; $i <= $pages; $i++): ?>
                    <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                        <a class="page-link" href="<?php echo BASE_URL; ?>/contratti?page=<?php echo $i; ?>&titolo=<?php echo urlencode($filterTitolo); ?>&IDCondominio=<?php echo urlencode($filterCondominio); ?>&IDTipoContratto=<?php echo urlencode($filterTipo); ?>&status=<?php echo urlencode($statusFilter); ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>

    </main>
</div>

<!-- Modal per Nuovo/Modifica Contratto -->
<div class="modal fade" id="newContrattoModal" tabindex="-1" aria-labelledby="newContrattoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Form per inserimento/modifica contratto -->
            <form id="contrattoForm" action="<?php echo BASE_URL; ?>/contratti/save" method="POST" novalidate>
                <div class="modal-header">
                    <h5 class="modal-title" id="newContrattoModalLabel">Nuovo Contratto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi" onclick="resetContrattoModal();"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="IDCondominioContratto" id="modal-IDCondominioContratto" value="">
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
                        <label for="modal-IDFornitore" class="form-label">Fornitore</label>
                        <select id="modal-IDFornitore" name="IDFornitore" class="form-select" required>
                            <option value="">Seleziona Fornitore</option>
                            <?php foreach ($fornitori as $fornitore): ?>
                                <option value="<?php echo $fornitore['IDFornitore']; ?>"><?php echo htmlspecialchars($fornitore['Nome']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="modal-IDTipoContratto" class="form-label">Tipo Contratto</label>
                        <select id="modal-IDTipoContratto" name="IDTipoContratto" class="form-select" required>
                            <option value="">Seleziona Tipo Contratto</option>
                            <?php foreach ($tipiContratti as $tipo): ?>
                                <option value="<?php echo $tipo['IDTipoContratto']; ?>"><?php echo htmlspecialchars($tipo['Nome']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="modal-titolo" class="form-label">Titolo</label>
                        <input type="text" id="modal-titolo" name="Titolo" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="modal-DataInizio" class="form-label">Data Inizio</label>
                        <input type="date" id="modal-DataInizio" name="DataInizio" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="modal-DataFine" class="form-label">Data Fine</label>
                        <input type="date" id="modal-DataFine" name="DataFine" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="modal-Note" class="form-label">Note</label>
                        <textarea id="modal-Note" name="Note" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="resetContrattoModal();">Chiudi</button>
                    <button type="submit" class="btn btn-neutral">Salva</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Resetta il form del modal Contratto
    function resetContrattoModal() {
        document.getElementById('newContrattoModalLabel').innerText = "Nuovo Contratto";
        document.getElementById('modal-IDCondominioContratto').value = "";
        document.getElementById('modal-IDCondominio').value = "";
        document.getElementById('modal-IDFornitore').value = "";
        document.getElementById('modal-IDTipoContratto').value = "";
        document.getElementById('modal-titolo').value = "";
        document.getElementById('modal-DataInizio').value = "";
        document.getElementById('modal-DataFine').value = "";
        document.getElementById('modal-Note').value = "";
        document.getElementById('contrattoForm').action = "<?php echo BASE_URL; ?>/contratti/save";
    }

    // Gestione del bottone di modifica: popola il modal e cambia l'action in update
    document.querySelectorAll('.btn-edit').forEach(function(button) {
        button.addEventListener('click', function() {
            var row = this.closest('tr');
            var c = JSON.parse(row.getAttribute('data-contratto'));
            document.getElementById('modal-IDCondominioContratto').value = c.IDCondominioContratto || "";
            document.getElementById('modal-IDCondominio').value = c.IDCondominio || "";
            document.getElementById('modal-IDFornitore').value = c.IDFornitore || "";
            document.getElementById('modal-IDTipoContratto').value = c.IDTipoContratto || "";
            document.getElementById('modal-titolo').value = c.Titolo || "";
            document.getElementById('modal-DataInizio').value = c.DataInizio ? new Date(c.DataInizio).toISOString().slice(0,10) : "";
            document.getElementById('modal-DataFine').value = c.DataFine ? new Date(c.DataFine).toISOString().slice(0,10) : "";
            document.getElementById('modal-Note').value = c.Note || "";
            document.getElementById('newContrattoModalLabel').innerText = "Modifica Contratto";
            document.getElementById('contrattoForm').action = "<?php echo BASE_URL; ?>/contratti/update";
        });
    });
</script>

<?php require_once BASE_PATH . 'app/Views/layout/footer.php'; ?>
