</div> <!-- End main-content -->

<footer class="bg-dark text-light py-4 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h6><i class="fas fa-graduation-cap me-2"></i>AI Powered Academic Hub</h6>
                <p class="mb-0">Streamlining academic management with AI-powered tools.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <p class="mb-0">
                    <small>&copy; <?php echo date('Y'); ?> AI Powered Academic Hub. All rights reserved.</small>
                </p>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Auto-hide alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});
</script>
</body>
</html>