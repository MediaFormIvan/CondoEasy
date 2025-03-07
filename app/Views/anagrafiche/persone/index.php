<?php
// app/Views/anagrafiche/persone/index.php
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
      <h1 class="h2">Elenco Persone</h1>
      <!-- Pulsante per nuovo inserimento -->
      <button type="button" class="btn btn-neutral" data-bs-toggle="modal" data-bs-target="#newPersonaModal" title="Nuova Persona" onclick="resetModalForm();">
        <i class="bi bi-plus-circle" style="font-size:1.5rem;"></i>
      </button>
    </div>
    
    <p>Visualizza e gestisci l'elenco delle persone. Utilizza i filtri per cercare per nome o per cognome.</p>
    
    <!-- Form di filtraggio -->
    <form method="GET" action="<?php echo BASE_URL; ?>/anagrafiche/persone" class="mb-4">
      <div class="row g-2 align-items-center">
        <div class="col-auto">
          <input type="text" name="nome" class="form-control" placeholder="Cerca per nome" value="<?php echo htmlspecialchars($_GET['nome'] ?? ''); ?>">
        </div>
        <div class="col-auto">
          <input type="text" name="cognome" class="form-control" placeholder="Cerca per cognome" value="<?php echo htmlspecialchars($_GET['cognome'] ?? ''); ?>">
        </div>
        <div class="col-auto">
          <!-- Pulsanti per Filtra / Esporta PDF / Esporta Excel -->
          <button type="submit" class="btn btn-neutral" title="Filtra">
            <i class="bi bi-search" style="font-size:1.25rem;"></i>
          </button>
          <a href="<?php echo BASE_URL; ?>/anagrafiche/persone/exportPdf?<?php echo $queryString; ?>" class="btn btn-neutral" title="Esporta PDF" target="_blank">
            <i class="bi bi-file-earmark-pdf" style="font-size:1.25rem;"></i>
          </a>
          <a href="<?php echo BASE_URL; ?>/anagrafiche/persone/exportExcel?<?php echo $queryString; ?>" class="btn btn-neutral" title="Esporta Excel" target="_blank">
            <i class="bi bi-file-earmark-spreadsheet" style="font-size:1.25rem;"></i>
          </a>
        </div>
      </div>
    </form>
    
    <!-- Tabella delle persone -->
    <table class="table table-striped">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nome</th>
          <th>Cognome</th>
          <th>Codice Fiscale</th>
          <th>Indirizzo</th>
          <th>CAP</th>
          <th>Città</th>
          <th>Provincia</th>
          <th>Telefono</th>
          <th>Telefono2</th>
          <th>Mail</th>
          <th>Pec</th>
          <th>Azioni</th>
        </tr>
      </thead>
      <tbody>
        <?php if (is_array($persone) && count($persone) > 0): ?>
          <?php foreach ($persone as $persona): ?>
            <tr data-persona="<?php echo htmlspecialchars(json_encode($persona), ENT_QUOTES, 'UTF-8'); ?>">
              <td><?php echo $persona['IDPersona']; ?></td>
              <td><?php echo htmlspecialchars($persona['Nome']); ?></td>
              <td><?php echo htmlspecialchars($persona['Cognome']); ?></td>
              <td><?php echo htmlspecialchars($persona['CodiceFiscale']); ?></td>
              <td><?php echo htmlspecialchars($persona['Indirizzo']); ?></td>
              <td><?php echo htmlspecialchars($persona['Cap']); ?></td>
              <td><?php echo htmlspecialchars($persona['Citta']); ?></td>
              <td><?php echo htmlspecialchars($persona['Provincia']); ?></td>
              <td><?php echo htmlspecialchars($persona['Telefono']); ?></td>
              <td><?php echo htmlspecialchars($persona['Telefono2']); ?></td>
              <td><?php echo htmlspecialchars($persona['Mail']); ?></td>
              <td><?php echo htmlspecialchars($persona['Pec']); ?></td>
              <td>
                <!-- Bottone Edit -->
                <button type="button" class="btn btn-sm btn-edit" title="Modifica" data-bs-toggle="modal" data-bs-target="#newPersonaModal">
                  <i class="bi bi-pencil"></i>
                </button>
                <!-- Bottone Detail -->
                <button type="button" class="btn btn-sm btn-detail" title="Dettagli" data-bs-toggle="modal" data-bs-target="#detailPersonaModal">
                  <i class="bi bi-info-circle"></i>
                </button>
                <!-- Bottone Archive con conferma -->
                <a href="<?php echo BASE_URL; ?>/anagrafiche/persone/archive?id=<?php echo $persona['IDPersona']; ?>" 
                   class="btn btn-sm btn-archive" 
                   title="Archivia"
                   onclick="return confirm('Sei sicuro di voler archiviare questa persona?');">
                  <i class="bi bi-archive"></i>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="13">Nessuna persona trovata.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
    
    <!-- Paginazione -->
    <nav aria-label="Paginazione">
      <ul class="pagination">
        <?php for ($i = 1; $i <= $pages; $i++): ?>
          <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
            <a class="page-link" href="<?php echo BASE_URL; ?>/anagrafiche/persone?page=<?php echo $i; ?>&<?php echo $queryString; ?>"><?php echo $i; ?></a>
          </li>
        <?php endfor; ?>
      </ul>
    </nav>
  </main>
</div>

<!-- Modal per Nuova/Modifica Persona -->
<div class="modal fade" id="newPersonaModal" tabindex="-1" aria-labelledby="newPersonaModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form action="<?php echo BASE_URL; ?>/anagrafiche/persone/save" method="POST" novalidate>
        <div class="modal-header">
          <h5 class="modal-title" id="newPersonaModalLabel">Nuova Persona</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi" onclick="resetModalForm();"></button>
        </div>
        <div class="modal-body">
          <!-- Campo nascosto per l'ID (utilizzato anche per l'edit) -->
          <input type="hidden" name="IDPersona" id="modal-IDPersona" value="">
          <div class="row mb-3">
            <div class="col-md-4">
              <label for="modal-nome" class="form-label">Nome</label>
              <input type="text" id="modal-nome" name="nome" class="form-control" required>
            </div>
            <div class="col-md-4">
              <label for="modal-cognome" class="form-label">Cognome</label>
              <input type="text" id="modal-cognome" name="cognome" class="form-control" required>
            </div>
            <div class="col-md-4">
              <label for="modal-codiceFiscale" class="form-label">Codice Fiscale</label>
              <input type="text" id="modal-codiceFiscale" name="codiceFiscale" class="form-control">
            </div>
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
            <div class="col-md-4">
              <label for="modal-citta" class="form-label">Città</label>
              <input type="text" id="modal-citta" name="citta" class="form-control">
            </div>
            <div class="col-md-5">
              <label for="modal-provincia" class="form-label">Provincia</label>
              <input type="text" id="modal-provincia" name="provincia" class="form-control">
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-4">
              <label for="modal-telefono" class="form-label">Telefono</label>
              <input type="text" id="modal-telefono" name="telefono" class="form-control">
            </div>
            <div class="col-md-4">
              <label for="modal-telefono2" class="form-label">Telefono 2</label>
              <input type="text" id="modal-telefono2" name="telefono2" class="form-control">
            </div>
            <div class="col-md-4">
              <label for="modal-mail" class="form-label">Mail</label>
              <input type="email" id="modal-mail" name="mail" class="form-control">
            </div>
          </div>
          <div class="mb-3">
            <label for="modal-pec" class="form-label">PEC</label>
            <input type="email" id="modal-pec" name="pec" class="form-control">
          </div>
          <div class="mb-3">
            <label for="modal-note" class="form-label">Note</label>
            <textarea id="modal-note" name="note" class="form-control" rows="3"></textarea>
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

<!-- Modal per Dettaglio Persona -->
<div class="modal fade" id="detailPersonaModal" tabindex="-1" aria-labelledby="detailPersonaModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailPersonaModalLabel">Dettaglio Persona</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered">
          <tbody id="detailPersonaBody">
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
  document.getElementById('newPersonaModalLabel').innerText = "Nuova Persona";
  document.getElementById('modal-IDPersona').value = "";
  document.getElementById('modal-nome').value = "";
  document.getElementById('modal-cognome').value = "";
  document.getElementById('modal-codiceFiscale').value = "";
  document.getElementById('modal-indirizzo').value = "";
  document.getElementById('modal-cap').value = "";
  document.getElementById('modal-citta').value = "";
  document.getElementById('modal-provincia').value = "";
  document.getElementById('modal-telefono').value = "";
  document.getElementById('modal-telefono2').value = "";
  document.getElementById('modal-mail').value = "";
  document.getElementById('modal-pec').value = "";
  document.getElementById('modal-note').value = "";
}

// Gestione del bottone Edit: popola il modale con i dati della persona
document.querySelectorAll('.btn-edit').forEach(function(button) {
  button.addEventListener('click', function() {
    var row = this.closest('tr');
    var persona = JSON.parse(row.getAttribute('data-persona'));
    
    document.getElementById('modal-IDPersona').value = persona.IDPersona || "";
    document.getElementById('modal-nome').value = persona.Nome || "";
    document.getElementById('modal-cognome').value = persona.Cognome || "";
    document.getElementById('modal-codiceFiscale').value = persona.CodiceFiscale || "";
    document.getElementById('modal-indirizzo').value = persona.Indirizzo || "";
    document.getElementById('modal-cap').value = persona.Cap || "";
    document.getElementById('modal-citta').value = persona.Citta || "";
    document.getElementById('modal-provincia').value = persona.Provincia || "";
    document.getElementById('modal-telefono').value = persona.Telefono || "";
    document.getElementById('modal-telefono2').value = persona.Telefono2 || "";
    document.getElementById('modal-mail').value = persona.Mail || "";
    document.getElementById('modal-pec').value = persona.Pec || "";
    document.getElementById('modal-note').value = persona.Note || "";
    
    document.getElementById('newPersonaModalLabel').innerText = "Modifica Persona";
  });
});

// Gestione del bottone Detail: popola il modale con i dati della persona
document.querySelectorAll('.btn-detail').forEach(function(button) {
  button.addEventListener('click', function() {
    var row = this.closest('tr');
    var persona = JSON.parse(row.getAttribute('data-persona'));
    var html = '';
    for (var key in persona) {
      if (persona.hasOwnProperty(key)) {
        var value = persona[key];
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
    document.getElementById('detailPersonaBody').innerHTML = html;
  });
});
</script>
<?php require_once BASE_PATH . 'app/Views/layout/footer.php'; ?>
