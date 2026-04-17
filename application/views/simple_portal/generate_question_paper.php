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
    <title>Question Paper Generator - AI Powered Academic Hub</title>
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

        .btn-primary {
            background: var(--gradient-primary);
            color: white;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
        }

        .btn-primary:hover {
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
            background: linear-gradient(135deg, var(--primary-blue), var(--primary-light));
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 8px 30px rgba(31, 94, 168, 0.2);
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
            background: linear-gradient(135deg, #8b5cf6, #7c3aed) !important;
            color: white !important;
            border: none !important;
        }

        .btn-publish:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(139, 92, 246, 0.3) !important;
            background: linear-gradient(135deg, #7c3aed, #6d28d9) !important;
        }

        .btn-publish:disabled {
            background: #cbd5e1 !important;
            color: #94a3b8 !important;
            cursor: not-allowed !important;
            transform: none !important;
            box-shadow: none !important;
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

        .section-card {
            background: #f8fafc;
            border: 2px solid var(--border-color);
            border-radius: 10px;
            margin-bottom: 15px;
            overflow: hidden;
        }

        .section-header {
            background: var(--primary-blue);
            color: white;
            padding: 12px 16px;
            font-weight: 600;
            font-size: 14px;
        }

        .section-body {
            padding: 16px;
        }

        .section-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 12px;
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

        .paper-content {
            background: var(--white);
            padding: 30px;
            border-radius: 10px;
            border: 1px solid var(--border-color);
        }

        .paper-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--border-color);
        }

        .paper-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary-blue);
            margin-bottom: 10px;
        }

        .question-section {
            margin-bottom: 30px;
        }

        .question-section-header {
            background: var(--primary-blue);
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .question-item {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border-color);
        }

        .question-item:last-child {
            border-bottom: none;
        }

        .question-text {
            font-weight: 500;
            margin-bottom: 5px;
            line-height: 1.6;
        }

        .question-marks {
            font-size: 12px;
            color: var(--text-light);
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
            border-top: 4px solid var(--primary-blue);
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
    <?php $this->load->view('simple_portal/components/faculty_sidebar', ['active_page' => 'question_papers']); ?>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Topbar -->
        <header class="topbar">
            <div class="page-info">
                <div class="page-title">
                    <i class="fas fa-file-alt"></i>
                    Question Paper Generator
                </div>
                <div class="breadcrumb">Create professional exam question papers with AI</div>
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
                    <i class="fas fa-cog me-2"></i>Paper Configuration
                </div>
                <div class="config-body">
                    <form id="paperForm">
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
                            <label class="form-label">Paper Title *</label>
                            <input type="text" name="title" id="title" class="form-control" 
                                   placeholder="e.g., Mid-Term Examination" required>
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

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div class="form-group">
                                <label class="form-label">Total Marks</label>
                                <input type="number" name="total_marks" id="total_marks" 
                                       class="form-control" value="100" min="10" max="200">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Duration (min)</label>
                                <input type="number" name="duration_minutes" id="duration_minutes" 
                                       class="form-control" value="180" min="30" max="300">
                            </div>
                        </div>

                        <div style="border-top: 1px solid var(--border-color); padding-top: 20px; margin-top: 20px;">
                            <label class="form-label">Paper Format</label>
                            
                            <!-- Section A -->
                            <div class="section-card">
                                <div class="section-header">Section A</div>
                                <div class="section-body">
                                    <div class="section-row">
                                        <div>
                                            <label style="font-size: 12px; font-weight: 600; margin-bottom: 5px; display: block;">Type</label>
                                            <select class="form-control" name="sections[0][type]" style="font-size: 12px; padding: 8px;">
                                                <option value="mcq">Multiple Choice</option>
                                                <option value="short">Short Answer</option>
                                                <option value="long">Long Answer</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label style="font-size: 12px; font-weight: 600; margin-bottom: 5px; display: block;">Questions</label>
                                            <input type="number" class="form-control" name="sections[0][num_questions]" 
                                                   value="10" min="1" max="50" style="font-size: 12px; padding: 8px;">
                                        </div>
                                        <div>
                                            <label style="font-size: 12px; font-weight: 600; margin-bottom: 5px; display: block;">Marks Each</label>
                                            <input type="number" class="form-control" name="sections[0][marks_per_question]" 
                                                   value="2" min="1" max="10" style="font-size: 12px; padding: 8px;">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section B -->
                            <div class="section-card">
                                <div class="section-header">Section B</div>
                                <div class="section-body">
                                    <div class="section-row">
                                        <div>
                                            <label style="font-size: 12px; font-weight: 600; margin-bottom: 5px; display: block;">Type</label>
                                            <select class="form-control" name="sections[1][type]" style="font-size: 12px; padding: 8px;">
                                                <option value="mcq">Multiple Choice</option>
                                                <option value="short" selected>Short Answer</option>
                                                <option value="long">Long Answer</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label style="font-size: 12px; font-weight: 600; margin-bottom: 5px; display: block;">Questions</label>
                                            <input type="number" class="form-control" name="sections[1][num_questions]" 
                                                   value="5" min="1" max="20" style="font-size: 12px; padding: 8px;">
                                        </div>
                                        <div>
                                            <label style="font-size: 12px; font-weight: 600; margin-bottom: 5px; display: block;">Marks Each</label>
                                            <input type="number" class="form-control" name="sections[1][marks_per_question]" 
                                                   value="5" min="1" max="20" style="font-size: 12px; padding: 8px;">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section C -->
                            <div class="section-card">
                                <div class="section-header">Section C</div>
                                <div class="section-body">
                                    <div class="section-row">
                                        <div>
                                            <label style="font-size: 12px; font-weight: 600; margin-bottom: 5px; display: block;">Type</label>
                                            <select class="form-control" name="sections[2][type]" style="font-size: 12px; padding: 8px;">
                                                <option value="mcq">Multiple Choice</option>
                                                <option value="short">Short Answer</option>
                                                <option value="long" selected>Long Answer</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label style="font-size: 12px; font-weight: 600; margin-bottom: 5px; display: block;">Questions</label>
                                            <input type="number" class="form-control" name="sections[2][num_questions]" 
                                                   value="3" min="1" max="10" style="font-size: 12px; padding: 8px;">
                                        </div>
                                        <div>
                                            <label style="font-size: 12px; font-weight: 600; margin-bottom: 5px; display: block;">Marks Each</label>
                                            <input type="number" class="form-control" name="sections[2][marks_per_question]" 
                                                   value="15" min="5" max="50" style="font-size: 12px; padding: 8px;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 20px;" id="generateBtn">
                            <i class="fas fa-magic"></i>Generate Question Paper
                        </button>
                    </form>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <small>Select resources and configure your paper format. The AI will generate questions based on your specifications.</small>
                    </div>
                </div>
            </div>

            <!-- Preview Panel -->
            <div class="preview-panel">
                <div class="preview-header">
                    <i class="fas fa-eye me-2"></i>Question Paper Preview
                </div>
                <div class="preview-body" id="paperPreview">
                    <div class="empty-state">
                        <i class="fas fa-file-alt"></i>
                        <h3>No Question Paper Generated Yet</h3>
                        <p>Configure your paper settings and click "Generate Question Paper" to see the preview here.</p>
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
        <h3 style="margin-bottom: 10px;">Generating Question Paper...</h3>
        <p style="color: var(--text-light); margin: 0;">AI is creating your question paper. This may take a moment.</p>
    </div>
</div>

<!-- Publish Modal -->
<div class="modal" id="publishModal">
    <div class="modal-content">
        <h3 style="margin-bottom: 20px; color: var(--primary-blue);">
            <i class="fas fa-share-alt me-2"></i>Publish Question Paper
        </h3>
        <p style="margin-bottom: 20px; color: var(--text-light);">
            This will make the question paper available to students enrolled in the selected subject.
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
    userMenu.className = 'config-panel'; // Reuse valid style class
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

let currentPaperId = null;
let currentSubjectId = null;

document.getElementById('paperForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const generateBtn = document.getElementById('generateBtn');
    const loadingModal = document.getElementById('loadingModal');
    
    // Show loading
    generateBtn.disabled = true;
    loadingModal.classList.add('show');
    
    // Send request
    fetch('<?php echo base_url('simple_portal/process_question_paper_generation'); ?>', {
        method: 'POST',
        body: new URLSearchParams(formData)
    })
    .then(response => response.json())
    .then(data => {
        loadingModal.classList.remove('show');
        generateBtn.disabled = false;
        
        if (data.success) {
            currentPaperId = data.paper_id;
            currentSubjectId = formData.get('subject_id');
            displayPaper(data.paper_data);
            
            // Show success message
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success';
            alertDiv.innerHTML = '<i class="fas fa-check-circle me-2"></i>Question paper generated successfully!';
            alertDiv.style.position = 'fixed';
            alertDiv.style.top = '20px';
            alertDiv.style.right = '20px';
            alertDiv.style.zIndex = '1001';
            document.body.appendChild(alertDiv);
            
            setTimeout(() => {
                document.body.removeChild(alertDiv);
            }, 3000);
        } else {
            alert('Error: ' + (data.error || 'Failed to generate question paper'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        loadingModal.classList.remove('show');
        generateBtn.disabled = false;
        alert('Failed to generate question paper. Please try again.');
    });
});

function displayPaper(paperData) {
    const preview = document.getElementById('paperPreview');
    let html = '<div class="paper-content">';
    
    try {
        const paper = typeof paperData === 'string' ? JSON.parse(paperData) : paperData;
        
        if (paper.sections && paper.sections.length > 0) {
            // Paper header
            html += `
                <div class="paper-header">
                    <div class="paper-title">${paper.title || 'Question Paper'}</div>
                    <div style="display: flex; justify-content: space-between; font-size: 14px; color: var(--text-light);">
                        <span><strong>Total Marks:</strong> ${paper.total_marks || 100}</span>
                        <span><strong>Duration:</strong> ${paper.duration_minutes || 180} minutes</span>
                    </div>
                </div>
            `;
            
            // Sections
            paper.sections.forEach((section, index) => {
                html += `
                    <div class="question-section">
                        <div class="question-section-header">
                            ${section.section_name || 'Section ' + (index + 1)}
                        </div>
                `;
                
                if (section.questions && section.questions.length > 0) {
                    section.questions.forEach((question, qIndex) => {
                        html += `
                            <div class="question-item">
                                <div class="question-text">
                                    <strong>Q${qIndex + 1}.</strong> ${question.question_text || 'Sample question based on selected resources...'}
                                </div>
                                <div class="question-marks">[${question.marks || 1} marks]</div>
                            </div>
                        `;
                    });
                } else {
                    html += '<p style="color: var(--text-light); font-style: italic;">No questions generated for this section</p>';
                }
                
                html += `</div>`;
            });
        } else {
            html += '<p style="color: var(--text-light);">No paper data available</p>';
        }
    } catch (e) {
        console.error('Error parsing paper data:', e);
        html += '<p style="color: var(--error-red);">Error displaying paper data</p>';
    }
    
    html += '</div>';
    html += `
        <div class="action-buttons">
            <button class="btn btn-primary" onclick="window.print()">
                <i class="fas fa-print"></i>Print Paper
            </button>
            <button class="btn btn-publish" onclick="showPublishModal()" ${!currentPaperId ? 'disabled' : ''}>
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
    if (!currentPaperId || !currentSubjectId) {
        alert('Please generate a question paper first and select a subject.');
        return;
    }
    document.getElementById('publishModal').classList.add('show');
}

function closePublishModal() {
    document.getElementById('publishModal').classList.remove('show');
}

function confirmPublish() {
    if (!currentPaperId || !currentSubjectId) {
        alert('No question paper to publish.');
        return;
    }
    
    const formData = new FormData();
    formData.append('paper_id', currentPaperId);
    formData.append('subject_id', currentSubjectId);
    
    fetch('<?php echo base_url('simple_portal/publish_question_paper'); ?>', {
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
            alertDiv.innerHTML = '<i class="fas fa-check-circle me-2"></i>Question paper published successfully! Students can now access it.';
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
            alert('Error: ' + (data.error || 'Failed to publish question paper'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        closePublishModal();
        alert('Failed to publish question paper. Please try again.');
    });
}
</script>

</body>
</html>