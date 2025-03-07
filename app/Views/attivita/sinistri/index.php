<?php require_once BASE_PATH . 'app/Views/layout/header.php'; ?>

<?php
// Creiamo mappe per sostituire gli ID con i nomi
$condominiMap = [];
foreach ($condomini as $condominio) {
    $condominiMap[$condominio['IDCondominio']] = $condominio['Nome'];
}
$statiMap = [];
foreach ($stati as $stato) {
    $statiMap[$stato['IDStato']] = $stato['Nome'];
}
$utentiMap = [];
foreach ($utenti as $utente) {
    $utentiMap[$utente['IDUtente']] = $utente['Nome'];
}
$studiMap = [];
foreach ($studiPeritali as $studio) {
    $studiMap[$studio['IDStudioPeritale']] = $studio['Nome'];
}

$filter = $_GET['filter'] ?? 'aperti';
$statiAperti = [1, 2, 3]; // Modifica se necessario
?>

<div class="main-wrapper">
    <?php require_once BASE_PATH . 'app/Views/layout/sidebar.php'; ?>
    <main class="content-area p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h5 mb-0">Elenco Sinistri</h1>
            <button type="button" class="btn btn-neutral" data-bs-toggle="modal" data-bs-target="#newSinistroModal" title="Nuovo Sinistro" onclick="resetSinistroModal();">
                <i class="bi bi-plus-circle" style="font-size:1.5rem;"></i>
            </button>
        </div>

        <p>Visualizza e gestisci l'elenco dei sinistri. Utilizza il filtro per cercare per titolo.</p>

        <form method="GET" action="<?php echo BASE_URL; ?>/attivita/sinistri" class="mb-3">
            <div class="row g-2 align-items-center">
                <div class="col-auto">
                    <input type="text" name="titolo" class="form-control" placeholder="Cerca per titolo" value="<?php echo htmlspecialchars($_GET['titolo'] ?? ''); ?>">
                </div>
                <div class="col-auto">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="filter" id="filter-aperti" value="aperti" <?php echo ($filter === 'aperti') ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="filter-aperti">Mostra solo aperti</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="filter" id="filter-anche" value="anche" <?php echo ($filter === 'anche') ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="filter-anche">Mostra anche chiusi</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="filter" id="filter-chiusi" value="chiusi" <?php echo ($filter === 'chiusi') ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="filter-chiusi">Mostra solo chiusi</label>
                    </div>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-neutral" title="Filtra">
                        <i class="bi bi-search" style="font-size:1.25rem;"></i>
                    </button>
                    <a href="<?php echo BASE_URL; ?>/attivita/sinistri/exportPdf?<?php echo http_build_query($_GET); ?>" class="btn btn-neutral" title="Esporta PDF" target="_blank">
                        <i class="bi bi-file-earmark-pdf" style="font-size:1.25rem;"></i>
                    </a>
                    <a href="<?php echo BASE_URL; ?>/attivita/sinistri/exportExcel?<?php echo http_build_query($_GET); ?>" class="btn btn-neutral" title="Esporta Excel" target="_blank">
                        <i class="bi bi-file-earmark-spreadsheet" style="font-size:1.25rem;"></i>
                    </a>
                </div>
            </div>
        </form>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Condominio</th>
                    <th>Data Apertura</th>
                    <th>Titolo</th>
                    <th>Stato</th>
                    <th>Numero</th>
                    <th class="text-center">Azioni</th>
                </tr>
            </thead>
            <tbody>
                <?php if (is_array($sinistri) && count($sinistri) > 0): ?>
                    <?php foreach ($sinistri as $s): ?>
                        <?php
                        $display = true;
                        if ($filter === 'aperti' && !in_array($s['IDStato'], $statiAperti)) {
                            $display = false;
                        }
                        if ($filter === 'chiusi' && in_array($s['IDStato'], $statiAperti)) {
                            $display = false;
                        }
                        if (!$display) continue;
                        ?>
                        <tr data-sinistro="<?php echo htmlspecialchars(json_encode($s), ENT_QUOTES, 'UTF-8'); ?>">
                            <td><?php echo isset($condominiMap[$s['IDCondominio']]) ? $condominiMap[$s['IDCondominio']] : $s['IDCondominio']; ?></td>
                            <td>
                                <?php
                                $date = new DateTime($s['DataApertura']);
                                echo $date->format('d/m/Y');
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($s['Titolo']); ?></td>
                            <td><?php echo isset($statiMap[$s['IDStato']]) ? $statiMap[$s['IDStato']] : $s['IDStato']; ?></td>
                            <td><?php echo htmlspecialchars($s['Numero']); ?></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-edit" title="Modifica" data-bs-toggle="modal" data-bs-target="#newSinistroModal">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <a href="<?php echo BASE_URL; ?>/attivita/sinistri/detail?id=<?php echo $s['IDSinistro']; ?>" class="btn btn-sm btn-detail" title="Dettagli">
                                    <i class="bi bi-info-circle"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-change-state" title="Cambia Stato" data-bs-toggle="modal" data-bs-target="#changeStateModal">
                                    <i class="bi bi-arrow-repeat"></i>
                                </button>
                                <a href="<?php echo BASE_URL; ?>/attivita/sinistri/delete?id=<?php echo $s['IDSinistro']; ?>" class="btn btn-sm btn-delete-doc" title="Cancella" onclick="return confirm('Sei sicuro di voler cancellare questo sinistro?');">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>

                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">Nessun sinistro trovato.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <nav aria-label="Paginazione">
            <ul class="pagination">
                <?php for ($i = 1; $i <= $pages; $i++): ?>
                    <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                        <a class="page-link" href="<?php echo BASE_URL; ?>/attivita/sinistri?page=<?php echo $i; ?>&<?php echo http_build_query($_GET); ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>

    </main>
</div>

<!-- Modal per Nuovo/Modifica Sinistro -->
<div class="modal fade" id="newSinistroModal" tabindex="-1" aria-labelledby="newSinistroModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Aggiungi id="sinistroForm" -->
            <form id="sinistroForm" action="<?php echo BASE_URL; ?>/attivita/sinistri/save" method="POST" novalidate>
                <div class="modal-header">
                    <h5 class="modal-title" id="newSinistroModalLabel">Nuovo Sinistro</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi" onclick="resetSinistroModal();"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="IDSinistro" id="modal-IDSinistro" value="">
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
                        <label for="modal-dataApertura" class="form-label">Data Apertura</label>
                        <input type="date" id="modal-dataApertura" name="DataApertura" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="modal-titolo" class="form-label">Titolo</label>
                        <input type="text" id="modal-titolo" name="Titolo" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="modal-descrizione" class="form-label">Descrizione</label>
                        <textarea id="modal-descrizione" name="Descrizione" class="form-control"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="modal-numero" class="form-label">Numero</label>
                        <input type="text" id="modal-numero" name="Numero" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="modal-IDStudioPeritale" class="form-label">Studio Peritale (opzionale)</label>
                        <select id="modal-IDStudioPeritale" name="IDStudioPeritale" class="form-select">
                            <option value="">Seleziona Studio Peritale</option>
                            <?php foreach ($studiPeritali as $studio): ?>
                                <option value="<?php echo $studio['IDStudioPeritale']; ?>"><?php echo htmlspecialchars($studio['Nome']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="modal-IDStato" class="form-label">Stato</label>
                        <select id="modal-IDStato" name="IDStato" class="form-select" required>
                            <option value="">Seleziona Stato</option>
                            <?php foreach ($stati as $stato): ?>
                                <option value="<?php echo $stato['IDStato']; ?>"><?php echo htmlspecialchars($stato['Nome']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="modal-dataChiusura" class="form-label">Data Chiusura</label>
                        <input type="date" id="modal-dataChiusura" name="DataChiusura" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="modal-rimborso" class="form-label">Rimborso</label>
                        <input type="number" step="0.01" id="modal-rimborso" name="Rimborso" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="resetSinistroModal();">Chiudi</button>
                    <button type="submit" class="btn btn-neutral">Salva</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal per Cambio Stato -->
<div class="modal fade" id="changeStateModal" tabindex="-1" aria-labelledby="changeStateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo BASE_URL; ?>/attivita/sinistri/changeState" method="POST" novalidate>
                <div class="modal-header">
                    <h5 class="modal-title" id="changeStateModalLabel">Cambia Stato</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="IDSinistro" id="modal-change-IDSinistro" value="">
                    <div class="mb-3">
                        <label for="modal-newState" class="form-label">Nuovo Stato</label>
                        <select id="modal-newState" name="newState" class="form-select" required>
                            <option value="">Seleziona Stato</option>
                            <?php foreach ($stati as $stato): ?>
                                <option value="<?php echo $stato['IDStato']; ?>"><?php echo htmlspecialchars($stato['Nome']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
                    <button type="submit" class="btn btn-neutral">Salva</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Funzione per resettare il form del modal Sinistri (nuovo sinistro)
    function resetSinistroModal() {
        document.getElementById('newSinistroModalLabel').innerText = "Nuovo Sinistro";
        document.getElementById('modal-IDSinistro').value = "";
        document.getElementById('modal-IDCondominio').value = "";
        document.getElementById('modal-dataApertura').value = new Date().toISOString().slice(0, 10);
        document.getElementById('modal-titolo').value = "";
        document.getElementById('modal-descrizione').value = "";
        document.getElementById('modal-numero').value = "";
        document.getElementById('modal-IDStudioPeritale').value = "";
        document.getElementById('modal-IDStato').value = "";
        document.getElementById('modal-dataChiusura').value = "";
        document.getElementById('modal-rimborso').value = "";

        // Imposta l'action del form a "save" per il nuovo sinistro
        document.getElementById('sinistroForm').action = "<?php echo BASE_URL; ?>/attivita/sinistri/save";
    }

    // Gestione del bottone Edit: popola il modal con i dati del sinistro e cambia l'action del form in update
    document.querySelectorAll('.btn-edit').forEach(function(button) {
        button.addEventListener('click', function() {
            var row = this.closest('tr');
            var s = JSON.parse(row.getAttribute('data-sinistro'));
            document.getElementById('modal-IDSinistro').value = s.IDSinistro || "";
            document.getElementById('modal-IDCondominio').value = s.IDCondominio || "";
            var date = new Date(s.DataApertura);
            document.getElementById('modal-dataApertura').value = !isNaN(date) ? date.toISOString().slice(0, 10) : "";
            document.getElementById('modal-titolo').value = s.Titolo || "";
            document.getElementById('modal-descrizione').value = s.Descrizione || "";
            document.getElementById('modal-numero').value = s.Numero || "";
            document.getElementById('modal-IDStudioPeritale').value = s.IDStudioPeritale || "";
            document.getElementById('modal-IDStato').value = s.IDStato || "";
            document.getElementById('modal-dataChiusura').value = s.DataChiusura ? new Date(s.DataChiusura).toISOString().slice(0, 10) : "";
            document.getElementById('modal-rimborso').value = s.Rimborso || "";
            document.getElementById('newSinistroModalLabel').innerText = "Modifica Sinistro";

            // Imposta l'action del form a "update" quando si modifica un sinistro esistente
            document.getElementById('sinistroForm').action = "<?php echo BASE_URL; ?>/attivita/sinistri/update";
        });
    });
</script>

<?php require_once BASE_PATH . 'app/Views/layout/footer.php'; ?>