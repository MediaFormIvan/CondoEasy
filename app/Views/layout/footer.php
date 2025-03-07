<!-- Footer -->
<footer class="footer mt-auto py-3 bg-light">
  <div class="container text-center">
    <span class="text-muted">&copy; <?php echo date("Y"); ?> CONDOEASY. Tutti i diritti riservati.</span>
  </div>
</footer>
<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    <?php if (isset($_GET['mailSent'])): ?>
        <?php if ($_GET['mailSent'] == 1): ?>
            toastr.success('Email inviata con successo!');
        <?php else: ?>
            toastr.error('<?php echo isset($_GET['mailError']) ? addslashes(urldecode($_GET['mailError'])) : "Errore nell\'invio dell\'email."; ?>');
        <?php endif; ?>
    <?php endif; ?>
</script>

</body>
</html>
