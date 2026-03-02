<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Chat - SLAi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <?php 
    if (isset($user_role) && $user_role === 'faculty') {
        $this->load->view('simple_portal/components/faculty_sidebar_css');
    } else {
        $this->load->view('simple_portal/components/student_sidebar_css');
    }
    ?>
<style>
/* AI Chat Container - Adjusted for Sidebar */
.ai-chat-container {
    min-height: 100vh;
    padding: 30px;
    margin-left: var(--sidebar-width);
}

.resource-sidebar {
    background: var(--white);
    border: 1px solid var(--border-color);
    border-radius: 10px;
    height: 80vh;
    overflow-y: auto;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.resource-item {
    cursor: pointer;
    transition: all 0.3s ease;
    border-bottom: 1px solid var(--border-color);
}

.resource-item:hover {
    background: #f8f9fa;
    transform: translateX(5px);
}

.resource-item.active {
    background: var(--primary-blue);
    color: white;
}

.chat-container {
    height: 80vh;
    display: flex;
    flex-direction: column;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    border: none;
    background: var(--white);
}

.chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
    background: var(--white);
}

.message {
    margin-bottom: 20px;
    display: flex;
    align-items: flex-start;
    animation: fadeIn 0.3s ease-in;
}

.message.user {
    justify-content: flex-end;
}

.message-content {
    max-width: 70%;
    padding: 15px 20px;
    border-radius: 18px;
    word-wrap: break-word;
    line-height: 1.5;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.message.user .message-content {
    background: linear-gradient(135deg, var(--primary-blue), var(--primary-light));
    color: white;
    border-bottom-right-radius: 5px;
}

.message.assistant .message-content {
    background: #f8f9fa;
    border: 1px solid var(--border-color);
    color: var(--text-dark);
    border-bottom-left-radius: 5px;
}

.message-avatar {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.message.user .message-avatar {
    background: linear-gradient(135deg, var(--primary-blue), var(--primary-light));
    color: white;
}

.message.assistant .message-avatar {
    background: linear-gradient(135deg, var(--success-green), #8fb85d);
    color: white;
}

.chat-input-area {
    padding: 20px;
    background: var(--white);
    border-top: 1px solid var(--border-color);
}

.typing-indicator {
    display: none;
    padding: 15px 20px;
    background: #f8f9fa;
    border-radius: 18px;
    width: fit-content;
    border-bottom-left-radius: 5px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.typing-indicator span {
    height: 8px;
    width: 8px;
    background: var(--success-green);
    border-radius: 50%;
    display: inline-block;
    margin: 0 3px;
    animation: typing 1.4s infinite;
}

.typing-indicator span:nth-child(2) {
    animation-delay: 0.2s;
}

.typing-indicator span:nth-child(3) {
    animation-delay: 0.4s;
}

@keyframes typing {
    0%, 60%, 100% { transform: translateY(0); opacity: 0.4; }
    30% { transform: translateY(-8px); opacity: 1; }
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.header-card {
    background: linear-gradient(135deg, var(--primary-dark), var(--primary-light));
    color: white;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    border: none;
}

.back-btn {
    background: var(--white);
    color: var(--primary-blue);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 10px 20px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.back-btn:hover {
    background: var(--primary-blue);
    color: var(--white);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(74, 118, 168, 0.2);
}

.btn-primary {
    background: var(--primary-blue);
    border-color: var(--primary-blue);
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background: var(--primary-dark);
    border-color: var(--primary-dark);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(74, 118, 168, 0.2);
}

.form-control {
    border-radius: 25px;
    padding: 12px 20px;
    border: 2px solid var(--border-color);
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: var(--primary-blue);
    box-shadow: 0 0 0 0.2rem rgba(74, 118, 168, 0.25);
}

.modal-content {
    border-radius: 15px;
    border: none;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.message-time {
    font-size: 11px;
    opacity: 0.7;
    margin-top: 5px;
}

/* Markdown content styling */
.markdown-content h1, .markdown-content h2, .markdown-content h3,
.markdown-content h4, .markdown-content h5, .markdown-content h6 {
    margin-top: 16px;
    margin-bottom: 8px;
    font-weight: 600;
    line-height: 1.25;
}

.markdown-content h1 { font-size: 1.5em; border-bottom: 1px solid #e0e0e0; padding-bottom: 8px; }
.markdown-content h2 { font-size: 1.3em; }
.markdown-content h3 { font-size: 1.15em; }

.markdown-content p {
    margin-bottom: 12px;
    line-height: 1.6;
}

.markdown-content ul, .markdown-content ol {
    margin-left: 20px;
    margin-bottom: 12px;
}

.markdown-content li {
    margin-bottom: 6px;
    line-height: 1.5;
}

.markdown-content strong {
    font-weight: 700;
    color: var(--text-dark);
}

.markdown-content em {
    font-style: italic;
}

.markdown-content code {
    background: #f5f5f5;
    padding: 2px 6px;
    border-radius: 3px;
    font-family: 'Courier New', monospace;
    font-size: 0.9em;
}

.markdown-content pre {
    background: #f5f5f5;
    padding: 12px;
    border-radius: 6px;
    overflow-x: auto;
    margin-bottom: 12px;
}

.markdown-content pre code {
    background: none;
    padding: 0;
}

.markdown-content blockquote {
    border-left: 4px solid var(--primary-blue);
    padding-left: 16px;
    margin: 12px 0;
    color: #666;
    font-style: italic;
}

.markdown-content hr {
    border: none;
    border-top: 1px solid #e0e0e0;
    margin: 16px 0;
}

.markdown-content a {
    color: var(--primary-blue);
    text-decoration: none;
}

.markdown-content a:hover {
    text-decoration: underline;
}

.markdown-content table {
    border-collapse: collapse;
    width: 100%;
    margin-bottom: 12px;
}

.markdown-content table th,
.markdown-content table td {
    border: 1px solid #e0e0e0;
    padding: 8px;
    text-align: left;
}

.markdown-content table th {
    background: #f5f5f5;
    font-weight: 600;
}

.chat-stats {
    background: linear-gradient(135deg, var(--primary-blue), var(--primary-light));
    color: white;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 20px;
}

.session-info {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 20px;
    border-left: 4px solid var(--primary-blue);
}

/* Scrollbar Styling */
.resource-sidebar::-webkit-scrollbar,
.chat-messages::-webkit-scrollbar {
    width: 6px;
}

.resource-sidebar::-webkit-scrollbar-track,
.chat-messages::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.resource-sidebar::-webkit-scrollbar-thumb,
.chat-messages::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.resource-sidebar::-webkit-scrollbar-thumb:hover,
.chat-messages::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

.input-group {
    border-radius: 25px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.input-group .form-control {
    border-radius: 25px 0 0 25px;
    border-right: none;
}

.input-group .btn {
    border-radius: 0 25px 25px 0;
}
</style>
</head>
<body>

<div class="portal-container" style="display: flex;">
    <!-- Sidebar -->
    <!-- Sidebar -->
    <?php 
    if (isset($user_role) && $user_role === 'faculty') {
        $this->load->view('simple_portal/components/faculty_sidebar', ['active_page' => 'ai_chat']);
    } else {
        $this->load->view('simple_portal/components/student_sidebar', ['active_page' => 'ai_chat']);
    }
    ?>

    <!-- Main Content Wrapper -->
    <div style="flex: 1; background: var(--light-bg);">
        <div class="ai-chat-container">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card header-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">
                                <i class="fas fa-comments me-2"></i>AI Chat
                            </h3>
                            <p class="mb-0 mt-2">
                                <?php if (isset($session) && $session->resource_title): ?>
                                    <i class="fas fa-file me-1"></i>Chatting about: <?php echo htmlspecialchars($session->resource_title); ?>
                                <?php else: ?>
                                    Your intelligent learning assistant
                                <?php endif; ?>
                            </p>
                        </div>
                        <div>
                        <!-- Back button removed as sidebar provides navigation -->
                        <div>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $this->session->flashdata('success'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $this->session->flashdata('error'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Left Sidebar - Resources -->
        <div class="col-md-3">
            <div class="card resource-sidebar">
                <div class="card-header" style="background: var(--primary-blue); color: white; border-radius: 10px 10px 0 0;">
                    <h6 class="mb-0">
                        <i class="fas fa-folder me-2"></i>Available Resources
                    </h6>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($resources)): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($resources as $resource): ?>
                                <div class="list-group-item resource-item <?php echo (isset($session) && $session->resource_id == $resource->id) ? 'active' : ''; ?>" 
                                     data-resource-id="<?php echo $resource->id; ?>">
                                    <div class="d-flex align-items-start">
                                        <div class="me-2">
                                            <?php if ($resource->file_type === 'pdf'): ?>
                                                <i class="fas fa-file-pdf text-danger fa-2x"></i>
                                            <?php elseif (in_array($resource->file_type, ['doc', 'docx'])): ?>
                                                <i class="fas fa-file-word text-primary fa-2x"></i>
                                            <?php else: ?>
                                                <i class="fas fa-file text-secondary fa-2x"></i>
                                            <?php endif; ?>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1"><?php echo htmlspecialchars($resource->title); ?></h6>
                                            <small class="text-muted">
                                                <?php echo htmlspecialchars($resource->subject_name); ?><br>
                                                <?php echo number_format($resource->file_size / 1024, 2); ?> KB
                                            </small>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-outline-primary w-100" 
                                                onclick="startNewChatWithResource(<?php echo $resource->id; ?>)">
                                            <i class="fas fa-comments me-1"></i>Chat with this
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-folder-open fa-3x mb-3"></i>
                            <p>No resources available yet.</p>
                            <small>Resources will appear here when faculty upload them.</small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Session Info -->
            <?php if (isset($session)): ?>
            <div class="session-info mt-3">
                <h6><i class="fas fa-info-circle me-2"></i>Session Info</h6>
                <p class="mb-1"><strong>Name:</strong> <?php echo htmlspecialchars($session->session_name); ?></p>
                <p class="mb-1"><strong>Started:</strong> <?php echo date('M d, Y g:i A', strtotime($session->created_at)); ?></p>
                <p class="mb-0"><strong>Messages:</strong> <?php echo count($messages); ?></p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Main Chat Area -->
        <div class="col-md-9">
            <?php if (isset($session)): ?>
            <!-- Chat Stats -->
            <div class="chat-stats">
                <div class="row text-center">
                    <div class="col-4">
                        <h4><?php echo count($messages); ?></h4>
                        <small>Messages</small>
                    </div>
                    <div class="col-4">
                        <h4><?php echo isset($session->resource_title) ? 1 : 0; ?></h4>
                        <small>Resources</small>
                    </div>
                    <div class="col-4">
                        <h4><?php echo date('g:i A', strtotime($session->updated_at)); ?></h4>
                        <small>Last Active</small>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Chat Interface -->
            <div class="card chat-container">
                <!-- Messages Area -->
                <div class="chat-messages" id="chatMessages">
                    <?php if (!empty($messages)): ?>
                        <?php foreach ($messages as $message): ?>
                            <div class="message <?php echo $message->role; ?>">
                                <?php if ($message->role === 'assistant'): ?>
                                    <div class="message-avatar">
                                        <i class="fas fa-robot"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="message-content markdown-content">
                                    <?php 
                                    // For assistant messages, we'll let JavaScript handle markdown rendering
                                    echo nl2br(htmlspecialchars($message->message)); 
                                    ?>
                                    <div class="message-time">
                                        <small>
                                            <?php echo date('g:i A', strtotime($message->created_at)); ?>
                                        </small>
                                    </div>
                                </div>
                                
                                <?php if ($message->role === 'user'): ?>
                                    <div class="message-avatar">
                                        <i class="fas fa-user"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-comments fa-4x mb-3" style="color: var(--primary-light);"></i>
                            <h5>Start a conversation</h5>
                            <p>Ask questions about your document or get help with your content.</p>
                            <?php if (!isset($session)): ?>
                                <p class="text-muted mt-3">Select a resource from the left sidebar to start.</p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Typing Indicator -->
                    <div class="message assistant" id="typingIndicator">
                        <div class="message-avatar">
                            <i class="fas fa-robot"></i>
                        </div>
                        <div class="typing-indicator">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </div>
                </div>

                <!-- Input Area -->
                <?php if (isset($session)): ?>
                <div class="chat-input-area">
                    <form id="chatForm" onsubmit="sendMessage(event)">
                        <div class="input-group">
                            <input type="text" 
                                   class="form-control" 
                                   id="messageInput" 
                                   placeholder="Type your message..." 
                                   required
                                   autocomplete="off">
                            <button class="btn btn-primary" type="submit" id="sendButton">
                                <i class="fas fa-paper-plane me-1"></i>Send
                            </button>
                        </div>
                    </form>
                    <div class="mt-3 text-center">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            <?php if (isset($session) && $session->resource_title): ?>
                                Ask specific questions about "<?php echo htmlspecialchars($session->resource_title); ?>" for better responses
                            <?php else: ?>
                                Tip: Select a resource from the sidebar for document-specific assistance
                            <?php endif; ?>
                        </small>
                    </div>
                </div>
                <?php else: ?>
                <div class="chat-input-area text-center">
                </div>
                <?php endif; ?>
    </div>
</div>
    </div>
</div>


<!-- New Session Modal Removed : Session starts by selecting a resource -->

<script>
const sessionId = <?php echo isset($session) ? $session->id : 'null'; ?>;
const chatMessages = document.getElementById('chatMessages');
const messageInput = document.getElementById('messageInput');
const sendButton = document.getElementById('sendButton');
const typingIndicator = document.getElementById('typingIndicator');

function sendMessage(event) {
    event.preventDefault();
    
    if (!sessionId) {
        alert('Please create a session first');
        return;
    }
    
    const message = messageInput.value.trim();
    if (!message) return;
    
    // Disable input
    messageInput.disabled = true;
    sendButton.disabled = true;
    sendButton.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Sending...';
    
    // Add user message to UI
    addMessageToUI('user', message);
    messageInput.value = '';
    
    // Show typing indicator
    typingIndicator.style.display = 'flex';
    scrollToBottom();
    
    // Send to server
    fetch('<?php echo base_url('simple_portal/send_ai_chat_message'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            session_id: sessionId,
            message: message
        })
    })
    .then(response => response.json())
    .then(data => {
        // Hide typing indicator
        typingIndicator.style.display = 'none';
        
        if (data.success) {
            // Add AI response to UI
            addMessageToUI('assistant', data.message);
        } else {
            // Show error message in chat
            addMessageToUI('assistant', 'Sorry, I encountered an error: ' + (data.error || 'Failed to get response'));
        }
        
        // Re-enable input
        messageInput.disabled = false;
        sendButton.disabled = false;
        sendButton.innerHTML = '<i class="fas fa-paper-plane me-1"></i>Send';
        messageInput.focus();
    })
    .catch(error => {
        console.error('Error:', error);
        typingIndicator.style.display = 'none';
        addMessageToUI('assistant', 'Sorry, there was a connection error. Please try again.');
        messageInput.disabled = false;
        sendButton.disabled = false;
        sendButton.innerHTML = '<i class="fas fa-paper-plane me-1"></i>Send';
    });
}

function addMessageToUI(role, content) {
    const messageDiv = document.createElement('div');
    messageDiv.className = 'message ' + role;
    
    const now = new Date();
    const timeStr = now.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
    
    // Parse markdown for assistant messages
    let formattedContent = content;
    if (role === 'assistant' && typeof marked !== 'undefined') {
        formattedContent = marked.parse(content);
    } else {
        formattedContent = content.replace(/\n/g, '<br>');
    }
    
    if (role === 'assistant') {
        messageDiv.innerHTML = `
            <div class="message-avatar">
                <i class="fas fa-robot"></i>
            </div>
            <div class="message-content markdown-content">
                ${formattedContent}
                <div class="message-time">
                    <small>${timeStr}</small>
                </div>
            </div>
        `;
    } else {
        messageDiv.innerHTML = `
            <div class="message-content">
                ${content.replace(/\n/g, '<br>')}
                <div class="message-time">
                    <small>${timeStr}</small>
                </div>
            </div>
            <div class="message-avatar">
                <i class="fas fa-user"></i>
            </div>
        `;
    }
    
    // Insert before typing indicator
    chatMessages.insertBefore(messageDiv, typingIndicator);
    scrollToBottom();
}

function scrollToBottom() {
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

function startNewChatWithResource(resourceId) {
    // Create form and submit
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?php echo base_url('simple_portal/create_ai_chat_session'); ?>';
    
    const resourceInput = document.createElement('input');
    resourceInput.type = 'hidden';
    resourceInput.name = 'resource_id';
    resourceInput.value = resourceId;
    
    const sessionNameInput = document.createElement('input');
    sessionNameInput.type = 'hidden';
    sessionNameInput.name = 'session_name';
    sessionNameInput.value = 'Chat Session - ' + new Date().toLocaleString();
    
    form.appendChild(resourceInput);
    form.appendChild(sessionNameInput);
    document.body.appendChild(form);
    form.submit();
}

// Auto-scroll on load
document.addEventListener('DOMContentLoaded', function() {
    // Render markdown for existing assistant messages
    if (typeof marked !== 'undefined') {
        document.querySelectorAll('.message.assistant .markdown-content').forEach(function(element) {
            // Get the text content (skip the time element)
            const timeElement = element.querySelector('.message-time');
            const timeHTML = timeElement ? timeElement.outerHTML : '';
            
            // Get text without the time element
            let textContent = '';
            element.childNodes.forEach(function(node) {
                if (node.nodeType === Node.TEXT_NODE || (node.nodeType === Node.ELEMENT_NODE && !node.classList.contains('message-time'))) {
                    textContent += node.textContent || node.innerText || '';
                }
            });
            
            // Parse markdown and add back the time
            if (textContent.trim()) {
                element.innerHTML = marked.parse(textContent.trim()) + timeHTML;
            }
        });
    }
    
    scrollToBottom();
    if (messageInput) {
        messageInput.focus();
    }
});

// Handle Enter key
if (messageInput) {
    messageInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage(e);
        }
    });
}

// Highlight selected resource
document.querySelectorAll('.resource-item').forEach(item => {
    item.addEventListener('click', function() {
        document.querySelectorAll('.resource-item').forEach(i => i.classList.remove('active'));
        this.classList.add('active');
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script>
// Configure marked for better rendering
marked.setOptions({
    breaks: true,
    gfm: true,
    headerIds: false,
    mangle: false
});
</script>
</body>
</html>