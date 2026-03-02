<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">
                                <i class="fas fa-robot me-2"></i>AI-Powered Features
                            </h4>
                            <p class="mb-0 mt-2">Generate question papers, assignments, and more with AI assistance</p>
                        </div>
                        <a href="<?php echo base_url(); ?>" class="btn btn-light">
                            <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Question Paper Generator -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-file-alt me-2"></i>Question Paper Generator
                    </h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Create professional exam question papers with multiple sections, different question types, and customizable formats.</p>
                    
                    <div class="mb-3">
                        <h6 class="text-info">Features:</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success me-2"></i>Multiple Choice Questions</li>
                            <li><i class="fas fa-check text-success me-2"></i>Short Answer Questions</li>
                            <li><i class="fas fa-check text-success me-2"></i>Long Answer Questions</li>
                            <li><i class="fas fa-check text-success me-2"></i>Customizable Sections</li>
                            <li><i class="fas fa-check text-success me-2"></i>Marks Distribution</li>
                        </ul>
                    </div>
                    
                    <div class="d-grid">
                        <a href="<?php echo base_url('ai_buddy/generate_question_paper'); ?>" class="btn btn-info text-white">
                            <i class="fas fa-magic me-2"></i>Generate Question Paper
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assignment Generator -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-tasks me-2"></i>Assignment Generator
                    </h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Create comprehensive assignments with detailed instructions, evaluation criteria, and submission guidelines.</p>
                    
                    <div class="mb-3">
                        <h6 class="text-warning">Assignment Types:</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success me-2"></i>Research Assignments</li>
                            <li><i class="fas fa-check text-success me-2"></i>Essay Assignments</li>
                            <li><i class="fas fa-check text-success me-2"></i>Project Assignments</li>
                            <li><i class="fas fa-check text-success me-2"></i>Case Study Analysis</li>
                            <li><i class="fas fa-check text-success me-2"></i>Presentation Assignments</li>
                        </ul>
                    </div>
                    
                    <div class="d-grid">
                        <a href="<?php echo base_url('ai_buddy/generate_assignment'); ?>" class="btn btn-warning text-dark">
                            <i class="fas fa-magic me-2"></i>Generate Assignment
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Quiz Generator -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-question-circle me-2"></i>Quiz Generator
                    </h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Generate interactive quizzes with multiple choice questions based on your uploaded resources.</p>
                    
                    <div class="mb-3">
                        <h6 class="text-success">Features:</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success me-2"></i>Multiple Choice Questions</li>
                            <li><i class="fas fa-check text-success me-2"></i>Automatic Answer Keys</li>
                            <li><i class="fas fa-check text-success me-2"></i>Difficulty Levels</li>
                            <li><i class="fas fa-check text-success me-2"></i>Customizable Length</li>
                            <li><i class="fas fa-check text-success me-2"></i>Instant Feedback</li>
                        </ul>
                    </div>
                    
                    <div class="d-grid">
                        <a href="<?php echo base_url('ai_buddy/generate_quiz'); ?>" class="btn btn-success">
                            <i class="fas fa-magic me-2"></i>Generate Quiz
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- AI Chat -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-comments me-2"></i>AI Chat Assistant
                    </h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Chat with AI about your uploaded documents. Get explanations, summaries, and answers to your questions.</p>
                    
                    <div class="mb-3">
                        <h6 class="text-secondary">Capabilities:</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success me-2"></i>Document Analysis</li>
                            <li><i class="fas fa-check text-success me-2"></i>Content Explanation</li>
                            <li><i class="fas fa-check text-success me-2"></i>Question Answering</li>
                            <li><i class="fas fa-check text-success me-2"></i>Concept Clarification</li>
                            <li><i class="fas fa-check text-success me-2"></i>Study Assistance</li>
                        </ul>
                    </div>
                    
                    <div class="d-grid">
                        <a href="<?php echo base_url('ai_buddy/chat'); ?>" class="btn btn-secondary">
                            <i class="fas fa-comments me-2"></i>Start AI Chat
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Usage Statistics -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Quick Stats
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="p-3">
                                <i class="fas fa-file-alt fa-2x text-info mb-2"></i>
                                <h4 class="text-info"><?php echo isset($stats['question_papers']) ? $stats['question_papers'] : 0; ?></h4>
                                <p class="mb-0">Question Papers</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3">
                                <i class="fas fa-tasks fa-2x text-warning mb-2"></i>
                                <h4 class="text-warning"><?php echo isset($stats['assignments']) ? $stats['assignments'] : 0; ?></h4>
                                <p class="mb-0">Assignments</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3">
                                <i class="fas fa-question-circle fa-2x text-success mb-2"></i>
                                <h4 class="text-success"><?php echo isset($stats['quizzes']) ? $stats['quizzes'] : 0; ?></h4>
                                <p class="mb-0">Quizzes</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3">
                                <i class="fas fa-comments fa-2x text-secondary mb-2"></i>
                                <h4 class="text-secondary"><?php echo isset($stats['chat_sessions']) ? $stats['chat_sessions'] : 0; ?></h4>
                                <p class="mb-0">Chat Sessions</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Instructions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="alert alert-info">
                <h6><i class="fas fa-info-circle me-2"></i>Getting Started</h6>
                <ol class="mb-0">
                    <li><strong>Upload Resources:</strong> First, upload your course materials (PDFs, documents) through the resource management section.</li>
                    <li><strong>Select AI Feature:</strong> Choose the AI feature you want to use from the cards above.</li>
                    <li><strong>Configure Settings:</strong> Select your resources and configure the generation parameters.</li>
                    <li><strong>Generate Content:</strong> Click the generate button and wait for the AI to create your content.</li>
                    <li><strong>Review & Use:</strong> Review the generated content and use it in your teaching.</li>
                </ol>
            </div>
        </div>
    </div>
</div>