<?php require_once BASE_PATH . 'app/Views/layout/header.php'; ?>

<?php
// Costruzione delle mappe per visualizzare i nomi al posto degli ID
$condominiMap = [];
foreach ($condomini as $condominio) {
    $condominiMap[$condominio['IDCondominio']] = $condominio['Nome'];
}
$tipiMap = [];
foreach ($tipiScadenze as $tipo) {
    $tipiMap[$tipo['IDTipoScadenza']] = $tipo['Nome'];
}
?>

<div class="main-wrapper">
    <?php require_once BASE_PATH . 'app/Views/layout/sidebar.php'; ?>
    <main class="content-area p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h5 mb-0">Dettaglio Scadenza</h1>
            <a href="<?php echo BASE_URL; ?>/scadenze" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Torna all'elenco
            </a>
        </div>

        <!-- Dati della scadenza -->
        <div class="card mb-3">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">Dati Scadenza</h5>
            </div>
            <div class="card-body p-3">
                <table class="table table-sm table-borderless">
                    <tbody>
                        <?php foreach ($scadenza as $key => $value): ?>
                            <tr>
                                <th style="width: 30%;"><?php echo htmlspecialchars($key); ?></th>
                                <td>
                                    <?php 
                                    if ($key === 'IDCondominio' && isset($condominiMap[$value])) {
                                        echo htmlspecialchars($condominiMap[$value]);
                                    } elseif ($key === 'IDTipoScadenza' && isset($tipiMap[$value])) {
                                        echo htmlspecialchars($tipiMap[$value]);
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

        <!-- Sezione per l'upload di un documento -->
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
                                <tr>
                                    <td><?php echo $doc['IDScadenzaDocumento']; ?></td>
                                    <td><?php echo htmlspecialchars($doc['Titolo']); ?></td>
                                    <td>
                                        <a href="<?php echo BASE_URL . '/storage/uploads/scadenze/' . htmlspecialchars($doc['File']); ?>" target="_blank">
                                            <?php echo htmlspecialchars($doc['File']); ?>
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?php echo BASE_URL; ?>/scadenze_documenti/delete?id=<?php echo $doc['IDScadenzaDocumento']; ?>&idScadenza=<?php echo $scadenza['IDScadenza']; ?>" class="btn btn-sm btn-delete-doc" title="Elimina Documento" onclick="return confirm('Sei sicuro di voler eliminare questo documento?');">
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

                <div class="mt-3">
                    <form action="<?php echo BASE_URL; ?>/scadenze/uploadDocumento" method="POST" enctype="multipart/form-data" novalidate>
                        <input type="hidden" name="IDScadenza" value="<?php echo $scadenza['IDScadenza']; ?>">
                        <div class="mb-3">
                            <label for="DocumentoTitolo" class="form-label">Titolo Documento</label>
                            <input type="text" id="DocumentoTitolo" name="DocumentoTitolo" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="File" class="form-label">File</label>
                            <input type="file" id="File" name="File" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-custom-blue">Carica Documento</button>
                    </form>
                </div>
            </div>
        </div>

    </main>
</div>

<?php require_once BASE_PATH . 'app/Views/layout/footer.php'; ?>
