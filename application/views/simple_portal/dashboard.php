<style>
/* UAI Brand Colors */
:root {
    --sidebar-width: 230px;
    --topbar-height: 70px;
    --primary-blue: #4A76A8;
    --primary-dark: #1D4486;
    --primary-light: #6B8BC3;
    --success-green: #759B49;
    --light-bg: #eef2f7;
    --white: #ffffff;
    --text-dark: #333333;
    --text-light: #666666;
    --border-color: #e0e0e0;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: "Segoe UI", -apple-system, BlinkMacSystemFont, sans-serif;
}

body {
    background: var(--light-bg);
    color: var(--text-dark);
    min-height: 100vh;
}

.ai-buddy-container {
    min-height: 100vh;
    padding: 30px;
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

.feature-card {
    transition: transform 0.3s, box-shadow 0.3s;
    cursor: pointer;
    border: 1px solid var(--border-color);
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.feature-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.stat-card {
    background: linear-gradient(135deg, var(--primary-blue), var(--primary-light));
    color: white;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
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

.nav-tabs .nav-link {
    border: none;
    color: var(--text-light);
    font-weight: 500;
}

.nav-tabs .nav-link.active {
    background: var(--primary-blue);
    color: var(--white);
    border-radius: 8px 8px 0 0;
}

.list-group-item-action:hover {
    background: #f8f9fa;
    border-color: var(--primary-blue);
}

.badge {
    border-radius: 15px;
    font-weight: 500;
}

.alert {
    border-radius: 10px;
    border: none;
}

/* Scrollbar Styling */
.resource-sidebar::-webkit-scrollbar {
    width: 6px;
}

.resource-sidebar::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.resource-sidebar::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.resource-sidebar::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>

<div class="ai-buddy-container">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card header-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">
                                <i class="fas fa-robot me-2"></i>AI Buddy
                            </h3>
                            <p class="mb-0 mt-2">
                                <?php if ($user_role === 'faculty'): ?>
                                    Your intelligent teaching assistant
                                <?php else: ?>
                                    Your intelligent learning assistant
                                <?php endif; ?>
                            </p>
                        </div>
                        <div>
                            <a href="<?php echo base_url(); ?>" class="back-btn">
                                <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                            </a>
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
                                <div class="list-group-item resource-item" data-resource-id="<?php echo $resource->id; ?>">
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
                                        <button class="btn btn-sm btn-outline-primary w-100 mb-1" 
                                                onclick="startChat(<?php echo $resource->id; ?>)">
                                            <i class="fas fa-comments me-1"></i>Chat
                                        </button>
                                        <?php if ($user_role === 'faculty'): ?>
                                        <button class="btn btn-sm btn-outline-success w-100 mb-1" 
                                                onclick="generateQuiz(<?php echo $resource->id; ?>)">
                                            <i class="fas fa-question-circle me-1"></i>Quiz
                                        </button>
                                        <button class="btn btn-sm btn-outline-info w-100 mb-1" 
                                                onclick="generateQuestionPaper(<?php echo $resource->id; ?>)">
                                            <i class="fas fa-file-alt me-1"></i>Question Paper
                                        </button>
                                        <button class="btn btn-sm btn-outline-warning w-100" 
                                                onclick="generateAssignment(<?php echo $resource->id; ?>)">
                                            <i class="fas fa-tasks me-1"></i>Assignment
                                        </button>
                                        <?php else: ?>
                                        <button class="btn btn-sm btn-outline-success w-100" 
                                                onclick="startStudySession(<?php echo $resource->id; ?>)">
                                            <i class="fas fa-book-reader me-1"></i>Study
                                        </button>
                                        <?php endif; ?>
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
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <!-- Feature Cards -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card feature-card h-100 border-primary" onclick="window.location.href='<?php echo base_url('ai_buddy/chat'); ?>'">
                        <div class="card-body text-center">
                            <i class="fas fa-comments fa-4x text-primary mb-3"></i>
                            <h5>AI Chat</h5>
                            <p class="text-muted">Have conversations about your documents</p>
                            <span class="badge bg-primary"><?php echo count($chat_sessions); ?> sessions</span>
                        </div>
                    </div>
                </div>
                
                <?php if ($user_role === 'faculty'): ?>
                <div class="col-md-3 mb-3">
                    <div class="card feature-card h-100 border-success" onclick="window.location.href='<?php echo base_url('ai_buddy/generate_quiz'); ?>'">
                        <div class="card-body text-center">
                            <i class="fas fa-question-circle fa-4x text-success mb-3"></i>
                            <h5>Quiz Generator</h5>
                            <p class="text-muted">Create quizzes from your content</p>
                            <span class="badge bg-success"><?php echo count($quizzes); ?> quizzes</span>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-3">
                    <div class="card feature-card h-100 border-info" onclick="window.location.href='<?php echo base_url('ai_buddy/generate_question_paper'); ?>'">
                        <div class="card-body text-center">
                            <i class="fas fa-file-alt fa-4x text-info mb-3"></i>
                            <h5>Question Papers</h5>
                            <p class="text-muted">Generate exam question papers</p>
                            <span class="badge bg-info"><?php echo count($question_papers); ?> papers</span>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-3">
                    <div class="card feature-card h-100 border-warning" onclick="window.location.href='<?php echo base_url('ai_buddy/generate_assignment'); ?>'">
                        <div class="card-body text-center">
                            <i class="fas fa-tasks fa-4x text-warning mb-3"></i>
                            <h5>Assignment Generator</h5>
                            <p class="text-muted">Create comprehensive assignments</p>
                            <span class="badge bg-warning text-dark">New!</span>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="col-md-3 mb-3">
                    <div class="card feature-card h-100 border-success">
                        <div class="card-body text-center">
                            <i class="fas fa-book-reader fa-4x text-success mb-3"></i>
                            <h5>Study Assistant</h5>
                            <p class="text-muted">Get help with your learning materials</p>
                            <span class="badge bg-success">Available 24/7</span>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-3">
                    <div class="card feature-card h-100 border-info">
                        <div class="card-body text-center">
                            <i class="fas fa-lightbulb fa-4x text-info mb-3"></i>
                            <h5>Smart Learning</h5>
                            <p class="text-muted">Ask questions and get instant answers</p>
                            <span class="badge bg-info">AI Powered</span>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Usage Statistics -->
            <?php if (!empty($usage_stats)): ?>
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-chart-line me-2"></i>Usage Statistics (Last 30 Days)
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php foreach ($usage_stats as $stat): ?>
                                    <div class="col-md-4 mb-3">
                                        <div class="card stat-card">
                                            <div class="card-body text-center">
                                                <h3><?php echo $stat->count; ?></h3>
                                                <p class="mb-0"><?php echo ucfirst($stat->feature_type); ?> Uses</p>
                                                <small><?php echo number_format($stat->total_tokens); ?> tokens</small>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Recent Activity Tabs -->
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#chats">
                                <i class="fas fa-comments me-1"></i>Recent Chats
                            </a>
                        </li>
                        <?php if ($user_role === 'faculty'): ?>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#quizzes">
                                <i class="fas fa-question-circle me-1"></i>Quizzes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#papers">
                                <i class="fas fa-file-alt me-1"></i>Question Papers
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <!-- Chat Sessions Tab -->
                        <div class="tab-pane fade show active" id="chats">
                            <?php if (!empty($chat_sessions)): ?>
                                <div class="list-group">
                                    <?php foreach (array_slice($chat_sessions, 0, 10) as $session): ?>
                                        <a href="<?php echo base_url('ai_buddy/chat/' . $session->id); ?>" 
                                           class="list-group-item list-group-item-action">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="mb-1"><?php echo htmlspecialchars($session->session_name); ?></h6>
                                                    <small class="text-muted">
                                                        <?php if ($session->resource_title): ?>
                                                            <i class="fas fa-file me-1"></i><?php echo htmlspecialchars($session->resource_title); ?>
                                                        <?php endif; ?>
                                                    </small>
                                                </div>
                                                <small class="text-muted">
                                                    <?php echo date('M d, Y', strtotime($session->updated_at)); ?>
                                                </small>
                                            </div>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-comments fa-3x mb-3"></i>
                                    <p>No chat sessions yet. Start a conversation!</p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Quizzes Tab (Faculty Only) -->
                        <?php if ($user_role === 'faculty'): ?>
                        <div class="tab-pane fade" id="quizzes">
                            <?php if (!empty($quizzes)): ?>
                                <div class="list-group">
                                    <?php foreach (array_slice($quizzes, 0, 10) as $quiz): ?>
                                        <div class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="mb-1"><?php echo htmlspecialchars($quiz->title); ?></h6>
                                                    <small class="text-muted">
                                                        <?php echo $quiz->num_questions; ?> questions • 
                                                        <?php echo ucfirst($quiz->difficulty); ?> difficulty
                                                        <?php if ($quiz->resource_title): ?>
                                                            • <?php echo htmlspecialchars($quiz->resource_title); ?>
                                                        <?php endif; ?>
                                                    </small>
                                                </div>
                                                <div>
                                                    <small class="text-muted d-block">
                                                        <?php echo date('M d, Y', strtotime($quiz->created_at)); ?>
                                                    </small>
                                                    <button class="btn btn-sm btn-primary mt-1" 
                                                            onclick="viewQuiz(<?php echo $quiz->id; ?>)">
                                                        <i class="fas fa-eye"></i> View
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-question-circle fa-3x mb-3"></i>
                                    <p>No quizzes generated yet.</p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Question Papers Tab (Faculty Only) -->
                        <div class="tab-pane fade" id="papers">
                            <?php if (!empty($question_papers)): ?>
                                <div class="list-group">
                                    <?php foreach (array_slice($question_papers, 0, 10) as $paper): ?>
                                        <div class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="mb-1"><?php echo htmlspecialchars($paper->title); ?></h6>
                                                    <small class="text-muted">
                                                        <?php echo $paper->total_marks; ?> marks • 
                                                        <?php echo $paper->duration_minutes; ?> minutes
                                                        <?php if ($paper->subject_name): ?>
                                                            • <?php echo htmlspecialchars($paper->subject_name); ?>
                                                        <?php endif; ?>
                                                    </small>
                                                </div>
                                                <div>
                                                    <small class="text-muted d-block">
                                                        <?php echo date('M d, Y', strtotime($paper->created_at)); ?>
                                                    </small>
                                                    <button class="btn btn-sm btn-info mt-1" 
                                                            onclick="viewPaper(<?php echo $paper->id; ?>)">
                                                        <i class="fas fa-eye"></i> View
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-file-alt fa-3x mb-3"></i>
                                    <p>No question papers generated yet.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function startChat(resourceId) {
    window.location.href = '<?php echo base_url('ai_buddy/chat'); ?>?resource_id=' + resourceId;
}

function generateQuiz(resourceId) {
    window.location.href = '<?php echo base_url('ai_buddy/generate_quiz/'); ?>' + resourceId;
}

function generateQuestionPaper(resourceId) {
    window.location.href = '<?php echo base_url('ai_buddy/generate_question_paper/'); ?>' + resourceId;
}

function generateAssignment(resourceId) {
    window.location.href = '<?php echo base_url('ai_buddy/generate_assignment/'); ?>' + resourceId;
}

function startStudySession(resourceId) {
    // For students, just start a chat session
    window.location.href = '<?php echo base_url('ai_buddy/chat'); ?>?resource_id=' + resourceId;
}

function viewQuiz(quizId) {
    // Implement quiz viewer
    alert('Quiz viewer coming soon! Quiz ID: ' + quizId);
}

function viewPaper(paperId) {
    // Implement paper viewer
    alert('Paper viewer coming soon! Paper ID: ' + paperId);
}

// Highlight selected resource
document.querySelectorAll('.resource-item').forEach(item => {
    item.addEventListener('click', function() {
        document.querySelectorAll('.resource-item').forEach(i => i.classList.remove('active'));
        this.classList.add('active');
    });
});
</script>