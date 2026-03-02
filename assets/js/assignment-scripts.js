/**
 * Assignment Result Page JavaScript
 * Enhanced functionality for assignment management
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize assignment page features
    initializeAssignmentFeatures();
    initializeCopyFunctionality();
    initializePrintFunctionality();
    initializeDownloadFunctionality();
    initializeEditFunctionality();
    initializeWordCount();
    initializeAutoSave();
});

/**
 * Initialize all assignment features
 */
function initializeAssignmentFeatures() {
    // Add line numbers to assignment content
    addLineNumbers();
    
    // Initialize syntax highlighting
    highlightKeywords();
    
    // Add auto-refresh for content
    setupAutoRefresh();
    
    // Add keyboard shortcuts
    setupKeyboardShortcuts();
}

/**
 * Add line numbers to assignment content
 */
function addLineNumbers() {
    const contentElement = document.getElementById('assignmentContent');
    if (!contentElement) return;
    
    const lines = contentElement.textContent.split('\n');
    if (lines.length > 1) {
        const lineNumbers = document.createElement('div');
        lineNumbers.className = 'line-numbers';
        lineNumbers.innerHTML = lines.map((_, i) => `<span>${i + 1}</span>`).join('\n');
        
        const wrapper = document.createElement('div');
        wrapper.className = 'assignment-wrapper';
        wrapper.appendChild(lineNumbers);
        wrapper.appendChild(contentElement.cloneNode(true));
        
        contentElement.parentNode.replaceChild(wrapper, contentElement);
        wrapper.children[1].id = 'assignmentContent';
    }
}

/**
 * Highlight important keywords in assignment
 */
function highlightKeywords() {
    const contentElement = document.getElementById('assignmentContent');
    if (!contentElement) return;
    
    const keywords = [
        'question', 'answer', 'explain', 'calculate', 'describe', 
        'compare', 'analyze', 'evaluate', 'discuss', 'define',
        'example', 'solution', 'problem', 'exercise', 'task'
    ];
    
    let html = contentElement.innerHTML;
    keywords.forEach(keyword => {
        const regex = new RegExp(`\\b(${keyword})\\b`, 'gi');
        html = html.replace(regex, '<span class="keyword">$1</span>');
    });
    contentElement.innerHTML = html;
}

/**
 * Copy content to clipboard
 */
function initializeCopyFunctionality() {
    const copyBtn = document.querySelector('[onclick*="copyToClipboard"]');
    if (copyBtn) {
        copyBtn.removeAttribute('onclick');
        copyBtn.addEventListener('click', copyToClipboard);
    }
}

async function copyToClipboard() {
    const contentElement = document.getElementById('assignmentContent');
    if (!contentElement) return;
    
    try {
        // Get clean text without line numbers or highlights
        const content = contentElement.textContent || contentElement.innerText;
        await navigator.clipboard.writeText(content);
        
        // Show success notification
        showNotification('Assignment content copied to clipboard!', 'success');
        
        // Update button state
        const btn = event.target.closest('button');
        if (btn) {
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fa fa-check"></i> Copied!';
            btn.classList.add('copied');
            
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.classList.remove('copied');
            }, 2000);
        }
    } catch (err) {
        console.error('Failed to copy:', err);
        showNotification('Failed to copy content. Please try again.', 'error');
    }
}

/**
 * Print assignment functionality
 */
function initializePrintFunctionality() {
    const printBtn = document.querySelector('[onclick*="printAssignment"]');
    if (printBtn) {
        printBtn.removeAttribute('onclick');
        printBtn.addEventListener('click', printAssignment);
    }
}

function printAssignment() {
    const contentElement = document.getElementById('assignmentContent');
    if (!contentElement) return;
    
    const content = contentElement.textContent || contentElement.innerText;
    const date = new Date().toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
    
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>UAI Assignment - ${date}</title>
            <style>
                @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap');
                
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }
                
                body {
                    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
                    line-height: 1.6;
                    color: #1e293b;
                    padding: 40px;
                    max-width: 800px;
                    margin: 0 auto;
                }
                
                .header {
                    text-align: center;
                    margin-bottom: 40px;
                    padding-bottom: 20px;
                    border-bottom: 3px solid #3b82f6;
                }
                
                .header h1 {
                    color: #1e40af;
                    font-size: 28px;
                    margin-bottom: 10px;
                    font-weight: 700;
                }
                
                .header .subtitle {
                    color: #64748b;
                    font-size: 14px;
                    margin-bottom: 20px;
                }
                
                .meta-info {
                    display: flex;
                    justify-content: center;
                    gap: 30px;
                    font-size: 14px;
                    color: #475569;
                    margin-bottom: 30px;
                }
                
                .meta-item {
                    display: flex;
                    align-items: center;
                    gap: 8px;
                }
                
                .assignment-content {
                    white-space: pre-wrap;
                    font-size: 14px;
                    line-height: 1.8;
                    background: #f8fafc;
                    padding: 25px;
                    border-radius: 8px;
                    border: 1px solid #e2e8f0;
                }
                
                .footer {
                    margin-top: 40px;
                    text-align: center;
                    font-size: 12px;
                    color: #94a3b8;
                    border-top: 1px solid #e2e8f0;
                    padding-top: 20px;
                }
                
                .keyword {
                    font-weight: 600;
                    color: #1e40af;
                }
                
                @media print {
                    body {
                        padding: 20px;
                    }
                    
                    .assignment-content {
                        page-break-inside: avoid;
                    }
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>University Assignment</h1>
                <div class="subtitle">UAI Academic Portal - Generated Content</div>
                <div class="meta-info">
                    <div class="meta-item">
                        <strong>Date:</strong> ${date}
                    </div>
                    <div class="meta-item">
                        <strong>Generated by:</strong> ${document.querySelector('.user-name')?.textContent || 'Faculty'}
                    </div>
                    <div class="meta-item">
                        <strong>Words:</strong> ${content.split(/\s+/).length}
                    </div>
                </div>
            </div>
            
            <div class="assignment-content">
                ${content.replace(/\n/g, '<br>')}
            </div>
            
            <div class="footer">
                <p>© ${new Date().getFullYear()} University of Artificial Intelligence. All rights reserved.</p>
                <p>This assignment was generated using UAI's AI-powered academic tools.</p>
            </div>
            
            <script>
                window.onload = function() {
                    window.print();
                    setTimeout(() => window.close(), 1000);
                }
            <\/script>
        </body>
        </html>
    `);
    printWindow.document.close();
}

/**
 * Download assignment as PDF
 */
function initializeDownloadFunctionality() {
    const downloadBtn = document.querySelector('[onclick*="downloadAssignment"]');
    if (downloadBtn) {
        downloadBtn.removeAttribute('onclick');
        downloadBtn.addEventListener('click', downloadAssignment);
    }
}

async function downloadAssignment() {
    const contentElement = document.getElementById('assignmentContent');
    if (!contentElement) return;
    
    try {
        // Show loading state
        showNotification('Preparing PDF download...', 'info');
        
        // In a real implementation, this would call a PDF generation API
        // For now, we'll create a downloadable text file
        const content = contentElement.textContent || contentElement.innerText;
        const filename = `UAI-Assignment-${new Date().toISOString().split('T')[0]}.txt`;
        
        const blob = new Blob([content], { type: 'text/plain' });
        const url = URL.createObjectURL(blob);
        
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
        
        showNotification('Assignment downloaded successfully!', 'success');
    } catch (err) {
        console.error('Download failed:', err);
        showNotification('Failed to download assignment.', 'error');
    }
}

/**
 * Edit assignment content
 */
function initializeEditFunctionality() {
    const editBtn = document.querySelector('[onclick*="editAssignment"]');
    if (!editBtn) return;
    
    editBtn.removeAttribute('onclick');
    editBtn.addEventListener('click', enableEditing);
}

function enableEditing() {
    const contentElement = document.getElementById('assignmentContent');
    if (!contentElement) return;
    
    // Check if already in edit mode
    if (contentElement.isContentEditable) {
        saveChanges();
        return;
    }
    
    // Enable editing
    contentElement.contentEditable = true;
    contentElement.classList.add('editing');
    contentElement.focus();
    
    // Update button text
    const btn = event.target.closest('button');
    if (btn) {
        btn.innerHTML = '<i class="fa fa-save"></i> Save Changes';
        btn.classList.add('editing');
    }
    
    // Show editing toolbar
    showEditingToolbar();
    
    // Add save on Ctrl+S
    document.addEventListener('keydown', handleSaveShortcut);
}

function saveChanges() {
    const contentElement = document.getElementById('assignmentContent');
    if (!contentElement) return;
    
    // Disable editing
    contentElement.contentEditable = false;
    contentElement.classList.remove('editing');
    
    // Update button text
    const btn = document.querySelector('.action-btn.editing');
    if (btn) {
        btn.innerHTML = '<i class="fa fa-edit"></i> Edit Content';
        btn.classList.remove('editing');
    }
    
    // Hide toolbar
    hideEditingToolbar();
    
    // Remove event listener
    document.removeEventListener('keydown', handleSaveShortcut);
    
    // Show success message
    showNotification('Changes saved successfully!', 'success');
    
    // In a real app, you would send changes to the server here
    console.log('Updated content:', contentElement.textContent);
}

function handleSaveShortcut(event) {
    if ((event.ctrlKey || event.metaKey) && event.key === 's') {
        event.preventDefault();
        saveChanges();
    }
}

/**
 * Word count and statistics
 */
function initializeWordCount() {
    const contentElement = document.getElementById('assignmentContent');
    if (!contentElement) return;
    
    const updateWordCount = () => {
        const text = contentElement.textContent || contentElement.innerText;
        const wordCount = text.trim().split(/\s+/).length;
        const charCount = text.length;
        const lineCount = text.split('\n').length;
        
        // Update stats display
        const wordCountElement = document.querySelector('.stat-item:first-child strong');
        if (wordCountElement) {
            wordCountElement.textContent = wordCount;
        }
        
        // Estimate reading time (assuming 200 words per minute)
        const readingTime = Math.ceil(wordCount / 200);
        const timeElement = document.querySelector('.stat-item:nth-child(2) strong');
        if (timeElement) {
            timeElement.textContent = `${readingTime} minute${readingTime !== 1 ? 's' : ''}`;
        }
    };
    
    // Initial update
    updateWordCount();
    
    // Update on content changes (for editing mode)
    const observer = new MutationObserver(updateWordCount);
    observer.observe(contentElement, {
        characterData: true,
        childList: true,
        subtree: true
    });
}

/**
 * Auto-save functionality
 */
function initializeAutoSave() {
    let autoSaveTimeout;
    let hasUnsavedChanges = false;
    
    const checkForChanges = () => {
        const contentElement = document.getElementById('assignmentContent');
        if (!contentElement || !contentElement.isContentEditable) return;
        
        hasUnsavedChanges = true;
        
        // Clear previous timeout
        clearTimeout(autoSaveTimeout);
        
        // Set new timeout for auto-save (5 seconds after last change)
        autoSaveTimeout = setTimeout(() => {
            if (hasUnsavedChanges) {
                autoSave();
            }
        }, 5000);
    };
    
    const contentElement = document.getElementById('assignmentContent');
    if (contentElement) {
        contentElement.addEventListener('input', checkForChanges);
    }
}

function autoSave() {
    const contentElement = document.getElementById('assignmentContent');
    if (!contentElement) return;
    
    // Simulate auto-save
    console.log('Auto-saving assignment...');
    showNotification('Auto-saved your changes.', 'info');
    
    // In a real implementation, you would save to localStorage or send to server
    localStorage.setItem('assignment_autosave', contentElement.textContent);
}

/**
 * Setup auto-refresh for real-time updates
 */
function setupAutoRefresh() {
    // Refresh content every 5 minutes to check for updates
    setInterval(async () => {
        try {
            // In a real app, this would fetch updates from the server
            console.log('Checking for assignment updates...');
        } catch (error) {
            console.error('Failed to check for updates:', error);
        }
    }, 300000); // 5 minutes
}

/**
 * Keyboard shortcuts
 */
function setupKeyboardShortcuts() {
    document.addEventListener('keydown', (event) => {
        // Ctrl/Cmd + P to print
        if ((event.ctrlKey || event.metaKey) && event.key === 'p') {
            event.preventDefault();
            printAssignment();
        }
        
        // Ctrl/Cmd + C to copy (when not in input field)
        if ((event.ctrlKey || event.metaKey) && event.key === 'c' && 
            !event.target.matches('input, textarea, [contenteditable="true"]')) {
            copyToClipboard();
        }
        
        // Escape to cancel editing
        if (event.key === 'Escape') {
            const contentElement = document.getElementById('assignmentContent');
            if (contentElement && contentElement.isContentEditable) {
                if (confirm('Discard changes?')) {
                    contentElement.contentEditable = false;
                    contentElement.classList.remove('editing');
                    showNotification('Editing cancelled.', 'info');
                }
            }
        }
    });
}

/**
 * Editing toolbar
 */
function showEditingToolbar() {
    const toolbar = document.createElement('div');
    toolbar.className = 'editing-toolbar';
    toolbar.innerHTML = `
        <div class="toolbar-content">
            <button class="toolbar-btn" onclick="formatText('bold')" title="Bold (Ctrl+B)">
                <i class="fa fa-bold"></i>
            </button>
            <button class="toolbar-btn" onclick="formatText('italic')" title="Italic (Ctrl+I)">
                <i class="fa fa-italic"></i>
            </button>
            <button class="toolbar-btn" onclick="formatText('underline')" title="Underline (Ctrl+U)">
                <i class="fa fa-underline"></i>
            </button>
            <div class="separator"></div>
            <button class="toolbar-btn" onclick="insertBulletList()" title="Bullet List">
                <i class="fa fa-list-ul"></i>
            </button>
            <button class="toolbar-btn" onclick="insertNumberedList()" title="Numbered List">
                <i class="fa fa-list-ol"></i>
            </button>
            <div class="separator"></div>
            <button class="toolbar-btn" onclick="clearFormatting()" title="Clear Formatting">
                <i class="fa fa-eraser"></i>
            </button>
        </div>
    `;
    
    const contentArea = document.querySelector('.content-area');
    if (contentArea) {
        const assignmentCard = contentArea.querySelector('.assignment-card');
        if (assignmentCard) {
            assignmentCard.insertBefore(toolbar, assignmentCard.firstChild);
            
            // Add styles
            const style = document.createElement('style');
            style.textContent = `
                .editing-toolbar {
                    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
                    padding: 10px 20px;
                    border-radius: 8px 8px 0 0;
                    margin: -1px -1px 0 -1px;
                }
                
                .toolbar-content {
                    display: flex;
                    gap: 10px;
                    align-items: center;
                }
                
                .toolbar-btn {
                    background: rgba(255, 255, 255, 0.1);
                    border: none;
                    color: white;
                    width: 36px;
                    height: 36px;
                    border-radius: 6px;
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    transition: all 0.3s ease;
                }
                
                .toolbar-btn:hover {
                    background: rgba(255, 255, 255, 0.2);
                    transform: translateY(-1px);
                }
                
                .separator {
                    width: 1px;
                    height: 20px;
                    background: rgba(255, 255, 255, 0.3);
                    margin: 0 5px;
                }
                
                .editing {
                    outline: 2px solid #3b82f6;
                    outline-offset: -2px;
                    min-height: 200px;
                    padding: 20px;
                }
            `;
            document.head.appendChild(style);
        }
    }
}

function hideEditingToolbar() {
    const toolbar = document.querySelector('.editing-toolbar');
    if (toolbar) {
        toolbar.remove();
    }
}

/**
 * Text formatting functions
 */
function formatText(command) {
    document.execCommand(command, false, null);
}

function insertBulletList() {
    document.execCommand('insertUnorderedList', false, null);
}

function insertNumberedList() {
    document.execCommand('insertOrderedList', false, null);
}

function clearFormatting() {
    document.execCommand('removeFormat', false, null);
}

/**
 * Notification system
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

// Add line number styles
const lineNumberStyles = document.createElement('style');
lineNumberStyles.textContent = `
    .assignment-wrapper {
        display: flex;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        overflow: hidden;
    }
    
    .line-numbers {
        background: #e2e8f0;
        padding: 25px 15px;
        text-align: right;
        font-family: 'JetBrains Mono', 'Courier New', monospace;
        color: #64748b;
        font-size: 14px;
        line-height: 1.8;
        user-select: none;
        min-width: 40px;
    }
    
    .line-numbers span {
        display: block;
    }
    
    .keyword {
        color: #1e40af;
        font-weight: 600;
        background: rgba(59, 130, 246, 0.1);
        padding: 2px 4px;
        border-radius: 4px;
    }
    
    .copied {
        background: linear-gradient(135deg, #059669, #047857) !important;
    }
`;
document.head.appendChild(lineNumberStyles);