<?php require_once BASE_PATH . 'app/Views/layout/header.php'; ?>

<div class="main-wrapper">
  <?php require_once BASE_PATH . 'app/Views/layout/sidebar.php'; ?>
  <main class="content-area p-3" style="font-size: calc(1rem - 1px);">

    <?php if (isset($selectedGestione)): ?>
      <div class="mb-3 p-2" style="background-color: #d4edda;">
        Gestione selezionata: <strong><?php echo htmlspecialchars($selectedGestione['Nome']); ?></strong>
        (Condominio: <?php echo htmlspecialchars($selectedGestione['CondominioNome'] ?? 'N/A'); ?>)
      </div>
    <?php else: ?>
      <div class="mb-3 p-2" style="background-color: #f8d7da;">
        Nessuna gestione selezionata
      </div>
    <?php endif; ?>



    <!-- Titolo della sezione -->
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h1 class="h5 mb-0">Elenco Gestioni Bilanci</h1>
    </div>

    <!-- Elenco dei condominii -->
    <?php foreach ($condomini as $condominio): ?>
      <div class="card mb-2">
        <div class="card-header d-flex justify-content-between align-items-center"
          style="cursor: pointer;"
          data-bs-toggle="collapse"
          data-bs-target="#condominio-<?php echo $condominio['IDCondominio']; ?>">
          <span><?php echo htmlspecialchars($condominio['Nome']); ?></span>
          <!-- Pulsante +: apre il modale per nuova gestione, passando l'ID del condominio -->
          <button type="button" class="btn btn-sm btn-success" title="Nuova Gestione"
            onclick="openNewGestioneModal(<?php echo $condominio['IDCondominio']; ?>)">
            <i class="bi bi-plus-circle"></i>
          </button>
        </div>
        <div id="condominio-<?php echo $condominio['IDCondominio']; ?>" class="collapse">
          <!-- Accordion per le gestioni aperte -->
          <div class="accordion" id="accordion-open-<?php echo $condominio['IDCondominio']; ?>">
            <div class="accordion-item">
              <h2 class="accordion-header">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#open-<?php echo $condominio['IDCondominio']; ?>">
                  Gestioni Aperte
                </button>
              </h2>
              <div id="open-<?php echo $condominio['IDCondominio']; ?>" class="accordion-collapse collapse show">
                <div class="accordion-body p-2">
                  <?php if (!empty($condominio['gestioni_aperti'])): ?>
                    <table class="table table-striped table-sm">
                      <thead>
                        <tr>
                          <th>Nome Gestione</th>
                          <th>Data Inizio</th>
                          <th class="text-center">Azioni</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($condominio['gestioni_aperti'] as $gestione): ?>
                          <tr data-gestione='<?php echo json_encode($gestione); ?>'>
                            <td><?php echo htmlspecialchars($gestione['Nome']); ?></td>
                            <td><?php echo date("d/m/Y", strtotime($gestione['DataInizio'])); ?></td>
                            <td class="text-center">
                              <button type="button" class="btn btn-sm btn-warning btn-edit-gestione" title="Modifica">
                                <i class="bi bi-pencil"></i>
                              </button>
                              <a href="<?php echo BASE_URL; ?>/gestioni/bilanci/archive?id=<?php echo $gestione['IDGestione']; ?>" class="btn btn-sm btn-secondary" title="Archivia" onclick="return confirm('Sei sicuro di archiviare questa gestione?');">
                                <i class="bi bi-archive"></i>
                              </a>
                              <a href="<?php echo BASE_URL; ?>/gestioni/bilanci/delete?id=<?php echo $gestione['IDGestione']; ?>" class="btn btn-sm btn-danger" title="Elimina" onclick="return confirm('Sei sicuro di eliminare questa gestione?');">
                                <i class="bi bi-trash"></i>
                              </a>
                              <a href="<?php echo BASE_URL; ?>/gestioni/bilanci/select?id=<?php echo $gestione['IDGestione']; ?>" class="btn btn-sm btn-info" title="Seleziona">
                                <i class="bi bi-check-circle"></i>
                              </a>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  <?php else: ?>
                    <p class="mb-0">Nessuna gestione aperta.</p>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>

          <!-- Accordion per le gestioni chiuse -->
          <div class="accordion mt-2" id="accordion-closed-<?php echo $condominio['IDCondominio']; ?>">
            <div class="accordion-item">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#closed-<?php echo $condominio['IDCondominio']; ?>">
                  Gestioni Chiuse
                </button>
              </h2>
              <div id="closed-<?php echo $condominio['IDCondominio']; ?>" class="accordion-collapse collapse">
                <div class="accordion-body p-2">
                  <?php if (!empty($condominio['gestioni_chiusi'])): ?>
                    <table class="table table-striped table-sm">
                      <thead>
                        <tr>
                          <th>Nome Gestione</th>
                          <th>Data Inizio</th>
                          <th class="text-center">Azioni</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($condominio['gestioni_chiusi'] as $gestione): ?>
                          <tr data-gestione='<?php echo json_encode($gestione); ?>'>
                            <td><?php echo htmlspecialchars($gestione['Nome']); ?></td>
                            <td><?php echo date("d/m/Y", strtotime($gestione['DataInizio'])); ?></td>
                            <td class="text-center">
                              <button type="button" class="btn btn-sm btn-warning btn-edit-gestione" title="Modifica">
                                <i class="bi bi-pencil"></i>
                              </button>
                              <a href="<?php echo BASE_URL; ?>/gestioni/bilanci/delete?id=<?php echo $gestione['IDGestione']; ?>" class="btn btn-sm btn-danger" title="Elimina" onclick="return confirm('Sei sicuro di eliminare questa gestione?');">
                                <i class="bi bi-trash"></i>
                              </a>
                              <a href="<?php echo BASE_URL; ?>/gestioni/bilanci/select?id=<?php echo $gestione['IDGestione']; ?>" class="btn btn-sm btn-info" title="Seleziona">
                                <i class="bi bi-check-circle"></i>
                              </a>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  <?php else: ?>
                    <p class="mb-0">Nessuna gestione chiusa.</p>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    <?php endforeach; ?>

  </main>
</div>

<!-- Modale per Creazione/Modifica Gestione -->
<div class="modal fade" id="newGestioneModal" tabindex="-1" aria-labelledby="newGestioneModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form action="<?php echo BASE_URL; ?>/gestioni/bilanci/save" method="POST" novalidate>
        <div class="modal-header">
          <h5 class="modal-title" id="newGestioneModalLabel">Nuova Gestione</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi" onclick="resetGestioneModal();"></button>
        </div>
        <div class="modal-body">
          <!-- Campo nascosto per IDGestione (usato anche in modifica) -->
          <input type="hidden" name="IDGestione" id="modal-IDGestione" value="">
          <!-- Campo nascosto per IDCondominio (preimpostato dalla riga del condominio) -->
          <input type="hidden" name="IDCondominio" id="modal-IDCondominio" value="">

          <!-- Selezione del Tipo di Gestione -->
          <div class="mb-3">
            <label for="modal-IDTipoGestione" class="form-label">Tipo Gestione</label>
            <select id="modal-IDTipoGestione" name="IDTipoGestione" class="form-select" required>
              <option value="">Seleziona Tipo</option>
              <?php
              $tipiGestioni = $this->tipiGestioniModel->getAll();
              foreach ($tipiGestioni as $tipo): ?>
                <option value="<?php echo $tipo['IDTipoGestione']; ?>"><?php echo htmlspecialchars($tipo['Nome']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Nome Gestione -->
          <div class="mb-3">
            <label for="modal-Nome" class="form-label">Nome Gestione</label>
            <input type="text" id="modal-Nome" name="Nome" class="form-control" required>
          </div>

          <!-- Data Inizio -->
          <div class="mb-3">
            <label for="modal-DataInizio" class="form-label">Data Inizio</label>
            <input type="date" id="modal-DataInizio" name="DataInizio" class="form-control" required>
          </div>

          <!-- Data Fine -->
          <div class="mb-3">
            <label for="modal-DataFine" class="form-label">Data Fine</label>
            <input type="date" id="modal-DataFine" name="DataFine" class="form-control">
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="resetGestioneModal();">Chiudi</button>
          <button type="submit" class="btn btn-neutral">Salva</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  // Funzione per resettare il form del modale Gestione
  function resetGestioneModal() {
    document.getElementById('newGestioneModalLabel').innerText = "Nuova Gestione";
    document.getElementById('modal-IDGestione').value = "";
    // Il campo IDCondominio verrà impostato al momento dell'apertura
    document.getElementById('modal-Nome').value = "";
    document.getElementById('modal-IDTipoGestione').value = "";
    document.getElementById('modal-DataInizio').value = new Date().toISOString().slice(0, 10);
    document.getElementById('modal-DataFine').value = "";
  }

  // Funzione per aprire il modale in modalità creazione e preimpostare l'IDCondominio
  function openNewGestioneModal(condominioId) {
    resetGestioneModal();
    document.getElementById('modal-IDCondominio').value = condominioId;
    var modal = new bootstrap.Modal(document.getElementById('newGestioneModal'));
    modal.show();
  }

  // Gestione del pulsante Modifica: popola il modale con i dati della gestione selezionata
  document.querySelectorAll('.btn-edit-gestione').forEach(function(button) {
    button.addEventListener('click', function() {
      var row = this.closest('tr');
      var gestione = JSON.parse(row.getAttribute('data-gestione'));
      document.getElementById('modal-IDGestione').value = gestione.IDGestione || "";
      document.getElementById('modal-IDCondominio').value = gestione.IDCondominio || "";
      document.getElementById('modal-IDTipoGestione').value = gestione.IDTipoGestione || "";
      document.getElementById('modal-Nome').value = gestione.Nome || "";
      var dataInizio = new Date(gestione.DataInizio);
      document.getElementById('modal-DataInizio').value = !isNaN(dataInizio) ? dataInizio.toISOString().slice(0, 10) : "";
      document.getElementById('modal-DataFine').value = gestione.DataFine ? new Date(gestione.DataFine).toISOString().slice(0, 10) : "";
      document.getElementById('newGestioneModalLabel').innerText = "Modifica Gestione";
      var modal = new bootstrap.Modal(document.getElementById('newGestioneModal'));
      modal.show();
    });
  });
</script>

<?php require_once BASE_PATH . 'app/Views/layout/footer.php'; ?>