<?php
// app/Views/anagrafiche/fornitori/export_pdf.php
?>
<html>
<head>
  <meta charset="UTF-8">
  <style>
    body { font-family: 'Roboto', sans-serif; font-size: 12px; }
    .header { text-align: center; margin-bottom: 20px; }
    .header img { height: 50px; }
    .header h2 { margin: 0; }
    .table { width: 100%; border-collapse: collapse; }
    .table th, .table td { border: 1px solid #ccc; padding: 8px; text-align: left; }
    .table th { background-color: #f2f2f2; font-weight: bold; }
  </style>
</head>
<body>
  <div class="header">
    <img src="<?php echo BASE_URL; ?>/public/assets/images/logo.png" alt="Logo">
    <h2>Elenco Fornitori</h2>
  </div>
  <table class="table">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nome</th>
        <th>Tipologia</th>
        <th>Indirizzo</th>
        <th>Cap</th>
        <th>Citta</th>
        <th>Partita IVA</th>
        <!-- Altre colonne se necessario -->
      </tr>
    </thead>
    <tbody>
      <?php foreach ($fornitori as $fornitore): ?>
      <tr>
        <td><?php echo $fornitore['IDFornitore']; ?></td>
        <td><?php echo htmlspecialchars($fornitore['Nome']); ?></td>
        <td><?php echo htmlspecialchars($fornitore['IDTipoFornitore']); // o il nome della tipologia ?></td>
        <td><?php echo htmlspecialchars($fornitore['Indirizzo']); ?></td>
        <td><?php echo htmlspecialchars($fornitore['Cap']); ?></td>
        <td><?php echo htmlspecialchars($fornitore['Citta']); ?></td>
        <td><?php echo htmlspecialchars($fornitore['PartitaIva']); ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</body>
</html>
