<?php require_once BASE_PATH . 'app/Views/layout/header.php'; ?>

<?php
// Costruisci le mappe per visualizzare i nomi
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
?>

<div class="main-wrapper">
  <?php require_once BASE_PATH . 'app/Views/layout/sidebar.php'; ?>
  <main class="content-area p-3">
    <!-- Header: Titolo e pulsante per nuovo promemoria -->
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h1 class="h5 mb-0">Elenco Promemoria</h1>
      <button type="button" class="btn btn-neutral" data-bs-toggle="modal" data-bs-target="#newPromemoriaModal" title="Nuovo Promemoria" onclick="resetPromemoriaModal();">
        <i class="bi bi-plus-circle" style="font-size:1.5rem;"></i>
      </button>
    </div>
    
    <p>Filtra i promemoria per titolo, condominio e utente, e scegli lo stato:</p>
    
    <!-- Form di Filtraggio -->
    <form method="GET" action="<?php echo BASE_URL; ?>/promemoria" class="mb-3">
      <div class="row g-2 align-items-center">
        <!-- Ricerca per titolo -->
        <div class="col-auto">
          <input type="text" name="titolo" class="form-control" placeholder="Cerca per titolo" value="<?php echo htmlspecialchars($_GET['titolo'] ?? ''); ?>">
        </div>
        <!-- Filtro per Condominio -->
        <div class="col-auto">
          <select name="condominio" class="form-select">
            <option value="">Tutti i condomini</option>
            <?php foreach ($condomini as $condominio): ?>
              <option value="<?php echo $condominio['IDCondominio']; ?>" <?php echo (isset($_GET['condominio']) && $_GET['condominio'] == $condominio['IDCondominio']) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($condominio['Nome']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <!-- Filtro per Utente -->
        <div class="col-auto">
          <select name="utente" class="form-select">
            <option value="">Tutti gli utenti</option>
            <?php foreach ($utenti as $utente): ?>
              <option value="<?php echo $utente['IDUtente']; ?>" <?php echo (isset($_GET['utente']) && $_GET['utente'] == $utente['IDUtente']) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($utente['Nome']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <!-- Filtro per stato -->
        <div class="col-auto">
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="filter" id="filter-aperto" value="aperti" <?php echo (isset($_GET['filter']) && $_GET['filter'] == 'aperti') ? 'checked' : ''; ?>>
            <label class="form-check-label" for="filter-aperto">Solo Aperto</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="filter" id="filter-chiuso" value="chiusi" <?php echo (isset($_GET['filter']) && $_GET['filter'] == 'chiusi') ? 'checked' : ''; ?>>
            <label class="form-check-label" for="filter-chiuso">Solo Chiuso</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="filter" id="filter-tutti" value="tutti" <?php echo (!isset($_GET['filter']) || $_GET['filter'] == 'tutti') ? 'checked' : ''; ?>>
            <label class="form-check-label" for="filter-tutti">Tutti</label>
          </div>
        </div>
        <!-- Pulsanti per Filtra ed Esporta -->
        <div class="col-auto">
          <button type="submit" class="btn btn-neutral" title="Filtra">
            <i class="bi bi-search" style="font-size:1.25rem;"></i>
          </button>
          <a href="<?php echo BASE_URL; ?>/promemoria/exportPdf?<?php echo http_build_query($_GET); ?>" class="btn btn-neutral" title="Esporta PDF" target="_blank">
            <i class="bi bi-file-earmark-pdf" style="font-size:1.25rem;"></i>
          </a>
          <a href="<?php echo BASE_URL; ?>/promemoria/exportExcel?<?php echo http_build_query($_GET); ?>" class="btn btn-neutral" title="Esporta Excel" target="_blank">
            <i class="bi bi-file-earmark-spreadsheet" style="font-size:1.25rem;"></i>
          </a>
        </div>
      </div>
    </form>
    
    <!-- Tabella Promemoria -->
    <table class="table table-striped">
      <thead>
        <tr>
          <th>Condominio</th>
          <th>Data Scadenza</th>
          <th>Titolo</th>
          <th>Stato</th>
          <th>Utente</th>
          <th class="text-center">Azioni</th>
        </tr>
      </thead>
      <tbody>
        <?php if (is_array($promemoria) && count($promemoria) > 0): ?>
          <?php foreach ($promemoria as $p): ?>
            <?php 
              // Determina il nome dello stato e il colore
              $stateName = isset($statiMap[$p['IDStato']]) ? $statiMap[$p['IDStato']] : $p['IDStato'];
              // Se lo stato è "chiuso", che qui è definito come 4 o 5, coloriamo in rosso, altrimenti in verde
              $stateBg = in_array($p['IDStato'], [4,5]) ? '#F08080' : '#90EE90';
            ?>
            <tr data-promemoria="<?php echo htmlspecialchars(json_encode($p), ENT_QUOTES, 'UTF-8'); ?>">
              <td><?php echo isset($condominiMap[$p['IDCondominio']]) ? $condominiMap[$p['IDCondominio']] : $p['IDCondominio']; ?></td>
              <td>
                <?php 
                  $date = new DateTime($p['DataScadenza']);
                  echo $date->format('d/m/Y');
                ?>
              </td>
              <td><?php echo htmlspecialchars($p['Titolo']); ?></td>
              <td>
                <button type="button" class="btn btn-sm" style="background-color: <?php echo $stateBg; ?>; color: #fff;">
                  <?php echo htmlspecialchars($stateName); ?>
                </button>
              </td>
              <td><?php echo isset($utentiMap[$p['IDUtente']]) ? $utentiMap[$p['IDUtente']] : $p['IDUtente']; ?></td>
              <td class="text-center">
                <button type="button" class="btn btn-sm btn-edit" title="Modifica" data-bs-toggle="modal" data-bs-target="#newPromemoriaModal">
                  <i class="bi bi-pencil"></i>
                </button>
                <button type="button" class="btn btn-sm btn-change-state" title="Cambia Stato" data-bs-toggle="modal" data-bs-target="#changeStateModal">
                  <i class="bi bi-arrow-repeat"></i>
                </button>
                <a href="<?php echo BASE_URL; ?>/promemoria/delete?id=<?php echo $p['IDPromemoria']; ?>" class="btn btn-sm btn-delete-doc" title="Cancella" onclick="return confirm('Sei sicuro di voler cancellare questo promemoria?');">
                  <i class="bi bi-trash"></i>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="6">Nessun promemoria trovato.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
    
    <!-- Paginazione -->
    <nav aria-label="Paginazione">
      <ul class="pagination">
        <?php for ($i = 1; $i <= $pages; $i++): ?>
          <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
            <a class="page-link" href="<?php echo BASE_URL; ?>/promemoria?page=<?php echo $i; ?>&<?php echo http_build_query($_GET); ?>"><?php echo $i; ?></a>
          </li>
        <?php endfor; ?>
      </ul>
    </nav>
    
  </main>
</div>

<!-- Modal per Nuovo/Modifica Promemoria -->
<div class="modal fade" id="newPromemoriaModal" tabindex="-1" aria-labelledby="newPromemoriaModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="promemoriaForm" action="<?php echo BASE_URL; ?>/promemoria/save" method="POST" novalidate>
        <div class="modal-header">
          <h5 class="modal-title" id="newPromemoriaModalLabel">Nuovo Promemoria</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi" onclick="resetPromemoriaModal();"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="IDPromemoria" id="modal-IDPromemoria" value="">
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
            <label for="modal-IDUtente" class="form-label">Utente</label>
            <select id="modal-IDUtente" name="IDUtente" class="form-select" required>
              <option value="">Seleziona Utente</option>
              <?php foreach ($utenti as $utente): ?>
                <option value="<?php echo $utente['IDUtente']; ?>"><?php echo htmlspecialchars($utente['Nome']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <!-- Campo Stato -->
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
            <label for="modal-DataScadenza" class="form-label">Data Scadenza</label>
            <input type="date" id="modal-DataScadenza" name="DataScadenza" class="form-control" required>
          </div>
          <div class="mb-3">
            <label for="modal-Titolo" class="form-label">Titolo</label>
            <input type="text" id="modal-Titolo" name="Titolo" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="resetPromemoriaModal();">Chiudi</button>
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
      <form action="<?php echo BASE_URL; ?>/promemoria/changeState" method="POST" novalidate>
        <div class="modal-header">
          <h5 class="modal-title" id="changeStateModalLabel">Cambia Stato</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="IDPromemoria" id="modal-change-IDPromemoria" value="">
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
// Funzione per resettare il form del modal Promemoria
function resetPromemoriaModal() {
    document.getElementById('newPromemoriaModalLabel').innerText = "Nuovo Promemoria";
    document.getElementById('modal-IDPromemoria').value = "";
    document.getElementById('modal-IDCondominio').value = "";
    document.getElementById('modal-IDUtente').value = "";
    document.getElementById('modal-IDStato').value = "";
    document.getElementById('modal-DataScadenza').value = new Date().toISOString().slice(0, 10);
    document.getElementById('modal-Titolo').value = "";
    document.getElementById('promemoriaForm').action = "<?php echo BASE_URL; ?>/promemoria/save";
}

// Gestione del bottone Edit: popola il modal con i dati del promemoria
document.querySelectorAll('.btn-edit').forEach(function(button) {
    button.addEventListener('click', function() {
        var row = this.closest('tr');
        var p = JSON.parse(row.getAttribute('data-promemoria'));
        document.getElementById('modal-IDPromemoria').value = p.IDPromemoria || "";
        document.getElementById('modal-IDCondominio').value = p.IDCondominio || "";
        document.getElementById('modal-IDUtente').value = p.IDUtente || "";
        document.getElementById('modal-IDStato').value = p.IDStato || "";
        document.getElementById('modal-DataScadenza').value = p.DataScadenza || "";
        document.getElementById('modal-Titolo').value = p.Titolo || "";
        document.getElementById('newPromemoriaModalLabel').innerText = "Modifica Promemoria";
        document.getElementById('promemoriaForm').action = "<?php echo BASE_URL; ?>/promemoria/update";
    });
});

// Gestione del bottone Cambio Stato: imposta l'ID del promemoria nel form del modal Change State
document.querySelectorAll('.btn-change-state').forEach(function(button) {
    button.addEventListener('click', function() {
        var row = this.closest('tr');
        var p = JSON.parse(row.getAttribute('data-promemoria'));
        document.getElementById('modal-change-IDPromemoria').value = p.IDPromemoria || "";
    });
});
</script>

<?php require_once BASE_PATH . 'app/Views/layout/footer.php'; ?>
