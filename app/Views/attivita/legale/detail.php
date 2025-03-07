<?php require_once BASE_PATH . 'app/Views/layout/header.php'; ?>

<?php
// Costruisci le mappe per convertire gli ID in nomi
$condominiMap = [];
foreach ($condomini as $condominio) {
    $condominiMap[$condominio['IDCondominio']] = $condominio['Nome'];
}

$statiMap = [];
foreach ($stati as $stato) {
    $statiMap[$stato['IDStato']] = $stato['Nome'];
}

$fornitoriMap = [];
foreach ($fornitori as $fornitore) {
    $fornitoriMap[$fornitore['IDFornitore']] = $fornitore['Nome'];
}
?>

<div class="main-wrapper">
  <?php require_once BASE_PATH . 'app/Views/layout/sidebar.php'; ?>
  <main class="content-area p-3">
    <!-- Header con titolo e tasto "Torna all'elenco" -->
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h1 class="h5 mb-0">Dettaglio Pratica Legale</h1>
      <a href="<?php echo BASE_URL; ?>/attivita/legale" class="btn btn-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Torna all'elenco
      </a>
    </div>
    
    <!-- Dati della pratica legale -->
    <div class="card mb-3">
      <div class="card-header bg-light">
        <h5 class="card-title mb-0">Dati Pratica Legale</h5>
      </div>
      <div class="card-body p-3">
        <table class="table table-sm table-borderless">
          <tbody>
            <tr>
              <th style="width: 30%;">Condominio</th>
              <td>
                <?php 
                  echo isset($condominiMap[$legale['IDCondominio']]) 
                        ? htmlspecialchars($condominiMap[$legale['IDCondominio']]) 
                        : htmlspecialchars($legale['IDCondominio']);
                ?>
              </td>
            </tr>
            <tr>
              <th>Fornitore</th>
              <td>
                <?php 
                  echo isset($fornitoriMap[$legale['IDFornitore']]) 
                        ? htmlspecialchars($fornitoriMap[$legale['IDFornitore']]) 
                        : htmlspecialchars($legale['IDFornitore']);
                ?>
              </td>
            </tr>
            <tr>
              <th>Stato</th>
              <td>
                <?php 
                  echo isset($statiMap[$legale['IDStato']]) 
                        ? htmlspecialchars($statiMap[$legale['IDStato']]) 
                        : htmlspecialchars($legale['IDStato']);
                ?>
              </td>
            </tr>
            <tr>
              <th>Data Apertura</th>
              <td>
                <?php 
                  $date = new DateTime($legale['DataApertura']);
                  echo $date->format('d/m/Y');
                ?>
              </td>
            </tr>
            <tr>
              <th>Titolo</th>
              <td><?php echo htmlspecialchars($legale['Titolo']); ?></td>
            </tr>
            <tr>
              <th>Descrizione</th>
              <td><?php echo htmlspecialchars($legale['Descrizione']); ?></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    
    <!-- Documenti Allegati -->
    <div class="card mb-3">
      <div class="card-header bg-light">
        <h5 class="card-title mb-0">Documenti Allegati</h5>
      </div>
      <div class="card-body p-3">
        <?php if (count($documenti) > 0): ?>
          <table class="table table-sm table-striped">
            <thead>
              <tr>
                <th>ID</th>
                <th>Titolo</th>
                <th>File</th>
                <th class="text-center">Azioni</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($documenti as $doc): ?>
                <tr data-documento="<?php echo htmlspecialchars(json_encode($doc), ENT_QUOTES, 'UTF-8'); ?>">
                  <td><?php echo $doc['IDLegaleDocumento']; ?></td>
                  <td><?php echo htmlspecialchars($doc['Titolo']); ?></td>
                  <td>
                    <a href="<?php echo BASE_URL . '/storage/uploads/' . htmlspecialchars($doc['File']); ?>" target="_blank">
                      <?php echo htmlspecialchars($doc['File']); ?>
                    </a>
                  </td>
                  <td class="text-center">
                    <button type="button" class="btn btn-sm btn-edit-doc" title="Modifica Titolo" data-bs-toggle="modal" data-bs-target="#editDocumentoModal">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <a href="<?php echo BASE_URL; ?>/attivita/legale_documenti/delete?id=<?php echo $doc['IDLegaleDocumento']; ?>&idLegale=<?php echo $legale['IDLegale']; ?>" class="btn btn-sm btn-delete-doc" title="Cancella Documento" onclick="return confirm('Sei sicuro di voler cancellare questo documento?');">
                      <i class="bi bi-trash"></i>
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php else: ?>
          <p class="mb-0">Nessun documento allegato.</p>
        <?php endif; ?>
        <div class="mt-2">
          <button type="button" class="btn btn-sm btn-custom-blue" data-bs-toggle="modal" data-bs-target="#addDocumentoModal">
            <i class="bi bi-plus-circle"></i> Aggiungi Documento
          </button>
        </div>
      </div>
    </div>
    
    <!-- Chat -->
    <div class="card mb-3">
      <div class="card-header bg-light">
        <h5 class="card-title mb-0">Chat</h5>
      </div>
      <div class="card-body p-3">
        <div id="chat-container" class="mb-3" style="max-height: 250px; overflow-y: auto; background: #f8f9fa; padding: 10px; border: 1px solid #ccc;">
          <?php if (count($chatMessages) > 0): ?>
            <?php foreach ($chatMessages as $msg): ?>
              <?php $isMine = ($msg['IDUser'] == $_SESSION['user']['IDUtente']); ?>
              <div class="chat-message <?php echo $isMine ? 'chat-mine' : 'chat-other'; ?>" style="margin-bottom: 8px; <?php echo $isMine ? 'text-align: right;' : 'text-align: left;'; ?>">
                <small>
                  <?php echo $isMine ? "Io" : htmlspecialchars($utentiMap[$msg['IDUser']] ?? $msg['IDUser']); ?> - 
                  <?php echo htmlspecialchars($msg['Data'] . ' ' . $msg['Orario']); ?>
                </small>
                <p style="background: <?php echo $isMine ? '#e2f0d9' : '#f0e2e2'; ?>; display: inline-block; padding: 5px 8px; border-radius: 10px; max-width: 70%; margin: 0;">
                  <?php echo htmlspecialchars($msg['Testo']); ?>
                </p>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <p class="mb-0">Nessun messaggio nella chat.</p>
          <?php endif; ?>
        </div>
        <form action="<?php echo BASE_URL; ?>/attivita/legale_chat/save" method="POST">
          <input type="hidden" name="IDLegale" value="<?php echo $legale['IDLegale']; ?>">
          <div class="input-group">
            <input type="text" name="Testo" class="form-control form-control-sm" placeholder="Scrivi un messaggio..." required>
            <button class="btn btn-sm btn-custom-blue" type="submit">
              <i class="bi bi-send"></i> Invia
            </button>
          </div>
        </form>
      </div>
    </div>
    
  </main>
</div>

<!-- Modal per Aggiungere Documento -->
<div class="modal fade" id="addDocumentoModal" tabindex="-1" aria-labelledby="addDocumentoModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="<?php echo BASE_URL; ?>/attivita/legale_documenti/save" method="POST" enctype="multipart/form-data" novalidate>
        <div class="modal-header">
          <h5 class="modal-title" id="addDocumentoModalLabel">Aggiungi Documento</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="IDLegale" value="<?php echo $legale['IDLegale']; ?>">
          <div class="mb-3">
            <label for="doc-titolo" class="form-label">Titolo</label>
            <input type="text" class="form-control" id="doc-titolo" name="Titolo" required>
          </div>
          <div class="mb-3">
            <label for="doc-file" class="form-label">File</label>
            <input type="file" class="form-control" id="doc-file" name="File" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Chiudi</button>
          <button type="submit" class="btn btn-sm btn-custom-blue">Salva Documento</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal per Modifica Documento -->
<div class="modal fade" id="editDocumentoModal" tabindex="-1" aria-labelledby="editDocumentoModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="<?php echo BASE_URL; ?>/attivita/legale_documenti/update" method="POST" novalidate>
        <div class="modal-header">
          <h5 class="modal-title" id="editDocumentoModalLabel">Modifica Titolo Documento</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="IDLegaleDocumento" id="modal-doc-ID" value="">
          <input type="hidden" name="IDLegale" id="modal-doc-IDLegale" value="">
          <div class="mb-3">
            <label for="modal-doc-titolo" class="form-label">Titolo</label>
            <input type="text" class="form-control" id="modal-doc-titolo" name="Titolo" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Chiudi</button>
          <button type="submit" class="btn btn-sm btn-custom-blue">Salva Modifiche</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
// Modal Edit Documento
document.querySelectorAll('.btn-edit-doc').forEach(function(button) {
  button.addEventListener('click', function() {
    var row = this.closest('tr');
    var doc = JSON.parse(row.getAttribute('data-documento'));
    document.getElementById('modal-doc-ID').value = doc.IDLegaleDocumento || "";
    document.getElementById('modal-doc-titolo').value = doc.Titolo || "";
    document.getElementById('modal-doc-IDLegale').value = doc.IDLegale || "";
  });
});
</script>

<?php require_once BASE_PATH . 'app/Views/layout/footer.php'; ?>
