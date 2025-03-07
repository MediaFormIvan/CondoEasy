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

$fornitoriMap = [];
// Filtra solo i fornitori con IDTipoFornitore=11 (avvocati)
foreach ($fornitori as $fornitore) {
    if ($fornitore['IDTipoFornitore'] == 11) {
        $fornitoriMap[$fornitore['IDFornitore']] = $fornitore['Nome'];
    }
}

$utentiMap = [];
foreach ($utenti as $utente) {
    $utentiMap[$utente['IDUtente']] = $utente['Nome'];
}

// Recupera il filtro selezionato (default "aperti")
$filter = $_GET['filter'] ?? 'aperti';
$statiAperti = [1, 2, 3]; // Definisci gli ID degli stati "aperti"
?>

<div class="main-wrapper">
  <?php require_once BASE_PATH . 'app/Views/layout/sidebar.php'; ?>
  <main class="content-area p-3">
    <!-- Header con titolo e pulsante per Nuova Pratica Legale -->
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h1 class="h5 mb-0">Elenco Pratiche Legali</h1>
      <button type="button" class="btn btn-neutral" data-bs-toggle="modal" data-bs-target="#newLegaleModal" title="Nuova Pratica Legale" onclick="resetLegaleModal();">
        <i class="bi bi-plus-circle" style="font-size:1.5rem;"></i>
      </button>
    </div>
    
    <p>Visualizza e gestisci l'elenco delle pratiche legali. Utilizza i filtri per cercare per titolo, condominio e fornitore.</p>
    
    <!-- Form di filtraggio -->
    <form method="GET" action="<?php echo BASE_URL; ?>/attivita/legale" class="mb-3">
      <div class="row g-2 align-items-center">
        <!-- Filtro per titolo -->
        <div class="col-auto">
          <input type="text" name="titolo" class="form-control" placeholder="Cerca per titolo" value="<?php echo htmlspecialchars($_GET['titolo'] ?? ''); ?>">
        </div>
        <!-- Filtro per Condominio -->
        <div class="col-auto">
          <select name="condominio" class="form-select">
            <option value="">Tutti i condomini</option>
            <?php foreach ($condomini as $condominio): ?>
              <option value="<?php echo $condominio['IDCondominio']; ?>"
                <?php echo (isset($_GET['condominio']) && $_GET['condominio'] == $condominio['IDCondominio']) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($condominio['Nome']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <!-- Filtro per Fornitore (Avvocati) -->
        <div class="col-auto">
          <select name="fornitore" class="form-select">
            <option value="">Tutti gli avvocati</option>
            <?php foreach ($fornitori as $fornitore): ?>
              <?php if ($fornitore['IDTipoFornitore'] == 11): ?>
                <option value="<?php echo $fornitore['IDFornitore']; ?>"
                  <?php echo (isset($_GET['fornitore']) && $_GET['fornitore'] == $fornitore['IDFornitore']) ? 'selected' : ''; ?>>
                  <?php echo htmlspecialchars($fornitore['Nome']); ?>
                </option>
              <?php endif; ?>
            <?php endforeach; ?>
          </select>
        </div>
        <!-- Filtri per stato -->
        <div class="col-auto">
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="filter" id="filter-aperti" value="aperti" <?php echo ($filter === 'aperti') ? 'checked' : ''; ?>>
            <label class="form-check-label" for="filter-aperti">Solo Aperti</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="filter" id="filter-anche" value="anche" <?php echo ($filter === 'anche') ? 'checked' : ''; ?>>
            <label class="form-check-label" for="filter-anche">Mostra anche chiusi</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="filter" id="filter-chiusi" value="chiusi" <?php echo ($filter === 'chiusi') ? 'checked' : ''; ?>>
            <label class="form-check-label" for="filter-chiusi">Solo Chiusi</label>
          </div>
        </div>
        <!-- Pulsanti per invio ed esportazione -->
        <div class="col-auto">
          <button type="submit" class="btn btn-neutral" title="Filtra">
            <i class="bi bi-search" style="font-size:1.25rem;"></i>
          </button>
          <a href="<?php echo BASE_URL; ?>/attivita/legale/exportPdf?<?php echo http_build_query($_GET); ?>" class="btn btn-neutral" title="Esporta PDF" target="_blank">
            <i class="bi bi-file-earmark-pdf" style="font-size:1.25rem;"></i>
          </a>
          <a href="<?php echo BASE_URL; ?>/attivita/legale/exportExcel?<?php echo http_build_query($_GET); ?>" class="btn btn-neutral" title="Esporta Excel" target="_blank">
            <i class="bi bi-file-earmark-spreadsheet" style="font-size:1.25rem;"></i>
          </a>
        </div>
      </div>
    </form>
    
    <!-- Tabella delle pratiche legali -->
    <table class="table table-striped">
      <thead>
        <tr>
          <th>Condominio</th>
          <th>Data Apertura</th>
          <th>Titolo</th>
          <th>Stato</th>
          <th>Fornitore</th>
          <th class="text-center">Azioni</th>
        </tr>
      </thead>
      <tbody>
        <?php if (is_array($legali) && count($legali) > 0): ?>
          <?php foreach ($legali as $l): ?>
            <?php 
              // Applica i filtri in base allo stato
              $display = true;
              if ($filter === 'aperti' && !in_array($l['IDStato'], $statiAperti)) {
                $display = false;
              }
              if ($filter === 'chiusi' && in_array($l['IDStato'], $statiAperti)) {
                $display = false;
              }
              if (!$display) continue;
            ?>
            <tr data-legale="<?php echo htmlspecialchars(json_encode($l), ENT_QUOTES, 'UTF-8'); ?>">
              <td><?php echo isset($condominiMap[$l['IDCondominio']]) ? $condominiMap[$l['IDCondominio']] : $l['IDCondominio']; ?></td>
              <td>
                <?php 
                  $date = new DateTime($l['DataApertura']);
                  echo $date->format('d/m/Y');
                ?>
              </td>
              <td><?php echo htmlspecialchars($l['Titolo']); ?></td>
              <td>
                <?php 
                  $stateName = isset($statiMap[$l['IDStato']]) ? $statiMap[$l['IDStato']] : $l['IDStato'];
                  $stateBg = '';
                  switch (strtoupper($stateName)) {
                      case 'DA APRIRE':
                          $stateBg = '#D3D3D3'; break;
                      case 'IN GESTIONE':
                          $stateBg = '#ADD8E6'; break;
                      case 'IN ATTESA':
                          $stateBg = '#FFFF99'; break;
                      case 'CHIUSO (NEGATIVO)':
                          $stateBg = '#F08080'; break;
                      case 'CHIUSO (POSITIVO)':
                          $stateBg = '#90EE90'; break;
                      default:
                          $stateBg = '#CCCCCC'; break;
                  }
                ?>
                <button type="button" class="btn btn-sm" style="background-color: <?php echo $stateBg; ?>; color: #fff;">
                  <?php echo htmlspecialchars($stateName); ?>
                </button>
              </td>
              <td><?php echo isset($fornitoriMap[$l['IDFornitore']]) ? $fornitoriMap[$l['IDFornitore']] : $l['IDFornitore']; ?></td>
              <td class="text-center">
                <button type="button" class="btn btn-sm btn-edit" title="Modifica" data-bs-toggle="modal" data-bs-target="#newLegaleModal">
                  <i class="bi bi-pencil"></i>
                </button>
                <button type="button" class="btn btn-sm btn-change-state" title="Cambia Stato" data-bs-toggle="modal" data-bs-target="#changeStateModal">
                  <i class="bi bi-arrow-repeat"></i>
                </button>
                <a href="<?php echo BASE_URL; ?>/attivita/legale/detail?id=<?php echo $l['IDLegale']; ?>" class="btn btn-sm btn-detail" title="Dettagli">
                  <i class="bi bi-info-circle"></i>
                </a>
                <a href="<?php echo BASE_URL; ?>/attivita/legale/delete?id=<?php echo $l['IDLegale']; ?>" class="btn btn-sm btn-delete-doc" title="Cancella" onclick="return confirm('Sei sicuro di voler cancellare questa pratica legale?');">
                  <i class="bi bi-trash"></i>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="6">Nessuna pratica legale trovata.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
    
    <!-- Paginazione -->
    <nav aria-label="Paginazione">
      <ul class="pagination">
        <?php for ($i = 1; $i <= $pages; $i++): ?>
          <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
            <a class="page-link" href="<?php echo BASE_URL; ?>/attivita/legale?page=<?php echo $i; ?>&<?php echo http_build_query($_GET); ?>"><?php echo $i; ?></a>
          </li>
        <?php endfor; ?>
      </ul>
    </nav>
    
  </main>
</div>

<!-- Modal per Nuova/Modifica Pratica Legale -->
<div class="modal fade" id="newLegaleModal" tabindex="-1" aria-labelledby="newLegaleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="legaleForm" action="<?php echo BASE_URL; ?>/attivita/legale/save" method="POST" novalidate>
        <div class="modal-header">
          <h5 class="modal-title" id="newLegaleModalLabel">Nuova Pratica Legale</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi" onclick="resetLegaleModal();"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="IDLegale" id="modal-IDLegale" value="">
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
            <label for="modal-IDFornitore" class="form-label">Fornitore (Avvocato)</label>
            <select id="modal-IDFornitore" name="IDFornitore" class="form-select">
              <option value="">Seleziona Fornitore</option>
              <?php foreach ($fornitori as $fornitore): ?>
                <?php if ($fornitore['IDTipoFornitore'] == 11): ?>
                  <option value="<?php echo $fornitore['IDFornitore']; ?>"><?php echo htmlspecialchars($fornitore['Nome']); ?></option>
                <?php endif; ?>
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
            <label for="modal-DataApertura" class="form-label">Data Apertura</label>
            <input type="date" id="modal-DataApertura" name="DataApertura" class="form-control" required>
          </div>
          <div class="mb-3">
            <label for="modal-Titolo" class="form-label">Titolo</label>
            <input type="text" id="modal-Titolo" name="Titolo" class="form-control" required>
          </div>
          <div class="mb-3">
            <label for="modal-Descrizione" class="form-label">Descrizione</label>
            <textarea id="modal-Descrizione" name="Descrizione" class="form-control"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="resetLegaleModal();">Chiudi</button>
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
      <form action="<?php echo BASE_URL; ?>/attivita/legale/changeState" method="POST" novalidate>
        <div class="modal-header">
          <h5 class="modal-title" id="changeStateModalLabel">Cambia Stato</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="IDLegale" id="modal-change-IDLegale" value="">
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
// Funzione per resettare il form del modal Legale
function resetLegaleModal() {
    document.getElementById('newLegaleModalLabel').innerText = "Nuova Pratica Legale";
    document.getElementById('modal-IDLegale').value = "";
    document.getElementById('modal-IDCondominio').value = "";
    document.getElementById('modal-IDFornitore').value = "";
    document.getElementById('modal-IDStato').value = "";
    document.getElementById('modal-DataApertura').value = new Date().toISOString().slice(0, 10);
    document.getElementById('modal-Titolo').value = "";
    document.getElementById('modal-Descrizione').value = "";
    document.getElementById('legaleForm').action = "<?php echo BASE_URL; ?>/attivita/legale/save";
}

// Gestione del bottone Edit: popola il modal con i dati della pratica legale
document.querySelectorAll('.btn-edit').forEach(function(button) {
    button.addEventListener('click', function() {
        var row = this.closest('tr');
        var l = JSON.parse(row.getAttribute('data-legale'));
        document.getElementById('modal-IDLegale').value = l.IDLegale || "";
        document.getElementById('modal-IDCondominio').value = l.IDCondominio || "";
        document.getElementById('modal-IDFornitore').value = l.IDFornitore || "";
        document.getElementById('modal-IDStato').value = l.IDStato || "";
        var date = new Date(l.DataApertura);
        document.getElementById('modal-DataApertura').value = !isNaN(date) ? date.toISOString().slice(0, 10) : "";
        document.getElementById('modal-Titolo').value = l.Titolo || "";
        document.getElementById('modal-Descrizione').value = l.Descrizione || "";
        document.getElementById('newLegaleModalLabel').innerText = "Modifica Pratica Legale";
        document.getElementById('legaleForm').action = "<?php echo BASE_URL; ?>/attivita/legale/update";
    });
});

// Gestione del bottone Cambio Stato: imposta l'ID della pratica nel form del modal Change State
document.querySelectorAll('.btn-change-state').forEach(function(button) {
    button.addEventListener('click', function() {
        var row = this.closest('tr');
        var l = JSON.parse(row.getAttribute('data-legale'));
        document.getElementById('modal-change-IDLegale').value = l.IDLegale || "";
    });
});
</script>

<?php require_once BASE_PATH . 'app/Views/layout/footer.php'; ?>
