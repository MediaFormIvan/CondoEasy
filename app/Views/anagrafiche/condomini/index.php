<?php
// app/Views/anagrafiche/condomini/index.php
?>
<?php require_once BASE_PATH . 'app/Views/layout/header.php'; ?>

<?php
// Rimuovi il parametro "url" dalla query string per l'esportazione
$params = $_GET;
unset($params['url']);
$queryString = http_build_query($params);

// Se non è definito, inizializza l'array dei condominii come vuoto
if (!isset($condomini)) {
    $condomini = [];
}

// Ordina l'array dei condominii per il campo 'Nome'
usort($condomini, function($a, $b) {
    return strcmp($a['Nome'], $b['Nome']);
});
?>
<div class="main-wrapper">
  <?php require_once BASE_PATH . 'app/Views/layout/sidebar.php'; ?>
  <main class="content-area">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h2">Elenco Condomini</h1>
      <!-- Pulsante per nuovo inserimento -->
      <button type="button" class="btn btn-neutral" data-bs-toggle="modal" data-bs-target="#newCondominioModal" title="Nuovo Condominio" onclick="resetModalForm();">
        <i class="bi bi-plus-circle" style="font-size:1.5rem;"></i>
      </button>
    </div>
    
    <p>Visualizza e gestisci l'elenco dei condominii. Utilizza il filtro per cercare per nome.</p>
    
    <!-- Form di filtraggio -->
    <form method="GET" action="<?php echo BASE_URL; ?>/anagrafiche/condomini" class="mb-4">
      <div class="row g-2 align-items-center">
        <div class="col-auto">
          <input type="text" name="nome" class="form-control" placeholder="Cerca per nome" value="<?php echo htmlspecialchars($_GET['nome'] ?? ''); ?>">
        </div>
        <div class="col-auto">
          <!-- Pulsanti per Filtra / Esporta PDF / Esporta Excel -->
          <button type="submit" class="btn btn-neutral" title="Filtra">
            <i class="bi bi-search" style="font-size:1.25rem;"></i>
          </button>
          <a href="<?php echo BASE_URL; ?>/anagrafiche/condomini/exportPdf?<?php echo $queryString; ?>" class="btn btn-neutral" title="Esporta PDF" target="_blank">
            <i class="bi bi-file-earmark-pdf" style="font-size:1.25rem;"></i>
          </a>
          <a href="<?php echo BASE_URL; ?>/anagrafiche/condomini/exportExcel?<?php echo $queryString; ?>" class="btn btn-neutral" title="Esporta Excel" target="_blank">
            <i class="bi bi-file-earmark-spreadsheet" style="font-size:1.25rem;"></i>
          </a>
        </div>
      </div>
    </form>
    
    <!-- Tabella dei condominii -->
    <table class="table table-striped">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nome</th>
          <th>Indirizzo</th>
          <th>CAP</th>
          <th>Città</th>
          <th>Codice Fiscale</th>
          <th>Azioni</th>
        </tr>
      </thead>
      <tbody>
        <?php if (is_array($condomini) && count($condomini) > 0): ?>
          <?php foreach ($condomini as $condominio): ?>
            <tr data-condominio="<?php echo htmlspecialchars(json_encode($condominio), ENT_QUOTES, 'UTF-8'); ?>">
              <td><?php echo $condominio['IDCondominio']; ?></td>
              <td><?php echo htmlspecialchars($condominio['Nome']); ?></td>
              <td><?php echo htmlspecialchars($condominio['Indirizzo']); ?></td>
              <td><?php echo htmlspecialchars($condominio['Cap']); ?></td>
              <td><?php echo htmlspecialchars($condominio['Citta']); ?></td>
              <td><?php echo htmlspecialchars($condominio['CodiceFiscale']); ?></td>
              <td>
                <!-- Bottone Edit -->
                <button type="button" class="btn btn-sm btn-edit" title="Modifica" data-bs-toggle="modal" data-bs-target="#newCondominioModal">
                  <i class="bi bi-pencil"></i>
                </button>
                <!-- Bottone Detail -->
                <button type="button" class="btn btn-sm btn-detail" title="Dettagli" data-bs-toggle="modal" data-bs-target="#detailCondominioModal">
                  <i class="bi bi-info-circle"></i>
                </button>
                <!-- Bottone Archive con conferma -->
                <a href="<?php echo BASE_URL; ?>/anagrafiche/condomini/archive?id=<?php echo $condominio['IDCondominio']; ?>" 
                   class="btn btn-sm btn-archive" 
                   title="Archivia"
                   onclick="return confirm('Sei sicuro di voler archiviare questo condominio?');">
                  <i class="bi bi-archive"></i>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="7">Nessun condominio trovato.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
    
    <!-- Paginazione -->
    <nav aria-label="Paginazione">
      <ul class="pagination">
        <?php for ($i = 1; $i <= $pages; $i++): ?>
          <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
            <a class="page-link" href="<?php echo BASE_URL; ?>/anagrafiche/condomini?page=<?php echo $i; ?>&<?php echo $queryString; ?>"><?php echo $i; ?></a>
          </li>
        <?php endfor; ?>
      </ul>
    </nav>
  </main>
</div>

<!-- Modal per Nuovo/Modifica Condominio -->
<div class="modal fade" id="newCondominioModal" tabindex="-1" aria-labelledby="newCondominioModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form action="<?php echo BASE_URL; ?>/anagrafiche/condomini/save" method="POST" novalidate>
        <div class="modal-header">
          <h5 class="modal-title" id="newCondominioModalLabel">Nuovo Condominio</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi" onclick="resetModalForm();"></button>
        </div>
        <div class="modal-body">
          <!-- Campo nascosto per l'ID (utilizzato anche per l'edit) -->
          <input type="hidden" name="IDCondominio" id="modal-IDCondominio" value="">
          <div class="mb-3">
            <label for="modal-nome" class="form-label">Nome</label>
            <input type="text" id="modal-nome" name="nome" class="form-control" required>
          </div>
          <div class="mb-3">
            <label for="modal-indirizzo" class="form-label">Indirizzo</label>
            <input type="text" id="modal-indirizzo" name="indirizzo" class="form-control">
          </div>
          <div class="row mb-3">
            <div class="col-md-3">
              <label for="modal-cap" class="form-label">CAP</label>
              <input type="text" id="modal-cap" name="cap" class="form-control">
            </div>
            <div class="col-md-5">
              <label for="modal-citta" class="form-label">Città</label>
              <input type="text" id="modal-citta" name="citta" class="form-control">
            </div>
            <div class="col-md-4">
              <label for="modal-codiceFiscale" class="form-label">Codice Fiscale</label>
              <input type="text" id="modal-codiceFiscale" name="codiceFiscale" class="form-control">
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

<!-- Modal per Dettaglio Condominio -->
<div class="modal fade" id="detailCondominioModal" tabindex="-1" aria-labelledby="detailCondominioModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailCondominioModalLabel">Dettaglio Condominio</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered">
          <tbody id="detailCondominioBody">
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
  document.getElementById('newCondominioModalLabel').innerText = "Nuovo Condominio";
  document.getElementById('modal-IDCondominio').value = "";
  document.getElementById('modal-nome').value = "";
  document.getElementById('modal-indirizzo').value = "";
  document.getElementById('modal-cap').value = "";
  document.getElementById('modal-citta').value = "";
  document.getElementById('modal-codiceFiscale').value = "";
}

// Gestione del pulsante Edit: popola il modal con i dati del condominio
document.querySelectorAll('.btn-edit').forEach(function(button) {
  button.addEventListener('click', function() {
    var row = this.closest('tr');
    var condominio = JSON.parse(row.getAttribute('data-condominio'));
    
    document.getElementById('modal-IDCondominio').value = condominio.IDCondominio || "";
    document.getElementById('modal-nome').value = condominio.Nome || "";
    document.getElementById('modal-indirizzo').value = condominio.Indirizzo || "";
    document.getElementById('modal-cap').value = condominio.Cap || "";
    document.getElementById('modal-citta').value = condominio.Citta || "";
    document.getElementById('modal-codiceFiscale').value = condominio.CodiceFiscale || "";
    
    document.getElementById('newCondominioModalLabel').innerText = "Modifica Condominio";
  });
});

// Gestione del pulsante Detail: popola il modal con i dati del condominio
document.querySelectorAll('.btn-detail').forEach(function(button) {
  button.addEventListener('click', function() {
    var row = this.closest('tr');
    var condominio = JSON.parse(row.getAttribute('data-condominio'));
    var html = '';
    for (var key in condominio) {
      if (condominio.hasOwnProperty(key)) {
        var value = condominio[key];
        if (key === 'Creato' || key === 'Modificato') {
          var date = new Date(value);
          if (!isNaN(date)) {
            value = ('0' + date.getDate()).slice(-2) + '/' + ('0' + (date.getMonth()+1)).slice(-2) + '/' + date.getFullYear();
          }
        }
        html += '<tr><th>' + key + '</th><td>' + value + '</td></tr>';
      }
    }
    document.getElementById('detailCondominioBody').innerHTML = html;
  });
});
</script>
<?php require_once BASE_PATH . 'app/Views/layout/footer.php'; ?>
