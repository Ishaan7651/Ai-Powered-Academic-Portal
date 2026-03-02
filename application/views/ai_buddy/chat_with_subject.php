<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HawkAI - <?php echo htmlspecialchars($subject->subject_name); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <?php 
    // Load appropriate sidebar CSS based on user role
    if ($user_role === 'student') {
        $this->load->view('simple_portal/components/student_sidebar_css');
    } else {
        $this->load->view('simple_portal/components/faculty_sidebar_css');
    }
    ?>
    
    <style>
        .chat-page-container {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            padding: 20px;
            background: var(--light-bg);
        }

        .subject-header {
            background: linear-gradient(135deg, var(--primary-blue), var(--primary-dark));
            color: white;
            padding: 20px 30px;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .subject-header h2 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }

        .subject-header p {
            margin: 5px 0 0 0;
            opacity: 0.9;
            font-size: 14px;
        }

        .chat-layout {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 20px;
            height: calc(100vh - 200px);
        }

        .resources-panel {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            overflow-y: auto;
        }

        .resources-panel h3 {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--border-color);
        }

        .resource-item {
            padding: 12px;
            margin-bottom: 10px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .resource-item:hover {
            border-color: var(--primary-blue);
            background: #f8f9fa;
            transform: translateX(5px);
        }

        .resource-item.selected {
            background: linear-gradient(135deg, var(--primary-blue), var(--primary-dark));
            color: white;
            border-color: var(--primary-blue);
        }

        .resource-item .resource-title {
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 4px;
        }

        .resource-item .resource-type {
            font-size: 12px;
            opacity: 0.8;
        }

        .chat-panel {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
        }

        .message {
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .message.user {
            justify-content: flex-end;
        }

        .message-content {
            max-width: 70%;
            padding: 15px 20px;
            border-radius: 18px;
            word-wrap: break-word;
            line-height: 1.6;
        }

        .message.user .message-content {
            background: linear-gradient(135deg, var(--primary-blue), #6B8BC3);
            color: white;
            border-bottom-right-radius: 5px;
        }

        .message.assistant .message-content {
            background: #f8f9fa;
            border: 1px solid var(--border-color);
            color: var(--text-dark);
            border-bottom-left-radius: 5px;
        }

        .chat-input-area {
            padding: 20px;
            border-top: 1px solid var(--border-color);
            background: #f8f9fa;
        }

        .input-group {
            display: flex;
            gap: 10px;
        }

        .chat-input {
            flex: 1;
            padding: 12px 20px;
            border: 1px solid var(--border-color);
            border-radius: 25px;
            font-size: 15px;
            outline: none;
            transition: all 0.3s ease;
        }

        .chat-input:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(74, 118, 168, 0.1);
        }

        .send-button {
            padding: 12px 30px;
            background: linear-gradient(135deg, var(--success-green), #8BAD5A);
            color: white;
            border: none;
            border-radius: 25px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .send-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(117, 155, 73, 0.3);
        }

        .send-button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .no-resources {
            text-align: center;
            padding: 40px 20px;
            color: var(--text-light);
        }

        .no-resources i {
            font-size: 48px;
            color: var(--border-color);
            margin-bottom: 15px;
        }

        .welcome-message {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-light);
        }

        .welcome-message i {
            font-size: 64px;
            color: var(--border-color);
            margin-bottom: 20px;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: white;
            text-decoration: none;
            opacity: 0.9;
            transition: opacity 0.3s ease;
        }

        .back-link:hover {
            opacity: 1;
            color: white;
        }

        @media (max-width: 992px) {
            .chat-layout {
                grid-template-columns: 1fr;
                height: auto;
            }

            .resources-panel {
                max-height: 300px;
            }

            .chat-panel {
                height: 500px;
            }
        }
    </style>
</head>
<body>

<?php 
// Load appropriate sidebar based on user role
if ($user_role === 'student') {
    $this->load->view('simple_portal/components/student_sidebar', ['active_page' => 'ai_chat']);
} else {
    $this->load->view('simple_portal/components/faculty_sidebar', ['active_page' => 'ai_chat']);
}
?>

<div class="chat-page-container">
    <div class="subject-header">
        <a href="<?php echo base_url('simple_portal/select_subject_for_chat' . (isset($semester) ? '?semester=' . $semester : '')); ?>" class="back-link">
            <i class="fas fa-arrow-left"></i> Change Subject
        </a>
        <h2><i class="fas fa-book"></i> <?php echo htmlspecialchars($subject->subject_code); ?> - <?php echo htmlspecialchars($subject->subject_name); ?></h2>
        <p><i class="fas fa-comments"></i> AI Chat Assistant</p>
    </div>

    <div class="chat-layout">
        <!-- Resources Panel -->
        <div class="resources-panel">
            <h3><i class="fas fa-folder-open"></i> Resources</h3>
            
            <?php if (empty($resources)): ?>
                <div class="no-resources">
                    <i class="fas fa-inbox"></i>
                    <p>No resources available for this subject yet.</p>
                </div>
            <?php else: ?>
                <?php foreach ($resources as $resource): ?>
                    <div class="resource-item" data-resource-id="<?php echo $resource->id; ?>" 
                         onclick="selectResource(<?php echo $resource->id; ?>, '<?php echo htmlspecialchars(addslashes($resource->title)); ?>')">
                        <div class="resource-title">
                            <i class="fas fa-file-<?php echo $resource->file_type === 'pdf' ? 'pdf' : 'alt'; ?>"></i>
                            <?php echo htmlspecialchars($resource->title); ?>
                        </div>
                        <div class="resource-type"><?php echo strtoupper($resource->file_type); ?></div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Chat Panel -->
        <div class="chat-panel">
            <div class="chat-messages" id="chatMessages">
                <div class="welcome-message">
                    <i class="fas fa-robot"></i>
                    <h3>Welcome to AI Chat!</h3>
                    <p>Select a resource from the left panel to start chatting about it.</p>
                </div>
            </div>

            <div class="chat-input-area">
                <div class="input-group">
                    <input type="text" 
                           class="chat-input" 
                           id="messageInput" 
                           placeholder="Select a resource and type your message..." 
                           disabled>
                    <button class="send-button" id="sendButton" onclick="sendMessage()" disabled>
                        <i class="fas fa-paper-plane"></i> Send
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let selectedResourceId = null;
let sessionId = null;
let currentResourceTitle = '';

function selectResource(resourceId, resourceTitle) {
    // Remove previous selection
    document.querySelectorAll('.resource-item').forEach(item => {
        item.classList.remove('selected');
    });
    
    // Mark as selected
    event.currentTarget.classList.add('selected');
    selectedResourceId = resourceId;
    currentResourceTitle = resourceTitle;
    
    // Reset session ID when switching resources - force new session creation
    sessionId = null;
    
    // Enable input
    document.getElementById('messageInput').disabled = false;
    document.getElementById('messageInput').placeholder = `Ask about ${resourceTitle}...`;
    document.getElementById('sendButton').disabled = false;
    
    // Clear chat and show new welcome message
    const chatMessages = document.getElementById('chatMessages');
    chatMessages.innerHTML = `
        <div class="message assistant">
            <div class="message-content">
                <strong>AI Assistant:</strong><br>
                Hello! I'm ready to help you with questions about <strong>${resourceTitle}</strong>. What would you like to know?
            </div>
        </div>
    `;
}

function sendMessage() {
    const input = document.getElementById('messageInput');
    const message = input.value.trim();
    
    if (!message || !selectedResourceId) return;
    
    // Disable input while sending
    input.disabled = true;
    document.getElementById('sendButton').disabled = true;
    
    // Add user message to chat
    addMessage('user', message);
    input.value = '';
    
    // Send to server
    fetch('<?php echo base_url("simple_portal/send_ai_chat_message"); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `session_id=${sessionId || 0}&message=${encodeURIComponent(message)}&resource_id=${selectedResourceId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.session_id) {
                sessionId = data.session_id;
            }
            addMessage('assistant', data.message);
        } else {
            addMessage('assistant', 'Sorry, I encountered an error: ' + (data.error || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        addMessage('assistant', 'Sorry, I could not process your request. Please try again.');
    })
    .finally(() => {
        // Re-enable input
        input.disabled = false;
        document.getElementById('sendButton').disabled = false;
        input.focus();
    });
}

function addMessage(role, content) {
    const chatMessages = document.getElementById('chatMessages');
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${role}`;
    
    const contentDiv = document.createElement('div');
    contentDiv.className = 'message-content';
    
    if (role === 'assistant') {
        // Convert markdown to HTML for AI responses
        const formattedContent = formatMarkdown(content);
        contentDiv.innerHTML = `<strong>AI Assistant:</strong><br>${formattedContent}`;
    } else {
        contentDiv.textContent = content;
    }
    
    messageDiv.appendChild(contentDiv);
    chatMessages.appendChild(messageDiv);
    
    // Scroll to bottom
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

// Simple markdown formatter
function formatMarkdown(text) {
    // Convert **bold** to <strong>
    text = text.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
    
    // Convert *italic* to <em>
    text = text.replace(/\*(.+?)\*/g, '<em>$1</em>');
    
    // Convert `code` to <code>
    text = text.replace(/`(.+?)`/g, '<code style="background: #f0f0f0; padding: 2px 6px; border-radius: 3px; font-family: monospace;">$1</code>');
    
    // Convert ### Heading to <h3>
    text = text.replace(/^### (.+)$/gm, '<h3 style="margin: 10px 0 5px 0; font-size: 16px;">$1</h3>');
    
    // Convert ## Heading to <h2>
    text = text.replace(/^## (.+)$/gm, '<h2 style="margin: 12px 0 6px 0; font-size: 18px;">$1</h2>');
    
    // Convert # Heading to <h1>
    text = text.replace(/^# (.+)$/gm, '<h1 style="margin: 15px 0 8px 0; font-size: 20px;">$1</h1>');
    
    // Convert bullet points (- item or * item)
    text = text.replace(/^[\*\-] (.+)$/gm, '<li style="margin-left: 20px;">$1</li>');
    
    // Wrap consecutive <li> items in <ul>
    text = text.replace(/(<li[^>]*>.*<\/li>\s*)+/g, '<ul style="margin: 5px 0; padding-left: 20px;">$&</ul>');
    
    // Convert numbered lists (1. item)
    text = text.replace(/^\d+\. (.+)$/gm, '<li style="margin-left: 20px;">$1</li>');
    
    // Convert line breaks to <br>
    text = text.replace(/\n/g, '<br>');
    
    return text;
}

// Allow Enter key to send message
document.getElementById('messageInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
    }
});
</script>

</body>
</html>
