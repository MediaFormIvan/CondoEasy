<?php
// app/Views/anagrafiche/tipifornitori/index.php
?>
<?php require_once BASE_PATH . 'app/Views/layout/header.php'; ?>
<?php
// Rimuovi il parametro "url" dalla query string per gli export
$params = $_GET;
unset($params['url']);
$queryString = http_build_query($params);
?>
<div class="main-wrapper">
  <?php require_once BASE_PATH . 'app/Views/layout/sidebar.php'; ?>
  <main class="content-area">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h2">Elenco Tipi Fornitore</h1>
      <!-- Pulsante per nuovo inserimento -->
      <button type="button" class="btn btn-neutral" data-bs-toggle="modal" data-bs-target="#newTipoModal" title="Nuovo Tipo Fornitore" onclick="resetModalForm();">
        <i class="bi bi-plus-circle" style="font-size:1.5rem;"></i>
      </button>
    </div>
    
    <p>Visualizza e gestisci l'elenco dei tipi fornitore. Utilizza il filtro per cercare per nome.</p>
    
    <!-- Form di filtraggio -->
    <form method="GET" action="<?php echo BASE_URL; ?>/anagrafiche/tipifornitori" class="mb-4">
      <div class="row g-2 align-items-center">
        <div class="col-auto">
          <input type="text" name="nome" class="form-control" placeholder="Cerca per nome" value="<?php echo htmlspecialchars($_GET['nome'] ?? ''); ?>">
        </div>
        <div class="col-auto">
          <!-- Pulsanti per Filtra / Esporta PDF / Esporta Excel -->
          <button type="submit" class="btn btn-neutral" title="Filtra">
            <i class="bi bi-search" style="font-size:1.25rem;"></i>
          </button>
          <a href="<?php echo BASE_URL; ?>/anagrafiche/tipifornitori/exportPdf?<?php echo $queryString; ?>" class="btn btn-neutral" title="Esporta PDF" target="_blank">
            <i class="bi bi-file-earmark-pdf" style="font-size:1.25rem;"></i>
          </a>
          <a href="<?php echo BASE_URL; ?>/anagrafiche/tipifornitori/exportExcel?<?php echo $queryString; ?>" class="btn btn-neutral" title="Esporta Excel" target="_blank">
            <i class="bi bi-file-earmark-spreadsheet" style="font-size:1.25rem;"></i>
          </a>
        </div>
      </div>
    </form>
    
    <!-- Tabella dei tipi fornitore -->
    <table class="table table-striped">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nome</th>
          <th>Azioni</th>
        </tr>
      </thead>
      <tbody>
        <?php if (is_array($tipi) && count($tipi) > 0): ?>
          <?php foreach ($tipi as $tipo): ?>
            <tr data-tipo="<?php echo htmlspecialchars(json_encode($tipo), ENT_QUOTES, 'UTF-8'); ?>">
              <td><?php echo $tipo['IDTipoFornitore']; ?></td>
              <td><?php echo htmlspecialchars($tipo['Nome']); ?></td>
              <td>
                <!-- Bottone Edit -->
                <button type="button" class="btn btn-sm btn-edit" title="Modifica" data-bs-toggle="modal" data-bs-target="#newTipoModal">
                  <i class="bi bi-pencil"></i>
                </button>
                <!-- Bottone Detail -->
                <button type="button" class="btn btn-sm btn-detail" title="Dettagli" data-bs-toggle="modal" data-bs-target="#detailTipoModal">
                  <i class="bi bi-info-circle"></i>
                </button>
                <!-- Bottone Archive con conferma -->
                <a href="<?php echo BASE_URL; ?>/anagrafiche/tipifornitori/archive?id=<?php echo $tipo['IDTipoFornitore']; ?>" 
                   class="btn btn-sm btn-archive" 
                   title="Archivia"
                   onclick="return confirm('Sei sicuro di voler archiviare questo tipo fornitore?');">
                  <i class="bi bi-archive"></i>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="3">Nessun tipo fornitore trovato.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
    
    <!-- Paginazione -->
    <nav aria-label="Paginazione">
      <ul class="pagination">
        <?php for ($i = 1; $i <= $pages; $i++): ?>
          <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
            <a class="page-link" href="<?php echo BASE_URL; ?>/anagrafiche/tipifornitori?page=<?php echo $i; ?>&<?php echo $queryString; ?>"><?php echo $i; ?></a>
          </li>
        <?php endfor; ?>
      </ul>
    </nav>
  </main>
</div>

<!-- Modal per Nuovo/Modifica Tipo Fornitore -->
<div class="modal fade" id="newTipoModal" tabindex="-1" aria-labelledby="newTipoModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="<?php echo BASE_URL; ?>/anagrafiche/tipifornitori/save" method="POST" novalidate>
        <div class="modal-header">
          <h5 class="modal-title" id="newTipoModalLabel">Nuovo Tipo Fornitore</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi" onclick="resetModalForm();"></button>
        </div>
        <div class="modal-body">
          <!-- Campo nascosto per l'ID (usato anche per l'edit) -->
          <input type="hidden" name="IDTipoFornitore" id="modal-IDTipoFornitore" value="">
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

<!-- Modal per Dettaglio Tipo Fornitore -->
<div class="modal fade" id="detailTipoModal" tabindex="-1" aria-labelledby="detailTipoModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailTipoModalLabel">Dettaglio Tipo Fornitore</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered">
          <tbody id="detailTipoBody">
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
  document.getElementById('newTipoModalLabel').innerText = "Nuovo Tipo Fornitore";
  document.getElementById('modal-IDTipoFornitore').value = "";
  document.getElementById('modal-nome').value = "";
}

// Gestione del bottone Edit: popola il modale con i dati del tipo fornitore
document.querySelectorAll('.btn-edit').forEach(function(button) {
  button.addEventListener('click', function() {
    var row = this.closest('tr');
    var tipo = JSON.parse(row.getAttribute('data-tipo'));
    
    document.getElementById('modal-IDTipoFornitore').value = tipo.IDTipoFornitore || "";
    document.getElementById('modal-nome').value = tipo.Nome || "";
    
    document.getElementById('newTipoModalLabel').innerText = "Modifica Tipo Fornitore";
  });
});

// Gestione del bottone Detail: popola il modale con i dati del tipo fornitore
document.querySelectorAll('.btn-detail').forEach(function(button) {
  button.addEventListener('click', function() {
    var row = this.closest('tr');
    var tipo = JSON.parse(row.getAttribute('data-tipo'));
    var html = '';
    for (var key in tipo) {
      if (tipo.hasOwnProperty(key)) {
        var value = tipo[key];
        // Se il campo è una data (Creato o Modificato), formattala in dd/mm/YYYY
        if (key === 'Creato' || key === 'Modificato') {
          var date = new Date(value);
          if (!isNaN(date)) {
            value = ('0' + date.getDate()).slice(-2) + '/' + ('0' + (date.getMonth()+1)).slice(-2) + '/' + date.getFullYear();
          }
        }
        html += '<tr><th>' + key + '</th><td>' + value + '</td></tr>';
      }
    }
    document.getElementById('detailTipoBody').innerHTML = html;
  });
});
</script>
<?php require_once BASE_PATH . 'app/Views/layout/footer.php'; ?>
