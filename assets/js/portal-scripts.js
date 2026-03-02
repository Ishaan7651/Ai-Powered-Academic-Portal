/**
 * College Academic Portal - Enhanced JavaScript Functionality
 * Provides interactive features and improved user experience
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // Initialize all interactive features
    initializeFlashMessages();
    initializeFormHandling();
    initializeCardHoverEffects();
    initializeSmoothScrolling();
    initializeFileUpload();
    initializeChatInterface();
    initializeResponsiveFeatures();
    initializeAccessibilityFeatures();
    initializeLoadingStates();
    initializeTooltips();
});

/**
 * Enhanced Flash Message Handling
 */
function initializeFlashMessages() {
    const alerts = document.querySelectorAll('.message-alert, .alert');
    
    alerts.forEach(function(alert) {
        // Add dismiss functionality
        alert.style.cursor = 'pointer';
        alert.title = 'Click to dismiss';
        
        // Auto-hide after 5 seconds
        setTimeout(function() {
            if (alert.parentNode) {
                hideAlert(alert);
            }
        }, 5000);
        
        // Click to dismiss
        alert.addEventListener('click', function() {
            hideAlert(this);
        });
    });
}

function hideAlert(alert) {
    alert.style.transition = 'all 0.5s ease-out';
    alert.style.transform = 'translateY(-10px)';
    alert.style.opacity = '0';
    
    setTimeout(function() {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 500);
}

/**
 * Enhanced Form Handling
 */
function initializeFormHandling() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(function(form) {
        // Add loading states to submit buttons
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
            if (submitBtn && !submitBtn.disabled) {
                addLoadingState(submitBtn);
            }
        });
        
        // Real-time validation
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(function(input) {
            input.addEventListener('blur', function() {
                validateField(this);
            });
            
            input.addEventListener('input', function() {
                clearFieldError(this);
            });
        });
    });
    
    // Enhanced form validation
    const validationForms = document.querySelectorAll('.needs-validation');
    validationForms.forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                
                // Focus on first invalid field
                const firstInvalid = form.querySelector(':invalid');
                if (firstInvalid) {
                    firstInvalid.focus();
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
            form.classList.add('was-validated');
        });
    });
}

function addLoadingState(button) {
    const originalText = button.innerHTML;
    const originalWidth = button.offsetWidth;
    
    button.disabled = true;
    button.style.width = originalWidth + 'px';
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
    
    // Store original state for restoration
    button.dataset.originalText = originalText;
    button.dataset.originalWidth = originalWidth;
    
    // Auto-restore after 10 seconds as fallback
    setTimeout(function() {
        restoreButtonState(button);
    }, 10000);
}

function restoreButtonState(button) {
    if (button.dataset.originalText) {
        button.disabled = false;
        button.innerHTML = button.dataset.originalText;
        button.style.width = 'auto';
        delete button.dataset.originalText;
        delete button.dataset.originalWidth;
    }
}

function validateField(field) {
    const value = field.value.trim();
    const fieldType = field.type;
    const isRequired = field.hasAttribute('required');
    
    // Clear previous validation
    clearFieldError(field);
    
    // Required field validation
    if (isRequired && !value) {
        showFieldError(field, 'This field is required');
        return false;
    }
    
    // Email validation
    if (fieldType === 'email' && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            showFieldError(field, 'Please enter a valid email address');
            return false;
        }
    }
    
    // Password validation
    if (fieldType === 'password' && value) {
        if (value.length < 6) {
            showFieldError(field, 'Password must be at least 6 characters long');
            return false;
        }
    }
    
    return true;
}

function showFieldError(field, message) {
    field.classList.add('is-invalid');
    
    let errorDiv = field.parentNode.querySelector('.invalid-feedback');
    if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        field.parentNode.appendChild(errorDiv);
    }
    errorDiv.textContent = message;
}

function clearFieldError(field) {
    field.classList.remove('is-invalid');
    const errorDiv = field.parentNode.querySelector('.invalid-feedback');
    if (errorDiv) {
        errorDiv.remove();
    }
}

/**
 * Enhanced Card Hover Effects
 */
function initializeCardHoverEffects() {
    const cards = document.querySelectorAll('.card:not(.disabled)');
    
    cards.forEach(function(card) {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-4px)';
            this.style.transition = 'all 0.3s ease';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
    
    // Special handling for semester cards
    const semesterCards = document.querySelectorAll('.semester-card');
    semesterCards.forEach(function(card) {
        if (!card.classList.contains('disabled')) {
            card.addEventListener('click', function() {
                // Add click animation
                this.style.transform = 'scale(0.98)';
                setTimeout(() => {
                    this.style.transform = 'translateY(-4px)';
                }, 100);
            });
        }
    });
}

/**
 * Smooth Scrolling for Anchor Links
 */
function initializeSmoothScrolling() {
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    
    anchorLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            const target = document.querySelector(targetId);
            
            if (target) {
                e.preventDefault();
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
                
                // Update URL without jumping
                history.pushState(null, null, targetId);
            }
        });
    });
}

/**
 * Enhanced File Upload Handling
 */
function initializeFileUpload() {
    const fileInputs = document.querySelectorAll('input[type="file"]');
    
    fileInputs.forEach(function(input) {
        const dropArea = input.closest('.file-upload-area');
        
        if (dropArea) {
            // Drag and drop functionality
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, preventDefaults, false);
            });
            
            ['dragenter', 'dragover'].forEach(eventName => {
                dropArea.addEventListener(eventName, () => dropArea.classList.add('dragover'), false);
            });
            
            ['dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, () => dropArea.classList.remove('dragover'), false);
            });
            
            dropArea.addEventListener('drop', function(e) {
                const files = e.dataTransfer.files;
                handleFiles(files, input);
            });
            
            // Click to upload
            dropArea.addEventListener('click', function() {
                input.click();
            });
        }
        
        // File selection handling
        input.addEventListener('change', function() {
            handleFiles(this.files, this);
        });
    });
}

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

function handleFiles(files, input) {
    const maxSize = 100 * 1024 * 1024; // 100MB
    const allowedTypes = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'csv', 'epub'];
    
    Array.from(files).forEach(function(file) {
        // File size validation
        if (file.size > maxSize) {
            showAlert('File size must be less than 100MB', 'danger');
            return;
        }
        
        // File type validation
        const fileExtension = file.name.split('.').pop().toLowerCase();
        if (!allowedTypes.includes(fileExtension)) {
            showAlert('File type not supported. Please upload PDF, DOC, PPT, XLS, CSV, or ePub files.', 'danger');
            return;
        }
        
        // Show file preview
        showFilePreview(file, input);
    });
}

function showFilePreview(file, input) {
    const previewArea = input.parentNode.querySelector('.file-preview') || createFilePreview(input);
    
    const fileItem = document.createElement('div');
    fileItem.className = 'file-item d-flex align-items-center justify-content-between p-2 border rounded mb-2';
    
    fileItem.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas fa-file-alt text-primary me-2"></i>
            <div>
                <div class="fw-semibold">${file.name}</div>
                <small class="text-muted">${formatFileSize(file.size)}</small>
            </div>
        </div>
        <button type="button" class="btn btn-sm btn-outline-danger remove-file">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    // Remove file functionality
    fileItem.querySelector('.remove-file').addEventListener('click', function() {
        fileItem.remove();
        input.value = '';
    });
    
    previewArea.appendChild(fileItem);
}

function createFilePreview(input) {
    const previewArea = document.createElement('div');
    previewArea.className = 'file-preview mt-3';
    input.parentNode.appendChild(previewArea);
    return previewArea;
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

/**
 * Chat Interface Enhancement
 */
function initializeChatInterface() {
    const chatContainers = document.querySelectorAll('.chat-container');
    
    chatContainers.forEach(function(container) {
        const messagesArea = container.querySelector('.chat-messages');
        const inputArea = container.querySelector('.chat-input-area');
        const sendButton = container.querySelector('.send-message');
        const messageInput = container.querySelector('.message-input');
        
        if (sendButton && messageInput) {
            // Send message on button click
            sendButton.addEventListener('click', function() {
                sendChatMessage(messageInput, messagesArea);
            });
            
            // Send message on Enter key
            messageInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendChatMessage(messageInput, messagesArea);
                }
            });
            
            // Auto-resize textarea
            messageInput.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = this.scrollHeight + 'px';
            });
        }
        
        // Auto-scroll to bottom
        if (messagesArea) {
            scrollToBottom(messagesArea);
        }
    });
}

function sendChatMessage(input, messagesArea) {
    const message = input.value.trim();
    if (!message) return;
    
    // Add user message to chat
    addMessageToChat(message, 'user', messagesArea);
    
    // Clear input
    input.value = '';
    input.style.height = 'auto';
    
    // Show typing indicator
    showTypingIndicator(messagesArea);
    
    // Simulate AI response (replace with actual API call)
    setTimeout(function() {
        hideTypingIndicator(messagesArea);
        addMessageToChat('This is a simulated response. Integrate with your chat API here.', 'assistant', messagesArea);
    }, 2000);
}

function addMessageToChat(message, type, messagesArea) {
    const messageDiv = document.createElement('div');
    messageDiv.className = `chat-message ${type}`;
    
    const bubble = document.createElement('div');
    bubble.className = `message-bubble ${type}`;
    bubble.textContent = message;
    
    messageDiv.appendChild(bubble);
    messagesArea.appendChild(messageDiv);
    
    scrollToBottom(messagesArea);
}

function showTypingIndicator(messagesArea) {
    const typingDiv = document.createElement('div');
    typingDiv.className = 'chat-message assistant typing-indicator';
    typingDiv.innerHTML = `
        <div class="message-bubble assistant">
            <div class="typing-dots">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    `;
    
    messagesArea.appendChild(typingDiv);
    scrollToBottom(messagesArea);
}

function hideTypingIndicator(messagesArea) {
    const typingIndicator = messagesArea.querySelector('.typing-indicator');
    if (typingIndicator) {
        typingIndicator.remove();
    }
}

function scrollToBottom(element) {
    element.scrollTop = element.scrollHeight;
}

/**
 * Responsive Features
 */
function initializeResponsiveFeatures() {
    // Mobile menu handling
    const navbarToggler = document.querySelector('.navbar-toggler');
    const navbarCollapse = document.querySelector('.navbar-collapse');
    
    if (navbarToggler && navbarCollapse) {
        navbarToggler.addEventListener('click', function() {
            navbarCollapse.classList.toggle('show');
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!navbarToggler.contains(e.target) && !navbarCollapse.contains(e.target)) {
                navbarCollapse.classList.remove('show');
            }
        });
    }
    
    // Responsive table handling
    const tables = document.querySelectorAll('.table-responsive table');
    tables.forEach(function(table) {
        if (table.offsetWidth > table.parentElement.offsetWidth) {
            table.parentElement.style.overflowX = 'auto';
        }
    });
    
    // Handle window resize
    window.addEventListener('resize', function() {
        // Recalculate table responsiveness
        tables.forEach(function(table) {
            if (table.offsetWidth > table.parentElement.offsetWidth) {
                table.parentElement.style.overflowX = 'auto';
            } else {
                table.parentElement.style.overflowX = 'visible';
            }
        });
    });
}

/**
 * Accessibility Features
 */
function initializeAccessibilityFeatures() {
    // Skip link functionality
    const skipLink = document.querySelector('.skip-link');
    if (skipLink) {
        skipLink.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.focus();
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    }
    
    // Enhanced focus management
    const focusableElements = document.querySelectorAll(
        'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
    );
    
    focusableElements.forEach(function(element) {
        element.addEventListener('focus', function() {
            this.classList.add('focused');
        });
        
        element.addEventListener('blur', function() {
            this.classList.remove('focused');
        });
    });
    
    // Keyboard navigation for cards
    const clickableCards = document.querySelectorAll('.card[data-href], .semester-card');
    clickableCards.forEach(function(card) {
        card.setAttribute('tabindex', '0');
        card.setAttribute('role', 'button');
        
        card.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.click();
            }
        });
    });
}

/**
 * Loading States Management
 */
function initializeLoadingStates() {
    // Global loading indicator
    const loadingIndicator = createLoadingIndicator();
    
    // Show loading for AJAX requests
    const originalFetch = window.fetch;
    window.fetch = function(...args) {
        showGlobalLoading();
        return originalFetch.apply(this, args)
            .finally(() => {
                hideGlobalLoading();
            });
    };
    
    // Show loading for form submissions
    document.addEventListener('submit', function(e) {
        if (e.target.tagName === 'FORM') {
            showGlobalLoading();
            setTimeout(hideGlobalLoading, 500); // Fallback
        }
    });
}

function createLoadingIndicator() {
    const indicator = document.createElement('div');
    indicator.id = 'global-loading';
    indicator.className = 'position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center';
    indicator.style.cssText = `
        background-color: rgba(255, 255, 255, 0.9);
        z-index: 9999;
        display: none;
    `;
    indicator.innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-primary mb-3" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <div class="fw-semibold text-primary">Loading...</div>
        </div>
    `;
    document.body.appendChild(indicator);
    return indicator;
}

function showGlobalLoading() {
    const indicator = document.getElementById('global-loading');
    if (indicator) {
        indicator.style.display = 'flex';
    }
}

function hideGlobalLoading() {
    const indicator = document.getElementById('global-loading');
    if (indicator) {
        indicator.style.display = 'none';
    }
}

/**
 * Tooltip Initialization
 */
function initializeTooltips() {
    // Initialize Bootstrap tooltips if available
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
    
    // Custom tooltip for elements with title attribute
    const elementsWithTitle = document.querySelectorAll('[title]:not([data-bs-toggle="tooltip"])');
    elementsWithTitle.forEach(function(element) {
        const title = element.getAttribute('title');
        element.removeAttribute('title');
        element.setAttribute('data-tooltip', title);
        
        element.addEventListener('mouseenter', function() {
            showCustomTooltip(this, title);
        });
        
        element.addEventListener('mouseleave', function() {
            hideCustomTooltip();
        });
    });
}

function showCustomTooltip(element, text) {
    const tooltip = document.createElement('div');
    tooltip.className = 'custom-tooltip position-absolute bg-dark text-white px-2 py-1 rounded';
    tooltip.style.cssText = `
        font-size: 0.75rem;
        z-index: 1000;
        pointer-events: none;
        white-space: nowrap;
    `;
    tooltip.textContent = text;
    
    document.body.appendChild(tooltip);
    
    const rect = element.getBoundingClientRect();
    tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
    tooltip.style.top = rect.top - tooltip.offsetHeight - 5 + 'px';
    
    tooltip.id = 'custom-tooltip';
}

function hideCustomTooltip() {
    const tooltip = document.getElementById('custom-tooltip');
    if (tooltip) {
        tooltip.remove();
    }
}

/**
 * Utility Functions
 */
function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    const container = document.querySelector('.container');
    if (container) {
        container.insertBefore(alertDiv, container.firstChild);
        
        // Auto-hide after 5 seconds
        setTimeout(function() {
            if (alertDiv.parentNode) {
                hideAlert(alertDiv);
            }
        }, 5000);
    }
}

// Add CSS for typing indicator and custom styles
const additionalStyles = `
<style>
.typing-dots {
    display: flex;
    align-items: center;
    gap: 4px;
}

.typing-dots span {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background-color: #6c757d;
    animation: typing 1.4s infinite ease-in-out;
}

.typing-dots span:nth-child(1) { animation-delay: -0.32s; }
.typing-dots span:nth-child(2) { animation-delay: -0.16s; }

@keyframes typing {
    0%, 80%, 100% { transform: scale(0.8); opacity: 0.5; }
    40% { transform: scale(1); opacity: 1; }
}

.focused {
    outline: 2px solid var(--primary-color);
    outline-offset: 2px;
}

.file-item {
    transition: all 0.2s ease;
}

.file-item:hover {
    background-color: var(--light-color);
}

.custom-tooltip {
    animation: fadeIn 0.2s ease-in;
}

@media (prefers-reduced-motion: reduce) {
    .typing-dots span {
        animation: none;
    }
    
    .custom-tooltip {
        animation: none;
    }
}
</style>
`;

// Inject additional styles
document.head.insertAdjacentHTML('beforeend', additionalStyles);