<?php
// app/Views/anagrafiche/banche/index.php
?>
<?php require_once BASE_PATH . 'app/Views/layout/header.php'; ?>
<?php
// Rimuovi il parametro "url" dalla query string
$params = $_GET;
unset($params['url']);
$queryString = http_build_query($params);
?>
<div class="main-wrapper">
  <?php require_once BASE_PATH . 'app/Views/layout/sidebar.php'; ?>
  <main class="content-area">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h2">Elenco Banche</h1>
      <!-- Pulsante per nuovo inserimento -->
      <button type="button" class="btn btn-neutral" data-bs-toggle="modal" data-bs-target="#newBancaModal" title="Nuova Banca" onclick="resetModalForm();">
        <i class="bi bi-plus-circle" style="font-size:1.5rem;"></i>
      </button>
    </div>
    
    <p>Visualizza e gestisci l'elenco delle banche. Utilizza il filtro per cercare per nome.</p>
    
    <!-- Form di filtraggio -->
    <form method="GET" action="<?php echo BASE_URL; ?>/anagrafiche/banche" class="mb-4">
      <div class="row g-2 align-items-center">
        <div class="col-auto">
          <input type="text" name="nome" class="form-control" placeholder="Cerca per nome" value="<?php echo htmlspecialchars($_GET['nome'] ?? ''); ?>">
        </div>
        <div class="col-auto">
          <!-- Pulsanti per Filtra / Esporta PDF / Esporta Excel -->
          <button type="submit" class="btn btn-neutral" title="Filtra">
            <i class="bi bi-search" style="font-size:1.25rem;"></i>
          </button>
          <a href="<?php echo BASE_URL; ?>/anagrafiche/banche/exportPdf?<?php echo $queryString; ?>" class="btn btn-neutral" title="Esporta PDF" target="_blank">
            <i class="bi bi-file-earmark-pdf" style="font-size:1.25rem;"></i>
          </a>
          <a href="<?php echo BASE_URL; ?>/anagrafiche/banche/exportExcel?<?php echo $queryString; ?>" class="btn btn-neutral" title="Esporta Excel" target="_blank">
            <i class="bi bi-file-earmark-spreadsheet" style="font-size:1.25rem;"></i>
          </a>
        </div>
      </div>
    </form>
    
    <!-- Tabella delle banche -->
    <table class="table table-striped">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nome</th>
          <th>Azioni</th>
        </tr>
      </thead>
      <tbody>
        <?php if (is_array($banche) && count($banche) > 0): ?>
          <?php foreach ($banche as $banca): ?>
            <tr data-banca="<?php echo htmlspecialchars(json_encode($banca), ENT_QUOTES, 'UTF-8'); ?>">
              <td><?php echo $banca['IDBanca']; ?></td>
              <td><?php echo htmlspecialchars($banca['Nome']); ?></td>
              <td>
                <!-- Bottone Edit -->
                <button type="button" class="btn btn-sm btn-edit" title="Modifica" data-bs-toggle="modal" data-bs-target="#newBancaModal">
                  <i class="bi bi-pencil"></i>
                </button>
                <!-- Bottone Detail -->
                <button type="button" class="btn btn-sm btn-detail" title="Dettagli" data-bs-toggle="modal" data-bs-target="#detailBancaModal">
                  <i class="bi bi-info-circle"></i>
                </button>
                <!-- Bottone Archive con conferma -->
                <a href="<?php echo BASE_URL; ?>/anagrafiche/banche/archive?id=<?php echo $banca['IDBanca']; ?>" 
                   class="btn btn-sm btn-archive" 
                   title="Archivia"
                   onclick="return confirm('Sei sicuro di voler archiviare questa banca?');">
                  <i class="bi bi-archive"></i>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="3">Nessuna banca trovata.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
    
    <!-- Paginazione -->
    <nav aria-label="Paginazione">
      <ul class="pagination">
        <?php for ($i = 1; $i <= $pages; $i++): ?>
          <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
            <a class="page-link" href="<?php echo BASE_URL; ?>/anagrafiche/banche?page=<?php echo $i; ?>&<?php echo $queryString; ?>"><?php echo $i; ?></a>
          </li>
        <?php endfor; ?>
      </ul>
    </nav>
  </main>
</div>

<!-- Modal per Nuova/Modifica Banca -->
<div class="modal fade" id="newBancaModal" tabindex="-1" aria-labelledby="newBancaModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="<?php echo BASE_URL; ?>/anagrafiche/banche/save" method="POST" novalidate>
        <div class="modal-header">
          <h5 class="modal-title" id="newBancaModalLabel">Nuova Banca</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi" onclick="resetModalForm();"></button>
        </div>
        <div class="modal-body">
          <!-- Campo nascosto per l'ID (usato anche per l'edit) -->
          <input type="hidden" name="IDBanca" id="modal-IDBanca" value="">
          <div class="mb-3">
            <label for="modal-nome" class="form-label">Nome</label>
            <input type="text" id="modal-nome" name="nome" class="form-control" required>
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

<!-- Modal per Dettaglio Banca -->
<div class="modal fade" id="detailBancaModal" tabindex="-1" aria-labelledby="detailBancaModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailBancaModalLabel">Dettaglio Banca</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered">
          <tbody id="detailBancaBody">
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
// Funzione per resettare il form del modale (per un nuovo inserimento)
function resetModalForm() {
  document.getElementById('newBancaModalLabel').innerText = "Nuova Banca";
  document.getElementById('modal-IDBanca').value = "";
  document.getElementById('modal-nome').value = "";
}

// Gestione del bottone Edit: popola il modale con i dati della banca
document.querySelectorAll('.btn-edit').forEach(function(button) {
  button.addEventListener('click', function() {
    var row = this.closest('tr');
    var banca = JSON.parse(row.getAttribute('data-banca'));
    
    document.getElementById('modal-IDBanca').value = banca.IDBanca || "";
    document.getElementById('modal-nome').value = banca.Nome || "";
    
    document.getElementById('newBancaModalLabel').innerText = "Modifica Banca";
  });
});

// Gestione del bottone Detail: popola il modale con i dati della banca
document.querySelectorAll('.btn-detail').forEach(function(button) {
  button.addEventListener('click', function() {
    var row = this.closest('tr');
    var banca = JSON.parse(row.getAttribute('data-banca'));
    var html = '';
    for (var key in banca) {
      if (banca.hasOwnProperty(key)) {
        var value = banca[key];
        // Se il campo è una data (Creato, Modificato), formattala in dd/mm/YYYY
        if (key === 'Creato' || key === 'Modificato') {
          var date = new Date(value);
          if (!isNaN(date)) {
            value = ('0' + date.getDate()).slice(-2) + '/' + ('0' + (date.getMonth()+1)).slice(-2) + '/' + date.getFullYear();
          }
        }
        html += '<tr><th>' + key + '</th><td>' + value + '</td></tr>';
      }
    }
    document.getElementById('detailBancaBody').innerHTML = html;
  });
});
</script>
<?php require_once BASE_PATH . 'app/Views/layout/footer.php'; ?>
