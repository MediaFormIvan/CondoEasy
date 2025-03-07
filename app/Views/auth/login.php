<!DOCTYPE html>
<html lang="it">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - CONDOEASY</title>
  <!-- CDN Bootstrap 5.3 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Foglio di stile custom per sovrascrivere i colori -->
  <link href="<?php echo BASE_URL; ?>/public/assets/css/custom.css" rel="stylesheet">
  <!-- Optional: Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>

<body class="bg-light">
  <div class="container d-flex align-items-center vh-100">
    <div class="card mx-auto shadow" style="max-width: 400px; width: 100%;">
      <div class="card-body">
        <div class="text-center mb-4">
          <!-- Logo -->
          <img src="<?php echo BASE_URL; ?>/public/assets/images/logo.png" alt="Logo" style="max-height: 50px;">
          <h3 class="mt-2">Accedi</h3>
        </div>
        <form action="<?php echo BASE_URL; ?>/login" method="POST" novalidate>
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" name="email" class="form-control" required>
            <div class="invalid-feedback">
              Inserisci una email valida.
            </div>
          </div>
          <div class="mb-3 position-relative">
            <label for="password" class="form-label">Password</label>
            <input type="password" id="password" name="password" class="form-control" required>
            <button type="button" class="btn btn-sm btn-outline-secondary position-absolute top-50 end-0 translate-middle-y me-2" id="togglePassword">
              <i class="bi bi-eye"></i>
            </button>
            <div class="invalid-feedback">
              Inserisci la password.
            </div>
          </div>
          <div class="d-grid mb-3">
            <button type="submit" class="btn btn-primary">Accedi</button>
          </div>
          <div class="text-center">
            <a href="<?php echo BASE_URL; ?>/password_reset">Password dimenticata?</a> |
            <a href="<?php echo BASE_URL; ?>/register">Registrati</a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Script per mostra/nascondi password -->
  <script>
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    togglePassword.addEventListener('click', function() {
      const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordInput.setAttribute('type', type);
      this.querySelector('i').classList.toggle('bi-eye');
      this.querySelector('i').classList.toggle('bi-eye-slash');
    });
  </script>
</body>

</html>