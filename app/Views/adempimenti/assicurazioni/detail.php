<?php require_once BASE_PATH . 'app/Views/layout/header.php'; ?>

<?php
// Costruisci le mappe per condomini e fornitori
$condominiMap = [];
foreach ($condomini as $condominio) {
    $condominiMap[$condominio['IDCondominio']] = $condominio['Nome'];
}
$fornitoriMap = [];
foreach ($fornitori as $fornitore) {
    $fornitoriMap[$fornitore['IDFornitore']] = $fornitore['Nome'];
}
?>

<div class="main-wrapper">
    <?php require_once BASE_PATH . 'app/Views/layout/sidebar.php'; ?>
    <main class="content-area p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h5 mb-0">Dettaglio Assicurazione</h1>
            <a href="<?php echo BASE_URL; ?>/assicurazioni" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Torna all'elenco
            </a>
        </div>
        <!-- Dati dell'assicurazione -->
        <div class="card mb-3">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">Dati Assicurazione</h5>
            </div>
            <div class="card-body p-3">
                <table class="table table-sm table-borderless">
                    <tbody>
                        <tr>
                            <th style="width: 30%;">Condominio</th>
                            <td><?php echo isset($condominiMap[$assicurazione['IDCondominio']]) ? $condominiMap[$assicurazione['IDCondominio']] : $assicurazione['IDCondominio']; ?></td>
                        </tr>
                        <tr>
                            <th>Fornitore</th>
                            <td><?php echo isset($fornitoriMap[$assicurazione['IDFornitore']]) ? $fornitoriMap[$assicurazione['IDFornitore']] : $assicurazione['IDFornitore']; ?></td>
                        </tr>
                        <tr>
                            <th>Data Scadenza</th>
                            <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($assicurazione['DataScadenza']))); ?></td>
                        </tr>
                        <tr>
                            <th>Durata (mesi)</th>
                            <td><?php echo htmlspecialchars($assicurazione['Durata']); ?></td>
                        </tr>
                        <tr>
                            <th>Polizza</th>
                            <td><?php echo htmlspecialchars($assicurazione['Polizza']); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Documenti allegati -->
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
                                    <td><?php echo $doc['IDAssicurazioneDocumento']; ?></td>
                                    <td><?php echo htmlspecialchars($doc['Titolo']); ?></td>
                                    <td>
                                        <a href="<?php echo BASE_URL . '/storage/uploads/assicurazioni/' . htmlspecialchars($doc['File']); ?>" target="_blank">
                                            <?php echo htmlspecialchars($doc['File']); ?>
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?php echo BASE_URL; ?>/assicurazioni_documenti/delete?id=<?php echo $doc['IDAssicurazioneDocumento']; ?>&idAssicurazione=<?php echo $assicurazione['IDAssicurazione']; ?>" class="btn btn-sm btn-delete-doc" title="Elimina Documento" onclick="return confirm('Sei sicuro di voler eliminare questo documento?');">
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
                    <form action="<?php echo BASE_URL; ?>/assicurazioni/uploadDocumento" method="POST" enctype="multipart/form-data" novalidate>
                        <input type="hidden" name="IDAssicurazione" value="<?php echo $assicurazione['IDAssicurazione']; ?>">
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
