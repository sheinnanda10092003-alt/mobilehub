/**
 * Password visibility toggle functionality
 * Works with any password input that has a toggle button with class 'password-toggle'
 * and data-target attribute pointing to the password input's id
 */

function initPasswordToggle() {
    const passwordToggles = document.querySelectorAll('.password-toggle');
    
    passwordToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const passwordInput = document.getElementById(targetId);
            const icon = this.querySelector('i');
            
            if (!passwordInput || !icon) return;
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
                this.setAttribute('title', 'Hide password');
                this.setAttribute('aria-label', 'Hide password');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
                this.setAttribute('title', 'Show password');
                this.setAttribute('aria-label', 'Show password');
            }
        });
        
        // Set initial attributes for accessibility
        toggle.setAttribute('title', 'Show password');
        toggle.setAttribute('aria-label', 'Show password');
        toggle.setAttribute('type', 'button'); // Ensure it's not a submit button
    });
}

// Auto-initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initPasswordToggle();
});

// Also export for manual initialization if needed
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { initPasswordToggle };
}