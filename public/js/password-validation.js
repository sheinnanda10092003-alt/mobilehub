/**
 * Enhanced Password Validation with Real-time Feedback
 * Validates password strength and confirms password matching
 */

class PasswordValidator {
    constructor(passwordFieldId, confirmFieldId = null) {
        this.passwordField = document.getElementById(passwordFieldId);
        this.confirmField = confirmFieldId ? document.getElementById(confirmFieldId) : null;
        this.init();
    }

    init() {
        if (!this.passwordField) return;

        this.createValidationUI();
        this.attachEventListeners();
    }

    createValidationUI() {
        // Create password requirements indicator
        const requirementsHtml = `
            <div class="password-requirements mt-2" id="${this.passwordField.id}-requirements">
                <small class="text-muted d-block mb-1">Password must contain:</small>
                <div class="requirement-list">
                    <div class="requirement" data-requirement="minLength">
                        <i class="fas fa-times text-danger"></i>
                        <span>At least 8 characters</span>
                    </div>
                    <div class="requirement" data-requirement="uppercase">
                        <i class="fas fa-times text-danger"></i>
                        <span>One uppercase letter (A-Z)</span>
                    </div>
                    <div class="requirement" data-requirement="lowercase">
                        <i class="fas fa-times text-danger"></i>
                        <span>One lowercase letter (a-z)</span>
                    </div>
                    <div class="requirement" data-requirement="number">
                        <i class="fas fa-times text-danger"></i>
                        <span>One number (0-9)</span>
                    </div>
                </div>
            </div>
        `;

        // Insert requirements after the password field container
        const container = this.passwordField.closest('.mb-3') || this.passwordField.closest('.form-group') || this.passwordField.parentElement;
        if (container) {
            container.insertAdjacentHTML('beforeend', requirementsHtml);
        }

        // Create password confirmation feedback if confirm field exists
        if (this.confirmField) {
            const confirmFeedbackHtml = `
                <div class="password-match-feedback mt-2" id="${this.confirmField.id}-feedback" style="display: none;">
                    <small class="match-status"></small>
                </div>
            `;
            const confirmContainer = this.confirmField.closest('.mb-3') || this.confirmField.closest('.form-group') || this.confirmField.parentElement;
            if (confirmContainer) {
                confirmContainer.insertAdjacentHTML('beforeend', confirmFeedbackHtml);
            }
        }
    }

    attachEventListeners() {
        // Password field validation
        this.passwordField.addEventListener('input', () => {
            this.validatePassword();
            if (this.confirmField) {
                this.validatePasswordMatch();
            }
        });

        this.passwordField.addEventListener('focus', () => {
            this.showRequirements();
        });

        // Password confirmation validation
        if (this.confirmField) {
            this.confirmField.addEventListener('input', () => {
                this.validatePasswordMatch();
            });

            this.confirmField.addEventListener('focus', () => {
                this.validatePasswordMatch();
            });
        }
    }

    validatePassword() {
        const password = this.passwordField.value;
        const requirements = {
            minLength: password.length >= 8,
            uppercase: /[A-Z]/.test(password),
            lowercase: /[a-z]/.test(password),
            number: /\d/.test(password)
        };

        // Update requirement indicators
        Object.keys(requirements).forEach(req => {
            const element = document.querySelector(`[data-requirement="${req}"]`);
            if (element) {
                const icon = element.querySelector('i');
                const isValid = requirements[req];

                if (isValid) {
                    icon.className = 'fas fa-check text-success';
                    element.classList.add('valid');
                    element.classList.remove('invalid');
                } else {
                    icon.className = 'fas fa-times text-danger';
                    element.classList.add('invalid');
                    element.classList.remove('valid');
                }
            }
        });

        // Update password field styling
        const isPasswordValid = Object.values(requirements).every(Boolean);
        this.updateFieldStyling(this.passwordField, isPasswordValid && password.length > 0);

        return isPasswordValid;
    }

    validatePasswordMatch() {
        if (!this.confirmField) return true;

        const password = this.passwordField.value;
        const confirmPassword = this.confirmField.value;
        const feedback = document.getElementById(`${this.confirmField.id}-feedback`);

        if (confirmPassword.length === 0) {
            feedback.style.display = 'none';
            this.updateFieldStyling(this.confirmField, null);
            return false;
        }

        feedback.style.display = 'block';
        const isMatch = password === confirmPassword;

        if (isMatch) {
            feedback.innerHTML = '<small class="text-success"><i class="fas fa-check me-1"></i>Passwords match</small>';
            this.updateFieldStyling(this.confirmField, true);
        } else {
            feedback.innerHTML = '<small class="text-danger"><i class="fas fa-times me-1"></i>Passwords do not match</small>';
            this.updateFieldStyling(this.confirmField, false);
        }

        return isMatch;
    }

    updateFieldStyling(field, isValid) {
        field.classList.remove('is-valid', 'is-invalid');
        
        if (isValid === true) {
            field.classList.add('is-valid');
        } else if (isValid === false) {
            field.classList.add('is-invalid');
        }
    }

    showRequirements() {
        const requirements = document.getElementById(`${this.passwordField.id}-requirements`);
        if (requirements) {
            requirements.style.display = 'block';
        }
    }

    hideRequirements() {
        const requirements = document.getElementById(`${this.passwordField.id}-requirements`);
        if (requirements) {
            requirements.style.display = 'none';
        }
    }

    isValid() {
        const passwordValid = this.validatePassword();
        const confirmValid = this.confirmField ? this.validatePasswordMatch() : true;
        return passwordValid && confirmValid;
    }
}

// Auto-initialize password validators
document.addEventListener('DOMContentLoaded', function() {
    // Check for password fields and initialize validators
    const passwordField = document.getElementById('password');
    const confirmField = document.getElementById('password_confirmation');

    if (passwordField) {
        const validator = new PasswordValidator('password', confirmField ? 'password_confirmation' : null);
        
        // Store validator instance for potential external access
        window.passwordValidator = validator;

        // Enhance form submission validation
        const form = passwordField.closest('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                if (!validator.isValid()) {
                    e.preventDefault();
                    
                    // Show validation messages
                    if (!validator.validatePassword()) {
                        passwordField.focus();
                        alert('Please ensure your password meets all requirements.');
                    } else if (confirmField && !validator.validatePasswordMatch()) {
                        confirmField.focus();
                        alert('Password confirmation does not match.');
                    }
                }
            });
        }
    }
});

// Export for manual initialization
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { PasswordValidator };
}