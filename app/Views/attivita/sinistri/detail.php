<?php require_once BASE_PATH . 'app/Views/layout/header.php'; ?>

<div class="main-wrapper">
    <?php require_once BASE_PATH . 'app/Views/layout/sidebar.php'; ?>
    <main class="content-area p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h5 mb-0">Dettaglio Sinistro</h1>
            <a href="<?php echo BASE_URL; ?>/attivita/sinistri" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Torna all'elenco
            </a>
        </div>

        <!-- PARTE UNO: Dati del Sinistro -->
        <div class="card mb-3">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Dati Sinistro</h5>
                <!-- Pulsante per inviare la mail -->
                <a href="<?php echo BASE_URL; ?>/attivita/sinistri/sendMail?id=<?php echo $sinistro['IDSinistro']; ?>" class="btn btn-sm btn-custom-blue" title="Invia Email">
                    <i class="bi bi-envelope"></i> Invia Email
                </a>
            </div>
            <div class="card-body p-3">
                <table class="table table-sm table-borderless">
                    <tbody>
                        <?php foreach ($sinistro as $key => $value): ?>
                            <tr>
                                <th style="width: 30%;"><?php echo htmlspecialchars($key); ?></th>
                                <td>
                                    <?php
                                    if ($key === 'IDCondominio' && isset($condominiMap[$value])) {
                                        echo htmlspecialchars($condominiMap[$value]);
                                    } elseif ($key === 'IDStato' && isset($statiMap[$value])) {
                                        echo htmlspecialchars($statiMap[$value]);
                                    } elseif ($key === 'IDStudioPeritale' && isset($studiMap[$value])) {
                                        echo htmlspecialchars($studiMap[$value]);
                                    } else {
                                        echo htmlspecialchars($value);
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
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
                                    <td><?php echo $doc['IDSinistroDocumento']; ?></td>
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
                                        <a href="<?php echo BASE_URL; ?>/attivita/sinistri_documenti/delete?id=<?php echo $doc['IDSinistroDocumento']; ?>&idSinistro=<?php echo $sinistro['IDSinistro']; ?>" class="btn btn-sm btn-delete-doc" title="Elimina Documento">
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
                <form action="<?php echo BASE_URL; ?>/attivita/sinistri_chat/save" method="POST">
                    <input type="hidden" name="IDSinistro" value="<?php echo $sinistro['IDSinistro']; ?>">
                    <div class="input-group">
                        <input type="text" name="Testo" class="form-control form-control-sm" placeholder="Scrivi un messaggio..." required>
                        <button class="btn btn-sm btn-custom-blue" type="submit"><i class="bi bi-send"></i> Invia</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- PARTE QUATTRO: Foto -->
        <div class="card mb-3">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">Foto</h5>
            </div>
            <div class="card-body p-3">
                <?php if (count($foto) > 0): ?>
                    <div class="row">
                        <?php foreach ($foto as $index => $f): ?>
                            <div class="col-auto mb-2">
                                <!-- Mostra thumbnail fissa a 100x100, con object-fit per centrare/croppare l'immagine -->
                                <img src="<?php echo BASE_URL . '/storage/uploads/' . htmlspecialchars($f['File']); ?>"
                                    class="img-thumbnail lightbox-trigger"
                                    data-index="<?php echo $index; ?>"
                                    style="width: 100px; height: 100px; object-fit: cover; cursor: pointer;"
                                    alt="Foto Sinistro">
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="mb-0">Nessuna foto caricata.</p>
                <?php endif; ?>
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-custom-blue" data-bs-toggle="modal" data-bs-target="#addFotoModal">
                        <i class="bi bi-plus-circle"></i> Aggiungi Foto
                    </button>
                </div>
                <div class="mt-2">
                    <a href="<?php echo BASE_URL; ?>/attivita/sinistri_foto/downloadZip?id=<?php echo $sinistro['IDSinistro']; ?>" class="btn btn-sm btn-custom-blue">
                        <i class="bi bi-download"></i> Scarica ZIP
                    </a>
                </div>

            </div>
        </div>


    </main>
</div>

<!-- Modal per Aggiungere Documento -->
<div class="modal fade" id="addDocumentoModal" tabindex="-1" aria-labelledby="addDocumentoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo BASE_URL; ?>/attivita/sinistri_documenti/save" method="POST" enctype="multipart/form-data" novalidate>
                <div class="modal-header">
                    <h5 class="modal-title" id="addDocumentoModalLabel">Aggiungi Documento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="IDSinistro" value="<?php echo $sinistro['IDSinistro']; ?>">
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
            <form action="<?php echo BASE_URL; ?>/attivita/sinistri_documenti/update" method="POST" novalidate>
                <div class="modal-header">
                    <h5 class="modal-title" id="editDocumentoModalLabel">Modifica Titolo Documento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="IDSinistroDocumento" id="modal-doc-ID" value="">
                    <input type="hidden" name="IDSinistro" id="modal-doc-IDSinistro" value="">
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

<!-- Modal per Aggiungere Foto -->
<div class="modal fade" id="addFotoModal" tabindex="-1" aria-labelledby="addFotoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo BASE_URL; ?>/attivita/sinistri_foto/save" method="POST" enctype="multipart/form-data" novalidate>
                <div class="modal-header">
                    <h5 class="modal-title" id="addFotoModalLabel">Aggiungi Foto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="IDSinistro" value="<?php echo $sinistro['IDSinistro']; ?>">
                    <div class="mb-3">
                        <label for="foto-file" class="form-label">File</label>
                        <!-- Abilita la selezione multipla -->
                        <input type="file" class="form-control" id="foto-file" name="File[]" multiple required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Chiudi</button>
                    <button type="submit" class="btn btn-sm btn-custom-blue">Salva Foto</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Lightbox Modal -->
<div class="modal fade" id="lightboxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen">
        <div class="modal-content bg-dark">
            <div class="modal-body text-center position-relative">
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
                <!-- L'immagine è contenuta entro la larghezza/altezza della viewport -->
                <img id="lightboxImage" src="" alt="Foto Sinistro" class="img-fluid" style="max-width: 100%; max-height: 100vh; object-fit: contain;">
                <!-- Frecce per navigazione -->
                <a id="prevLightbox" href="#" class="position-absolute top-50 start-0 translate-middle-y text-white" style="font-size: 3rem; text-decoration: none;">&#8249;</a>
                <a id="nextLightbox" href="#" class="position-absolute top-50 end-0 translate-middle-y text-white" style="font-size: 3rem; text-decoration: none;">&#8250;</a>
            </div>
        </div>
    </div>
</div>

<script>
    // Crea un array di URL per le immagini dal PHP
    var lightboxImages = <?php echo json_encode(array_map(function ($f) {
                                return BASE_URL . '/storage/uploads/' . $f['File'];
                            }, $foto)); ?>;
    var currentLightboxIndex = 0;

    // Gestisci il click sulle thumbnails
    document.querySelectorAll('.lightbox-trigger').forEach(function(img) {
        img.addEventListener('click', function() {
            currentLightboxIndex = parseInt(this.getAttribute('data-index'));
            showLightboxImage(currentLightboxIndex);
            var lightboxModal = new bootstrap.Modal(document.getElementById('lightboxModal'));
            lightboxModal.show();
        });
    });

    // Funzione per mostrare l'immagine corrente nel lightbox
    function showLightboxImage(index) {
        document.getElementById('lightboxImage').src = lightboxImages[index];
    }

    // Navigazione "Precedente" (loop)
    document.getElementById('prevLightbox').addEventListener('click', function(e) {
        e.preventDefault();
        currentLightboxIndex = (currentLightboxIndex - 1 + lightboxImages.length) % lightboxImages.length;
        showLightboxImage(currentLightboxIndex);
    });

    // Navigazione "Successivo" (loop)
    document.getElementById('nextLightbox').addEventListener('click', function(e) {
        e.preventDefault();
        currentLightboxIndex = (currentLightboxIndex + 1) % lightboxImages.length;
        showLightboxImage(currentLightboxIndex);
    });
</script>



<script>
    // Modal Edit Documento
    document.querySelectorAll('.btn-edit-doc').forEach(function(button) {
        button.addEventListener('click', function() {
            var row = this.closest('tr');
            var doc = JSON.parse(row.getAttribute('data-documento'));
            document.getElementById('modal-doc-ID').value = doc.IDSinistroDocumento || "";
            document.getElementById('modal-doc-titolo').value = doc.Titolo || "";
            document.getElementById('modal-doc-IDSinistro').value = doc.IDSinistro || "";
        });
    });
</script>

<?php require_once BASE_PATH . 'app/Views/layout/footer.php'; ?>