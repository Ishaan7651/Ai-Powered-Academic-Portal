
<?php
// Helper function for formatting file size
if (!function_exists('formatFileSize')) {
    function formatFileSize($bytes) {
        if ($bytes == 0) return '0 Bytes';
        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes) / log($k));
        return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment Generator - AI Powered Academic Hub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <?php $this->load->view('simple_portal/components/faculty_sidebar_css'); ?>
    
    <style>
        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Topbar */
        .topbar {
            background: var(--white);
            padding: 0 35px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--border-color);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .page-title {
            font-size: 22px;
            font-weight: 700;
            color: var(--text-dark);
            position: relative;
            padding-left: 15px;
        }

        .page-title::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 22px;
            background: linear-gradient(to bottom, var(--primary-blue), var(--primary-light));
            border-radius: 2px;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 10px 16px;
            border-radius: 12px;
            background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
            cursor: pointer;
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }

        .user-profile:hover {
            background: linear-gradient(135deg, #e2e8f0, #cbd5e1);
            border-color: #cbd5e1;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .user-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 16px;
            box-shadow: 0 3px 8px rgba(102, 126, 234, 0.4);
        }

        .user-info {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-weight: 700;
            font-size: 15px;
            color: var(--text-dark);
        }

        .user-role {
            font-size: 12px;
            color: var(--text-light);
            background: rgba(99, 102, 241, 0.1);
            padding: 2px 8px;
            border-radius: 12px;
            display: inline-block;
            font-weight: 600;
        }

        /* Topbar - Premium Design */
        .topbar {
            height: var(--topbar-height);
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            padding: 0 2.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            box-shadow: var(--shadow-md);
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .page-info {
            display: flex;
            flex-direction: column;
            gap: 0.375rem;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--gray-900);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            letter-spacing: -0.025em;
        }

        .page-title i {
            color: var(--primary-blue);
            background: rgba(59, 130, 246, 0.1);
            padding: 0.5rem;
            border-radius: 10px;
            font-size: 1.25rem;
        }

        .breadcrumb {
            font-size: 0.875rem;
            color: var(--gray-500);
            font-weight: 500;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        /* Search Bar */
        .search-container {
            position: relative;
            width: 360px;
        }

        .search-bar {
            width: 100%;
            padding: 0.875rem 1.25rem 0.875rem 3.25rem;
            border: 2px solid var(--gray-200);
            border-radius: 14px;
            font-size: 0.9375rem;
            color: var(--gray-800);
            background: var(--white);
            transition: all var(--transition-base);
            box-shadow: var(--shadow-sm);
        }

        .search-bar:focus {
            outline: none;
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15), var(--shadow-md);
        }

        .search-icon {
            position: absolute;
            left: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-400);
            font-size: 1.125rem;
        }

        /* Notification Bell */
        .notification-bell {
            position: relative;
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--white);
            color: var(--gray-600);
            cursor: pointer;
            transition: all var(--transition-base);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--gray-200);
        }

        .notification-bell:hover {
            background: var(--gray-50);
            color: var(--gray-700);
            transform: translateY(-2px);
        }

        .notification-count {
            position: absolute;
            top: -6px;
            right: -6px;
            background: linear-gradient(135deg, var(--error), #dc2626);
            color: white;
            font-size: 0.75rem;
            font-weight: 800;
            min-width: 22px;
            height: 22px;
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 6px;
            border: 3px solid var(--white);
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.4);
        }

        /* User Profile */
        .user-profile {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem 1rem 0.75rem 1.25rem;
            border-radius: 16px;
            background: var(--white);
            cursor: pointer;
            transition: all var(--transition-base);
            border: 1px solid var(--gray-200);
            min-width: 220px;
            box-shadow: var(--shadow-sm);
        }

        .user-profile:hover {
            background: var(--gray-50);
            border-color: var(--gray-300);
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .user-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: var(--gradient-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 800;
            font-size: 1.125rem;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
            flex-shrink: 0;
            border: 3px solid var(--white);
        }

        .user-info {
            flex: 1;
            min-width: 0;
        }

        .user-name {
            font-weight: 700;
            font-size: 0.9375rem;
            color: var(--gray-900);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-role {
            font-size: 0.8125rem;
            color: var(--gray-600);
            background: rgba(37, 99, 235, 0.1);
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            display: inline-block;
            font-weight: 700;
            margin-top: 0.25rem;
        }

        .user-dropdown {
            color: var(--gray-400);
            font-size: 0.875rem;
            transition: transform var(--transition-fast);
        }

        /* Content Area */
        .content-area {
            flex: 1;
            padding: 2.5rem;
            overflow-y: auto;
            background: transparent;
        }

        .content-grid {
            display: grid;
            grid-template-columns: 400px 1fr;
            gap: 30px;
            align-items: start;
        }

        .config-panel {
            background: var(--white);
            border-radius: 20px;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--gray-200);
            overflow: hidden;
        }

        .config-header {
            background: var(--gradient-primary);
            color: white;
            padding: 20px;
            font-weight: 700;
            font-size: 16px;
        }

        .config-body {
            padding: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: var(--gray-800);
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--gray-200);
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: var(--white);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(31, 94, 168, 0.1);
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-success {
            background: var(--gradient-success);
            color: white;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.4);
        }
        
        .preview-panel {
            background: var(--white);
            border-radius: 20px;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--gray-200);
            overflow: hidden;
            min-height: 600px;
        }

        .preview-header {
            background: var(--gray-800);
            color: white;
            padding: 20px;
            font-weight: 600;
            font-size: 16px;
        }

        .page-header {
            background: linear-gradient(135deg, var(--success-green), #6ba832);
            color: Black !important;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 8px 30px rgba(120, 184, 63, 0.2);
        }

        .page-header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .page-header p {
            font-size: 16px;
            opacity: 0.9;
            margin: 0;
        }

        .content-grid {
            display: grid;
            grid-template-columns: 400px 1fr;
            gap: 30px;
            align-items: start;
        }

        .config-panel {
            background: var(--white);
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid var(--border-color);
            overflow: hidden;
        }

        .config-header {
            background: linear-gradient(135deg, var(--primary-blue), var(--primary-light));
            color: white;
            padding: 20px;
            font-weight: 600;
            font-size: 16px;
        }

        .config-body {
            padding: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: var(--white);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(31, 94, 168, 0.1);
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-blue), var(--primary-light));
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(31, 94, 168, 0.3);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success-green), #6ba832);
            color: white;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(120, 184, 63, 0.3);
        }

        .btn-publish {
            background: linear-gradient(135deg, var(--purple), #7c3aed);
            color: white;
        }

        .btn-publish:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(139, 92, 246, 0.3);
        }

        .preview-panel {
            background: var(--white);
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid var(--border-color);
            overflow: hidden;
        }

        .preview-header {
            background: linear-gradient(135deg, var(--text-dark), #334155);
            color: white;
            padding: 20px;
            font-weight: 600;
            font-size: 16px;
        }

        .preview-body {
            padding: 25px;
            min-height: 400px;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-light);
        }

        .empty-state i {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .empty-state h3 {
            font-size: 20px;
            margin-bottom: 10px;
            color: var(--text-dark);
        }

        .alert {
            padding: 16px;
            border-radius: 8px;
            margin-top: 20px;
            border-left: 4px solid;
        }

        .alert-info {
            background: #e0f2fe;
            border-color: var(--primary-blue);
            color: #0277bd;
        }

        .alert-success {
            background: #e8f5e8;
            border-color: var(--success-green);
            color: #2e7d32;
        }

        .quiz-content {
            background: var(--white);
            padding: 20px;
            border-radius: 10px;
            border: 1px solid var(--border-color);
        }

        .question-card {
            background: #f8fafc;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .question-header {
            color: var(--success-green);
            font-weight: 600;
            margin-bottom: 10px;
        }

        .question-text {
            font-weight: 500;
            margin-bottom: 15px;
            line-height: 1.6;
        }

        .options {
            margin-bottom: 15px;
        }

        .option-item {
            display: flex;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .option-item:last-child {
            border-bottom: none;
        }

        .option-radio {
            width: 16px;
            height: 16px;
            border: 2px solid var(--border-color);
            border-radius: 50%;
            margin-right: 10px;
            flex-shrink: 0;
        }

        .correct-answer {
            background: #e8f5e8;
            color: var(--success-green);
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }

        .action-buttons {
            display: flex;
            gap: 12px;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: var(--white);
            border-radius: 15px;
            padding: 30px;
            max-width: 400px;
            width: 90%;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid var(--border-color);
            border-top: 4px solid var(--success-green);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 1200px) {
            .content-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
            
            .content-area {
                padding: 1.5rem;
            }
            
            .sidebar {
                transform: translateX(-100%);
            }
        }
    </style>
</head>
<body>

<div class="portal-container">
    <!-- Sidebar -->
    <!-- Sidebar -->
    <?php $this->load->view('simple_portal/components/faculty_sidebar', ['active_page' => 'ai_assignment']); ?>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Topbar -->
        <header class="topbar">
            <div class="page-info">
                <div class="page-title">
                    <i class="fas fa-tasks"></i>
                    Assignment Generator
                </div>
                <div class="breadcrumb">Generate comprehensive assignments from your documents</div>
            </div>

            <div class="topbar-actions">
                <div class="search-container">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="search-bar" placeholder="Search resources..." id="globalSearch">
                </div>

                <div class="notification-bell">
                    <i class="fas fa-bell"></i>
                    <span class="notification-count">2</span>
                </div>

                <div class="user-profile">
                    <div class="user-avatar">
                        <?php echo isset($user_data['username']) ? strtoupper(substr($user_data['username'], 0, 1)) : 'F'; ?>
                    </div>
                    <div class="user-info">
                        <div class="user-name"><?php echo isset($user_data['username']) ? htmlspecialchars($user_data['username']) : 'Faculty User'; ?></div>
                        <div class="user-role"><?php echo isset($user_role) ? strtoupper($user_role) : 'FACULTY'; ?></div>
                    </div>
                    <i class="fas fa-chevron-down user-dropdown"></i>
                </div>
            </div>
        </header>

        <div class="content-area">

        <div class="content-grid">
            <!-- Configuration Panel -->
            <div class="config-panel">
                <div class="config-header">
                    <i class="fas fa-cog me-2"></i>Assignment Configuration
                </div>
                <div class="config-body">
                    <form id="assignmentForm">
                        <div class="form-group">
                            <label class="form-label">Select Resources *</label>
                            <select name="resource_ids[]" id="resource_ids" class="form-control" multiple required>
                                <?php if (!empty($resources)): ?>
                                    <?php foreach ($resources as $resource): ?>
                                        <option value="<?php echo $resource->id; ?>"
                                                <?php echo ($selected_resource_id == $resource->id) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($resource->title); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <small style="color: var(--text-light); font-size: 12px;">Hold Ctrl/Cmd to select multiple resources</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Assignment Title *</label>
                            <input type="text" name="title" id="title" class="form-control" 
                                   placeholder="e.g., Research Project on AI" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Subject *</label>
                            <select name="subject_id" id="subject_id" class="form-control" required>
                                <option value="">Select subject...</option>
                                <?php if (!empty($subjects)): ?>
                                    <?php foreach ($subjects as $subject): ?>
                                        <option value="<?php echo $subject->id; ?>">
                                            <?php echo htmlspecialchars($subject->subject_code . ' - ' . $subject->subject_name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Assignment Type *</label>
                            <select name="assignment_type" id="assignment_type" class="form-control" required>
                                <option value="research">Research Assignment</option>
                                <option value="essay">Essay Assignment</option>
                                <option value="project">Project Assignment</option>
                                <option value="case_study">Case Study</option>
                                <option value="presentation">Presentation</option>
                            </select>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div class="form-group">
                                <label class="form-label">Word Count</label>
                                <input type="number" name="word_count" id="word_count" 
                                       class="form-control" value="1000" min="500" max="5000" step="100">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Difficulty</label>
                                <select name="difficulty" id="difficulty" class="form-control">
                                    <option value="easy">Easy</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="hard">Hard</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Due in (weeks) *</label>
                            <input type="number" name="due_weeks" id="due_weeks" class="form-control" 
                                   value="2" min="1" max="12" required>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 20px;" id="generateBtn">
                            <i class="fas fa-magic"></i>Generate Assignment
                        </button>
                    </form>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <small>Select one or more resources. The AI will analyze all selected documents and create a comprehensive assignment.</small>
                    </div>
                </div>
            </div>

            <!-- Preview Panel -->
            <div class="preview-panel">
                <div class="preview-header">
                    <i class="fas fa-eye me-2"></i>Assignment Preview
                </div>
                <div class="preview-body" id="assignmentPreview">
                    <div class="empty-state">
                        <i class="fas fa-tasks"></i>
                        <h3>No Assignment Generated Yet</h3>
                        <p>Configure your assignment settings and click "Generate Assignment" to create a comprehensive assignment.</p>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div class="modal" id="loadingModal">
    <div class="modal-content">
        <div class="spinner"></div>
        <h3 style="margin-bottom: 10px;">Generating Assignment...</h3>
        <p style="color: var(--text-light); margin: 0;">AI is analyzing your document and creating a comprehensive assignment. This may take a moment.</p>
    </div>
</div>

<!-- Publish Modal -->
<div class="modal" id="publishModal">
    <div class="modal-content">
        <h3 style="margin-bottom: 20px; color: var(--success-green);">
            <i class="fas fa-share-alt me-2"></i>Publish Assignment
        </h3>
        <p style="margin-bottom: 20px; color: var(--text-light);">
            This will make the assignment available to students enrolled in the selected subject.
        </p>
        <div style="display: flex; gap: 12px; justify-content: center;">
            <button class="btn btn-publish" onclick="confirmPublish()">
                <i class="fas fa-check"></i>Publish Now
            </button>
            <button class="btn" style="background: var(--border-color); color: var(--text-dark);" onclick="closePublishModal()">
                Cancel
            </button>
        </div>
    </div>
</div>

<script>
let currentAssignmentId = null;
let currentSubjectId = null;

document.getElementById('assignmentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const generateBtn = document.getElementById('generateBtn');
    const loadingModal = document.getElementById('loadingModal');
    
    // Show loading
    generateBtn.disabled = true;
    loadingModal.classList.add('show');
    
    // Send request
    fetch('<?php echo base_url('simple_portal/process_assignment_generation'); ?>', {
        method: 'POST',
        body: new URLSearchParams(formData)
    })
    .then(response => response.json())
    .then(data => {
        loadingModal.classList.remove('show');
        generateBtn.disabled = false;
        
        if (data.success) {
            currentAssignmentId = data.assignment_id;
            currentSubjectId = formData.get('subject_id');
            displayAssignment(data.assignment_data);
            
            // Show success message
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success';
            alertDiv.innerHTML = '<i class="fas fa-check-circle me-2"></i>Assignment generated successfully!';
            alertDiv.style.position = 'fixed';
            alertDiv.style.top = '20px';
            alertDiv.style.right = '20px';
            alertDiv.style.zIndex = '1001';
            document.body.appendChild(alertDiv);
            
            setTimeout(() => {
                document.body.removeChild(alertDiv);
            }, 3000);
        } else {
            alert('Error: ' + (data.error || 'Failed to generate assignment'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        loadingModal.classList.remove('show');
        generateBtn.disabled = false;
        alert('Failed to generate assignment. Please try again.');
    });
});

function displayAssignment(assignmentData) {
    const preview = document.getElementById('assignmentPreview');
    let html = '<div class="quiz-content">';
    
    try {
        const assignment = typeof assignmentData === 'string' ? JSON.parse(assignmentData) : assignmentData;
        
        if (assignment.assignment) {
            const data = assignment.assignment;
            
            html += `
                <div style="margin-bottom: 30px;">
                    <h2 style="color: var(--gray-900); margin-bottom: 15px;">${data.title || 'Assignment'}</h2>
                    <div style="display: flex; gap: 20px; color: var(--gray-600); font-size: 14px; margin-bottom: 20px;">
                        <span><i class="fas fa-calendar"></i> Due: ${data.due_date || 'Not set'}</span>
                        <span><i class="fas fa-file-word"></i> ${data.total_marks || 100} marks</span>
                    </div>
                </div>
            `;
            
            if (data.description) {
                html += `
                    <div style="margin-bottom: 25px;">
                        <h3 style="color: var(--gray-800); margin-bottom: 10px;"><i class="fas fa-info-circle"></i> Description</h3>
                        <p style="line-height: 1.6;">${data.description}</p>
                    </div>
                `;
            }
            
            if (data.objectives && data.objectives.length > 0) {
                html += `
                    <div style="margin-bottom: 25px;">
                        <h3 style="color: var(--gray-800); margin-bottom: 10px;"><i class="fas fa-bullseye"></i> Learning Objectives</h3>
                        <ul style="list-style: none; padding: 0;">
                            ${data.objectives.map(obj => `
                                <li style="padding: 10px 15px; margin-bottom: 8px; background: var(--gray-50); border-radius: 8px; border-left: 3px solid var(--accent-orange);">
                                    ${obj}
                                </li>
                            `).join('')}
                        </ul>
                    </div>
                `;
            }
            
            if (data.tasks && data.tasks.length > 0) {
                html += `
                    <div style="margin-bottom: 25px;">
                        <h3 style="color: var(--gray-800); margin-bottom: 10px;"><i class="fas fa-tasks"></i> Tasks</h3>
                        ${data.tasks.map((task, index) => `
                            <div style="background: var(--gray-50); padding: 20px; border-radius: 12px; margin-bottom: 15px; border-left: 4px solid var(--accent-orange);">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                    <span style="background: var(--accent-orange); color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700;">
                                        Task ${task.task_number || index + 1}
                                    </span>
                                    <span style="color: var(--gray-600); font-size: 14px;">${task.word_count || 0} words</span>
                                </div>
                                <h4 style="margin-bottom: 10px;">${task.task_title || 'Task ' + (index + 1)}</h4>
                                <p style="line-height: 1.6;">${task.description || ''}</p>
                            </div>
                        `).join('')}
                    </div>
                `;
            }
            
            if (data.evaluation_criteria && data.evaluation_criteria.length > 0) {
                html += `
                    <div style="margin-bottom: 25px;">
                        <h3 style="color: var(--gray-800); margin-bottom: 10px;"><i class="fas fa-check-square"></i> Evaluation Criteria</h3>
                        ${data.evaluation_criteria.map(criteria => `
                            <div style="background: var(--gray-50); padding: 15px; border-radius: 10px; margin-bottom: 10px;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                    <strong>${criteria.criterion || ''}</strong>
                                    <span style="background: var(--accent-orange); color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700;">
                                        ${criteria.weight || ''}
                                    </span>
                                </div>
                                <p style="margin: 0; color: var(--gray-600);">${criteria.description || ''}</p>
                            </div>
                        `).join('')}
                    </div>
                `;
            }
            
            if (data.resources && data.resources.length > 0) {
                html += `
                    <div style="margin-bottom: 25px;">
                        <h3 style="color: var(--gray-800); margin-bottom: 10px;"><i class="fas fa-book"></i> Suggested Resources</h3>
                        <ul style="list-style: none; padding: 0;">
                            ${data.resources.map(resource => `
                                <li style="padding: 10px 15px; margin-bottom: 8px; background: var(--gray-50); border-radius: 8px; border-left: 3px solid var(--accent-orange);">
                                    ${resource}
                                </li>
                            `).join('')}
                        </ul>
                    </div>
                `;
            }
            
            if (data.submission_guidelines && data.submission_guidelines.length > 0) {
                html += `
                    <div style="margin-bottom: 25px;">
                        <h3 style="color: var(--gray-800); margin-bottom: 10px;"><i class="fas fa-clipboard-list"></i> Submission Guidelines</h3>
                        <ul style="list-style: none; padding: 0;">
                            ${data.submission_guidelines.map(guideline => `
                                <li style="padding: 10px 15px; margin-bottom: 8px; background: var(--gray-50); border-radius: 8px; border-left: 3px solid var(--accent-orange);">
                                    ${guideline}
                                </li>
                            `).join('')}
                        </ul>
                    </div>
                `;
            }
        } else {
            html += '<p style="color: var(--text-light);">No assignment data available</p>';
        }
    } catch (e) {
        console.error('Error parsing assignment data:', e);
        html += '<p style="color: var(--error-red);">Error parsing assignment data</p>';
    }
    
    html += '</div>';
    html += `
        <div class="action-buttons">
            <button class="btn btn-primary" onclick="window.print()">
                <i class="fas fa-print"></i>Print Assignment
            </button>
            <button class="btn btn-publish" onclick="showPublishModal()" ${!currentAssignmentId ? 'disabled' : ''}>
                <i class="fas fa-share-alt"></i>Publish to Students
            </button>
            <button class="btn btn-success" onclick="window.location.href='<?php echo base_url('simple_portal/ai_features'); ?>'">
                <i class="fas fa-check"></i>Done
            </button>
        </div>
    `;
    
    preview.innerHTML = html;
}

function showPublishModal() {
    if (!currentAssignmentId || !currentSubjectId) {
        alert('Please generate an assignment first and select a subject.');
        return;
    }
    document.getElementById('publishModal').classList.add('show');
}

function closePublishModal() {
    document.getElementById('publishModal').classList.remove('show');
}

function confirmPublish() {
    if (!currentAssignmentId || !currentSubjectId) {
        alert('No assignment to publish.');
        return;
    }
    
    const formData = new FormData();
    formData.append('assignment_id', currentAssignmentId);
    formData.append('subject_id', currentSubjectId);
    
    fetch('<?php echo base_url('simple_portal/publish_assignment'); ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        closePublishModal();
        
        if (data.success) {
            // Show success message
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success';
            alertDiv.innerHTML = '<i class="fas fa-check-circle me-2"></i>Assignment published successfully! Students can now access it.';
            alertDiv.style.position = 'fixed';
            alertDiv.style.top = '20px';
            alertDiv.style.right = '20px';
            alertDiv.style.zIndex = '1001';
            alertDiv.style.maxWidth = '400px';
            document.body.appendChild(alertDiv);
            
            setTimeout(() => {
                document.body.removeChild(alertDiv);
            }, 4000);
            
            // Update publish button
            const publishBtn = document.querySelector('.btn-publish');
            if (publishBtn) {
                publishBtn.innerHTML = '<i class="fas fa-check-circle"></i>Published';
                publishBtn.disabled = true;
                publishBtn.style.background = 'var(--success-green)';
            }
        } else {
            alert('Error: ' + (data.error || 'Failed to publish assignment'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        closePublishModal();
        alert('Failed to publish assignment. Please try again.');
    });
}

let userMenu = null;

function showUserMenu() {
    if (userMenu) {
        userMenu.remove();
        userMenu = null;
        return;
    }
    
    const profile = document.querySelector('.user-profile');
    const rect = profile.getBoundingClientRect();
    
    userMenu = document.createElement('div');
    userMenu.className = 'config-panel';
    userMenu.style.position = 'fixed';
    userMenu.style.top = (rect.bottom + 10) + 'px';
    userMenu.style.right = '40px';
    userMenu.style.width = '200px';
    userMenu.style.zIndex = '2000';
    userMenu.style.padding = '10px 0';
    
    userMenu.innerHTML = `
        <a href="<?php echo base_url('simple_portal/profile'); ?>" style="display: block; padding: 10px 20px; color: var(--gray-800); text-decoration: none; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-user"></i> Profile
        </a>
        <a href="<?php echo base_url('logout'); ?>" style="display: block; padding: 10px 20px; color: var(--error); text-decoration: none; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    `;
    
    document.body.appendChild(userMenu);
    
    setTimeout(() => {
        document.addEventListener('click', function close(e) {
            if (userMenu && !userMenu.contains(e.target) && !profile.contains(e.target)) {
                userMenu.remove();
                userMenu = null;
                document.removeEventListener('click', close);
            }
        });
    }, 0);
}

function toggleNotifications() {
    alert('No new notifications');
}
</script>

</body>
</html>