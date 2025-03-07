<!-- Sidebar -->
<nav id="sidebarMenu" class="sidebar">
  <ul class="nav flex-column">
    <!-- Dashboard -->
    <li class="nav-item">
      <a class="nav-link active" href="<?php echo BASE_URL; ?>/dashboard">
        <i class="bi bi-speedometer2"></i> Dashboard
      </a>
    </li>

    <!-- Anagrafiche -->
    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#anagraficheMenu" role="button" aria-expanded="false" aria-controls="anagraficheMenu">
        <i class="bi bi-card-list"></i> Anagrafiche
      </a>
      <div class="collapse" id="anagraficheMenu">
        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
          <li><a href="<?php echo BASE_URL; ?>/anagrafiche/condomini" class="nav-link">Condominii</a></li>
          <li><a href="<?php echo BASE_URL; ?>/anagrafiche/fornitori" class="nav-link">Fornitori</a></li>
          <li><a href="<?php echo BASE_URL; ?>/anagrafiche/banche" class="nav-link">Banche</a></li>
          <li><a href="<?php echo BASE_URL; ?>/anagrafiche/persone" class="nav-link">Persone</a></li>
          <li><a href="<?php echo BASE_URL; ?>/anagrafiche/tipifornitori" class="nav-link">Tipologie Fornitori</a></li>
          <li><a href="<?php echo BASE_URL; ?>/anagrafiche/tipi_persone" class="nav-link">Tipologie Persone</a></li>
        </ul>
      </div>
    </li>

    <!-- Gestione Condominiale -->
    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#gestioneCondominaleMenu" role="button" aria-expanded="false" aria-controls="gestioneCondominaleMenu">
        <i class="bi bi-building"></i> Gestione Condominiale
      </a>
      <div class="collapse" id="gestioneCondominaleMenu">
        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
          <li><a href="<?php echo BASE_URL; ?>/gestioni/bilanci" class="nav-link">Bilanci &amp; Esercizi</a></li>
          <li><a href="<?php echo BASE_URL; ?>/gestioni/strutture" class="nav-link">Struttura &amp; Unità</a></li>
          <li><a href="<?php echo BASE_URL; ?>/gestione/tabelle" class="nav-link">Tabelle</a></li>
          <li><a href="<?php echo BASE_URL; ?>/gestione/piano_conti" class="nav-link">Piano dei conti</a></li>
          <li><a href="<?php echo BASE_URL; ?>/gestione/piano_rateale" class="nav-link">Piano rateale</a></li>
          <li><a href="<?php echo BASE_URL; ?>/gestione/movimenti" class="nav-link">Movimenti</a></li>
          <li><a href="<?php echo BASE_URL; ?>/gestione/assemblee" class="nav-link">Assemblee</a></li>
        </ul>
      </div>
    </li>

    <!-- Attività -->
    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#attivitaMenu" role="button" aria-expanded="false" aria-controls="attivitaMenu">
        <i class="bi bi-calendar-check"></i> Attività
      </a>
      <div class="collapse" id="attivitaMenu">
        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
          <li><a href="<?php echo BASE_URL; ?>/attivita/manutenzioni" class="nav-link">Manutenzioni</a></li>
          <li><a href="<?php echo BASE_URL; ?>/attivita/sinistri" class="nav-link">Sinistri</a></li>
          <li><a href="<?php echo BASE_URL; ?>/attivita/legale" class="nav-link">Pratiche legali</a></li>
        </ul>
      </div>
    </li>

    <!-- Nuova Sezione: Contratti & Scadenze -->
    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#contrattiScadenzeMenu" role="button" aria-expanded="false" aria-controls="contrattiScadenzeMenu">
        <i class="bi bi-file-earmark-text"></i> Contratti &amp; Scadenze
      </a>
      <div class="collapse" id="contrattiScadenzeMenu">
        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
          <li><a href="<?php echo BASE_URL; ?>/scadenze" class="nav-link">Scadenze adempimenti</a></li>
          <li><a href="<?php echo BASE_URL; ?>/contratti" class="nav-link">Contratti fornitori</a></li>
          <li><a href="<?php echo BASE_URL; ?>/assicurazioni" class="nav-link">Assicurazioni</a></li>
          <li><a href="<?php echo BASE_URL; ?>/detrazioni_fiscali" class="nav-link">Detrazioni fiscali</a></li>
          <li><a href="<?php echo BASE_URL; ?>/promemoria" class="nav-link">Promemoria</a></li>
        </ul>
      </div>
    </li>

    <!-- Stampe -->
    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#stampeMenu" role="button" aria-expanded="false" aria-controls="stampeMenu">
        <i class="bi bi-printer"></i> Stampe
      </a>
      <div class="collapse" id="stampeMenu">
        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
          <!-- Consuntivo -->
          <li>
            <a class="nav-link" data-bs-toggle="collapse" href="#consuntivoMenu" role="button" aria-expanded="false" aria-controls="consuntivoMenu">
              Consuntivo
            </a>
            <div class="collapse" id="consuntivoMenu">
              <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                <li><a href="<?php echo BASE_URL; ?>/stampe/consuntivo/rendiconto" class="nav-link">Rendiconto</a></li>
                <li><a href="<?php echo BASE_URL; ?>/stampe/consuntivo/riparto" class="nav-link">Riparto</a></li>
                <li><a href="<?php echo BASE_URL; ?>/stampe/consuntivo/riparto_anagrafica" class="nav-link">Riparto anagrafica</a></li>
              </ul>
            </div>
          </li>
          <!-- Preventivo -->
          <li>
            <a class="nav-link" data-bs-toggle="collapse" href="#preventivoMenu" role="button" aria-expanded="false" aria-controls="preventivoMenu">
              Preventivo
            </a>
            <div class="collapse" id="preventivoMenu">
              <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                <li><a href="<?php echo BASE_URL; ?>/stampe/preventivo/rendiconto" class="nav-link">Rendiconto</a></li>
                <li><a href="<?php echo BASE_URL; ?>/stampe/preventivo/riparto" class="nav-link">Riparto</a></li>
                <li><a href="<?php echo BASE_URL; ?>/stampe/preventivo/riparto_anagrafica" class="nav-link">Riparto anagrafica</a></li>
              </ul>
            </div>
          </li>
        </ul>
      </div>
    </li>
  </ul>
</nav>
