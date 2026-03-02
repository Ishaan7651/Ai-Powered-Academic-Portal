/**
 * Faculty Dashboard JavaScript
 * Enhanced functionality for faculty dashboard
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize dashboard features
    initializeDashboardFeatures();
    initializeQuickActions();
    initializeSubjectManagement();
    initializeActivityRefresh();
    initializeDeadlineTracking();
    initializeSearchFunctionality();
    initializeNotifications();
});

/**
 * Initialize all dashboard features
 */
function initializeDashboardFeatures() {
    // Animate stat cards on scroll
    animateStatCards();
    
    // Initialize tooltips
    initializeTooltips();
    
    // Initialize real-time updates
    initializeRealTimeUpdates();
    
    // Initialize dark mode toggle if needed
    initializeThemeToggle();
    
    // Initialize greeting based on time of day
    updateGreeting();
}

/**
 * Animate stat cards when they come into view
 */
function animateStatCards() {
    const statCards = document.querySelectorAll('.stat-card');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '50px'
    });
    
    statCards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'all 0.6s ease-out';
        observer.observe(card);
    });
}

/**
 * Initialize quick actions functionality
 */
function initializeQuickActions() {
    const actionCards = document.querySelectorAll('.action-card');
    
    actionCards.forEach(card => {
        card.addEventListener('click', function(e) {
            // Add click animation
            this.style.transform = 'scale(0.98)';
            setTimeout(() => {
                this.style.transform = 'translateY(-3px)';
            }, 100);
            
            // Track action in analytics (in a real app)
            trackAction(this.querySelector('h4').textContent);
        });
        
        // Add keyboard navigation
        card.setAttribute('tabindex', '0');
        card.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.click();
            }
        });
    });
}

/**
 * Track user actions (for analytics)
 */
function trackAction(actionName) {
    console.log(`Faculty action: ${actionName}`);
    // In a real app, this would send data to your analytics service
    // Example: sendAnalyticsEvent('faculty_action', { action: actionName });
}

/**
 * Initialize subject management
 */
function initializeSubjectManagement() {
    const viewButtons = document.querySelectorAll('[onclick*="viewSubject"]');
    const manageButtons = document.querySelectorAll('[onclick*="manageSubject"]');
    
    viewButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const subjectId = this.getAttribute('onclick').match(/'([^']+)'/)[1];
            viewSubject(subjectId);
        });
    });
    
    manageButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const subjectId = this.getAttribute('onclick').match(/'([^']+)'/)[1];
            manageSubject(subjectId);
        });
    });
    
    // Subject card click handlers
    const subjectCards = document.querySelectorAll('.subject-card');
    subjectCards.forEach(card => {
        card.addEventListener('click', function(e) {
            if (!e.target.closest('button')) {
                const subjectCode = this.querySelector('.subject-code').textContent;
                viewSubjectDetails(subjectCode);
            }
        });
    });
}

/**
 * View subject details
 */
function viewSubject(subjectId) {
    // Show loading state
    showLoading('Loading subject details...');
    
    // In a real app, this would fetch subject details from the server
    setTimeout(() => {
        hideLoading();
        showNotification('Opening subject details...', 'info');
        
        // Mock subject data
        const subjectData = {
            id: subjectId,
            name: 'Artificial Intelligence Fundamentals',
            code: 'UAI-301',
            description: 'Introduction to AI concepts, machine learning, and neural networks.',
            credits: 3,
            semester: 3,
            enrolled: 45,
            schedule: 'Mon/Wed 10:00 AM - 11:30 AM',
            room: 'Room 301, AI Building'
        };
        
        openSubjectModal(subjectData);
    }, 800);
}

/**
 * Manage subject
 */
function manageSubject(subjectId) {
    showLoading('Opening subject management...');
    
    setTimeout(() => {
        hideLoading();
        showNotification('Redirecting to subject management...', 'info');
        
        // In a real app, this would redirect to subject management page
        // window.location.href = `/faculty/subjects/manage/${subjectId}`;
    }, 800);
}

/**
 * View subject details from card click
 */
function viewSubjectDetails(subjectCode) {
    showNotification(`Opening ${subjectCode} dashboard...`, 'info');
    
    // In a real app, this would redirect to the subject dashboard
    // window.location.href = `/faculty/subjects/${subjectCode}`;
}

/**
 * Open subject modal
 */
function openSubjectModal(data) {
    // Create modal
    const modal = document.createElement('div');
    modal.className = 'subject-modal';
    modal.innerHTML = `
        <div class="modal-overlay"></div>
        <div class="modal-content">
            <div class="modal-header">
                <h3>${data.name}</h3>
                <span class="modal-subtitle">${data.code} • ${data.credits} Credits</span>
                <button class="modal-close" onclick="closeModal()">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="modal-section">
                    <h4><i class="fa fa-info-circle"></i> Course Information</h4>
                    <p>${data.description}</p>
                </div>
                
                <div class="modal-grid">
                    <div class="modal-item">
                        <i class="fa fa-users"></i>
                        <div>
                            <strong>Enrolled Students</strong>
                            <span>${data.enrolled} students</span>
                        </div>
                    </div>
                    <div class="modal-item">
                        <i class="fa fa-calendar"></i>
                        <div>
                            <strong>Semester</strong>
                            <span>Semester ${data.semester}</span>
                        </div>
                    </div>
                    <div class="modal-item">
                        <i class="fa fa-clock"></i>
                        <div>
                            <strong>Schedule</strong>
                            <span>${data.schedule}</span>
                        </div>
                    </div>
                    <div class="modal-item">
                        <i class="fa fa-building"></i>
                        <div>
                            <strong>Classroom</strong>
                            <span>${data.room}</span>
                        </div>
                    </div>
                </div>
                
                <div class="modal-section">
                    <h4><i class="fa fa-tasks"></i> Quick Actions</h4>
                    <div class="modal-actions">
                        <button class="btn-primary" onclick="openSubjectResources('${data.id}')">
                            <i class="fa fa-folder-open"></i> View Resources
                        </button>
                        <button class="btn-outline" onclick="openStudentList('${data.id}')">
                            <i class="fa fa-users"></i> Student List
                        </button>
                        <button class="btn-outline" onclick="openAttendance('${data.id}')">
                            <i class="fa fa-clipboard-check"></i> Attendance
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    document.body.style.overflow = 'hidden';
    
    // Add modal styles
    const modalStyles = document.createElement('style');
    modalStyles.textContent = `
        .subject-modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 9999;
            animation: fadeIn 0.3s ease;
        }
        
        .modal-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
        }
        
        .modal-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            border-radius: 16px;
            width: 90%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.4s ease;
        }
        
        .modal-header {
            padding: 24px;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            border-radius: 16px 16px 0 0;
            position: relative;
        }
        
        .modal-header h3 {
            margin: 0 0 8px 0;
            font-size: 20px;
            font-weight: 700;
        }
        
        .modal-subtitle {
            opacity: 0.9;
            font-size: 14px;
        }
        
        .modal-close {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .modal-close:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: rotate(90deg);
        }
        
        .modal-body {
            padding: 24px;
        }
        
        .modal-section {
            margin-bottom: 24px;
        }
        
        .modal-section h4 {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #1e40af;
            font-size: 16px;
            margin-bottom: 12px;
        }
        
        .modal-section p {
            color: #64748b;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 16px;
        }
        
        .modal-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }
        
        .modal-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            background: #f8fafc;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
        
        .modal-item i {
            color: #3b82f6;
            font-size: 18px;
        }
        
        .modal-item div {
            flex: 1;
        }
        
        .modal-item strong {
            display: block;
            font-size: 12px;
            color: #64748b;
            margin-bottom: 4px;
        }
        
        .modal-item span {
            display: block;
            font-size: 14px;
            color: #1e293b;
            font-weight: 600;
        }
        
        .modal-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        
        .modal-actions .btn-primary,
        .modal-actions .btn-outline {
            padding: 10px 16px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            display: flex;
            align-items: center;
            gap: 8px;
            flex: 1;
            min-width: 140px;
            justify-content: center;
        }
        
        .modal-actions .btn-primary {
            background: #3b82f6;
            color: white;
        }
        
        .modal-actions .btn-primary:hover {
            background: #2563eb;
            transform: translateY(-1px);
        }
        
        .modal-actions .btn-outline {
            background: white;
            color: #3b82f6;
            border: 2px solid #3b82f6;
        }
        
        .modal-actions .btn-outline:hover {
            background: #eff6ff;
            border-color: #2563eb;
            transform: translateY(-1px);
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translate(-50%, -40%);
            }
            to {
                opacity: 1;
                transform: translate(-50%, -50%);
            }
        }
        
        @media (max-width: 480px) {
            .modal-grid {
                grid-template-columns: 1fr;
            }
            
            .modal-actions {
                flex-direction: column;
            }
            
            .modal-actions .btn-primary,
            .modal-actions .btn-outline {
                width: 100%;
            }
        }
    `;
    document.head.appendChild(modalStyles);
}

/**
 * Close modal
 */
function closeModal() {
    const modal = document.querySelector('.subject-modal');
    if (modal) {
        modal.style.animation = 'fadeOut 0.3s ease';
        setTimeout(() => {
            modal.remove();
            document.body.style.overflow = '';
        }, 300);
    }
}

/**
 * Open subject resources
 */
function openSubjectResources(subjectId) {
    showNotification('Opening subject resources...', 'info');
    closeModal();
    // In a real app: window.location.href = `/faculty/subjects/${subjectId}/resources`;
}

/**
 * Open student list
 */
function openStudentList(subjectId) {
    showNotification('Opening student list...', 'info');
    closeModal();
    // In a real app: window.location.href = `/faculty/subjects/${subjectId}/students`;
}

/**
 * Open attendance
 */
function openAttendance(subjectId) {
    showNotification('Opening attendance sheet...', 'info');
    closeModal();
    // In a real app: window.location.href = `/faculty/subjects/${subjectId}/attendance`;
}

/**
 * Request subjects from admin
 */
function requestSubjects() {
    showNotification('Sending subject request to admin...', 'info');
    
    // In a real app, this would send a request to the admin
    // Example: fetch('/api/faculty/subjects/request', { method: 'POST' })
    
    setTimeout(() => {
        showNotification('Subject request sent successfully!', 'success');
    }, 1500);
}

/**
 * Contact admin
 */
function contactAdmin() {
    const modal = document.createElement('div');
    modal.className = 'contact-modal';
    modal.innerHTML = `
        <div class="modal-overlay" onclick="closeContactModal()"></div>
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fa fa-user-tie"></i> Contact Administrator</h3>
                <button class="modal-close" onclick="closeContactModal()">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="contact-info">
                    <div class="contact-item">
                        <i class="fa fa-envelope"></i>
                        <div>
                            <strong>Email</strong>
                            <span>admin@uai.edu</span>
                            <button class="copy-btn" onclick="copyToClipboard('admin@uai.edu')">
                                <i class="fa fa-copy"></i> Copy
                            </button>
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="fa fa-phone"></i>
                        <div>
                            <strong>Phone</strong>
                            <span>+1 (555) 123-4567</span>
                            <button class="copy-btn" onclick="copyToClipboard('+15551234567')">
                                <i class="fa fa-copy"></i> Copy
                            </button>
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="fa fa-building"></i>
                        <div>
                            <strong>Office</strong>
                            <span>Room 101, Administration Building</span>
                        </div>
                    </div>
                </div>
                
                <div class="contact-form">
                    <h4>Send Message</h4>
                    <textarea id="messageText" placeholder="Type your message here..." rows="4"></textarea>
                    <button class="btn-primary" onclick="sendMessage()">
                        <i class="fa fa-paper-plane"></i> Send Message
                    </button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    document.body.style.overflow = 'hidden';
}

/**
 * Close contact modal
 */
function closeContactModal() {
    const modal = document.querySelector('.contact-modal');
    if (modal) {
        modal.remove();
        document.body.style.overflow = '';
    }
}

/**
 * Copy text to clipboard
 */
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showNotification('Copied to clipboard!', 'success');
    });
}

/**
 * Send message to admin
 */
function sendMessage() {
    const message = document.getElementById('messageText').value;
    if (!message.trim()) {
        showNotification('Please enter a message', 'error');
        return;
    }
    
    showLoading('Sending message...');
    
    setTimeout(() => {
        hideLoading();
        showNotification('Message sent to administrator!', 'success');
        closeContactModal();
    }, 1500);
}

/**
 * Initialize activity refresh
 */
function initializeActivityRefresh() {
    // Refresh activity every 5 minutes
    setInterval(() => {
        refreshActivity();
    }, 300000);
}

/**
 * Refresh activity data
 */
async function refreshActivity() {
    try {
        // In a real app, this would fetch fresh activity data
        // const response = await fetch('/api/faculty/activity');
        // const data = await response.json();
        // updateActivityList(data);
        
        console.log('Refreshing activity data...');
    } catch (error) {
        console.error('Failed to refresh activity:', error);
    }
}

/**
 * Initialize deadline tracking
 */
function initializeDeadlineTracking() {
    // Check deadlines every minute
    setInterval(() => {
        checkUpcomingDeadlines();
    }, 60000);
    
    // Initial check
    checkUpcomingDeadlines();
}

/**
 * Check upcoming deadlines
 */
function checkUpcomingDeadlines() {
    const deadlineItems = document.querySelectorAll('.deadline-item');
    const now = new Date();
    
    deadlineItems.forEach(item => {
        const timeLeftElement = item.querySelector('.time-left');
        if (timeLeftElement) {
            const timeText = timeLeftElement.textContent;
            if (timeText.includes('hours') && timeText.includes('left')) {
                const hours = parseInt(timeText.match(/\d+/)[0]);
                if (hours <= 24) {
                    // Add urgent class for deadlines within 24 hours
                    item.classList.add('urgent');
                }
            }
        }
    });
}

/**
 * View deadline details
 */
function viewDeadline(deadlineId) {
    showNotification(`Opening deadline ${deadlineId}...`, 'info');
    // In a real app: window.location.href = `/faculty/deadlines/${deadlineId}`;
}

/**
 * Initialize search functionality
 */
function initializeSearchFunctionality() {
    const searchInput = document.querySelector('.search-box input');
    if (!searchInput) return;
    
    searchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        
        // Search in subjects
        const subjectCards = document.querySelectorAll('.subject-card');
        subjectCards.forEach(card => {
            const subjectName = card.querySelector('h4').textContent.toLowerCase();
            const subjectCode = card.querySelector('.subject-code').textContent.toLowerCase();
            
            if (subjectName.includes(searchTerm) || subjectCode.includes(searchTerm)) {
                card.style.display = '';
                card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            } else {
                card.style.display = 'none';
            }
        });
        
        // Search in activity
        const activityItems = document.querySelectorAll('.activity-item');
        activityItems.forEach(item => {
            const activityText = item.textContent.toLowerCase();
            if (activityText.includes(searchTerm)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    });
}

/**
 * Initialize notifications
 */
function initializeNotifications() {
    const notificationButton = document.querySelector('[onclick*="showNotifications"]');
    if (notificationButton) {
        notificationButton.addEventListener('click', showNotifications);
    }
    
    // Check for new notifications every 30 seconds
    setInterval(() => {
        checkForNotifications();
    }, 30000);
}

/**
 * Show notifications
 */
function showNotifications() {
    // In a real app, this would show a notifications dropdown
    showNotification('You have no new notifications', 'info');
}

/**
 * Check for new notifications
 */
function checkForNotifications() {
    // In a real app, this would poll the server for new notifications
    console.log('Checking for notifications...');
}

/**
 * Initialize tooltips
 */
function initializeTooltips() {
    // Add tooltips to action cards
    const actionCards = document.querySelectorAll('.action-card');
    actionCards.forEach(card => {
        const title = card.querySelector('h4').textContent;
        card.setAttribute('title', `Click to open ${title}`);
    });
}

/**
 * Initialize real-time updates
 */
function initializeRealTimeUpdates() {
    // Simulate real-time updates
    setInterval(() => {
        updateLiveStats();
    }, 10000);
}

/**
 * Update live stats
 */
function updateLiveStats() {
    // In a real app, this would fetch live data
    // For now, just log to console
    console.log('Updating live stats...');
}

/**
 * Initialize theme toggle
 */
function initializeThemeToggle() {
    // Check for saved theme preference
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        document.body.classList.add('dark-mode');
    }
}

/**
 * Update greeting based on time of day
 */
function updateGreeting() {
    const hour = new Date().getHours();
    let greeting = 'Good ';
    
    if (hour < 12) {
        greeting += 'Morning';
    } else if (hour < 18) {
        greeting += 'Afternoon';
    } else {
        greeting += 'Evening';
    }
    
    const greetingElement = document.querySelector('.welcome-content h2');
    if (greetingElement) {
        const currentText = greetingElement.textContent;
        if (!currentText.includes(greeting)) {
            greetingElement.textContent = greeting + ', ' + currentText.replace(/^Welcome back, /, '');
        }
    }
}

/**
 * Show loading overlay
 */
function showLoading(message = 'Loading...') {
    let loadingOverlay = document.getElementById('loading-overlay');
    
    if (!loadingOverlay) {
        loadingOverlay = document.createElement('div');
        loadingOverlay.id = 'loading-overlay';
        loadingOverlay.innerHTML = `
            <div class="loading-content">
                <div class="loading-spinner"></div>
                <div class="loading-text">${message}</div>
            </div>
        `;
        document.body.appendChild(loadingOverlay);
        
        // Add loading styles
        const loadingStyles = document.createElement('style');
        loadingStyles.textContent = `
            #loading-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(255, 255, 255, 0.9);
                backdrop-filter: blur(4px);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 9998;
                animation: fadeIn 0.3s ease;
            }
            
            .loading-content {
                text-align: center;
            }
            
            .loading-spinner {
                width: 50px;
                height: 50px;
                border: 4px solid #e2e8f0;
                border-top-color: #3b82f6;
                border-radius: 50%;
                animation: spin 1s linear infinite;
                margin: 0 auto 16px;
            }
            
            .loading-text {
                color: #64748b;
                font-weight: 500;
            }
        `;
        document.head.appendChild(loadingStyles);
    }
    
    loadingOverlay.style.display = 'flex';
}

/**
 * Hide loading overlay
 */
function hideLoading() {
    const loadingOverlay = document.getElementById('loading-overlay');
    if (loadingOverlay) {
        loadingOverlay.style.display = 'none';
    }
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

// Add notification styles (if not already added)
if (!document.querySelector('#notification-styles')) {
    const notificationStyles = document.createElement('style');
    notificationStyles.id = 'notification-styles';
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
        
        /* Urgent deadline styles */
        .deadline-item.urgent {
            border-left: 4px solid #ef4444;
            background: linear-gradient(90deg, rgba(239, 68, 68, 0.05), transparent);
        }
        
        .deadline-item.urgent .deadline-date {
            background: linear-gradient(135deg, #ef4444, #dc2626);
        }
        
        .deadline-item.urgent .status.pending {
            background: #fee2e2;
            color: #dc2626;
        }
    `;
    document.head.appendChild(notificationStyles);
}