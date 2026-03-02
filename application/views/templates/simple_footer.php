</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Simple JavaScript for enhanced UX
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            if (alert.parentNode) {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }
        }, 5000);
    });
    
    // Add loading states to buttons
    const forms = document.querySelectorAll('form');
    forms.forEach(function(form) {
        form.addEventListener('submit', function() {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Loading...';
                submitBtn.disabled = true;
            }
        });
    });
});

function showLogin(role) {
    document.getElementById('selectedRole').value = role;
    document.getElementById('loginForm').style.display = 'block';
    document.getElementById('loginForm').scrollIntoView({ behavior: 'smooth' });
}

function hideLogin() {
    document.getElementById('loginForm').style.display = 'none';
}
</script>

</body>
</html>