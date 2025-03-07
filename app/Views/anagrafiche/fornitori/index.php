<?php
// app/Views/anagrafiche/fornitori/index.php
?>
<?php require_once BASE_PATH . 'app/Views/layout/header.php'; ?>

<?php
// Rimuovi il parametro "url" dalla query string per gli export
$params = $_GET;
unset($params['url']);
$queryString = http_build_query($params);

// Se non sono definiti, inizializza le variabili come array vuoti
if (!isset($tipiFornitori)) {
    $tipiFornitori = [];
}
if (!isset($fornitori)) {
    $fornitori = [];
}
if (!isset($utenti)) {
    $utenti = [];
}

// Creiamo la mappa per i tipi fornitori
$tipiFornitoriMap = [];
foreach ($tipiFornitori as $tipo) {
    $tipiFornitoriMap[$tipo['IDTipoFornitore']] = $tipo['Nome'];
}

// Ordiniamo l'elenco dei fornitori per Nome
usort($fornitori, function($a, $b) {
    return strcmp($a['Nome'], $b['Nome']);
});
?>
<div class="main-wrapper">
  <?php require_once BASE_PATH . 'app/Views/layout/sidebar.php'; ?>
  <main class="content-area">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h2">Elenco Fornitori</h1>
      <!-- Pulsante per nuovo inserimento -->
      <button type="button" class="btn btn-neutral" data-bs-toggle="modal" data-bs-target="#newFornitoreModal" title="Nuovo Fornitore" onclick="resetModalForm();">
        <i class="bi bi-plus-circle" style="font-size:1.5rem;"></i>
      </button>
    </div>
    
    <p>Visualizza e gestisci l'elenco dei fornitori. Utilizza i filtri per cercare per nome o per tipologia.</p>
    
    <!-- Form di filtraggio -->
    <form method="GET" action="<?php echo BASE_URL; ?>/anagrafiche/fornitori" class="mb-4">
      <div class="row g-2 align-items-center">
        <div class="col-auto">
          <input type="text" name="nome" class="form-control" placeholder="Cerca per nome" value="<?php echo htmlspecialchars($_GET['nome'] ?? ''); ?>">
        </div>
        <div class="col-auto">
          <select name="idTipoFornitore" class="form-select">
            <option value="">Tutte le tipologie</option>
            <?php foreach ($tipiFornitori as $tipo): ?>
              <option value="<?php echo $tipo['IDTipoFornitore']; ?>" <?php echo (isset($_GET['idTipoFornitore']) && $_GET['idTipoFornitore'] == $tipo['IDTipoFornitore']) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($tipo['Nome']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-auto">
          <!-- Pulsanti per Filtra / Esporta PDF / Esporta Excel -->
          <button type="submit" class="btn btn-neutral" title="Filtra">
            <i class="bi bi-search" style="font-size:1.25rem;"></i>
          </button>
          <a href="<?php echo BASE_URL; ?>/anagrafiche/fornitori/exportPdf?<?php echo $queryString; ?>" class="btn btn-neutral" title="Esporta PDF" target="_blank">
            <i class="bi bi-file-earmark-pdf" style="font-size:1.25rem;"></i>
          </a>
          <a href="<?php echo BASE_URL; ?>/anagrafiche/fornitori/exportExcel?<?php echo $queryString; ?>" class="btn btn-neutral" title="Esporta Excel" target="_blank">
            <i class="bi bi-file-earmark-spreadsheet" style="font-size:1.25rem;"></i>
          </a>
        </div>
      </div>
    </form>
    
    <!-- Tabella dei fornitori -->
    <table class="table table-striped">
      <thead>
        <tr>
          <th>Nome Fornitore</th>
          <th>Tipo Fornitore</th>
          <th>Mail</th>
          <th>Telefono</th>
          <th>Partita IVA</th>
          <th>Iban</th>
          <th>Azioni</th>
        </tr>
      </thead>
      <tbody>
        <?php if (is_array($fornitori) && count($fornitori) > 0): ?>
          <?php foreach ($fornitori as $fornitore): ?>
            <tr data-fornitore="<?php echo htmlspecialchars(json_encode($fornitore), ENT_QUOTES, 'UTF-8'); ?>">
              <td><strong><?php echo htmlspecialchars($fornitore['Nome']); ?></strong></td>
              <td>
                <?php 
                  echo isset($tipiFornitoriMap[$fornitore['IDTipoFornitore']]) 
                      ? htmlspecialchars($tipiFornitoriMap[$fornitore['IDTipoFornitore']]) 
                      : htmlspecialchars($fornitore['IDTipoFornitore']);
                ?>
              </td>
              <td><?php echo htmlspecialchars($fornitore['Mail']); ?></td>
              <td><?php echo htmlspecialchars($fornitore['Telefono']); ?></td>
              <td><?php echo htmlspecialchars($fornitore['PartitaIva']); ?></td>
              <td><?php echo htmlspecialchars($fornitore['Iban'] ?? $fornitore['IBAN'] ?? ''); ?></td>
              <td>
                <!-- Bottone Edit -->
                <button type="button" class="btn btn-sm btn-edit" title="Modifica" data-bs-toggle="modal" data-bs-target="#newFornitoreModal">
                  <i class="bi bi-pencil"></i>
                </button>
                <!-- Bottone Detail -->
                <button type="button" class="btn btn-sm btn-detail" title="Dettagli" data-bs-toggle="modal" data-bs-target="#detailFornitoreModal">
                  <i class="bi bi-info-circle"></i>
                </button>
                <!-- Bottone Archive con conferma -->
                <a href="<?php echo BASE_URL; ?>/anagrafiche/fornitori/archive?id=<?php echo $fornitore['IDFornitore']; ?>" 
                   class="btn btn-sm btn-archive" 
                   title="Archivia"
                   onclick="return confirm('Sei sicuro di voler archiviare questo fornitore?');">
                  <i class="bi bi-archive"></i>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="7">Nessun fornitore trovato.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
    
    <!-- Paginazione -->
    <nav aria-label="Paginazione">
      <ul class="pagination">
        <?php for ($i = 1; $i <= $pages; $i++): ?>
          <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
            <a class="page-link" href="<?php echo BASE_URL; ?>/anagrafiche/fornitori?page=<?php echo $i; ?>&<?php echo $queryString; ?>"><?php echo $i; ?></a>
          </li>
        <?php endfor; ?>
      </ul>
    </nav>
  </main>
</div>

<!-- Modal per Nuovo/Modifica Fornitore -->
<div class="modal fade" id="newFornitoreModal" tabindex="-1" aria-labelledby="newFornitoreModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form action="<?php echo BASE_URL; ?>/anagrafiche/fornitori/save" method="POST" novalidate>
        <div class="modal-header">
          <h5 class="modal-title" id="newFornitoreModalLabel">Nuovo Fornitore</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi" onclick="resetModalForm();"></button>
        </div>
        <div class="modal-body">
          <!-- Campo nascosto per l'ID (utilizzato anche per l'edit) -->
          <input type="hidden" name="IDFornitore" id="modal-IDFornitore" value="">
          <div class="mb-3">
            <label for="modal-nome" class="form-label">Nome</label>
            <input type="text" id="modal-nome" name="nome" class="form-control" required>
          </div>
          <div class="mb-3">
            <label for="modal-idTipoFornitore" class="form-label">Tipologia</label>
            <select id="modal-idTipoFornitore" name="idTipoFornitore" class="form-select" required>
              <option value="">Seleziona la tipologia</option>
              <?php foreach ($tipiFornitori as $tipo): ?>
                <option value="<?php echo $tipo['IDTipoFornitore']; ?>"><?php echo htmlspecialchars($tipo['Nome']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="modal-indirizzo" class="form-label">Indirizzo</label>
              <input type="text" id="modal-indirizzo" name="indirizzo" class="form-control">
            </div>
            <div class="col-md-2">
              <label for="modal-cap" class="form-label">CAP</label>
              <input type="text" id="modal-cap" name="cap" class="form-control">
            </div>
            <div class="col-md-4">
              <label for="modal-citta" class="form-label">Città</label>
              <input type="text" id="modal-citta" name="citta" class="form-control">
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-4">
              <label for="modal-PartitaIva" class="form-label">Partita IVA</label>
              <input type="text" id="modal-PartitaIva" name="partitaIva" class="form-control">
            </div>
            <div class="col-md-4">
              <label for="modal-codiceFiscale" class="form-label">Codice Fiscale</label>
              <input type="text" id="modal-codiceFiscale" name="codiceFiscale" class="form-control">
            </div>
            <div class="col-md-4">
              <label for="modal-IBAN" class="form-label">Iban</label>
              <input type="text" id="modal-IBAN" name="iban" class="form-control">
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-4">
              <label for="modal-telefono" class="form-label">Telefono</label>
              <input type="text" id="modal-telefono" name="telefono" class="form-control">
            </div>
            <div class="col-md-4">
              <label for="modal-mail" class="form-label">Mail</label>
              <input type="email" id="modal-mail" name="mail" class="form-control">
            </div>
            <div class="col-md-4">
              <label for="modal-PEC" class="form-label">PEC</label>
              <input type="email" id="modal-PEC" name="pec" class="form-control">
            </div>
          </div>
          <div class="mb-3">
            <label for="modal-note" class="form-label">Note</label>
            <textarea id="modal-note" name="note" class="form-control" rows="3"></textarea>
          </div>
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="modal-codiceRitenuta" class="form-label">Codice Ritenuta</label>
              <input type="text" id="modal-codiceRitenuta" name="codiceRitenuta" class="form-control">
            </div>
            <div class="col-md-6 d-flex align-items-center">
              <div class="form-check mt-4">
                <input class="form-check-input" type="checkbox" id="modal-ritenuta" name="ritenuta" value="1">
                <label class="form-check-label" for="modal-ritenuta">
                  Ritenuta
                </label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="resetModalForm();">Chiudi</button>
          <button type="submit" class="btn btn-neutral">Salva</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal per Dettaglio Fornitore -->
<div class="modal fade" id="detailFornitoreModal" tabindex="-1" aria-labelledby="detailFornitoreModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailFornitoreModalLabel">Dettaglio Fornitore</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered">
          <tbody id="detailFornitoreBody">
            <!-- I dettagli verranno popolati via JavaScript -->
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-neutral" data-bs-dismiss="modal">Chiudi</button>
      </div>
    </div>
  </div>
</div>

<script>
// Funzione per resettare il form del modal (per un nuovo inserimento)
function resetModalForm() {
  document.getElementById('newFornitoreModalLabel').innerText = "Nuovo Fornitore";
  document.getElementById('modal-IDFornitore').value = "";
  document.getElementById('modal-nome').value = "";
  document.getElementById('modal-idTipoFornitore').value = "";
  document.getElementById('modal-indirizzo').value = "";
  document.getElementById('modal-cap').value = "";
  document.getElementById('modal-citta').value = "";
  document.getElementById('modal-PartitaIva').value = "";
  document.getElementById('modal-codiceFiscale').value = "";
  document.getElementById('modal-IBAN').value = "";
  document.getElementById('modal-telefono').value = "";
  document.getElementById('modal-mail').value = "";
  document.getElementById('modal-PEC').value = "";
  document.getElementById('modal-note').value = "";
  document.getElementById('modal-codiceRitenuta').value = "";
  document.getElementById('modal-ritenuta').checked = false;
}

// Gestione del pulsante Edit: popola il modal con i dati del fornitore
document.querySelectorAll('.btn-edit').forEach(function(button) {
  button.addEventListener('click', function() {
    var row = this.closest('tr');
    var fornitore = JSON.parse(row.getAttribute('data-fornitore'));

    document.getElementById('modal-IDFornitore').value = fornitore.IDFornitore || "";
    document.getElementById('modal-nome').value = fornitore.Nome || "";
    document.getElementById('modal-idTipoFornitore').value = fornitore.IDTipoFornitore || "";
    document.getElementById('modal-indirizzo').value = fornitore.Indirizzo || "";
    document.getElementById('modal-cap').value = fornitore.Cap || "";
    document.getElementById('modal-citta').value = fornitore.Citta || "";
    document.getElementById('modal-PartitaIva').value = fornitore.PartitaIva || "";
    document.getElementById('modal-codiceFiscale').value = fornitore.CodiceFiscale || "";
    document.getElementById('modal-IBAN').value = fornitore.Iban || ""; 
    document.getElementById('modal-telefono').value = fornitore.Telefono || "";
    document.getElementById('modal-mail').value = fornitore.Mail || "";
    document.getElementById('modal-PEC').value = fornitore.Pec || ""; 
    document.getElementById('modal-note').value = fornitore.Note || "";
    document.getElementById('modal-codiceRitenuta').value = fornitore.CodiceRitenuta || "";
    document.getElementById('modal-ritenuta').checked = fornitore.Ritenuta == 1;
    
    document.getElementById('newFornitoreModalLabel').innerText = "Modifica Fornitore";
  });
});

// Gestione del pulsante Detail: popola il modal con i dati del fornitore
document.querySelectorAll('.btn-detail').forEach(function(button) {
  button.addEventListener('click', function() {
    var row = this.closest('tr');
    var fornitore = JSON.parse(row.getAttribute('data-fornitore'));
    var html = '';
    for (var key in fornitore) {
      if (fornitore.hasOwnProperty(key)) {
        var value = fornitore[key];
        if (key === 'IDTipoFornitore') {
          if (typeof tipiFornitoriMap !== 'undefined' && tipiFornitoriMap[value]) {
            value = tipiFornitoriMap[value];
          }
        }
        if (key === 'Creato' || key === 'Modificato') {
          var date = new Date(value);
          if (!isNaN(date)) {
            value = ('0' + date.getDate()).slice(-2) + '/' + ('0' + (date.getMonth()+1)).slice(-2) + '/' + date.getFullYear();
          }
        }
        html += '<tr><th>' + key + '</th><td>' + value + '</td></tr>';
      }
    }
    document.getElementById('detailFornitoreBody').innerHTML = html;
  });
});
</script>
<?php require_once BASE_PATH . 'app/Views/layout/footer.php'; ?>
