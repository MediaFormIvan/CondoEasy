<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Imposta Nuova Password - CONDOEASY</title>
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
                    <h3 class="mt-2">Imposta Nuova Password</h3>
                </div>
                <!-- Il token viene passato come parametro GET -->
                <form action="<?php echo BASE_URL; ?>/reset_password?token=<?php echo htmlspecialchars($_GET['token'] ?? ''); ?>" method="POST" novalidate>
                    <div class="mb-3">
                        <label for="password" class="form-label">Nuova Password</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                        <div class="invalid-feedback">
                            Inserisci una password.
                        </div>
                    </div>
                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary">Aggiorna Password</button>
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