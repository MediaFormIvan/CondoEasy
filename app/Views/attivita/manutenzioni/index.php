<?php
// app/Views/attivita/manutenzioni/index.php

session_start();
$currentUserId = $_SESSION['user']['IDUtente'] ?? null;
if (!$currentUserId) {
    echo "Utente non autenticato.";
    exit;
}

// Rimuovi il parametro "url" dalla query string per gli export
$params = $_GET;
unset($params['url']);
$queryString = http_build_query($params);

// Creiamo mappe per sostituire gli ID con i nomi
$condominiMap = [];
foreach ($condomini as $condominio) {
    $condominiMap[$condominio['IDCondominio']] = $condominio['Nome'];
}
$fornitoriMap = [];
foreach ($fornitori as $fornitore) {
    $fornitoriMap[$fornitore['IDFornitore']] = $fornitore['Nome'];
}
$utentiMap = [];
foreach ($utenti as $utente) {
    $utentiMap[$utente['IDUtente']] = $utente['Nome'];
}
$statiMap = [];
foreach ($stati as $stato) {
    $statiMap[$stato['IDStato']] = $stato['Nome'];
}

// Recupera il filtro per lo stato (default "aperti")
$filter = $_GET['filter'] ?? 'aperti';
$statiAperti = [1, 2, 3];
?>
<?php require_once BASE_PATH . 'app/Views/layout/header.php'; ?>

<div class="main-wrapper">
  <?php require_once BASE_PATH . 'app/Views/layout/sidebar.php'; ?>
  <main class="content-area p-3">
    <!-- Intestazione con titolo e pulsante per nuovo inserimento -->
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h1 class="h5 mb-0">Elenco Manutenzioni</h1>
      <button type="button" class="btn btn-neutral" data-bs-toggle="modal" data-bs-target="#newManutenzioneModal" title="Nuova Manutenzione" onclick="resetManutenzioneModal();">
        <i class="bi bi-plus-circle" style="font-size:1.5rem;"></i>
      </button>
    </div>
    
    <p>Visualizza e gestisci l'elenco delle manutenzioni. Utilizza il filtro per cercare per titolo.</p>
    
    <!-- Form di filtraggio -->
    <form method="GET" action="<?php echo BASE_URL; ?>/attivita/manutenzioni" class="mb-3">
      <div class="row g-2 align-items-center">
        <div class="col-auto">
          <input type="text" name="titolo" class="form-control" placeholder="Cerca per titolo" value="<?php echo htmlspecialchars($_GET['titolo'] ?? ''); ?>">
        </div>
        <div class="col-auto">
          <!-- Radio group per selezionare il filtro in base allo stato -->
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
          <a href="<?php echo BASE_URL; ?>/attivita/manutenzioni/exportPdf?<?php echo $queryString; ?>" class="btn btn-neutral" title="Esporta PDF" target="_blank">
            <i class="bi bi-file-earmark-pdf" style="font-size:1.25rem;"></i>
          </a>
          <a href="<?php echo BASE_URL; ?>/attivita/manutenzioni/exportExcel?<?php echo $queryString; ?>" class="btn btn-neutral" title="Esporta Excel" target="_blank">
            <i class="bi bi-file-earmark-spreadsheet" style="font-size:1.25rem;"></i>
          </a>
        </div>
      </div>
    </form>
    
    <!-- Tabella delle manutenzioni con font ridotto -->
    <table class="table table-striped" style="font-size: 13px;">
      <thead>
        <tr>
          <th>Novità</th>
          <th>Condominio</th>
          <th>Data Apertura</th>
          <th>Fornitore</th>
          <th>Titolo</th>
          <th>Stato</th>
          <th>Assegnato a</th>
          <th class="text-center">Azioni</th>
        </tr>
      </thead>
      <tbody>
        <?php if (is_array($manutenzioni) && count($manutenzioni) > 0): ?>
          <?php foreach ($manutenzioni as $m): ?>
            <?php 
              // Applica il filtro in base allo stato
              $display = true;
              if ($filter === 'aperti' && !in_array($m['IDStato'], $statiAperti)) {
                $display = false;
              }
              if ($filter === 'chiusi' && in_array($m['IDStato'], $statiAperti)) {
                $display = false;
              }
              if (!$display) continue;
              
              // Formattazione data
              $date = new DateTime($m['DataApertura']);
              $dataFormattata = $date->format('d/m/Y');
              
              // Troncamento del titolo a 35 caratteri
              $titolo = $m['Titolo'];
              if (strlen($titolo) > 35) {
                  $titolo = substr($titolo, 0, 35) . '...';
              }
              
              // Determina il nome dello stato e imposta il colore per il button
              $stateName = isset($statiMap[$m['IDStato']]) ? $statiMap[$m['IDStato']] : $m['IDStato'];
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
              
              // Recupera il flag di novità per questa manutenzione (calcolato dal controller)
              $hasNew = isset($novita[$m['IDManutenzione']]) ? $novita[$m['IDManutenzione']] : false;
            ?>
            <tr data-manutenzione="<?php echo htmlspecialchars(json_encode($m), ENT_QUOTES, 'UTF-8'); ?>">
              <td>
                <span class="status-indicator" style="
                  background-color: <?php echo $hasNew ? '#00FF00' : '#808080'; ?>;
                  width: 10px;
                  height: 10px;
                  border-radius: 50%;
                  display: inline-block;
                " title="<?php echo $hasNew ? 'Nuovi aggiornamenti' : 'Nessuna novità'; ?>"></span>
              </td>
              <td><?php echo isset($condominiMap[$m['IDCondominio']]) ? $condominiMap[$m['IDCondominio']] : $m['IDCondominio']; ?></td>
              <td><?php echo $dataFormattata; ?></td>
              <td>
                <?php 
                  if ($m['IDFornitore'] && $m['IDFornitore'] != 0 && isset($fornitoriMap[$m['IDFornitore']])) {
                    echo htmlspecialchars($fornitoriMap[$m['IDFornitore']]);
                  } else {
                    echo "";
                  }
                ?>
              </td>
              <td><?php echo htmlspecialchars($titolo); ?></td>
              <td>
                <button type="button" class="btn btn-sm" style="background-color: <?php echo $stateBg; ?>; border: none; cursor: default;">
                  <?php echo htmlspecialchars($stateName); ?>
                </button>
              </td>
              <td><?php echo isset($utentiMap[$m['IDUser']]) ? $utentiMap[$m['IDUser']] : $m['IDUser']; ?></td>
              <td class="text-center">
                <button type="button" class="btn btn-sm btn-edit" title="Modifica" data-bs-toggle="modal" data-bs-target="#newManutenzioneModal">
                  <i class="bi bi-pencil"></i>
                </button>
                <a href="<?php echo BASE_URL; ?>/attivita/manutenzioni/detail?id=<?php echo $m['IDManutenzione']; ?>" class="btn btn-sm btn-detail" title="Dettagli">
                  <i class="bi bi-info-circle"></i>
                </a>
                <button type="button" class="btn btn-sm btn-change-state" title="Cambia Stato" data-bs-toggle="modal" data-bs-target="#changeStateModal">
                  <i class="bi bi-arrow-repeat"></i>
                </button>
                <!-- L'indicatore di novità sostituisce l'icona di archiviazione -->
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="8">Nessuna manutenzione trovata.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
    
    <!-- Paginazione -->
    <nav aria-label="Paginazione">
      <ul class="pagination">
        <?php for ($i = 1; $i <= $pages; $i++): ?>
          <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
            <a class="page-link" href="<?php echo BASE_URL; ?>/attivita/manutenzioni?page=<?php echo $i; ?>&<?php echo $queryString; ?>"><?php echo $i; ?></a>
          </li>
        <?php endfor; ?>
      </ul>
    </nav>
    
  </main>
</div>

<!-- Modal per Nuova/Modifica Manutenzione -->
<div class="modal fade" id="newManutenzioneModal" tabindex="-1" aria-labelledby="newManutenzioneModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form action="<?php echo BASE_URL; ?>/attivita/manutenzioni/save" method="POST" novalidate>
        <div class="modal-header">
          <h5 class="modal-title" id="newManutenzioneModalLabel">Nuova Manutenzione</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi" onclick="resetManutenzioneModal();"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="IDManutenzione" id="modal-IDManutenzione" value="">
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
            <input type="date" id="modal-dataApertura" name="dataApertura" class="form-control" required>
          </div>
          <div class="mb-3">
            <label for="modal-IDFornitore" class="form-label">Fornitore (opzionale)</label>
            <select id="modal-IDFornitore" name="IDFornitore" class="form-select">
              <option value="">Seleziona Fornitore</option>
              <?php foreach ($fornitori as $fornitore): ?>
                <option value="<?php echo $fornitore['IDFornitore']; ?>"><?php echo htmlspecialchars($fornitore['Nome']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label for="modal-titolo" class="form-label">Titolo</label>
            <input type="text" id="modal-titolo" name="titolo" class="form-control" required>
          </div>
          <div class="mb-3">
            <label for="modal-descrizione" class="form-label">Descrizione</label>
            <textarea id="modal-descrizione" name="descrizione" class="form-control" rows="3"></textarea>
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
            <label for="modal-IDUser" class="form-label">Assegnato a</label>
            <select id="modal-IDUser" name="IDUser" class="form-select" required>
              <option value="">Seleziona Utente</option>
              <?php foreach ($utenti as $utente): ?>
                <option value="<?php echo $utente['IDUtente']; ?>"><?php echo htmlspecialchars($utente['Nome']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="resetManutenzioneModal();">Chiudi</button>
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
      <form action="<?php echo BASE_URL; ?>/attivita/manutenzioni/changeState" method="POST" novalidate>
        <div class="modal-header">
          <h5 class="modal-title" id="changeStateModalLabel">Cambia Stato</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="IDManutenzione" id="modal-change-IDManutenzione" value="">
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
// Funzione per resettare il form del modal Manutenzioni
function resetManutenzioneModal() {
    document.getElementById('newManutenzioneModalLabel').innerText = "Nuova Manutenzione";
    document.getElementById('modal-IDManutenzione').value = "";
    document.getElementById('modal-IDCondominio').value = "";
    document.getElementById('modal-dataApertura').value = new Date().toISOString().slice(0, 10);
    document.getElementById('modal-IDFornitore').value = "";
    document.getElementById('modal-titolo').value = "";
    document.getElementById('modal-descrizione').value = "";
    document.getElementById('modal-IDStato').value = "";
    document.getElementById('modal-IDUser').value = "";
}

// Gestione del pulsante Edit: popola il modal con i dati della manutenzione
document.querySelectorAll('.btn-edit').forEach(function(button) {
    button.addEventListener('click', function() {
        var row = this.closest('tr');
        var m = JSON.parse(row.getAttribute('data-manutenzione'));

        document.getElementById('modal-IDManutenzione').value = m.IDManutenzione || "";
        document.getElementById('modal-IDCondominio').value = m.IDCondominio || "";
        var date = new Date(m.DataApertura);
        document.getElementById('modal-dataApertura').value = !isNaN(date) ? date.toISOString().slice(0, 10) : "";
        document.getElementById('modal-IDFornitore').value = m.IDFornitore || "";
        document.getElementById('modal-titolo').value = m.Titolo || "";
        document.getElementById('modal-descrizione').value = m.Descrizione || "";
        document.getElementById('modal-IDStato').value = m.IDStato || "";
        document.getElementById('modal-IDUser').value = m.IDUser || "";

        document.getElementById('newManutenzioneModalLabel').innerText = "Modifica Manutenzione";
    });
});

// Gestione del pulsante Cambio Stato: imposta l'ID della manutenzione nel form del modal Change State
document.querySelectorAll('.btn-change-state').forEach(function(button) {
    button.addEventListener('click', function() {
        var row = this.closest('tr');
        var m = JSON.parse(row.getAttribute('data-manutenzione'));
        document.getElementById('modal-change-IDManutenzione').value = m.IDManutenzione || "";
    });
});
</script>

<?php require_once BASE_PATH . 'app/Views/layout/footer.php'; ?>
