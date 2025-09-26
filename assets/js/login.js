/**
 * Gebert Segurança Patrimonial - Login Scripts
 * ============================================
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // Toggle password visibility
    const togglePassword = document.getElementById('togglePassword');
    const passwordField = document.getElementById('password');
    
    if (togglePassword && passwordField) {
        togglePassword.addEventListener('click', function() {
            const icon = this.querySelector('i');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
                this.setAttribute('aria-label', 'Ocultar senha');
            } else {
                passwordField.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
                this.setAttribute('aria-label', 'Mostrar senha');
            }
        });
    }

    // Form validation feedback
    const forms = document.getElementsByClassName('needs-validation');
    
    if (forms.length > 0) {
        Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }

    // Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            if (alert && alert.classList.contains('show')) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 5000);
    });

    // Focus enhancement for accessibility
    const inputs = document.querySelectorAll('input');
    inputs.forEach(function(input) {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('focused');
        });
    });

    // Prevent form submission on Enter in password field (optional)
    // Uncomment if you want to prevent accidental submissions
    /*
    if (passwordField) {
        passwordField.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                document.querySelector('.btn-login').click();
            }
        });
    }
    */

    // Add loading state to login button
    const loginForm = document.querySelector('form');
    const loginButton = document.querySelector('.btn-login');
    
    if (loginForm && loginButton) {
        loginForm.addEventListener('submit', function() {
            loginButton.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Entrando...';
            loginButton.disabled = true;
            
            // Re-enable after 3 seconds (in case of client-side validation errors)
            setTimeout(function() {
                loginButton.innerHTML = '<i class="bi bi-box-arrow-in-right me-1"></i> Entrar';
                loginButton.disabled = false;
            }, 3000);
        });
    }

    // Smooth animations for better UX
    const card = document.querySelector('.card');
    if (card) {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(function() {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 100);
    }

});

// Utility function to show custom alerts
function showAlert(message, type = 'info') {
    const alertContainer = document.querySelector('.card-body');
    const existingAlert = alertContainer.querySelector('.alert-custom');
    
    if (existingAlert) {
        existingAlert.remove();
    }
    
    const alertHTML = `
        <div class="alert alert-${type} alert-dismissible fade show alert-custom" role="alert">
            <i class="bi bi-info-circle-fill me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    alertContainer.insertAdjacentHTML('afterbegin', alertHTML);
    
    // Auto-dismiss after 4 seconds
    setTimeout(function() {
        const alert = alertContainer.querySelector('.alert-custom');
        if (alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }
    }, 4000);
}

// Export functions for global use (optional)
window.GebertLogin = {
    showAlert: showAlert
};