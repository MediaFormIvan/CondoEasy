<?php require_once BASE_PATH . 'app/Views/layout/header.php'; ?>

<?php
// Costruisci le mappe per visualizzare i nomi al posto degli ID
$condominiMap = [];
foreach ($condomini as $condominio) {
    $condominiMap[$condominio['IDCondominio']] = $condominio['Nome'];
}
$fornitoriMap = [];
foreach ($fornitori as $fornitore) {
    $fornitoriMap[$fornitore['IDFornitore']] = $fornitore['Nome'];
}
$tipiMap = [];
foreach ($tipiContratti as $tipo) {
    $tipiMap[$tipo['IDTipoContratto']] = $tipo['Nome'];
}
?>

<div class="main-wrapper">
    <?php require_once BASE_PATH . 'app/Views/layout/sidebar.php'; ?>
    <main class="content-area p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h5 mb-0">Dettaglio Contratto</h1>
            <a href="<?php echo BASE_URL; ?>/contratti" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Torna all'elenco
            </a>
        </div>

        <!-- Dati del contratto -->
        <div class="card mb-3">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">Dati Contratto</h5>
            </div>
            <div class="card-body p-3">
                <table class="table table-sm table-borderless">
                    <tbody>
                        <?php foreach ($contratto as $key => $value): ?>
                            <tr>
                                <th style="width: 30%;"><?php echo htmlspecialchars($key); ?></th>
                                <td>
                                    <?php 
                                    if ($key === 'IDCondominio' && isset($condominiMap[$value])) {
                                        echo htmlspecialchars($condominiMap[$value]);
                                    } elseif ($key === 'IDFornitore' && isset($fornitoriMap[$value])) {
                                        echo htmlspecialchars($fornitoriMap[$value]);
                                    } elseif ($key === 'IDTipoContratto' && isset($tipiMap[$value])) {
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
                                <th>Nome File</th>
                                <th class="text-center">Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($documenti as $doc): ?>
                                <tr>
                                    <td><?php echo $doc['IDContrattoDocumento']; ?></td>
                                    <td><?php echo htmlspecialchars($doc['Titolo']); ?></td>
                                    <td>
                                        <a href="<?php echo BASE_URL . '/storage/uploads/contratti/' . htmlspecialchars($doc['NomeFile']); ?>" target="_blank">
                                            <?php echo htmlspecialchars($doc['NomeFile']); ?>
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?php echo BASE_URL; ?>/contratti_documenti/delete?id=<?php echo $doc['IDContrattoDocumento']; ?>&idContratto=<?php echo $contratto['IDCondominioContratto']; ?>" class="btn btn-sm btn-delete-doc" title="Elimina Documento" onclick="return confirm('Sei sicuro di voler eliminare questo documento?');">
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
                    <form action="<?php echo BASE_URL; ?>/contratti/uploadDocumento" method="POST" enctype="multipart/form-data" novalidate>
                        <input type="hidden" name="IDContratto" value="<?php echo $contratto['IDCondominioContratto']; ?>">
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
