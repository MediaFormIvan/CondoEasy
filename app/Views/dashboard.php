<?php require_once BASE_PATH . 'app/Views/layout/header.php'; ?>
<!-- Wrapper principale per sidebar e contenuto -->
<div class="main-wrapper">
  <?php require_once BASE_PATH . 'app/Views/layout/sidebar.php'; ?>
  <main class="content-area">
    <h1 class="h2">Dashboard</h1>
    <p>Benvenuto nella dashboard di CONDOEASY. In questa sezione trovi una panoramica delle attività e dei dati principali.</p>
    
    <!-- Esempio di cards informativi -->
    <div class="row">
      <div class="col-md-4 mb-4">
        <div class="card shadow-sm">
          <div class="card-body">
            <h5 class="card-title">Utenti Attivi</h5>
            <p class="card-text display-6">120</p>
          </div>
        </div>
      </div>
      <div class="col-md-4 mb-4">
        <div class="card shadow-sm">
          <div class="card-body">
            <h5 class="card-title">Report Mensili</h5>
            <p class="card-text display-6">15</p>
          </div>
        </div>
      </div>
      <div class="col-md-4 mb-4">
        <div class="card shadow-sm">
          <div class="card-body">
            <h5 class="card-title">Ticket di Supporto</h5>
            <p class="card-text display-6">8</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Sezione aggiuntiva di esempio -->
    <div class="mt-4">
      <h3>Statistiche Recenti</h3>
      <p>Qui potresti visualizzare grafici e tabelle riassuntive dei dati raccolti, notifiche ed aggiornamenti.</p>
    </div>
  </main>
</div>
<?php require_once BASE_PATH . 'app/Views/layout/footer.php'; ?>
