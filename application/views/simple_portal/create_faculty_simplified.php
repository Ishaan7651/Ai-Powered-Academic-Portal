<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Faculty - SLAi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <?php $this->load->view('simple_portal/components/admin_sidebar_css'); ?>
    <?php $this->load->view('simple_portal/components/admin_content_css'); ?>
    <style>
        /* Page Specific Styles */
        
        /* Topbar - Standardized */
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
            z-index: 10;
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

        /* Tabs Styling */
        .tabs-container {
            margin-bottom: 30px;
            border-bottom: 1px solid var(--border-color);
        }

        .tabs {
            display: flex;
            gap: 20px;
        }

        .tab {
            padding: 12px 5px;
            background: none;
            border: none;
            border-bottom: 3px solid transparent;
            color: var(--text-light);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: -1px;
        }

        .tab:hover {
            color: var(--primary-blue);
        }

        .tab.active {
            color: var(--primary-blue);
            border-bottom-color: var(--primary-blue);
        }

        .tab-content {
            display: none;
            animation: fadeIn 0.3s ease;
        }

        .tab-content.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Form Styling - Refined for Card */
        .form-container {
            /* Resetting simplified styles */
            background: transparent;
            border-radius: 0;
            padding: 0;
            box-shadow: none;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-label {
            display: block;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 10px;
            font-size: 14px;
        }

        .required:after {
            content: " *";
            color: #dc2626;
        }

        .form-control {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid var(--border-color);
            border-radius: 12px;
            font-size: 14px;
            color: var(--text-dark);
            background: var(--white);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 4px rgba(31, 94, 168, 0.15);
        }

        .form-hint {
            font-size: 12px;
            color: var(--text-light);
            margin-top: 8px;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
        }

        .btn {
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-blue), var(--primary-dark));
            color: white;
            box-shadow: 0 4px 12px rgba(31, 94, 168, 0.2);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(31, 94, 168, 0.3);
        }

        .btn-secondary {
            background: white;
            color: var(--text-dark);
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover {
            background: #f1f5f9;
            border-color: var(--text-light);
        }

        /* Upload Area */
        .upload-area {
            border: 2px dashed var(--border-color);
            border-radius: 18px;
            padding: 40px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            background: #f8fafc;
        }

        .upload-area:hover {
            border-color: var(--primary-blue);
            background: rgba(31, 94, 168, 0.05);
        }

        .upload-area.dragover {
            border-color: var(--primary-blue);
            background: rgba(31, 94, 168, 0.1);
            transform: scale(1.02);
        }

        .upload-icon {
            font-size: 48px;
            color: var(--primary-blue);
            margin-bottom: 20px;
            background: rgba(31, 94, 168, 0.1);
            width: 80px;
            height: 80px;
            line-height: 80px;
            border-radius: 50%;
            display: inline-block;
        }

        .upload-text {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        /* Template Box */
        .template-box {
            background: linear-gradient(to right, #eff6ff, #fcfaff);
            border: 1px solid #bfdbfe;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .template-info h4 {
            color: var(--primary-blue);
            margin-bottom: 5px;
            font-size: 16px;
            font-weight: 700;
        }

        .template-info p {
            color: var(--text-light);
            font-size: 14px;
            margin: 0;
        }

        /* Flash Messages */
        .flash-message {
            padding: 16px 24px;
            border-radius: 12px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            font-weight: 500;
            animation: slideIn 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .flash-message.success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .flash-message.error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        @keyframes slideIn {
            from { transform: translateY(-10px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>
</head>
<body>

<div class="portal-container">

    <!-- Sidebar Component -->
    <?php $this->load->view('simple_portal/components/admin_sidebar', ['active_page' => 'create_faculty']); ?>

    <!-- Main Content -->
    <div class="main-content">
        
        <!-- Topbar -->
        <header class="topbar">
            <div class="page-title">
                Create Faculty Account
            </div>

            <div class="user-profile">
                <div class="user-avatar">
                    <?php echo isset($username) ? strtoupper(substr($username, 0, 1)) : 'A'; ?>
                </div>
                <div class="user-info">
                    <span class="user-name"><?php echo isset($username) ? htmlspecialchars($username) : 'Admin'; ?></span>
                    <span class="user-role">Administrator</span>
                </div>
            </div>
        </header>

        <div class="content-area">
            
            <!-- Dashboard Header -->
            <div class="dashboard-header">
                <div class="header-title">
                    <h1>Create Faculty Account</h1>
                    <p>Add faculty members individually or upload multiple faculty via CSV/Excel</p>
                </div>
            </div>

            <!-- Flash Messages -->
            <?php if ($this->session->flashdata('message')): ?>
                <div class="flash-message <?php echo $this->session->flashdata('message_type'); ?>">
                    <i class="fa fa-<?php echo $this->session->flashdata('message_type') === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                    <span><?php echo $this->session->flashdata('message'); ?></span>
                </div>
            <?php endif; ?>
            
            <?php if ($this->session->flashdata('errors')): ?>
                <div class="flash-message error">
                    <div style="flex: 1;">
                        <strong>Upload Errors:</strong>
                        <ul style="margin: 10px 0 0 20px;">
                            <?php foreach ($this->session->flashdata('errors') as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Main Card -->
            <div class="main-card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fas fa-chalkboard-teacher"></i>
                        Faculty Registration
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Tabs -->
                    <div class="tabs-container">
                        <div class="tabs">
                            <button class="tab active" onclick="switchTab('single')">
                                <i class="fas fa-user-plus"></i> Single Faculty
                            </button>
                            <button class="tab" onclick="switchTab('bulk')">
                                <i class="fas fa-file-upload"></i> Bulk Upload
                            </button>
                        </div>
                    </div>

                    <!-- Single Faculty Form -->
                    <div id="single-tab" class="tab-content active">
                        <div class="form-container">
                            <form method="POST" action="<?php echo base_url('simple_portal/create_faculty'); ?>">
                                <input type="hidden" name="action" value="create_faculty">
                                
                                <div class="form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                    <div class="form-group">
                                        <label class="form-label required">Username</label>
                                        <input type="text" name="username" class="form-control" 
                                               placeholder="e.g., dr.smith" 
                                               pattern="[A-Za-z0-9._-]{3,50}" 
                                               required>
                                        <div class="form-hint">3-50 characters: letters, numbers, dots, hyphens, underscores</div>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label required">Email Address</label>
                                        <input type="email" name="email" class="form-control" 
                                               placeholder="faculty@college.edu" 
                                               required>
                                        <div class="form-hint">Faculty's institutional email address</div>
                                    </div>
                                </div>

                                <div class="form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                    <div class="form-group">
                                        <label class="form-label required">Password</label>
                                        <input type="password" name="password" class="form-control" 
                                               placeholder="Create a strong password" 
                                               minlength="6"
                                               required>
                                        <div class="form-hint">Minimum 6 characters</div>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Employee ID</label>
                                        <input type="text" name="employee_id" class="form-control" 
                                               placeholder="e.g., FAC2024 (optional)">
                                        <div class="form-hint">Unique employee identification number (optional)</div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Departments (Select multiple)</label>
                                    <select name="department_ids[]" class="form-control" multiple size="5" style="height: auto;">
                                        <?php if (!empty($departments)): ?>
                                            <?php foreach ($departments as $dept): ?>
                                                <option value="<?php echo $dept->id; ?>">
                                                    <?php echo htmlspecialchars($dept->name); ?> (<?php echo htmlspecialchars($dept->code); ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <div class="form-hint">Hold Ctrl (Windows) or Cmd (Mac) to select multiple departments</div>
                                </div>

                                <div class="form-actions">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-user-plus"></i> Create Faculty
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Bulk Upload Form -->
                    <div id="bulk-tab" class="tab-content">
                        <div class="form-container">
                            <!-- Template Download -->
                            <div class="template-box">
                                <div class="template-info">
                                    <h4><i class="fas fa-download"></i> Download Template</h4>
                                    <p>Download the CSV template, fill in faculty details, and upload it below.</p>
                                </div>
                                <a href="<?php echo base_url('simple_portal/download_faculty_template'); ?>" class="btn btn-primary" style="text-decoration: none;">
                                    <i class="fas fa-file-csv"></i> Download CSV
                                </a>
                            </div>

                            <!-- Upload Form -->
                            <form method="POST" action="<?php echo base_url('simple_portal/bulk_upload_faculty'); ?>" enctype="multipart/form-data" id="bulkUploadForm">
                                <input type="hidden" name="action" value="bulk_upload">
                                
                                <div class="upload-area" id="uploadArea" onclick="document.getElementById('fileInput').click()">
                                    <div class="upload-icon">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                    </div>
                                    <div class="upload-text">Click to upload or drag and drop</div>
                                    <div class="upload-hint">CSV or Excel files (Max 5MB)</div>
                                    <input type="file" id="fileInput" name="faculty_file" class="file-input" accept=".csv,.xlsx,.xls" required style="display: none;">
                                </div>

                                <div id="fileInfo" style="margin-top: 15px; display: none;">
                                    <p><strong>Selected file:</strong> <span id="fileName"></span></p>
                                </div>

                                <div class="form-actions">
                                    <button type="button" class="btn btn-secondary" onclick="resetUpload()">
                                        <i class="fas fa-times"></i> Clear
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-upload"></i> Upload Faculty
                                    </button>
                                </div>
                            </form>

                            <!-- Instructions -->
                            <div style="margin-top: 30px; padding: 25px; background: #f8fafc; border-radius: 12px; border: 1px solid var(--border-color);">
                                <h4 style="margin-bottom: 15px; color: var(--text-dark); font-size: 16px;">CSV Format Instructions:</h4>
                                <ul style="color: var(--text-light); line-height: 1.8; font-size: 14px; padding-left: 20px;">
                                    <li><strong>Required columns:</strong> username, email, password</li>
                                    <li><strong>Optional columns:</strong> employee_id, department</li>
                                    <li><strong>Username:</strong> Must be unique, 3-50 characters</li>
                                    <li><strong>Email:</strong> Must be valid email format</li>
                                    <li><strong>Password:</strong> Minimum 6 characters</li>
                                    <li><strong>Example:</strong> dr.smith,smith@college.edu,SecurePass24,FAC2024,Computer Science</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                </div> <!-- End card-body -->
            </div> <!-- End main-card -->

        </div> <!-- End content-area -->
    </div> <!-- End main-content -->
</div> <!-- End portal-container -->

<script>
function switchTab(tab) {
    // Update tab buttons
    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
    event.target.closest('.tab').classList.add('active');
    
    // Update tab content
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
    document.getElementById(tab + '-tab').classList.add('active');
}

// File upload handling
const uploadArea = document.getElementById('uploadArea');
const fileInput = document.getElementById('fileInput');
const fileInfo = document.getElementById('fileInfo');
const fileName = document.getElementById('fileName');

fileInput.addEventListener('change', function() {
    if (this.files.length > 0) {
        fileName.textContent = this.files[0].name;
        fileInfo.style.display = 'block';
    }
});

// Drag and drop
uploadArea.addEventListener('dragover', function(e) {
    e.preventDefault();
    this.classList.add('dragover');
});

uploadArea.addEventListener('dragleave', function() {
    this.classList.remove('dragover');
});

uploadArea.addEventListener('drop', function(e) {
    e.preventDefault();
    this.classList.remove('dragover');
    
    if (e.dataTransfer.files.length > 0) {
        fileInput.files = e.dataTransfer.files;
        fileName.textContent = e.dataTransfer.files[0].name;
        fileInfo.style.display = 'block';
    }
});

function resetUpload() {
    fileInput.value = '';
    fileInfo.style.display = 'none';
}
</script>

</body>
</html>
