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
        /* Topbar - Same as Admin Dashboard */
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

        /* Form Header - Specific to this page inside card-body if needed, or similar to card-header */
        .form-header {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border-color);
        }

        .form-header h3 {
            font-size: 22px;
            font-weight: 700;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 8px;
        }

        .form-header h3 i {
            color: var(--primary-blue);
            font-size: 20px;
        }

        .form-header p {
            color: var(--text-light);
            font-size: 14px;
            line-height: 1.5;
        }

        /* Form Sections */
        .form-section {
            margin-bottom: 40px;
            padding-bottom: 30px;
            border-bottom: 1px solid var(--border-color);
        }

        .form-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title i {
            color: var(--primary-blue);
            background: rgba(59, 130, 246, 0.1);
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Form Grid */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
        }

        .form-group {
            margin-bottom: 0;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        /* Form Labels */
        .form-label {
            display: block;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 10px;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .form-label .required {
            color: #dc2626;
            font-size: 12px;
            font-weight: 600;
        }

        /* Form Controls */
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

        .form-control::placeholder {
            color: var(--text-light);
            opacity: 0.7;
        }

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%2364748b' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 16px center;
            background-size: 16px;
            padding-right: 40px;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }

        /* Password Input */
        .password-input {
            position: relative;
        }

        .password-input .form-control {
            padding-right: 50px;
        }

        .toggle-password {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-light);
            cursor: pointer;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.3s ease;
        }

        .toggle-password:hover {
            color: var(--primary-blue);
        }

        /* Form Hints */
        .form-hint {
            font-size: 12px;
            color: var(--text-light);
            margin-top: 8px;
            line-height: 1.4;
        }

        /* Checkbox */
        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            font-size: 14px;
            color: var(--text-dark);
        }

        .checkbox-label input[type="checkbox"] {
            width: 18px;
            height: 18px;
            border: 2px solid var(--border-color);
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .checkbox-label input[type="checkbox"]:checked {
            background-color: var(--primary-blue);
            border-color: var(--primary-blue);
        }

        /* Flash Messages - Same as Admin Dashboard */
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
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .flash-message.success {
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
            color: #065f46;
            border: 1px solid #34d399;
        }

        .flash-message.error {
            background: linear-gradient(135deg, #fee2e2, #fca5a5);
            color: #991b1b;
            border: 1px solid #f87171;
        }

        .flash-message .close-btn {
            margin-left: auto;
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
            color: inherit;
            opacity: 0.7;
            transition: opacity 0.3s;
            padding: 4px;
            border-radius: 4px;
        }

        .flash-message .close-btn:hover {
            opacity: 1;
            background: rgba(0, 0, 0, 0.1);
        }

        /* Form Actions */
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 20px;
            margin-top: 50px;
            padding-top: 30px;
            border-top: 1px solid var(--border-color);
        }

        .btn {
            padding: 16px 32px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 180px;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--success-green), #6ca736);
            color: var(--white);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #6ca736, #5a942f);
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(120, 184, 63, 0.4);
        }

        .btn-primary:active {
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: linear-gradient(135deg, var(--white), #f8fafc);
            color: var(--text-dark);
            border: 2px solid var(--border-color);
        }

        .btn-secondary:hover {
            background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
            border-color: var(--text-light);
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .sidebar {
                width: 80px;
            }
            
            .brand h1, .nav-title, .nav-item span {
                display: none;
            }
            
            .nav-item i {
                margin-right: 0;
                font-size: 20px;
            }
            
            .main-content {
                margin-left: 80px;
            }
            
            .content-area {
                padding: 25px;
            }
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .dashboard-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 20px;
            }
            
            .topbar {
                padding: 0 20px;
                height: 70px;
            }
            
            .content-area {
                padding: 20px;
            }
            
            .form-container {
                padding: 25px;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .btn {
                min-width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .content-area {
                padding: 20px 15px;
            }
            
            .header-title h1 {
                font-size: 24px;
            }
            
            .form-header h3 {
                font-size: 18px;
            }
            
            .section-title {
                font-size: 16px;
            }
        }

        /* Animations */
        @keyframes slideIn {
            from {
                transform: translateY(-10px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Password Strength */
        .password-strength {
            margin-top: 8px;
        }

        .strength-bar {
            height: 6px;
            background: var(--border-color);
            border-radius: 3px;
            overflow: hidden;
            margin-bottom: 4px;
        }

        .strength-progress {
            height: 100%;
            width: 0;
            background: #dc2626;
            border-radius: 3px;
            transition: all 0.3s ease;
        }

        .strength-text {
            font-size: 11px;
            color: var(--text-light);
            text-align: right;
        }
    </style>
</head>
<body>

<div class="portal-container">

    <!-- Sidebar -->
    <?php $this->load->view('simple_portal/components/admin_sidebar', ['active_page' => 'create_faculty']); ?>

    <!-- Main Content -->
    <div class="main-content">

        <!-- Topbar - Same as Admin Dashboard -->
        <header class="topbar">
            <div class="page-title">
                Create Faculty Account
            </div>

            <div class="user-profile">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($username, 0, 1)); ?>
                </div>
                <div class="user-info">
                    <div class="user-name"><?php echo htmlspecialchars($username); ?></div>
                    <div class="user-role">ADMIN</div>
                </div>
            </div>
        </header>

        <!-- Content Area -->
        <main class="content-area">

            <!-- Dashboard Header - Same Style -->
            <div class="dashboard-header">
                <div class="header-title">
                    <h1>Create Faculty Account</h1>
                    <p>Add new Faculty members to the SLAi academic system</p>
                </div>
            </div>

            <!-- Flash Messages -->
            <?php if ($this->session->flashdata('message')): ?>
                <div class="flash-message <?php echo $this->session->flashdata('message_type'); ?>">
                    <i class="fa fa-<?php echo $this->session->flashdata('message_type') === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                    <span><?php echo $this->session->flashdata('message'); ?></span>
                    <button class="close-btn" onclick="this.parentElement.remove()">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            <?php endif; ?>

            <!-- Main Form Card -->
            <div class="main-card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fa fa-user-plus"></i>
                        Faculty Registration Form
                    </div>
                    <div class="card-subtitle">Please fill in the faculty details below</div>
                </div>
                
                <div class="card-body" style="padding: 40px;">
                    <form id="facultyForm" method="POST" class="faculty-form">
                        <input type="hidden" name="action" value="create_faculty">
                    
                    <!-- Section 1: Personal Information -->
                    <div class="form-section">
                        <div class="section-title">
                            <i class="fa fa-user-circle"></i>
                            <span>Personal Information</span>
                        </div>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">
                                    Username
                                    <span class="required">* Required</span>
                                </label>
                                <input type="text" name="username" class="form-control" 
                                       placeholder="e.g., john.doe" required>
                                <div class="form-hint">Will be used for login system</div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    Email Address
                                    <span class="required">* Required</span>
                                </label>
                                <input type="email" name="email" class="form-control" 
                                       placeholder="faculty@college.edu" required>
                                <div class="form-hint">Official institutional email</div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    First Name
                                    <span class="required">* Required</span>
                                </label>
                                <input type="text" name="first_name" class="form-control" 
                                       placeholder="Enter first name" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">
                                    Last Name
                                    <span class="required">* Required</span>
                                </label>
                                <input type="text" name="last_name" class="form-control" 
                                       placeholder="Enter last name" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Phone Number</label>
                                <input type="tel" name="phone" class="form-control" 
                                       placeholder="+1 (555) 123-4567">
                                <div class="form-hint">Include country code</div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Gender</label>
                                <select name="gender" class="form-control">
                                    <option value="">Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            <div class="form-group full-width">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control" 
                                          placeholder="Enter complete address" rows="2"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Professional Information -->
                    <div class="form-section">
                        <div class="section-title">
                            <i class="fa fa-briefcase"></i>
                            <span>Professional Information</span>
                        </div>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">
                                    Employee ID
                                    <span class="required">* Required</span>
                                </label>
                                <input type="text" name="employee_id" class="form-control" 
                                       placeholder="EMP00123" required>
                                <div class="form-hint">Unique employee identification</div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">
                                    Department
                                    <span class="required">* Required</span>
                                </label>
                                <select name="department_id" class="form-control" required>
                                    <option value="">Select Department</option>
                                    <?php if (!empty($departments)): ?>
                                        <?php foreach ($departments as $dept): ?>
                                            <option value="<?php echo $dept->id; ?>">
                                                <?php echo htmlspecialchars($dept->name); ?> (<?php echo htmlspecialchars($dept->code); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    Designation
                                    <span class="required">* Required</span>
                                </label>
                                <select name="designation" class="form-control" required>
                                    <option value="">Select Designation</option>
                                    <option value="Professor">Professor</option>
                                    <option value="Associate Professor">Associate Professor</option>
                                    <option value="Assistant Professor">Assistant Professor</option>
                                    <option value="Lecturer">Lecturer</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Joining Date</label>
                                <input type="date" name="joining_date" class="form-control">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Highest Qualification</label>
                                <select name="highest_qualification" class="form-control">
                                    <option value="">Select Qualification</option>
                                    <option value="PhD">PhD</option>
                                    <option value="M.Tech">M.Tech</option>
                                    <option value="M.Sc">M.Sc</option>
                                    <option value="B.Tech">B.Tech</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Specialization</label>
                                <input type="text" name="specialization" class="form-control" 
                                       placeholder="Research specialization">
                            </div>

                            <div class="form-group full-width">
                                <label class="form-label">Subjects Expertise</label>
                                <textarea name="subjects_expertise" class="form-control" 
                                          placeholder="Subjects you can teach (comma separated)" rows="2"></textarea>
                                <div class="form-hint">Example: AI, Machine Learning, Data Structures</div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Security Information -->
                    <div class="form-section">
                        <div class="section-title">
                            <i class="fa fa-lock"></i>
                            <span>Security Information</span>
                        </div>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">
                                    Password
                                    <span class="required">* Required</span>
                                </label>
                                <div class="password-input">
                                    <input type="password" id="password" name="password" class="form-control" 
                                           placeholder="Create a strong password" required>
                                    <button type="button" class="toggle-password" onclick="togglePassword('password')">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                </div>
                                <div class="password-strength">
                                    <div class="strength-bar">
                                        <div class="strength-progress" id="passwordStrength"></div>
                                    </div>
                                    <div class="strength-text" id="passwordStrengthText">Password strength</div>
                                </div>
                                <div class="form-hint">Minimum 8 characters with uppercase, lowercase and number</div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">
                                    Confirm Password
                                    <span class="required">* Required</span>
                                </label>
                                <div class="password-input">
                                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" 
                                           placeholder="Re-enter password" required>
                                    <button type="button" class="toggle-password" onclick="togglePassword('confirm_password')">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                </div>
                                <div class="form-hint" id="passwordMatchMessage"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 4: Agreement -->
                    <div class="form-section">
                        <div class="section-title">
                            <i class="fa fa-file-signature"></i>
                            <span>Agreement</span>
                        </div>
                        
                        <div class="form-grid">
                            <div class="form-group full-width">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="terms_accepted" required>
                                    <span>I confirm that all information provided is accurate and accept the terms of employment</span>
                                    <span class="required">* Required</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <button type="reset" class="btn-secondary">
                            <i class="fa fa-undo"></i> Reset Form
                        </button>
                        <button type="submit" class="btn-primary">
                            <i class="fa fa-user-plus"></i> Create Faculty Account
                        </button>
                    </div>
                </form>
                </div> <!-- End card-body -->
            </div> <!-- End main-card -->

        </main>
    </div>
</div>

<script>
    // Same JavaScript functions as admin_dashboard
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('facultyForm');
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');
        const passwordMatchMessage = document.getElementById('passwordMatchMessage');
        const strengthProgress = document.getElementById('passwordStrength');
        const strengthText = document.getElementById('passwordStrengthText');
        
        // Password strength checker
        function checkPasswordStrength(password) {
            let strength = 0;
            if (password.length >= 8) strength += 25;
            if (/[a-z]/.test(password)) strength += 25;
            if (/[A-Z]/.test(password)) strength += 25;
            if (/[0-9]/.test(password)) strength += 25;
            
            return Math.min(strength, 100);
        }
        
        // Update password strength
        password.addEventListener('input', function() {
            const strength = checkPasswordStrength(this.value);
            strengthProgress.style.width = strength + '%';
            
            if (strength < 50) {
                strengthProgress.style.background = '#dc2626';
                strengthText.textContent = 'Weak password';
            } else if (strength < 75) {
                strengthProgress.style.background = '#f59e0b';
                strengthText.textContent = 'Moderate password';
            } else {
                strengthProgress.style.background = '#10b981';
                strengthText.textContent = 'Strong password';
            }
        });
        
        // Password validation
        function validatePassword() {
            const passwordValue = password.value;
            const confirmValue = confirmPassword.value;
            
            if (passwordValue && confirmValue) {
                if (passwordValue !== confirmValue) {
                    passwordMatchMessage.textContent = "Passwords do not match!";
                    passwordMatchMessage.style.color = "#dc2626";
                    return false;
                } else {
                    passwordMatchMessage.textContent = "Passwords match ✓";
                    passwordMatchMessage.style.color = "#10b981";
                    return true;
                }
            }
            return true;
        }
        
        // Real-time password validation
        password.addEventListener('input', validatePassword);
        confirmPassword.addEventListener('input', validatePassword);
        
        // Toggle password visibility
        window.togglePassword = function(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = field.nextElementSibling.querySelector('i');
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.className = 'fa fa-eye-slash';
            } else {
                field.type = 'password';
                icon.className = 'fa fa-eye';
            }
        };
        
        // Form submission
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!validatePassword()) {
                showNotification('Please fix the password mismatch before submitting.', 'error');
                return false;
            }
            
            // Validate password strength
            const passwordValue = password.value;
            const strongRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d@$!%*?&]{8,}$/;
            
            if (!strongRegex.test(passwordValue)) {
                showNotification('Password must be at least 8 characters long and contain uppercase, lowercase letters and a number.', 'error');
                return false;
            }
            
            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Creating...';
            submitBtn.disabled = true;
            
            // Submit the form
            setTimeout(() => {
                form.submit();
            }, 1000);
        });
        
        // Set max date for joining date (today or earlier)
        const joiningDateField = document.querySelector('input[name="joining_date"]');
        if (joiningDateField) {
            joiningDateField.max = new Date().toISOString().split('T')[0];
        }
    });
    
    // Notification function (same as admin_dashboard)
    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = 'flash-message ' + type;
        notification.innerHTML = `
            <i class="fa fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
            <span>${message}</span>
            <button class="close-btn" onclick="this.parentElement.remove()">
                <i class="fa fa-times"></i>
            </button>
        `;
        
        document.querySelector('.content-area').insertBefore(notification, document.querySelector('.form-container'));
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 5000);
    }
</script>

</body>
</html>