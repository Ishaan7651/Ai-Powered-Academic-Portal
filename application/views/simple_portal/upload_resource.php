<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Resource - AI Powered Academic Hub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <?php $this->load->view('simple_portal/components/faculty_sidebar_css'); ?>
    <style>
        /* Main Content Styling Overrides for this page */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Topbar - Premium Design (Matching Dashboard) */
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

        .page-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--gray-900);
            position: relative;
            padding-left: 0;
            display: flex; 
            align-items: center;
            gap: 0.75rem;
        }
        
        .page-title::before { display: none; } /* Remove old side bar */

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
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-weight: 700;
            font-size: 0.9375rem;
            color: var(--gray-900);
        }

        .user-role {
            font-size: 0.8125rem;
            color: var(--gray-600);
            background: rgba(37, 99, 235, 0.1);
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            display: inline-block;
            font-weight: 700;
        }

        /* Content Area */
        .content-area {
            flex: 1;
            padding: 2.5rem;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
            overflow-y: auto;
        }

        /* Header Section */
        .header-section {
            margin-bottom: 2.5rem;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .page-header h1 {
            font-size: 2rem;
            font-weight: 900;
            color: var(--gray-900);
            line-height: 1.2;
            letter-spacing: -0.025em;
        }

        .page-subtitle {
            color: var(--gray-500);
            font-size: 1.1rem;
            line-height: 1.5;
            margin-top: 0.5rem;
            font-weight: 500;
        }

        .back-btn {
            background: var(--white);
            color: var(--gray-700);
            border: 1px solid var(--gray-200);
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all var(--transition-base);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            box-shadow: var(--shadow-sm);
        }

        .back-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
            border-color: var(--gray-300);
            color: var(--primary-blue);
        }

        /* Upload Form Container - Matching .stat-card style */
        .upload-container {
            background: var(--white);
            border-radius: 20px;
            border: 1px solid var(--gray-200);
            overflow: hidden;
            box-shadow: var(--shadow-md);
            margin-bottom: 2.5rem;
            transition: all var(--transition-base);
            position: relative;
        }

        /* Clean Header */
        .upload-header {
            padding: 2rem 2.5rem;
            border-bottom: 1px solid var(--gray-100);
            background: var(--white); /* Removed the blue background */
        }

        .upload-header h2 {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--gray-900);
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .upload-header h2 i {
            color: var(--white);
            font-size: 1.25rem;
            background: var(--gradient-primary); /* Icon background */
            padding: 0.75rem;
            border-radius: 12px;
            box-shadow: var(--shadow-md);
        }

        .upload-header p {
            color: var(--gray-500);
            font-size: 0.95rem;
            margin-top: 0.75rem;
            line-height: 1.6;
            margin-left: 3.5rem; /* Align with text */
        }

        .upload-body {
            padding: 2.5rem;
        }

        /* Form Elements */
        .form-group {
            margin-bottom: 2rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 0.75rem;
            font-size: 0.95rem;
        }

        .form-label.required::after {
            content: ' *';
            color: var(--error);
        }

        .form-control {
            width: 100%;
            padding: 1rem 1.25rem;
            border: 2px solid var(--gray-200);
            border-radius: 12px;
            font-size: 1rem;
            transition: all var(--transition-fast);
            background: var(--white);
            color: var(--gray-900);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        .form-control.select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%2364748b' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1.25rem center;
            padding-right: 3rem;
            appearance: none;
        }

        textarea.form-control {
            min-height: 150px;
            resize: vertical;
            line-height: 1.6;
        }

        /* Resource Type Selection - Cards style */
        .resource-type-selector {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .type-option {
            background: var(--white);
            border: 2px solid var(--gray-200);
            border-radius: 16px;
            padding: 2rem;
            cursor: pointer;
            transition: all var(--transition-base);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .type-option:hover {
            border-color: var(--primary-light);
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .type-option.selected {
            border-color: var(--primary-blue);
            background: rgba(59, 130, 246, 0.03);
            box-shadow: var(--shadow-md);
        }

        .type-option.selected::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
        }

        .type-icon {
            width: 64px;
            height: 64px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            color: var(--white);
            box-shadow: var(--shadow-lg);
            margin-bottom: 0.5rem;
            transition: transform var(--transition-base);
        }
        
        .type-option:hover .type-icon {
            transform: scale(1.1) rotate(5deg);
        }

        .type-icon.file {
            background: var(--gradient-purple);
        }

        .type-icon.link {
            background: var(--gradient-success);
        }

        .type-label {
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--gray-900);
        }

        .type-description {
            font-size: 0.9rem;
            color: var(--gray-500);
            line-height: 1.5;
        }

        /* File Upload Area */
        .file-upload-area {
            border: 2px dashed var(--gray-300);
            border-radius: 16px;
            padding: 3rem;
            text-align: center;
            transition: all var(--transition-base);
            cursor: pointer;
            background: var(--gray-50);
            position: relative;
        }

        .file-upload-area:hover {
            border-color: var(--primary-blue);
            background: rgba(59, 130, 246, 0.05);
        }

        .file-upload-icon {
            font-size: 3.5rem;
            color: var(--gray-400);
            margin-bottom: 1.5rem;
            transition: all var(--transition-base);
        }
        
        .file-upload-area:hover .file-upload-icon {
            transform: translateY(-5px);
            color: var(--primary-blue);
        }

        .file-upload-text {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--gray-700);
            margin-bottom: 0.5rem;
        }

        /* Buttons */
        .form-actions {
            display: flex;
            gap: 1.5rem;
            justify-content: flex-end;
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid var(--gray-200);
        }

        .btn {
            padding: 0.875rem 2rem;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: all var(--transition-base);
            border: none;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            min-width: 160px;
            justify-content: center;
            text-decoration: none;
        }

        .btn-primary {
            background: var(--gradient-primary); /* Fixed matching dashboard blue */
            color: var(--white);
            box-shadow: 0 4px 6px -1px rgba(31, 94, 168, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(31, 94, 168, 0.4);
        }

        .btn-secondary {
            background: var(--white);
            color: var(--gray-700);
            border: 2px solid var(--gray-200);
        }

        .btn-secondary:hover {
            background: var(--gray-50);
            border-color: var(--gray-400);
            color: var(--gray-900);
            transform: translateY(-2px);
        }

        /* Helper Classes */
        .flash-message {
            padding: 1rem 1.25rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            animation: slideIn 0.3s ease-out;
            font-weight: 500;
        }

        .flash-success {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
        }

        .flash-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .supported-formats {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-top: 1.5rem;
            justify-content: center;
        }

        .format-badge {
            background: var(--white);
            color: var(--gray-600);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            border: 1px solid var(--gray-200);
            box-shadow: var(--shadow-sm);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .sidebar { width: 80px; }
            .main-content { margin-left: 80px; }
            .form-row, .resource-type-selector { grid-template-columns: 1fr; }
        }

        @media (max-width: 768px) {
            .content-area { padding: 1.5rem; }
            .upload-header { padding: 1.5rem; }
            .upload-body { padding: 1.5rem; }
        }
        
        /* Animations */
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

    </style>
</head>
<body>

<div class="portal-container">
    <?php $this->load->view('simple_portal/components/faculty_sidebar', ['active_page' => 'upload_resource']); ?>

    <!-- Main Content -->
    <div class="main-content">

        <!-- Topbar -->
        <header class="topbar">
            <div class="page-title">
                Upload Resource
            </div>

            <div class="user-profile">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($username, 0, 1)); ?>
                </div>
                <div class="user-info">
                    <div class="user-name"><?php echo htmlspecialchars($username); ?></div>
                    <div class="user-role">FACULTY</div>
                </div>
            </div>
        </header>

        <!-- Content Area -->
        <main class="content-area">

            <!-- Header Section -->
            <div class="header-section">
                <div class="page-header">
                    <div>
                        <h1>Upload New Resource</h1>
                        <p class="page-subtitle">Share study materials, notes, and learning resources with your students</p>
                    </div>
                    <a href="<?php echo base_url('simple_portal/resources'); ?>" class="back-btn">
                        <i class="fa fa-arrow-left"></i>
                        Back to Resources
                    </a>
                </div>
            </div>

            <!-- Flash Messages -->
            <?php if ($this->session->flashdata('message')): ?>
                <div class="flash-message flash-<?php echo $this->session->flashdata('message_type'); ?>">
                    <i class="fa fa-<?php echo $this->session->flashdata('message_type') === 'error' ? 'exclamation-triangle' : 'check-circle'; ?>"></i>
                    <span><?php echo $this->session->flashdata('message'); ?></span>
                    <button type="button" class="flash-close" onclick="this.parentElement.style.display='none'">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            <?php endif; ?>

            <!-- Upload Form Container -->
            <div class="upload-container">
                <div class="upload-header">
                    <h2>
                        <i class="fa fa-cloud-upload-alt"></i>
                        Share Your Resource
                    </h2>
                    <p>Upload files or share web links to enhance student learning</p>
                </div>

                <div class="upload-body">
                    <form method="POST" enctype="multipart/form-data" id="uploadForm" onsubmit="return validateForm()">
                        <input type="hidden" name="action" value="upload_resource">

                        <!-- Resource Type Selection -->
                        <div class="form-group">
                            <label class="form-label required">Resource Type</label>
                            <div class="resource-type-selector">
                                <div class="type-option selected" onclick="selectResourceType('file')">
                                    <div class="type-icon file">
                                        <i class="fa fa-file"></i>
                                    </div>
                                    <div class="type-label">File Upload</div>
                                    <div class="type-description">Upload documents, presentations, or other files</div>
                                </div>
                                <div class="type-option" onclick="selectResourceType('weblink')">
                                    <div class="type-icon link">
                                        <i class="fa fa-link"></i>
                                    </div>
                                    <div class="type-label">Web Link</div>
                                    <div class="type-description">Share online resources or external links</div>
                                </div>
                            </div>
                            <input type="hidden" name="resource_type" id="resource_type" value="file">
                        </div>

                        <!-- Resource Title -->
                        <div class="form-group">
                            <label class="form-label required">Resource Title</label>
                            <input type="text" name="title" class="form-control" required 
                                   placeholder="e.g., Introduction to Neural Networks, Calculus Study Guide"
                                   minlength="3" maxlength="200">
                            <div class="form-help">
                                <i class="fa fa-info-circle"></i>
                                Use a descriptive title that clearly indicates the content
                            </div>
                        </div>

                        <!-- Resource Description -->
                        <div class="form-group">
                            <label class="form-label">Description (Optional)</label>
                            <textarea name="description" class="form-control" 
                                      placeholder="Describe the resource content, learning objectives, or any specific instructions..."
                                      maxlength="500"></textarea>
                            <div class="form-help">
                                <i class="fa fa-info-circle"></i>
                                Maximum 500 characters. This helps students understand the resource better.
                            </div>
                        </div>

                        <!-- Subject and Semester -->
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label required">Subject</label>
                                <select name="subject_id" class="form-control select" required>
                                    <option value="">Select a subject...</option>
                                    <?php if (isset($subjects) && !empty($subjects)): ?>
                                        <?php foreach ($subjects as $subject): ?>
                                            <option value="<?php echo $subject->id; ?>">
                                                <?php echo htmlspecialchars($subject->subject_code . ' - ' . $subject->subject_name); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="">No subjects available</option>
                                    <?php endif; ?>
                                </select>
                                <div class="form-help">
                                    <i class="fa fa-info-circle"></i>
                                    Select the subject this resource belongs to
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label required">Semester</label>
                                <select name="semester" class="form-control select" required>
                                    <option value="">Select semester...</option>
                                    <?php for ($i = 1; $i <= 8; $i++): ?>
                                        <option value="<?php echo $i; ?>">Semester <?php echo $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                                <div class="form-help">
                                    <i class="fa fa-info-circle"></i>
                                    Choose the semester this resource is intended for
                                </div>
                            </div>
                        </div>

                        <!-- File Upload Section (Default) -->
                        <div id="file_upload_section" class="form-group">
                            <label class="form-label required">Select File</label>
                            <div class="file-upload-area" id="dropZone" onclick="document.getElementById('fileInput').click()">
                                <div class="file-upload-icon">
                                    <i class="fa fa-cloud-upload-alt"></i>
                                </div>
                                <div class="file-upload-text">Click to select or drag & drop your file here</div>
                                <div class="file-upload-hint">Maximum file size: 100MB • Supported formats listed below</div>
                            </div>
                            <input type="file" name="resource_file" id="fileInput" class="file-input" 
                                   accept=".pdf,.ppt,.pptx,.xls,.xlsx,.csv,.epub,.doc,.docx,.txt">
                            
                            <!-- Supported Formats -->
                            <div class="supported-formats">
                                <span class="format-badge"><i class="fa fa-file-pdf"></i> PDF</span>
                                <span class="format-badge"><i class="fa fa-file-powerpoint"></i> PPT/PPTX</span>
                                <span class="format-badge"><i class="fa fa-file-excel"></i> XLS/XLSX</span>
                                <span class="format-badge"><i class="fa fa-file-csv"></i> CSV</span>
                                <span class="format-badge"><i class="fa fa-book"></i> ePub</span>
                                <span class="format-badge"><i class="fa fa-file-word"></i> DOC/DOCX</span>
                                <span class="format-badge"><i class="fa fa-file-alt"></i> TXT</span>
                            </div>
                        </div>

                        <!-- Web Link Section (Hidden by default) -->
                        <div id="web_link_section" class="form-group" style="display: none;">
                            <label class="form-label required">Web URL</label>
                            <div class="url-input-group">
                                <input type="url" name="web_url" id="web_url" class="form-control" 
                                       placeholder="https://example.com/learning-resource">
                                <div class="url-icon">
                                    <i class="fa fa-link"></i>
                                </div>
                            </div>
                            <div class="form-help">
                                <i class="fa fa-info-circle"></i>
                                Enter a valid URL including https://
                            </div>
                        </div>

                        <!-- Upload Progress -->
                        <div class="upload-progress" id="uploadProgress">
                            <div class="progress-bar">
                                <div class="progress-fill" id="progressFill"></div>
                            </div>
                            <div class="progress-text">
                                <span id="progressStatus">Uploading...</span>
                                <span id="progressPercentage">0%</span>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="form-actions">
                            <a href="<?php echo base_url('simple_portal/resources'); ?>" class="btn btn-secondary">
                                <i class="fa fa-times"></i>
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fa fa-upload"></i>
                                Upload Resource
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </main>
    </div>
</div>

<script>
    // Resource Type Selection
    function selectResourceType(type) {
        const fileSection = document.getElementById('file_upload_section');
        const webSection = document.getElementById('web_link_section');
        const typeOptions = document.querySelectorAll('.type-option');
        const resourceTypeInput = document.getElementById('resource_type');
        
        // Update selected option styling
        typeOptions.forEach(option => option.classList.remove('selected'));
        event.currentTarget.classList.add('selected');
        
        // Update hidden input
        resourceTypeInput.value = type;
        
        // Show/hide sections
        if (type === 'file') {
            fileSection.style.display = 'block';
            webSection.style.display = 'none';
            document.getElementById('fileInput').required = true;
            document.getElementById('web_url').required = false;
        } else {
            fileSection.style.display = 'none';
            webSection.style.display = 'block';
            document.getElementById('fileInput').required = false;
            document.getElementById('web_url').required = true;
        }
    }

    // File Upload Handling
    document.addEventListener('DOMContentLoaded', function() {
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('fileInput');
        const progressSection = document.getElementById('uploadProgress');
        const progressFill = document.getElementById('progressFill');
        const progressPercentage = document.getElementById('progressPercentage');
        const progressStatus = document.getElementById('progressStatus');
        const submitBtn = document.getElementById('submitBtn');
        
        // File selection
        fileInput.addEventListener('change', handleFileSelect);
        
        // Drag and drop
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });
        
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, highlight, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, unhighlight, false);
        });
        
        function highlight() {
            dropZone.classList.add('dragover');
        }
        
        function unhighlight() {
            dropZone.classList.remove('dragover');
        }
        
        dropZone.addEventListener('drop', handleDrop, false);
        
        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            handleFiles(files);
        }
        
        function handleFileSelect(e) {
            const files = e.target.files;
            handleFiles(files);
        }
        
        function handleFiles(files) {
            if (files.length === 0) return;
            
            const file = files[0];
            const maxSize = 100 * 1024 * 1024; // 100MB
            
            // Validate file size
            if (file.size > maxSize) {
                alert('File size exceeds 100MB limit. Please select a smaller file.');
                fileInput.value = '';
                return;
            }
            
            // Validate file type
            const allowedExtensions = ['pdf', 'ppt', 'pptx', 'xls', 'xlsx', 'csv', 'epub', 'doc', 'docx', 'txt'];
            const fileExtension = file.name.split('.').pop().toLowerCase();
            
            if (!allowedExtensions.includes(fileExtension)) {
                alert('File type not allowed. Please select a supported file type.');
                fileInput.value = '';
                return;
            }
            
            // Update UI
            dropZone.classList.add('file-selected');
            dropZone.querySelector('.file-upload-text').textContent = file.name;
            dropZone.querySelector('.file-upload-hint').textContent = 
                `Size: ${formatFileSize(file.size)} • Type: ${fileExtension.toUpperCase()}`;
            dropZone.querySelector('.file-upload-icon i').className = 'fa fa-file-check';
        }
        
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
        
        // Form submission with progress indication
        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            const form = this;
            const formData = new FormData(form);
            const resourceType = document.getElementById('resource_type').value;
            
            // Validate resource type specific fields
            if (resourceType === 'file') {
                const fileInput = document.getElementById('fileInput');
                if (!fileInput.files.length) {
                    e.preventDefault();
                    alert('Please select a file to upload.');
                    return false;
                }
            } else if (resourceType === 'weblink') {
                const urlInput = document.getElementById('web_url');
                if (!urlInput.value.trim() || !isValidUrl(urlInput.value)) {
                    e.preventDefault();
                    alert('Please enter a valid web URL.');
                    return false;
                }
            }
            
            // Show progress bar for file uploads
            if (resourceType === 'file') {
                e.preventDefault();
                
                // Disable submit button
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<div class="spinner"></div> Uploading...';
                
                // Show progress bar
                progressSection.style.display = 'block';
                
                // Simulate upload progress (in real app, use XMLHttpRequest with progress event)
                let progress = 0;
                const interval = setInterval(() => {
                    progress += 10;
                    progressFill.style.width = progress + '%';
                    progressPercentage.textContent = progress + '%';
                    
                    if (progress >= 100) {
                        clearInterval(interval);
                        progressStatus.textContent = 'Processing...';
                        
                        // Submit form after progress completes
                        setTimeout(() => {
                            form.submit();
                        }, 500);
                    }
                }, 200);
            }
        });
        
        function isValidUrl(string) {
            try {
                new URL(string);
                return true;
            } catch (_) {
                return false;
            }
        }
        
        // Auto-hide flash messages after 5 seconds
        const flashMessages = document.querySelectorAll('.flash-message');
        flashMessages.forEach(message => {
            setTimeout(() => {
                if (message.parentElement) {
                    message.style.opacity = '0';
                    message.style.transform = 'translateY(-10px)';
                    setTimeout(() => {
                        if (message.parentElement) {
                            message.style.display = 'none';
                        }
                    }, 300);
                }
            }, 5000);
        });
        
        // Form validation
        function validateForm() {
            const title = document.querySelector('input[name="title"]').value.trim();
            const subject = document.querySelector('select[name="subject_id"]').value;
            const semester = document.querySelector('select[name="semester"]').value;
            const resourceType = document.getElementById('resource_type').value;
            
            // Basic validation
            if (!title || title.length < 3) {
                alert('Please enter a valid resource title (minimum 3 characters).');
                return false;
            }
            
            if (!subject) {
                alert('Please select a subject.');
                return false;
            }
            
            if (!semester) {
                alert('Please select a semester.');
                return false;
            }
            
            return true;
        }
    });
</script>

</body>
</html>