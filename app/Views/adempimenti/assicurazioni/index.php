<?php require_once BASE_PATH . 'app/Views/layout/header.php'; ?>

<?php
// Costruiamo le mappe per visualizzare i nomi di condomini e fornitori
$condominiMap = [];
foreach ($condomini as $condominio) {
    $condominiMap[$condominio['IDCondominio']] = $condominio['Nome'];
}
$fornitoriMap = [];
foreach ($fornitori as $fornitore) {
    $fornitoriMap[$fornitore['IDFornitore']] = $fornitore['Nome'];
}
$filterCondominio = $_GET['IDCondominio'] ?? '';
$filterFornitore  = $_GET['IDFornitore'] ?? '';
?>

<div class="main-wrapper">
    <?php require_once BASE_PATH . 'app/Views/layout/sidebar.php'; ?>
    <main class="content-area p-3">
        <!-- Intestazione e pulsante per aprire il modal -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h5 mb-0">Elenco Assicurazioni</h1>
            <button type="button" class="btn btn-neutral" data-bs-toggle="modal" data-bs-target="#newAssicurazioneModal" title="Nuova Assicurazione" onclick="resetAssicurazioneModal();">
                <i class="bi bi-plus-circle" style="font-size:1.5rem;"></i>
            </button>
        </div>
        <p>Visualizza e gestisci l'elenco delle assicurazioni. Filtra per Condominio e Fornitore.</p>
        <form method="GET" action="<?php echo BASE_URL; ?>/assicurazioni" class="mb-3">
            <div class="row g-2 align-items-center">
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
                    <th>Data Scadenza</th>
                    <th>Durata (mesi)</th>
                    <th>Polizza</th>
                    <th class="text-center">Azioni</th>
                </tr>
            </thead>
            <tbody>
                <?php if (is_array($assicurazioni) && count($assicurazioni) > 0): ?>
                    <?php foreach ($assicurazioni as $a): ?>
                        <tr data-assicurazione="<?php echo htmlspecialchars(json_encode($a), ENT_QUOTES, 'UTF-8'); ?>">
                            <td><?php echo isset($condominiMap[$a['IDCondominio']]) ? $condominiMap[$a['IDCondominio']] : $a['IDCondominio']; ?></td>
                            <td><?php echo isset($fornitoriMap[$a['IDFornitore']]) ? $fornitoriMap[$a['IDFornitore']] : $a['IDFornitore']; ?></td>
                            <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($a['DataScadenza']))); ?></td>
                            <td><?php echo htmlspecialchars($a['Durata']); ?></td>
                            <td><?php echo htmlspecialchars($a['Polizza']); ?></td>
                            <td class="text-center">
                                <!-- Pulsante per modifica -->
                                <button type="button" class="btn btn-sm btn-edit" title="Modifica" data-bs-toggle="modal" data-bs-target="#newAssicurazioneModal" onclick="populateAssicurazioneModal(this);">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <!-- Link per dettaglio -->
                                <a href="<?php echo BASE_URL; ?>/assicurazioni/detail?id=<?php echo $a['IDAssicurazione']; ?>" class="btn btn-sm btn-detail" title="Dettagli">
                                    <i class="bi bi-info-circle"></i>
                                </a>
                                <!-- Pulsante per archivia -->
                                <a href="<?php echo BASE_URL; ?>/assicurazioni/archive?id=<?php echo $a['IDAssicurazione']; ?>" class="btn btn-sm btn-warning" title="Archivia" onclick="return confirm('Sei sicuro di voler archiviare questa assicurazione?');">
                                    <i class="bi bi-archive"></i>
                                </a>
                                <!-- Pulsante per cancellare -->
                                <a href="<?php echo BASE_URL; ?>/assicurazioni/delete?id=<?php echo $a['IDAssicurazione']; ?>" class="btn btn-sm btn-delete-doc" title="Cancella" onclick="return confirm('Sei sicuro di voler cancellare questa assicurazione?');">
                                    <i class="bi bi-trash"></i>
                                </a>
                                <!-- Nuovo pulsante per rinnovare la polizza -->
                                <a href="<?php echo BASE_URL; ?>/assicurazioni/renew?id=<?php echo $a['IDAssicurazione']; ?>" class="btn btn-sm btn-info" title="Rinnova" onclick="return confirm('Sei sicuro di voler rinnovare questa polizza?');">
                                    <i class="bi bi-arrow-repeat"></i>
                                </a>
                            </td>

                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">Nessuna assicurazione trovata.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
</div>

<!-- Modal per Nuova/Modifica Assicurazione -->
<div class="modal fade" id="newAssicurazioneModal" tabindex="-1" aria-labelledby="newAssicurazioneModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="assicurazioneForm" action="<?php echo BASE_URL; ?>/assicurazioni/save" method="POST" novalidate>
                <div class="modal-header">
                    <h5 class="modal-title" id="newAssicurazioneModalLabel">Nuova Assicurazione</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi" onclick="resetAssicurazioneModal();"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="IDAssicurazione" id="modal-IDAssicurazione" value="">
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
                                <?php if ($fornitore['IDTipoFornitore'] == 5): ?>
                                    <option value="<?php echo $fornitore['IDFornitore']; ?>">
                                        <?php echo htmlspecialchars($fornitore['Nome']); ?>
                                    </option>
                                <?php endif; ?>
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
                        <label for="modal-Polizza" class="form-label">Polizza</label>
                        <input type="text" id="modal-Polizza" name="Polizza" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="resetAssicurazioneModal();">Chiudi</button>
                    <button type="submit" class="btn btn-neutral">Salva</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Funzione per resettare il form del modal
    function resetAssicurazioneModal() {
        document.getElementById('newAssicurazioneModalLabel').innerText = "Nuova Assicurazione";
        document.getElementById('modal-IDAssicurazione').value = "";
        document.getElementById('modal-IDCondominio').value = "";
        document.getElementById('modal-IDFornitore').value = "";
        document.getElementById('modal-DataScadenza').value = "";
        document.getElementById('modal-Durata').value = "";
        document.getElementById('modal-Polizza').value = "";
        document.getElementById('assicurazioneForm').action = "<?php echo BASE_URL; ?>/assicurazioni/save";
    }

    // Funzione per popolare il form con i dati della riga da modificare
    function populateAssicurazioneModal(button) {
        var row = button.closest('tr');
        var data = JSON.parse(row.getAttribute('data-assicurazione'));
        document.getElementById('modal-IDAssicurazione').value = data.IDAssicurazione || "";
        document.getElementById('modal-IDCondominio').value = data.IDCondominio || "";
        document.getElementById('modal-IDFornitore').value = data.IDFornitore || "";
        document.getElementById('modal-DataScadenza').value = data.DataScadenza ? new Date(data.DataScadenza).toISOString().slice(0, 10) : "";
        document.getElementById('modal-Durata').value = data.Durata || "";
        document.getElementById('modal-Polizza').value = data.Polizza || "";
        document.getElementById('newAssicurazioneModalLabel').innerText = "Modifica Assicurazione";
        document.getElementById('assicurazioneForm').action = "<?php echo BASE_URL; ?>/assicurazioni/update";
    }
</script>

<?php require_once BASE_PATH . 'app/Views/layout/footer.php'; ?>