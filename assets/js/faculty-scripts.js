/**
 * Create Faculty Page JavaScript
 * Enhanced functionality for faculty account creation
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize form validation
    initializeFormValidation();
    
    // Initialize password strength checker
    initializePasswordStrength();
    
    // Initialize form submission
    initializeFormSubmission();
    
    // Initialize sample data creation
    initializeSampleCreation();
    
    // Initialize real-time validation
    initializeRealTimeValidation();
    
    // Initialize keyboard shortcuts
    initializeKeyboardShortcuts();
});

/**
 * Initialize form validation
 */
function initializeFormValidation() {
    const form = document.getElementById('facultyForm');
    if (!form) return;
    
    form.addEventListener('submit', function(event) {
        event.preventDefault();
        
        // Validate all fields
        const isValid = validateForm();
        
        if (isValid) {
            // Show loading state
            showLoadingState();
            
            // Submit form after validation
            setTimeout(() => {
                form.submit();
            }, 1000);
        } else {
            // Show error notification
            showNotification('Please fix the errors in the form before submitting.', 'error');
            
            // Scroll to first error
            const firstError = form.querySelector('.form-control.invalid');
            if (firstError) {
                firstError.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                firstError.focus();
            }
        }
    });
}

/**
 * Validate entire form
 */
function validateForm() {
    let isValid = true;
    
    // Required fields
    const requiredFields = form.querySelectorAll('[required]');
    requiredFields.forEach(field => {
        if (!validateField(field)) {
            isValid = false;
        }
    });
    
    // Password confirmation
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');
    if (password && confirmPassword && password.value !== confirmPassword.value) {
        showFieldError(confirmPassword, 'Passwords do not match');
        isValid = false;
    }
    
    // Email format
    const emailField = document.getElementById('email');
    if (emailField && emailField.value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(emailField.value)) {
            showFieldError(emailField, 'Please enter a valid email address');
            isValid = false;
        }
    }
    
    // Phone format (if provided)
    const phoneField = document.getElementById('phone');
    if (phoneField && phoneField.value) {
        const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
        const cleanPhone = phoneField.value.replace(/\D/g, '');
        if (!phoneRegex.test(cleanPhone)) {
            showFieldError(phoneField, 'Please enter a valid phone number');
            isValid = false;
        }
    }
    
    return isValid;
}

/**
 * Validate individual field
 */
function validateField(field) {
    const value = field.value.trim();
    const fieldId = field.id;
    
    // Clear previous errors
    clearFieldError(field);
    
    // Check required fields
    if (field.hasAttribute('required') && !value) {
        showFieldError(field, 'This field is required');
        return false;
    }
    
    // Field-specific validation
    switch (fieldId) {
        case 'username':
            if (value.length < 3 || value.length > 20) {
                showFieldError(field, 'Username must be 3-20 characters');
                return false;
            }
            if (!/^[a-zA-Z0-9_]+$/.test(value)) {
                showFieldError(field, 'Only letters, numbers, and underscores allowed');
                return false;
            }
            break;
            
        case 'email':
            if (value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                showFieldError(field, 'Please enter a valid email address');
                return false;
            }
            break;
            
        case 'password':
            if (value && !isStrongPassword(value)) {
                showFieldError(field, 'Password must be at least 8 characters with uppercase, lowercase, number, and special character');
                return false;
            }
            break;
            
        case 'employee_id':
            if (value && !/^[A-Za-z0-9]{6,12}$/.test(value)) {
                showFieldError(field, 'Employee ID must be 6-12 alphanumeric characters');
                return false;
            }
            break;
            
        case 'phone':
            if (value) {
                const cleanPhone = value.replace(/\D/g, '');
                if (cleanPhone.length < 10) {
                    showFieldError(field, 'Phone number must be at least 10 digits');
                    return false;
                }
            }
            break;
    }
    
    // Mark field as valid
    field.classList.remove('invalid');
    field.classList.add('valid');
    
    return true;
}

/**
 * Check password strength
 */
function isStrongPassword(password) {
    const minLength = 8;
    const hasUpper = /[A-Z]/.test(password);
    const hasLower = /[a-z]/.test(password);
    const hasNumber = /\d/.test(password);
    const hasSpecial = /[!@#$%^&*(),.?":{}|<>]/.test(password);
    
    return password.length >= minLength && hasUpper && hasLower && hasNumber && hasSpecial;
}

/**
 * Show field error
 */
function showFieldError(field, message) {
    field.classList.remove('valid');
    field.classList.add('invalid');
    
    // Create or update error message
    let errorDiv = field.parentNode.querySelector('.validation-message');
    if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.className = 'validation-message invalid';
        field.parentNode.appendChild(errorDiv);
    }
    
    errorDiv.innerHTML = `<i class="fa fa-exclamation-circle"></i> ${message}`;
}

/**
 * Clear field error
 */
function clearFieldError(field) {
    field.classList.remove('invalid', 'valid');
    
    const errorDiv = field.parentNode.querySelector('.validation-message');
    if (errorDiv) {
        errorDiv.remove();
    }
}

/**
 * Initialize password strength checker
 */
function initializePasswordStrength() {
    const passwordField = document.getElementById('password');
    if (!passwordField) return;
    
    passwordField.addEventListener('input', function() {
        updatePasswordStrength(this.value);
        checkPasswordMatch();
    });
    
    // Check password match on confirm password input
    const confirmPasswordField = document.getElementById('confirm_password');
    if (confirmPasswordField) {
        confirmPasswordField.addEventListener('input', checkPasswordMatch);
    }
}

/**
 * Update password strength display
 */
function updatePasswordStrength(password) {
    const strengthBar = document.getElementById('passwordStrength');
    const strengthText = document.getElementById('passwordStrengthText');
    
    if (!strengthBar || !strengthText) return;
    
    let strength = 0;
    let text = 'Very Weak';
    let color = '#ef4444';
    
    if (password.length > 0) {
        strength += 20; // Length
    }
    
    if (password.length >= 8) {
        strength += 20; // Minimum length
    }
    
    if (/[A-Z]/.test(password)) {
        strength += 20; // Uppercase
    }
    
    if (/[a-z]/.test(password)) {
        strength += 20; // Lowercase
    }
    
    if (/\d/.test(password)) {
        strength += 10; // Number
    }
    
    if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
        strength += 10; // Special character
    }
    
    // Set strength level
    if (strength >= 80) {
        text = 'Very Strong';
        color = '#10b981';
    } else if (strength >= 60) {
        text = 'Strong';
        color = '#22c55e';
    } else if (strength >= 40) {
        text = 'Good';
        color = '#eab308';
    } else if (strength >= 20) {
        text = 'Weak';
        color = '#f97316';
    }
    
    strengthBar.style.width = `${strength}%`;
    strengthBar.style.background = color;
    strengthText.textContent = text;
    strengthText.style.color = color;
}

/**
 * Check if passwords match
 */
function checkPasswordMatch() {
    const passwordField = document.getElementById('password');
    const confirmField = document.getElementById('confirm_password');
    const messageElement = document.getElementById('passwordMatchMessage');
    
    if (!passwordField || !confirmField || !messageElement) return;
    
    const password = passwordField.value;
    const confirm = confirmField.value;
    
    if (confirm.length === 0) {
        messageElement.textContent = '';
        confirmField.classList.remove('invalid', 'valid');
        return;
    }
    
    if (password === confirm) {
        messageElement.textContent = 'Passwords match ✓';
        messageElement.style.color = '#10b981';
        confirmField.classList.remove('invalid');
        confirmField.classList.add('valid');
    } else {
        messageElement.textContent = 'Passwords do not match ✗';
        messageElement.style.color = '#ef4444';
        confirmField.classList.remove('valid');
        confirmField.classList.add('invalid');
    }
}

/**
 * Toggle password visibility
 */
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const button = event.currentTarget;
    const icon = button.querySelector('i');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
        button.title = 'Hide password';
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
        button.title = 'Show password';
    }
}

/**
 * Initialize form submission
 */
function initializeFormSubmission() {
    const form = document.getElementById('facultyForm');
    if (!form) return;
    
    // Add form reset handler
    const resetButton = form.querySelector('button[type="reset"]');
    if (resetButton) {
        resetButton.addEventListener('click', function() {
            setTimeout(() => {
                resetValidation();
                showNotification('Form has been reset', 'info');
            }, 100);
        });
    }
}

/**
 * Reset form validation
 */
function resetValidation() {
    const form = document.getElementById('facultyForm');
    if (!form) return;
    
    // Clear all validation states
    const fields = form.querySelectorAll('.form-control');
    fields.forEach(field => {
        field.classList.remove('valid', 'invalid');
    });
    
    // Clear validation messages
    const messages = form.querySelectorAll('.validation-message');
    messages.forEach(message => message.remove());
    
    // Reset password strength
    const strengthBar = document.getElementById('passwordStrength');
    const strengthText = document.getElementById('passwordStrengthText');
    if (strengthBar) strengthBar.style.width = '0%';
    if (strengthText) strengthText.textContent = 'Password strength';
}

/**
 * Initialize sample data creation
 */
function initializeSampleCreation() {
    const sampleCards = document.querySelectorAll('.sample-card');
    sampleCards.forEach(card => {
        card.addEventListener('click', function() {
            const sampleType = this.getAttribute('onclick').match(/createSample\('([^']+)'\)/)[1];
            createSample(sampleType);
        });
    });
}

/**
 * Create sample faculty data
 */
function createSample(type) {
    const samples = {
        cs_professor: {
            username: 'john_ai_expert',
            email: 'john.ai@uai.edu',
            password: 'Ai@Expert2024',
            confirm_password: 'Ai@Expert2024',
            first_name: 'John',
            last_name: 'Smith',
            employee_id: 'UAI-EMP-AI001',
            department: 'Artificial Intelligence',
            designation: 'Professor',
            phone: '+1 (555) 123-4567',
            specialization: 'Deep Learning, Neural Networks, Computer Vision'
        },
        ml_scientist: {
            username: 'jane_ml_research',
            email: 'jane.ml@uai.edu',
            password: 'Ml@Research2024',
            confirm_password: 'Ml@Research2024',
            first_name: 'Jane',
            last_name: 'Wilson',
            employee_id: 'UAI-EMP-ML002',
            department: 'Machine Learning',
            designation: 'Research Scholar',
            phone: '+1 (555) 987-6543',
            specialization: 'Supervised Learning, Unsupervised Learning, Reinforcement Learning'
        },
        ds_lecturer: {
            username: 'bob_ds_lecturer',
            email: 'bob.ds@uai.edu',
            password: 'Ds@Lecturer2024',
            confirm_password: 'Ds@Lecturer2024',
            first_name: 'Bob',
            last_name: 'Johnson',
            employee_id: 'UAI-EMP-DS003',
            department: 'Data Science',
            designation: 'Lecturer',
            phone: '+1 (555) 456-7890',
            specialization: 'Data Analysis, Statistics, Big Data Analytics'
        },
        cyber_expert: {
            username: 'alice_cyber_sec',
            email: 'alice.cs@uai.edu',
            password: 'Cyber@Sec2024',
            confirm_password: 'Cyber@Sec2024',
            first_name: 'Alice',
            last_name: 'Brown',
            employee_id: 'UAI-EMP-CS004',
            department: 'Cyber Security',
            designation: 'Associate Professor',
            phone: '+1 (555) 321-0987',
            specialization: 'Network Security, Cryptography, Ethical Hacking'
        }
    };
    
    const sample = samples[type];
    if (!sample) return;
    
    // Fill form with sample data
    Object.keys(sample).forEach(fieldName => {
        const field = document.querySelector(`[name="${fieldName}"]`);
        if (field) {
            field.value = sample[fieldName];
            
            // Trigger input events for validation
            const event = new Event('input', { bubbles: true });
            field.dispatchEvent(event);
        }
    });
    
    // Show notification
    showNotification(`Sample ${type.replace('_', ' ')} data loaded. Modify as needed and submit.`, 'info');
    
    // Scroll to top of form
    document.querySelector('.form-card').scrollIntoView({
        behavior: 'smooth',
        block: 'start'
    });
}

/**
 * Initialize real-time validation
 */
function initializeRealTimeValidation() {
    const form = document.getElementById('facultyForm');
    if (!form) return;
    
    // Add input event listeners for real-time validation
    const fields = form.querySelectorAll('.form-control');
    fields.forEach(field => {
        field.addEventListener('blur', function() {
            validateField(this);
        });
        
        field.addEventListener('input', function() {
            // Clear error state on input
            this.classList.remove('invalid');
            const errorDiv = this.parentNode.querySelector('.validation-message');
            if (errorDiv) {
                errorDiv.remove();
            }
            
            // Special handling for password match
            if (this.id === 'password' || this.id === 'confirm_password') {
                checkPasswordMatch();
            }
        });
    });
}

/**
 * Initialize keyboard shortcuts
 */
function initializeKeyboardShortcuts() {
    document.addEventListener('keydown', function(event) {
        // Ctrl/Cmd + Enter to submit form
        if ((event.ctrlKey || event.metaKey) && event.key === 'Enter') {
            const form = document.getElementById('facultyForm');
            if (form) {
                event.preventDefault();
                form.dispatchEvent(new Event('submit'));
            }
        }
        
        // Esc to reset form
        if (event.key === 'Escape') {
            const resetButton = document.querySelector('button[type="reset"]');
            if (resetButton) {
                resetButton.click();
            }
        }
        
        // Ctrl/Cmd + S to save (submit)
        if ((event.ctrlKey || event.metaKey) && event.key === 's') {
            event.preventDefault();
            const submitButton = document.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.click();
            }
        }
    });
}

/**
 * Show loading state
 */
function showLoadingState() {
    const form = document.querySelector('.form-card .card-body');
    if (!form) return;
    
    // Create loading overlay
    const overlay = document.createElement('div');
    overlay.className = 'loading-overlay';
    overlay.innerHTML = '<div class="loading-spinner"></div>';
    
    form.style.position = 'relative';
    form.appendChild(overlay);
    
    // Remove overlay after form submission
    setTimeout(() => {
        if (overlay.parentNode) {
            overlay.remove();
        }
    }, 2000);
}

/**
 * Show notification
 */
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.custom-notification');
    existingNotifications.forEach(notification => {
        notification.remove();
    });
    
    const notification = document.createElement('div');
    notification.className = `custom-notification ${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fa ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'}"></i>
            <span>${message}</span>
        </div>
        <button class="notification-close" onclick="this.parentElement.remove()">
            <i class="fa fa-times"></i>
        </button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}

// Add notification styles
const notificationStyles = document.createElement('style');
notificationStyles.textContent = `
    .custom-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 16px 20px;
        border-radius: 8px;
        color: white;
        font-weight: 500;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        z-index: 9999;
        animation: slideInRight 0.3s ease;
        min-width: 300px;
        max-width: 400px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    
    .custom-notification.success {
        background: linear-gradient(135deg, #059669, #047857);
    }
    
    .custom-notification.error {
        background: linear-gradient(135deg, #dc2626, #b91c1c);
    }
    
    .custom-notification.info {
        background: linear-gradient(135deg, #0891b2, #0e7490);
    }
    
    .notification-content {
        display: flex;
        align-items: center;
        gap: 12px;
        flex: 1;
    }
    
    .notification-content i {
        font-size: 20px;
    }
    
    .notification-close {
        background: none;
        border: none;
        color: white;
        opacity: 0.7;
        cursor: pointer;
        padding: 0;
        font-size: 18px;
        transition: opacity 0.3s ease;
    }
    
    .notification-close:hover {
        opacity: 1;
    }
    
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
`;
document.head.appendChild(notificationStyles);