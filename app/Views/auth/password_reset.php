<!DOCTYPE html>
<html lang="it">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Password Reset - CONDOEASY</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Foglio di stile custom per sovrascrivere i colori -->
  <link href="<?php echo BASE_URL; ?>/public/assets/css/custom.css" rel="stylesheet">
</head>

<body class="bg-light">
  <div class="container d-flex align-items-center vh-100">
    <div class="card mx-auto shadow" style="max-width: 400px; width: 100%;">
      <div class="card-body">
        <div class="text-center mb-4">
          <img src="<?php echo BASE_URL; ?>/public/assets/images/logo.png" alt="Logo" style="max-height: 50px;">
          <h3 class="mt-2">Reset Password</h3>
        </div>
        <form action="<?php echo BASE_URL; ?>/password_reset" method="POST" novalidate>
          <div class="mb-3">
            <label for="email" class="form-label">Inserisci la tua email</label>
            <input type="email" id="email" name="email" class="form-control" required>
            <div class="invalid-feedback">
              Inserisci una email valida.
            </div>
          </div>
          <div class="d-grid mb-3">
            <button type="submit" class="btn btn-primary">Invia Richiesta</button>
          </div>
          <div class="text-center">
            <a href="<?php echo BASE_URL; ?>/login">Torna al login</a>
          </div>
        </form>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>