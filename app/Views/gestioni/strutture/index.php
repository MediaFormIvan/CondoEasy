<?php require_once BASE_PATH . 'app/Views/layout/header.php'; ?>

<!-- Stili personalizzati per la struttura -->
<style>
  .fabbricato-row {
    background-color: #343a40;
    /* sfondo scuro */
    color: #ffffff;
    /* testo chiaro */
    padding: 8px 12px;
    margin-bottom: 4px;
    border-radius: 4px;
  }

  .civico-row {
    font-style: italic;
    padding: 6px 12px;
    border-bottom: 1px solid #ddd;
  }

  .scala-row {
    text-decoration: underline;
    padding: 6px 12px;
    border-bottom: 1px dashed #ccc;
  }

  .unita-row {
    font-weight: bold;
    padding: 4px 12px;
    border-bottom: 1px solid #eee;
  }

  .action-buttons {
    display: flex;
    gap: 4px;
    justify-content: flex-end;
  }

  .level-container {
    margin-left: 20px;
  }

  .persona-link {
    text-decoration: none;
    color: #007bff;
    cursor: pointer;
  }

  .persona-link:hover {
    text-decoration: underline;
  }
</style>

<div class="main-wrapper">
  <?php require_once BASE_PATH . 'app/Views/layout/sidebar.php'; ?>
  <main class="content-area p-3">

    <!-- Intestazione: visualizza il condominio selezionato -->
    <?php if (isset($condominio)): ?>
      <div class="mb-3 p-2" style="background-color: #d4edda;">
        <strong><?php echo htmlspecialchars($condominio['Nome']); ?></strong> - Struttura Condominiale
      </div>
    <?php else: ?>
      <div class="mb-3 p-2" style="background-color: #f8d7da;">
        Nessun condominio selezionato.
      </div>
    <?php endif; ?>

    <?php if (isset($condominio)): ?>
      <ul class="list-group">
        <!-- Sezione Fabbricati -->
        <?php if (!empty($condominio['fabbricati'])): ?>
          <?php foreach ($condominio['fabbricati'] as $fabbricato): ?>
            <li class="list-group-item fabbricato-row d-flex justify-content-between align-items-center">
              <span>
                <i class="bi bi-building me-1"></i>
                <?php echo htmlspecialchars($fabbricato['Nome']); ?>
              </span>
              <div class="action-buttons">
                <button type="button" class="btn btn-sm btn-warning" title="Modifica Fabbricato" onclick="openEditFabbricatoModal(<?php echo $fabbricato['IDFabbricato']; ?>)">
                  <i class="bi bi-pencil"></i>
                </button>
                <a href="<?php echo BASE_URL; ?>/gestioni/strutture/deleteFabbricato?id=<?php echo $fabbricato['IDFabbricato']; ?>" class="btn btn-sm btn-danger" title="Elimina Fabbricato" onclick="return confirm('Eliminare fabbricato?');">
                  <i class="bi bi-trash"></i>
                </a>
                <button type="button" class="btn btn-sm btn-success" title="Nuovo Civico" onclick="openNewCivicoModal(<?php echo $fabbricato['IDFabbricato']; ?>)">
                  <i class="bi bi-plus-circle"></i>
                </button>
              </div>
            </li>
            <!-- Civici -->
            <li class="list-group-item level-container">
              <?php if (!empty($fabbricato['civici'])): ?>
                <?php foreach ($fabbricato['civici'] as $civico): ?>
                  <div class="civico-row d-flex justify-content-between align-items-center">
                    <span>
                      <i class="bi bi-house-door me-1"></i>
                      <?php echo htmlspecialchars($civico['Nome']); ?>
                    </span>
                    <div class="action-buttons">
                      <button type="button" class="btn btn-sm btn-warning" title="Modifica Civico" onclick="openEditCivicoModal(<?php echo $civico['IDCivico']; ?>)">
                        <i class="bi bi-pencil"></i>
                      </button>
                      <a href="<?php echo BASE_URL; ?>/gestioni/strutture/deleteCivico?id=<?php echo $civico['IDCivico']; ?>" class="btn btn-sm btn-danger" title="Elimina Civico" onclick="return confirm('Eliminare civico?');">
                        <i class="bi bi-trash"></i>
                      </a>
                      <button type="button" class="btn btn-sm btn-success" title="Nuova Scala" onclick="openNewScalaModal(<?php echo $civico['IDCivico']; ?>)">
                        <i class="bi bi-plus-circle"></i>
                      </button>
                    </div>
                  </div>
                  <!-- Scale -->
                  <div class="level-container">
                    <?php if (!empty($civico['scale'])): ?>
                      <?php foreach ($civico['scale'] as $scala): ?>
                        <div class="scala-row d-flex justify-content-between align-items-center">
                          <span>
                            <i class="bi bi-arrow-up-square me-1"></i>
                            <?php echo htmlspecialchars($scala['Nome']); ?>
                          </span>
                          <div class="action-buttons">
                            <button type="button" class="btn btn-sm btn-warning" title="Modifica Scala" onclick="openEditScalaModal(<?php echo $scala['IDScala']; ?>)">
                              <i class="bi bi-pencil"></i>
                            </button>
                            <a href="<?php echo BASE_URL; ?>/gestioni/strutture/deleteScala?id=<?php echo $scala['IDScala']; ?>" class="btn btn-sm btn-danger" title="Elimina Scala" onclick="return confirm('Eliminare scala?');">
                              <i class="bi bi-trash"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-success" title="Nuova Unità" onclick="openNewUnitaModal(<?php echo $scala['IDScala']; ?>)">
                              <i class="bi bi-plus-circle"></i>
                            </button>
                          </div>
                        </div>
                        <!-- Unità -->
                        <div class="level-container">
                          <?php if (!empty($scala['unita'])): ?>
                            <?php foreach ($scala['unita'] as $unita): ?>
                              <div class="unita-row d-flex justify-content-between align-items-center">
                                <span>
                                  <i class="bi bi-grid me-1"></i>
                                  <?php
                                  echo "Interno: " . htmlspecialchars($unita['Interno']) .
                                    " | Piano: " . htmlspecialchars($unita['Piano']) .
                                    " | Sezione: " . htmlspecialchars($unita['Sezione']);
                                  ?>
                                  <?php
                                  // Recupera le associazioni per questa unità
                                  $unitaPersoneModel = new \App\Models\UnitaPersone();
                                  $associations = $unitaPersoneModel->getByUnita($unita['IDUnita']);
                                  if (!empty($associations)) {
                                    echo " - ";
                                    $assocArr = [];
                                    foreach ($associations as $assoc) {
                                      $assocArr[] = '<a class="persona-link" onclick="openPersonaModal(' . $assoc['IDPersona'] . ')" title="Visualizza dettagli">' . htmlspecialchars($assoc['PersonaNome'] . ' ' . $assoc['Cognome']) . '</a>';
                                    }
                                    echo implode(", ", $assocArr);
                                  }
                                  ?>
                                </span>
                                <div class="action-buttons">
                                  <button type="button" class="btn btn-sm btn-warning" title="Modifica Unità" onclick="openEditUnitaModal(<?php echo $unita['IDUnita']; ?>)">
                                    <i class="bi bi-pencil"></i>
                                  </button>
                                  <a href="<?php echo BASE_URL; ?>/gestioni/strutture/deleteUnita?id=<?php echo $unita['IDUnita']; ?>" class="btn btn-sm btn-danger" title="Elimina Unità" onclick="return confirm('Eliminare unità?');">
                                    <i class="bi bi-trash"></i>
                                  </a>
                                  <button type="button" class="btn btn-sm btn-info" title="Gestisci Persone" onclick="openManagePersoneModal(<?php echo $unita['IDUnita']; ?>)">
                                    <i class="bi bi-people"></i>
                                  </button>
                                </div>
                              </div>
                            <?php endforeach; ?>
                          <?php else: ?>
                            <p class="ms-3">Nessuna unità presente.</p>
                          <?php endif; ?>
                        </div>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <p class="ms-3">Nessuna scala presente.</p>
                    <?php endif; ?>
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <p class="ms-3">Nessun civico presente.</p>
              <?php endif; ?>
</div>
</li>
<?php endforeach; ?>
<?php else: ?>
  <li class="list-group-item">
    <p>Nessun fabbricato presente.</p>
    <button type="button" class="btn btn-sm btn-success" title="Nuovo Fabbricato" onclick="openNewFabbricatoModal(<?php echo htmlspecialchars($condominio['IDCondominio']); ?>)">
      <i class="bi bi-plus-circle"></i> Aggiungi Fabbricato
    </button>
  </li>
<?php endif; ?>
</ul>
<?php endif; ?>

</main>
</div>

<!-- ################ Modali ################ -->

<!-- Modale per Nuovo Fabbricato -->
<div class="modal fade" id="newFabbricatoModal" tabindex="-1" aria-labelledby="newFabbricatoModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="<?php echo BASE_URL; ?>/gestioni/strutture/createFabbricato" method="POST" novalidate>
        <div class="modal-header">
          <h5 class="modal-title" id="newFabbricatoModalLabel">Nuovo Fabbricato</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="IDCondominio" id="fabbricato-IDCondominio" value="<?php echo htmlspecialchars($condominio['IDCondominio']); ?>">
          <div class="mb-3">
            <label for="fabbricato-Nome" class="form-label">Nome Fabbricato</label>
            <input type="text" class="form-control" id="fabbricato-Nome" name="Nome" required>
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

<!-- Modale per Edit Fabbricato -->
<div class="modal fade" id="editFabbricatoModal" tabindex="-1" aria-labelledby="editFabbricatoModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="<?php echo BASE_URL; ?>/gestioni/strutture/updateFabbricato" method="POST" novalidate>
        <div class="modal-header">
          <h5 class="modal-title" id="editFabbricatoModalLabel">Modifica Fabbricato</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="IDFabbricato" id="editFabbricato-IDFabbricato" value="">
          <input type="hidden" name="IDCondominio" id="editFabbricato-IDCondominio" value="<?php echo htmlspecialchars($condominio['IDCondominio']); ?>">
          <div class="mb-3">
            <label for="editFabbricato-Nome" class="form-label">Nome Fabbricato</label>
            <input type="text" class="form-control" id="editFabbricato-Nome" name="Nome" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
          <button type="submit" class="btn btn-neutral">Salva Modifiche</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modale per Nuovo Civico -->
<div class="modal fade" id="newCivicoModal" tabindex="-1" aria-labelledby="newCivicoModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="<?php echo BASE_URL; ?>/gestioni/strutture/createCivico" method="POST" novalidate>
        <div class="modal-header">
          <h5 class="modal-title" id="newCivicoModalLabel">Nuovo Civico</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="IDFabbricato" id="civico-IDFabbricato" value="">
          <div class="mb-3">
            <label for="civico-Nome" class="form-label">Nome Civico</label>
            <input type="text" class="form-control" id="civico-Nome" name="Nome" required>
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

<!-- Modale per Edit Civico -->
<div class="modal fade" id="editCivicoModal" tabindex="-1" aria-labelledby="editCivicoModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="<?php echo BASE_URL; ?>/gestioni/strutture/updateCivico" method="POST" novalidate>
        <div class="modal-header">
          <h5 class="modal-title" id="editCivicoModalLabel">Modifica Civico</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="IDCivico" id="editCivico-IDCivico" value="">
          <div class="mb-3">
            <label for="editCivico-Nome" class="form-label">Nome Civico</label>
            <input type="text" class="form-control" id="editCivico-Nome" name="Nome" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
          <button type="submit" class="btn btn-neutral">Salva Modifiche</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modale per Nuova Scala -->
<div class="modal fade" id="newScalaModal" tabindex="-1" aria-labelledby="newScalaModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="<?php echo BASE_URL; ?>/gestioni/strutture/createScala" method="POST" novalidate>
        <div class="modal-header">
          <h5 class="modal-title" id="newScalaModalLabel">Nuova Scala</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="IDCivico" id="scala-IDCivico" value="">
          <div class="mb-3">
            <label for="scala-Nome" class="form-label">Nome Scala</label>
            <input type="text" class="form-control" id="scala-Nome" name="Nome" required>
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

<!-- Modale per Edit Scala -->
<div class="modal fade" id="editScalaModal" tabindex="-1" aria-labelledby="editScalaModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="<?php echo BASE_URL; ?>/gestioni/strutture/updateScala" method="POST" novalidate>
        <div class="modal-header">
          <h5 class="modal-title" id="editScalaModalLabel">Modifica Scala</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="IDScala" id="editScala-IDScala" value="">
          <div class="mb-3">
            <label for="editScala-Nome" class="form-label">Nome Scala</label>
            <input type="text" class="form-control" id="editScala-Nome" name="Nome" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
          <button type="submit" class="btn btn-neutral">Salva Modifiche</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modale per Nuova Unità -->
<div class="modal fade" id="newUnitaModal" tabindex="-1" aria-labelledby="newUnitaModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form action="<?php echo BASE_URL; ?>/gestioni/strutture/createUnita" method="POST" novalidate>
        <div class="modal-header">
          <h5 class="modal-title" id="newUnitaModalLabel">Nuova Unità</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="IDScala" id="unita-IDScala" value="">
          <div class="row g-2">
            <div class="col-md-2">
              <label for="unita-Interno" class="form-label">Interno</label>
              <input type="text" class="form-control" id="unita-Interno" name="Interno">
            </div>
            <div class="col-md-2">
              <label for="unita-Piano" class="form-label">Piano</label>
              <input type="text" class="form-control" id="unita-Piano" name="Piano">
            </div>
            <div class="col-md-2">
              <label for="unita-Sezione" class="form-label">Sezione</label>
              <input type="text" class="form-control" id="unita-Sezione" name="Sezione">
            </div>
            <div class="col-md-2">
              <label for="unita-Foglio" class="form-label">Foglio</label>
              <input type="text" class="form-control" id="unita-Foglio" name="Foglio">
            </div>
            <div class="col-md-2">
              <label for="unita-Subalterno" class="form-label">Subalterno</label>
              <input type="text" class="form-control" id="unita-Subalterno" name="Subalterno">
            </div>
            <div class="col-md-2">
              <label for="unita-Categoria" class="form-label">Categoria</label>
              <input type="text" class="form-control" id="unita-Categoria" name="Categoria">
            </div>
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

<!-- Modale per Edit Unità -->
<div class="modal fade" id="editUnitaModal" tabindex="-1" aria-labelledby="editUnitaModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form action="<?php echo BASE_URL; ?>/gestioni/strutture/updateUnita" method="POST" novalidate>
        <div class="modal-header">
          <h5 class="modal-title" id="editUnitaModalLabel">Modifica Unità</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="IDUnita" id="editUnita-IDUnita" value="">
          <div class="row g-2">
            <div class="col-md-2">
              <label for="editUnita-Interno" class="form-label">Interno</label>
              <input type="text" class="form-control" id="editUnita-Interno" name="Interno">
            </div>
            <div class="col-md-2">
              <label for="editUnita-Piano" class="form-label">Piano</label>
              <input type="text" class="form-control" id="editUnita-Piano" name="Piano">
            </div>
            <div class="col-md-2">
              <label for="editUnita-Sezione" class="form-label">Sezione</label>
              <input type="text" class="form-control" id="editUnita-Sezione" name="Sezione">
            </div>
            <div class="col-md-2">
              <label for="editUnita-Foglio" class="form-label">Foglio</label>
              <input type="text" class="form-control" id="editUnita-Foglio" name="Foglio">
            </div>
            <div class="col-md-2">
              <label for="editUnita-Subalterno" class="form-label">Subalterno</label>
              <input type="text" class="form-control" id="editUnita-Subalterno" name="Subalterno">
            </div>
            <div class="col-md-2">
              <label for="editUnita-Categoria" class="form-label">Categoria</label>
              <input type="text" class="form-control" id="editUnita-Categoria" name="Categoria">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
          <button type="submit" class="btn btn-neutral">Salva Modifiche</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modale per Gestione Persone con campi predefiniti -->
<div class="modal fade" id="managePersoneModal" tabindex="-1" aria-labelledby="managePersoneModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form action="<?php echo BASE_URL; ?>/gestioni/strutture/managePersone" method="POST" novalidate>
        <div class="modal-header">
          <h5 class="modal-title" id="managePersoneModalLabel">Associa Persona all'Unità</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="IDUnita" id="managePersone-IDUnita" value="">
          <div class="mb-3">
            <label for="managePersone-IDPersona" class="form-label">Seleziona Persona</label>
            <select class="form-select" id="managePersone-IDPersona" name="IDPersona" required>
              <option value="">Seleziona Persona</option>
              <?php
              $personaModel = new \App\Models\Persona();
              $persone = $personaModel->getAll();
              foreach ($persone as $p) {
                echo '<option value="' . $p['IDPersona'] . '">' . htmlspecialchars($p['Nome'] . ' ' . $p['Cognome']) . '</option>';
              }
              ?>
            </select>
          </div>
          <div class="mb-3">
            <label for="managePersone-IDTipoPersona" class="form-label">Ruolo</label>
            <select class="form-select" id="managePersone-IDTipoPersona" name="IDTipoPersona" required>
              <option value="1" selected>Proprietario</option>
              <?php
              $tipiPersoneModel = new \App\Models\TipiPersone();
              $tipi = $tipiPersoneModel->getAll();
              foreach ($tipi as $tipo) {
                if ($tipo['IDTipoPersona'] != 1) {
                  echo '<option value="' . $tipo['IDTipoPersona'] . '">' . htmlspecialchars($tipo['Nome']) . '</option>';
                }
              }
              ?>
            </select>
          </div>
          <div class="mb-3">
            <label for="managePersone-Percentuale" class="form-label">Percentuale</label>
            <input type="number" step="0.01" class="form-control" id="managePersone-Percentuale" name="Percentuale" value="100" placeholder="Percentuale (facoltativo)">
          </div>
          <div class="mb-3">
            <label for="managePersone-DataInizio" class="form-label">Data Inizio</label>
            <input type="date" class="form-control" id="managePersone-DataInizio" name="DataInizio" value="2000-01-01" required>
          </div>
          <div class="mb-3">
            <label for="managePersone-DataFine" class="form-label">Data Fine</label>
            <input type="date" class="form-control" id="managePersone-DataFine" name="DataFine" value="2100-12-31">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
          <button type="submit" class="btn btn-neutral">Salva Associazione</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modale per Visualizzare Informazioni Persona -->
<div class="modal fade" id="personaModal" tabindex="-1" aria-labelledby="personaModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="personaModalLabel">Dettagli Persona</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
      </div>
      <div class="modal-body" id="personaModalBody">
        <p>Caricamento...</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
      </div>
    </div>
  </div>
</div>

<!-- JavaScript per gestire i modali -->
<script>
  // Fabbricato
  function openNewFabbricatoModal(IDCondominio) {
    document.getElementById('fabbricato-IDCondominio').value = IDCondominio;
    var modal = new bootstrap.Modal(document.getElementById('newFabbricatoModal'));
    modal.show();
  }
  // Civico
  function openNewCivicoModal(IDFabbricato) {
    document.getElementById('civico-IDFabbricato').value = IDFabbricato;
    var modal = new bootstrap.Modal(document.getElementById('newCivicoModal'));
    modal.show();
  }
  // Scala
  function openNewScalaModal(IDCivico) {
    document.getElementById('scala-IDCivico').value = IDCivico;
    var modal = new bootstrap.Modal(document.getElementById('newScalaModal'));
    modal.show();
  }
  // Unità
  function openNewUnitaModal(IDScala) {
    document.getElementById('unita-IDScala').value = IDScala;
    var modal = new bootstrap.Modal(document.getElementById('newUnitaModal'));
    modal.show();
  }
  // Gestione Persone
  function openManagePersoneModal(IDUnita) {
    document.getElementById('managePersone-IDUnita').value = IDUnita;
    var modal = new bootstrap.Modal(document.getElementById('managePersoneModal'));
    modal.show();
  }
  // Modale per Visualizzare Informazioni Persona
  function openPersonaModal(IDPersona) {
    console.log("Richiesta dettagli per IDPersona:", IDPersona);
    fetch("<?php echo BASE_URL; ?>/anagrafiche/persone/detailAjax?id=" + IDPersona)
      .then(response => {
        if (!response.ok) {
          throw new Error("HTTP error, status = " + response.status);
        }
        return response.json();
      })
      .then(data => {
        console.log("Dati JSON ricevuti:", data);
        if (data.error) {
          document.getElementById('personaModalBody').innerHTML = "<p>" + data.error + "</p>";
        } else {
          let html = "<p><strong>Nome:</strong> " + data.Nome + " " + data.Cognome + "</p>";
          html += "<p><strong>Codice Fiscale:</strong> " + data.CodiceFiscale + "</p>";
          html += "<p><strong>Indirizzo:</strong> " + data.Indirizzo + "</p>";
          html += "<p><strong>CAP:</strong> " + data.Cap + "</p>";
          html += "<p><strong>Città:</strong> " + data.Citta + "</p>";
          html += "<p><strong>Provincia:</strong> " + data.Provincia + "</p>";
          html += "<p><strong>Telefono:</strong> " + data.Telefono + "</p>";
          html += "<p><strong>Telefono2:</strong> " + data.Telefono2 + "</p>";
          html += "<p><strong>Mail:</strong> " + data.Mail + "</p>";
          html += "<p><strong>Pec:</strong> " + data.Pec + "</p>";
          html += "<p><strong>Note:</strong> " + data.Note + "</p>";
          document.getElementById('personaModalBody').innerHTML = html;
        }
        var modal = new bootstrap.Modal(document.getElementById('personaModal'));
        modal.show();
      })
      .catch(error => {
        console.error("Errore nella fetch:", error);
        document.getElementById('personaModalBody').innerHTML =
          "<p>Errore durante il caricamento dei dati.</p><p>Debug: " + error.message + "</p>";
        var modal = new bootstrap.Modal(document.getElementById('personaModal'));
        modal.show();
      });
  }


  // Funzioni per i modali di modifica (utilizzando endpoint AJAX per prepopolare i campi)
  function openEditFabbricatoModal(IDFabbricato) {
    fetch("<?php echo BASE_URL; ?>/gestioni/strutture/getFabbricato?id=" + IDFabbricato)
      .then(response => response.json())
      .then(data => {
        if (data.error) {
          alert(data.error);
        } else {
          document.getElementById('editFabbricato-IDFabbricato').value = data.IDFabbricato;
          document.getElementById('editFabbricato-Nome').value = data.Nome;
          var modal = new bootstrap.Modal(document.getElementById('editFabbricatoModal'));
          modal.show();
        }
      }).catch(err => {
        alert("Errore nel caricamento dei dati.");
      });
  }

  function openEditCivicoModal(IDCivico) {
    fetch("<?php echo BASE_URL; ?>/gestioni/strutture/getCivico?id=" + IDCivico)
      .then(response => response.json())
      .then(data => {
        if (data.error) {
          alert(data.error);
        } else {
          document.getElementById('editCivico-IDCivico').value = data.IDCivico;
          document.getElementById('editCivico-Nome').value = data.Nome;
          var modal = new bootstrap.Modal(document.getElementById('editCivicoModal'));
          modal.show();
        }
      }).catch(err => {
        alert("Errore nel caricamento dei dati.");
      });
  }

  function openEditScalaModal(IDScala) {
    fetch("<?php echo BASE_URL; ?>/gestioni/strutture/getScala?id=" + IDScala)
      .then(response => response.json())
      .then(data => {
        if (data.error) {
          alert(data.error);
        } else {
          document.getElementById('editScala-IDScala').value = data.IDScala;
          document.getElementById('editScala-Nome').value = data.Nome;
          var modal = new bootstrap.Modal(document.getElementById('editScalaModal'));
          modal.show();
        }
      }).catch(err => {
        alert("Errore nel caricamento dei dati.");
      });
  }

  function openEditUnitaModal(IDUnita) {
    fetch("<?php echo BASE_URL; ?>/gestioni/strutture/getUnita?id=" + IDUnita)
      .then(response => response.json())
      .then(data => {
        if (data.error) {
          alert(data.error);
        } else {
          document.getElementById('editUnita-IDUnita').value = data.IDUnita;
          document.getElementById('editUnita-Interno').value = data.Interno;
          document.getElementById('editUnita-Piano').value = data.Piano;
          document.getElementById('editUnita-Sezione').value = data.Sezione;
          document.getElementById('editUnita-Foglio').value = data.Foglio;
          document.getElementById('editUnita-Subalterno').value = data.Subalterno;
          document.getElementById('editUnita-Categoria').value = data.Categoria;
          var modal = new bootstrap.Modal(document.getElementById('editUnitaModal'));
          modal.show();
        }
      }).catch(err => {
        alert("Errore nel caricamento dei dati.");
      });
  }
</script>

<?php require_once BASE_PATH . 'app/Views/layout/footer.php'; ?>