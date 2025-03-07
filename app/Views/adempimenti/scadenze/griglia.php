<?php require_once BASE_PATH . 'app/Views/layout/header.php'; ?>

<?php
// Mappa fornitori: IDFornitore => Nome
$fornitoriMap = [];
foreach ($fornitori as $f) {
    $fornitoriMap[$f['IDFornitore']] = $f['Nome'];
}

// Crea una nuova lista di tipi scadenze raggruppando quelli con ID 1 e 2 in una sola colonna
$newTipiScadenze = [];
$merged = false;
foreach ($tipiScadenze as $tipo) {
    if ($tipo['IDTipoScadenza'] == 1 || $tipo['IDTipoScadenza'] == 2) {
        if (!$merged) {
            // Inseriamo una sola colonna per ID 1 e 2
            $newTipiScadenze[] = [
                'IDTipoScadenza' => '1_2',
                'Nome' => 'AMIANTO FR/CO',
                // Se necessario Tipo 1puoi decidere come gestire la durata: qui non viene usata direttamente
                'Durata' => null 
            ];
            $merged = true;
        }
    } else {
        $newTipiScadenze[] = $tipo;
    }
}
?>

<div class="main-wrapper">
    <?php require_once BASE_PATH . 'app/Views/layout/sidebar.php'; ?>
    <main class="content-area p-3">
        <h1 class="h5 mb-3">Griglia Adempimenti Scadenze</h1>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Condominio</th>
                    <?php foreach ($newTipiScadenze as $tipo): ?>
                        <th><?php echo htmlspecialchars($tipo['Nome']); ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($condomini as $condominio): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($condominio['Nome']); ?></td>
                        <?php foreach ($newTipiScadenze as $tipo): ?>
                            <?php
                            $cellContent = "";
                            $bgColor = "#fff";
                            
                            // Se il tipo è il raggruppamento di 1 e 2
                            if ($tipo['IDTipoScadenza'] === '1_2') {
                                $cellData1 = isset($grid[$condominio['IDCondominio']][1]) ? $grid[$condominio['IDCondominio']][1] : null;
                                $cellData2 = isset($grid[$condominio['IDCondominio']][2]) ? $grid[$condominio['IDCondominio']][2] : null;
                                
                                $mergedData = [];
                                if ($cellData1) {
                                    $mergedData[] = $cellData1;
                                }
                                if ($cellData2) {
                                    $mergedData[] = $cellData2;
                                }
                                
                                if (!empty($mergedData)) {
                                    $parts = [];
                                    $worstColor = "#6BAF85"; // assumiamo in regola di default
                                    foreach ($mergedData as $cd) {
                                        // Calcola stato per ciascuna scadenza
                                        $scadenzaDate = new DateTime($cd['DataScadenza']);
                                        $today = new DateTime();
                                        $marginMonths = $cd['Durata'] / 6;
                                        $thresholdDate = clone $scadenzaDate;
                                        $thresholdDate->sub(new DateInterval('P' . (int)$marginMonths . 'M'));
                                        
                                        if ($today > $scadenzaDate) {
                                            $color = "#7c4040"; // scaduto
                                        } elseif ($today >= $thresholdDate && $today <= $scadenzaDate) {
                                            $color = "#ccbb6a"; // in scadenza
                                        } else {
                                            $color = "#6BAF85"; // in regola
                                        }
                                        // Per il raggruppamento, scegliamo il colore "più grave"
                                        if ($color == "#7c4040" || ($color == "#ccbb6a" && $worstColor != "#7c4040")) {
                                            $worstColor = $color;
                                        }
                                        
                                        $formattedDate = $scadenzaDate->format('d/m/Y');
                                        // Aggiungi un asterisco se sono presenti documenti
                                        $docCount = isset($documentCounts[$cd['IDScadenza']]) ? $documentCounts[$cd['IDScadenza']] : 0;
                                        if ($docCount > 0) {
                                            $formattedDate .= " *";
                                        }
                                        $fornitoreName = isset($fornitoriMap[$cd['IDFornitore']]) ? $fornitoriMap[$cd['IDFornitore']] : $cd['IDFornitore'];
                                        $parts[] = $formattedDate . "<br>" . htmlspecialchars($fornitoreName);
                                    }
                                    $cellContent = implode("<hr>", $parts);
                                    $bgColor = $worstColor;
                                    // Link di dettaglio: se c'è almeno un record, usiamo quello del primo
                                    $link = BASE_URL . "/scadenze/detail?id=" . $mergedData[0]['IDScadenza'];
                                    $cellContent = "<a href=\"$link\" style=\"color: inherit; text-decoration: none;\">" . $cellContent . "</a>";
                                } else {
                                    $cellContent = "Non inserito";
                                }
                            } else {
                                // Per gli altri tipi, cerchiamo la cella normalmente
                                $cellData = isset($grid[$condominio['IDCondominio']][$tipo['IDTipoScadenza']]) ? $grid[$condominio['IDCondominio']][$tipo['IDTipoScadenza']] : null;
                                if ($cellData) {
                                    $scadenzaDate = new DateTime($cellData['DataScadenza']);
                                    $today = new DateTime();
                                    $marginMonths = $cellData['Durata'] / 6;
                                    $thresholdDate = clone $scadenzaDate;
                                    $thresholdDate->sub(new DateInterval('P' . (int)$marginMonths . 'M'));
                                    
                                    if ($today > $scadenzaDate) {
                                        $bgColor = "#7c4040"; // scaduto
                                    } elseif ($today >= $thresholdDate && $today <= $scadenzaDate) {
                                        $bgColor = "#ccbb6a"; // in scadenza
                                    } else {
                                        $bgColor = "#6BAF85"; // in regola
                                    }
                                    
                                    $formattedDate = $scadenzaDate->format('d/m/Y');
                                    $docCount = isset($documentCounts[$cellData['IDScadenza']]) ? $documentCounts[$cellData['IDScadenza']] : 0;
                                    if ($docCount > 0) {
                                        $formattedDate .= " *";
                                    }
                                    $fornitoreName = isset($fornitoriMap[$cellData['IDFornitore']]) ? $fornitoriMap[$cellData['IDFornitore']] : $cellData['IDFornitore'];
                                    $cellContent = $formattedDate . "<br>" . htmlspecialchars($fornitoreName);
                                    $link = BASE_URL . "/scadenze/detail?id=" . $cellData['IDScadenza'];
                                    $cellContent = "<a href=\"$link\" style=\"color: inherit; text-decoration: none;\">" . $cellContent . "</a>";
                                } else {
                                    $cellContent = "Non inserito";
                                }
                            }
                            ?>
                            <td style="background-color: <?php echo $bgColor; ?>; font-size: 0.9em;">
                                <?php echo $cellContent; ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</div>

<?php require_once BASE_PATH . 'app/Views/layout/footer.php'; ?>
