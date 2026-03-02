/**
 * Create Student Page JavaScript
 * Enhanced functionality for student account creation
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
    
    // Initialize date validation
    initializeDateValidation();
    
    // Initialize keyboard shortcuts
    initializeKeyboardShortcuts();
});

/**
 * Initialize form validation
 */
function initializeFormValidation() {
    const form = document.getElementById('studentForm');
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
    
    // Age validation (minimum 16 years)
    const dobField = document.getElementById('date_of_birth');
    if (dobField && dobField.value) {
        const dob = new Date(dobField.value);
        const today = new Date();
        const age = today.getFullYear() - dob.getFullYear();
        const monthDiff = today.getMonth() - dob.getMonth();
        
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
            age--;
        }
        
        if (age < 16) {
            showFieldError(dobField, 'Student must be at least 16 years old');
            isValid = false;
        }
    }
    
    // Phone validation
    const phoneField = document.getElementById('phone');
    if (phoneField && phoneField.value) {
        if (!validatePhoneNumber(phoneField.value)) {
            showFieldError(phoneField, 'Please enter a valid phone number');
            isValid = false;
        }
    }
    
    // Student ID validation
    const studentIdField = document.getElementById('student_id');
    if (studentIdField && studentIdField.value) {
        if (!/^[A-Za-z0-9\-]{8,15}$/.test(studentIdField.value)) {
            showFieldError(studentIdField, 'Student ID must be 8-15 alphanumeric characters');
            isValid = false;
        }
    }
    
    // Terms acceptance
    const termsCheckbox = document.getElementById('terms_accepted');
    if (termsCheckbox && !termsCheckbox.checked) {
        showFieldError(termsCheckbox, 'You must accept the terms of enrollment');
        isValid = false;
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
                showFieldError(field, 'Password must be at least 8 characters with uppercase, lowercase, and number');
                return false;
            }
            break;
            
        case 'student_id':
            if (value && !/^[A-Za-z0-9\-]{8,15}$/.test(value)) {
                showFieldError(field, 'Student ID must be 8-15 alphanumeric characters');
                return false;
            }
            break;
            
        case 'phone':
            if (value && !validatePhoneNumber(value)) {
                showFieldError(field, 'Please enter a valid phone number');
                return false;
            }
            break;
            
        case 'emergency_contact':
            if (value && !validatePhoneNumber(value)) {
                showFieldError(field, 'Please enter a valid emergency contact number');
                return false;
            }
            break;
            
        case 'enrollment_year':
            const currentYear = new Date().getFullYear();
            if (value && (value < currentYear - 5 || value > currentYear)) {
                showFieldError(field, 'Enrollment year must be within the last 5 years');
                return false;
            }
            break;
    }
    
    // Mark field as valid
    if (field.type !== 'checkbox') {
        field.classList.remove('invalid');
        field.classList.add('valid');
    }
    
    return true;
}

/**
 * Check password strength (simpler for students)
 */
function isStrongPassword(password) {
    const minLength = 8;
    const hasUpper = /[A-Z]/.test(password);
    const hasLower = /[a-z]/.test(password);
    const hasNumber = /\d/.test(password);
    
    return password.length >= minLength && hasUpper && hasLower && hasNumber;
}

/**
 * Validate phone number
 */
function validatePhoneNumber(phone) {
    // Remove all non-digit characters
    const cleanPhone = phone.replace(/\D/g, '');
    
    // Check if phone number has at least 10 digits
    return cleanPhone.length >= 10;
}

/**
 * Show field error
 */
function showFieldError(field, message) {
    if (field.type === 'checkbox') {
        // Special handling for checkboxes
        const checkboxContainer = field.closest('.checkbox-label');
        if (checkboxContainer) {
            checkboxContainer.classList.add('invalid');
            
            let errorDiv = checkboxContainer.parentNode.querySelector('.validation-message');
            if (!errorDiv) {
                errorDiv = document.createElement('div');
                errorDiv.className = 'validation-message invalid';
                checkboxContainer.parentNode.appendChild(errorDiv);
            }
            
            errorDiv.innerHTML = `<i class="fa fa-exclamation-circle"></i> ${message}`;
        }
    } else {
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
}

/**
 * Clear field error
 */
function clearFieldError(field) {
    if (field.type === 'checkbox') {
        const checkboxContainer = field.closest('.checkbox-label');
        if (checkboxContainer) {
            checkboxContainer.classList.remove('invalid');
        }
    } else {
        field.classList.remove('invalid', 'valid');
    }
    
    const parent = field.type === 'checkbox' ? field.closest('.checkbox-label')?.parentNode : field.parentNode;
    if (parent) {
        const errorDiv = parent.querySelector('.validation-message');
        if (errorDiv) {
            errorDiv.remove();
        }
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
        strength += 25; // Length
    }
    
    if (password.length >= 8) {
        strength += 25; // Minimum length
    }
    
    if (/[A-Z]/.test(password)) {
        strength += 25; // Uppercase
    }
    
    if (/[a-z]/.test(password)) {
        strength += 25; // Lowercase
    }
    
    // Set strength level
    if (strength >= 75) {
        text = 'Strong';
        color = '#10b981';
    } else if (strength >= 50) {
        text = 'Good';
        color = '#eab308';
    } else if (strength >= 25) {
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
    const form = document.getElementById('studentForm');
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
    const form = document.getElementById('studentForm');
    if (!form) return;
    
    // Clear all validation states
    const fields = form.querySelectorAll('.form-control, .checkbox-label');
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
 * Create sample student data
 */
function createSample(type) {
    const currentYear = new Date().getFullYear();
    
    const samples = {
        freshman_ai: {
            username: 'alice_ai_student',
            email: 'alice.ai@uai.edu',
            password: 'Ai@Student2024',
            confirm_password: 'Ai@Student2024',
            first_name: 'Alice',
            last_name: 'Johnson',
            date_of_birth: '2005-06-15',
            gender: 'Female',
            address: '123 University Ave, Tech City',
            phone: '+1 (555) 123-4567',
            emergency_contact: '+1 (555) 987-6543',
            student_id: `UAI-${currentYear}-00123`,
            enrollment_year: currentYear,
            program: 'B.Tech Artificial Intelligence',
            current_semester: '1',
            previous_education: 'St. Mary High School, GPA: 4.0/4.0',
            nationality: 'American',
            blood_group: 'O+',
            medical_conditions: 'None',
            terms_accepted: true
        },
        junior_ds: {
            username: 'mike_ds_junior',
            email: 'mike.ds@uai.edu',
            password: 'Ds@Junior2024',
            confirm_password: 'Ds@Junior2024',
            first_name: 'Mike',
            last_name: 'Brown',
            date_of_birth: '2003-03-22',
            gender: 'Male',
            address: '456 Data Street, Analytics City',
            phone: '+1 (555) 234-5678',
            emergency_contact: '+1 (555) 876-5432',
            student_id: `UAI-${currentYear - 2}-04567`,
            enrollment_year: currentYear - 2,
            program: 'B.Tech Data Science',
            current_semester: '5',
            previous_education: 'Tech High School, GPA: 3.8/4.0',
            nationality: 'Canadian',
            blood_group: 'A+',
            medical_conditions: 'Asthma (mild)',
            terms_accepted: true
        },
        senior_cs: {
            username: 'sarah_cs_senior',
            email: 'sarah.cs@uai.edu',
            password: 'Cs@Senior2024',
            confirm_password: 'Cs@Senior2024',
            first_name: 'Sarah',
            last_name: 'Davis',
            date_of_birth: '2002-11-30',
            gender: 'Female',
            address: '789 Code Lane, Programming City',
            phone: '+1 (555) 345-6789',
            emergency_contact: '+1 (555) 765-4321',
            student_id: `UAI-${currentYear - 3}-08901`,
            enrollment_year: currentYear - 3,
            program: 'B.Tech Computer Science',
            current_semester: '7',
            previous_education: 'Computer Science High School, GPA: 3.9/4.0',
            nationality: 'British',
            blood_group: 'B+',
            medical_conditions: 'None',
            terms_accepted: true
        },
        mtech_ml: {
            username: 'john_ml_mtech',
            email: 'john.ml@uai.edu',
            password: 'Ml@Mtech2024',
            confirm_password: 'Ml@Mtech2024',
            first_name: 'John',
            last_name: 'Wilson',
            date_of_birth: '2000-08-14',
            gender: 'Male',
            address: '321 AI Boulevard, Machine City',
            phone: '+1 (555) 456-7890',
            emergency_contact: '+1 (555) 654-3210',
            student_id: `UAI-MT-${currentYear - 1}-02345`,
            enrollment_year: currentYear - 1,
            program: 'M.Tech AI & ML',
            current_semester: '3',
            previous_education: 'B.Tech Computer Science, GPA: 3.7/4.0',
            nationality: 'Australian',
            blood_group: 'AB+',
            medical_conditions: 'None',
            terms_accepted: true
        }
    };
    
    const sample = samples[type];
    if (!sample) return;
    
    // Fill form with sample data
    Object.keys(sample).forEach(fieldName => {
        const field = document.querySelector(`[name="${fieldName}"]`);
        if (field) {
            if (field.type === 'checkbox') {
                field.checked = sample[fieldName];
            } else {
                field.value = sample[fieldName];
            }
            
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
    const form = document.getElementById('studentForm');
    if (!form) return;
    
    // Add input event listeners for real-time validation
    const fields = form.querySelectorAll('.form-control, input[type="checkbox"]');
    fields.forEach(field => {
        field.addEventListener('blur', function() {
            validateField(this);
        });
        
        field.addEventListener('input', function() {
            // Clear error state on input
            clearFieldError(this);
            
            // Special handling for password match
            if (this.id === 'password' || this.id === 'confirm_password') {
                checkPasswordMatch();
            }
            
            // Special handling for date of birth
            if (this.id === 'date_of_birth') {
                validateDateOfBirth(this);
            }
        });
    });
}

/**
 * Initialize date validation
 */
function initializeDateValidation() {
    const dobField = document.getElementById('date_of_birth');
    if (!dobField) return;
    
    // Set max date (today) and min date (100 years ago)
    const today = new Date();
    const maxDate = new Date();
    maxDate.setFullYear(today.getFullYear() - 16); // Minimum 16 years old
    const minDate = new Date();
    minDate.setFullYear(today.getFullYear() - 100); // Maximum 100 years old
    
    dobField.max = maxDate.toISOString().split('T')[0];
    dobField.min = minDate.toISOString().split('T')[0];
    
    // Set default value (18 years ago)
    const defaultDob = new Date();
    defaultDob.setFullYear(today.getFullYear() - 18);
    dobField.value = defaultDob.toISOString().split('T')[0];
}

/**
 * Validate date of birth
 */
function validateDateOfBirth(field) {
    if (!field.value) return;
    
    const dob = new Date(field.value);
    const today = new Date();
    const age = today.getFullYear() - dob.getFullYear();
    const monthDiff = today.getMonth() - dob.getMonth();
    
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
        age--;
    }
    
    if (age < 16) {
        showFieldError(field, 'Student must be at least 16 years old');
        return false;
    }
    
    return true;
}

/**
 * Initialize keyboard shortcuts
 */
function initializeKeyboardShortcuts() {
    document.addEventListener('keydown', function(event) {
        // Ctrl/Cmd + Enter to submit form
        if ((event.ctrlKey || event.metaKey) && event.key === 'Enter') {
            const form = document.getElementById('studentForm');
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

// Add checkbox invalid styles
const checkboxStyles = document.createElement('style');
checkboxStyles.textContent = `
    .checkbox-label.invalid {
        color: #ef4444;
    }
    
    .checkbox-label.invalid input[type="checkbox"] {
        accent-color: #ef4444;
    }
`;
document.head.appendChild(checkboxStyles);