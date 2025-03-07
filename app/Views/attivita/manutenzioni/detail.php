<?php require_once BASE_PATH . 'app/Views/layout/header.php'; ?>

<div class="main-wrapper">
  <?php require_once BASE_PATH . 'app/Views/layout/sidebar.php'; ?>
  <main class="content-area p-3">
    <!-- Header con tasto per tornare all'elenco -->
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h1 class="h5 mb-0">Dettaglio Manutenzione</h1>
      <a href="<?php echo BASE_URL; ?>/attivita/manutenzioni" class="btn btn-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Torna all'elenco
      </a>
    </div>

    <!-- PARTE UNO: Dati della Manutenzione -->
    <div class="card mb-3">
      <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Dati Manutenzione</h5>
        <div>
          <!-- Pulsante per invio Email -->
          <a href="<?php echo BASE_URL; ?>/attivita/manutenzioni/sendMail?id=<?php echo $manutenzione['IDManutenzione']; ?>"
            class="btn btn-sm btn-custom-blue" title="Invia Email">
            <i class="bi bi-envelope"></i> Invia Email
          </a>

          <!-- Pulsante per invio WhatsApp -->
          <?php
          // Recupera il nome del condominio dalla mappa
          $nomeCondominio = isset($condominiMap[$manutenzione['IDCondominio']])
            ? $condominiMap[$manutenzione['IDCondominio']]
            : 'Condominio sconosciuto';

          // Ricerca il fornitore associato alla manutenzione per ottenere telefono e nome
          $fornitoreTelefono = '';
          $nomeFornitore = 'Fornitore sconosciuto';
          if (isset($fornitori) && is_array($fornitori)) {
            foreach ($fornitori as $fornitore) {
              if ($fornitore['IDFornitore'] == $manutenzione['IDFornitore']) {
                $fornitoreTelefono = $fornitore['Telefono'] ?? '';
                $nomeFornitore = $fornitore['Nome'] ?? 'Fornitore sconosciuto';
                break;
              }
            }
          }

          // Componi il messaggio precompilato (stesso schema della mail)
          $whatsAppMessage = "Spett.le $nomeFornitore,\n";
          $whatsAppMessage .= "con la presente si richiede intervento nel condominio in oggetto in merito alla problematica: " . $manutenzione['Titolo'] . ".\n";
          $whatsAppMessage .= "Per questa situazione si dettaglia che: " . $manutenzione['Descrizione'] . ".\n";
          $whatsAppMessage .= "Restiamo in attesa di cortese riscontro su questa mail o su Whatsapp al numero 3515160645.\n";
          $whatsAppMessage .= "Cordiali saluti\nGPI SRL";
          ?>
          <!-- Utilizziamo il link diretto a WhatsApp Web -->
          <a href="https://web.whatsapp.com/send?phone=<?php echo urlencode($fornitoreTelefono); ?>&text=<?php echo urlencode($whatsAppMessage); ?>"
            class="btn btn-sm btn-custom-blue" title="Invia WhatsApp" target="_blank">
            <i class="bi bi-whatsapp"></i> Invia WhatsApp
          </a>
        </div>
      </div>



      <div class="card-body p-3">
        <!-- Esempio di tabella dei dati della manutenzione -->
        <table class="table table-sm table-borderless">
          <tbody>
            <?php foreach ($manutenzione as $key => $value): ?>
              <tr>
                <th style="width: 30%;"><?php echo htmlspecialchars($key); ?></th>
                <td>
                  <?php
                  if ($key === 'IDCondominio' && isset($condominiMap[$value])) {
                    echo htmlspecialchars($condominiMap[$value]);
                  } elseif ($key === 'IDFornitore' && $value && isset($fornitoriMap[$value])) {
                    echo htmlspecialchars($fornitoriMap[$value]);
                  } elseif ($key === 'IDStato' && isset($statiMap[$value])) {
                    echo htmlspecialchars($statiMap[$value]);
                  } elseif ($key === 'IDUser' && isset($utentiMap[$value])) {
                    echo htmlspecialchars($utentiMap[$value]);
                  } else {
                    echo htmlspecialchars($value);
                  }
                  ?>
                </td>
              </tr>
            <?php endforeach; ?>

            <!-- Nuova riga per il Sinistro Associato -->
            <tr>
              <th>Sinistro associato</th>
              <td>
                <?php if (isset($manutenzioneSinistro) && $manutenzioneSinistro): ?>
                  <?php
                  // Recupera l'ID sinistro associato e, eventualmente, altre info (ad es. titolo)
                  $IDSinistro = $manutenzioneSinistro['IDSinistro'];
                  // Si ipotizza che il modello Sinistri fornisca anche un getById() per recuperare i dettagli
                  $sinistroAssoc = (new \App\Models\Sinistro())->getById($IDSinistro);
                  $sinistroTitolo = $sinistroAssoc ? $sinistroAssoc['Titolo'] : "Sinistro #$IDSinistro";
                  ?>
                  <a href="<?php echo BASE_URL; ?>/attivita/sinistri/detail?id=<?php echo $IDSinistro; ?>">
                    <?php echo htmlspecialchars($sinistroTitolo); ?>
                  </a>
                <?php else: ?>
                  <button type="button" class="btn btn-sm btn-custom-blue" data-bs-toggle="modal" data-bs-target="#associaSinistroModal">
                    + Associa sinistro
                  </button>
                <?php endif; ?>
              </td>
            </tr>

          </tbody>
        </table>

      </div>
    </div>

    <!-- PARTE DUE: Documenti Allegati -->
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
                  <td><?php echo $doc['IDManutenzioneDocumento']; ?></td>
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
                    <a href="<?php echo BASE_URL; ?>/attivita/manutenzioni_documenti/delete?id=<?php echo $doc['IDManutenzioneDocumento']; ?>&idManutenzione=<?php echo $manutenzione['IDManutenzione']; ?>" class="btn btn-sm btn-delete-doc" title="Elimina Documento">
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

    <!-- PARTE TRE: Chat -->
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
                <small><?php echo $isMine ? "Io" : htmlspecialchars($utentiMap[$msg['IDUser']] ?? $msg['IDUser']); ?> - <?php echo htmlspecialchars($msg['Data'] . ' ' . $msg['Orario']); ?></small>
                <p style="background: <?php echo $isMine ? '#e2f0d9' : '#f0e2e2'; ?>; display: inline-block; padding: 5px 8px; border-radius: 10px; max-width: 70%; margin: 0;">
                  <?php echo htmlspecialchars($msg['Testo']); ?>
                </p>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <p class="mb-0">Nessun messaggio nella chat.</p>
          <?php endif; ?>
        </div>
        <form action="<?php echo BASE_URL; ?>/attivita/manutenzioni_chat/save" method="POST">
          <input type="hidden" name="IDManutenzione" value="<?php echo $manutenzione['IDManutenzione']; ?>">
          <div class="input-group">
            <input type="text" name="Testo" class="form-control form-control-sm" placeholder="Scrivi un messaggio..." required>
            <button class="btn btn-sm btn-custom-blue" type="submit"><i class="bi bi-send"></i> Invia</button>
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
      <form action="<?php echo BASE_URL; ?>/attivita/manutenzioni_documenti/save" method="POST" enctype="multipart/form-data" novalidate>
        <div class="modal-header">
          <h5 class="modal-title" id="addDocumentoModalLabel">Aggiungi Documento</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="IDManutenzione" value="<?php echo $manutenzione['IDManutenzione']; ?>">
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

<!-- Modal per Modifica Documento (Edit Titolo) -->
<div class="modal fade" id="editDocumentoModal" tabindex="-1" aria-labelledby="editDocumentoModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="<?php echo BASE_URL; ?>/attivita/manutenzioni_documenti/update" method="POST" novalidate>
        <div class="modal-header">
          <h5 class="modal-title" id="editDocumentoModalLabel">Modifica Titolo Documento</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="IDManutenzioneDocumento" id="modal-doc-ID" value="">
          <input type="hidden" name="IDManutenzione" id="modal-doc-IDManutenzione" value="">
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
<!-- Modale per associare un sinistro -->
<div class="modal fade" id="associaSinistroModal" tabindex="-1" aria-labelledby="associaSinistroModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="<?php echo BASE_URL; ?>/attivita/manutenzioni/associaSinistro" method="POST">
        <div class="modal-header">
          <h5 class="modal-title" id="associaSinistroModalLabel">Associa Sinistro</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="IDManutenzione" value="<?php echo $manutenzione['IDManutenzione']; ?>">
          <div class="mb-3">
            <label for="IDSinistro" class="form-label">Seleziona Sinistro</label>
            <select id="IDSinistro" name="IDSinistro" class="form-select" required>
              <option value="">Seleziona Sinistro</option>
              <?php foreach ($sinistriAperti as $sinistro): ?>
                <option value="<?php echo $sinistro['IDSinistro']; ?>">
                  <?php echo htmlspecialchars($sinistro['Titolo']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Annulla</button>
          <button type="submit" class="btn btn-custom-blue btn-sm">Associa</button>
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
      document.getElementById('modal-doc-ID').value = doc.IDManutenzioneDocumento || "";
      document.getElementById('modal-doc-titolo').value = doc.Titolo || "";
      document.getElementById('modal-doc-IDManutenzione').value = doc.IDManutenzione || "";
    });
  });
</script>

<?php require_once BASE_PATH . 'app/Views/layout/footer.php'; ?>