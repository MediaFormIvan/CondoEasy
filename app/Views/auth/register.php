<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registrazione - CONDOEASY</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
  <!-- Assicurati di includere il CSS custom se necessario -->
  <link href="<?php echo BASE_URL; ?>/public/assets/css/custom.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container d-flex align-items-center vh-100">
    <div class="card mx-auto shadow" style="max-width: 400px; width: 100%;">
      <div class="card-body">
        <div class="text-center mb-4">
          <img src="<?php echo BASE_URL; ?>/public/assets/images/logo.png" alt="Logo" style="max-height: 50px;">
          <h3 class="mt-2">Registrati</h3>
        </div>
        <?php
        // Recupero dinamico dei ruoli
        require_once BASE_PATH . 'app/Models/Role.php';
        $roleModel = new Role();
        $roles = $roleModel->getAll();
        ?>
        <form action="<?php echo BASE_URL; ?>/register" method="POST" novalidate>
          <div class="mb-3">
            <label for="nome" class="form-label">Nome</label>
            <input type="text" id="nome" name="nome" class="form-control" required>
            <div class="invalid-feedback">
              Inserisci il tuo nome.
            </div>
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" name="email" class="form-control" required>
            <div class="invalid-feedback">
              Inserisci una email valida.
            </div>
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" id="password" name="password" class="form-control" required>
            <div class="invalid-feedback">
              Inserisci una password.
            </div>
          </div>
          <div class="mb-3">
            <label for="confirm_password" class="form-label">Conferma Password</label>
            <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
            <div class="invalid-feedback">
              Le password non coincidono.
            </div>
          </div>
          <div class="mb-3">
            <label for="ruolo" class="form-label">Ruolo</label>
            <select id="ruolo" name="ruolo" class="form-select" required>
              <option value="">Seleziona il ruolo</option>
              <?php foreach ($roles as $role): ?>
                <option value="<?php echo $role['IDRuolo']; ?>"><?php echo htmlspecialchars($role['Nome']); ?></option>
              <?php endforeach; ?>
            </select>
            <div class="invalid-feedback">
              Seleziona un ruolo.
            </div>
          </div>
          <div class="d-grid mb-3">
            <button type="submit" class="btn btn-primary">Registrati</button>
          </div>
          <div class="text-center">
            <a href="<?php echo BASE_URL; ?>/login">Hai già un account? Accedi</a>
          </div>
        </form>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
