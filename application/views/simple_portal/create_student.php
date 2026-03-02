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
        :root {
            --primary-blue: #1f5ea8;
            --primary-dark: #0b2a4a;
            --primary-light: #114a7d;
            --success-green: #78b83f;
            --light-bg: #f8fafc;
            --white: #ffffff;
            --text-dark: #1e293b;
            --text-light: #64748b;
            --border-color: #e2e8f0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        body {
            background: var(--light-bg);
            color: var(--text-dark);
            min-height: 100vh;
        }

        .portal-container {
            display: flex;
            min-height: 100vh;
        }

        /* Topbar */
        .topbar {
            background: var(--white);
            padding: 0 30px;
            height: 80px; /* Standardized height */
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--border-color);
        }

        .page-title {
            font-size: 20px;
            font-weight: 600;
            color: var(--text-dark);
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 16px;
        }

        /* Page Header */
        .page-header {
            margin-bottom: 30px;
        }

        .page-header h1 {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        .page-header p {
            color: var(--text-light);
            font-size: 14px;
        }

        .form-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border-color);
        }

        /* Form Styles */
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
            padding: 10px 12px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 14px;
            color: var(--text-dark);
            background: var(--white);
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(31, 94, 168, 0.1);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        /* Form Sections */
        .form-section {
            margin-bottom: 30px;
            padding-bottom: 25px;
            border-bottom: 1px solid var(--border-color);
        }

        .form-section:last-child {
            border-bottom: none;
        }

        .form-section h5 {
            font-size: 16px;
            font-weight: 600;
            color: var(--primary-blue);
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-section h5 i {
            color: var(--primary-blue);
        }

        /* Flash Messages */
        .flash-message {
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
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

        .flash-message .close-btn {
            margin-left: auto;
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
            color: inherit;
            opacity: 0.7;
        }

        .flash-message .close-btn:hover {
            opacity: 1;
        }

        /* Form Actions */
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 20px;
            margin-top: 40px;
            padding-top: 25px;
            border-top: 1px solid var(--border-color);
        }

        .btn {
            padding: 14px 28px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 160px;
            justify-content: center;
        }

        .btn-primary {
            background: var(--success-green);
            color: var(--white);
        }

        .btn-primary:hover {
            background: #6ca736;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(120, 184, 63, 0.3);
        }

        .btn-secondary {
            background: var(--white);
            color: var(--text-light);
            border: 1px solid var(--border-color);
        }
        
        .btn-secondary:hover {
            background: #f1f5f9;
            border-color: var(--text-light);
            transform: translateY(-2px);
        }

        /* Password Input */
        .password-input {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-light);
            cursor: pointer;
        }

        .form-hint {
            font-size: 12px;
            color: var(--text-light);
            margin-top: 4px;
        }

        /* Required field indicator */
        .required:after {
            content: " *";
            color: #dc2626;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
                padding: 20px 10px;
            }
            
            .brand h1, .nav-title, .nav-item span {
                display: none;
            }
            
            .nav-item i {
                margin-right: 0;
                font-size: 18px;
            }
            
            .main-content {
                margin-left: 70px;
            }
            
            .content-area {
                padding: 20px;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .form-container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>

<div class="portal-container">

    <!-- Sidebar -->
    <?php $this->load->view('simple_portal/components/admin_sidebar', ['active_page' => 'create_student']); ?>

    <!-- Main Content -->
    <div class="main-content">

        <!-- Topbar -->
        <header class="topbar">
            <div class="page-title">
                Create Faculty Account
            </div>

            <div class="user-profile">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($username, 0, 1)); ?>
                </div>
                <div>
                    <div style="font-weight: 600; font-size: 14px;"><?php echo htmlspecialchars($username); ?></div>
                    <div style="font-size: 12px; color: var(--text-light);">ADMIN</div>
                </div>
            </div>
        </header>

        <!-- Content Area -->
        <main class="content-area">

            <!-- Page Header -->
            <div class="dashboard-header">
                <div class="header-title">
                    <h1>Create Student Account</h1>
                    <p>Add new Student to the SLAi academic system</p>
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
                        Student Registration Form
                    </div>
                </div>
                
                <div class="card-body" style="padding: 40px;">
                    <form id="facultyForm" method="POST" class="faculty-form">
                        <input type="hidden" name="action" value="create_faculty">
                    
                    <!-- Section 1: Personal Information -->
                    <div class="form-section">
                        <h5><i class="fa fa-user-circle"></i> Personal Information</h5>
                        
                        <div class="form-group">
                            <label class="form-label required">Username</label>
                            <input type="text" name="username" class="form-control" 
                                   placeholder="Enter username (e.g., john.doe)" 
                                   pattern="[A-Za-z0-9._-]{3,50}" 
                                   title="3-50 characters: letters, numbers, dots, hyphens, underscores"
                                   required>
                            <div class="form-hint">Will be used for login (letters, numbers, . _ - only)</div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label required">First Name</label>
                                <input type="text" name="first_name" class="form-control" 
                                       placeholder="Enter first name" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label required">Last Name</label>
                                <input type="text" name="last_name" class="form-control" 
                                       placeholder="Enter last name" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label required">Email Address</label>
                            <input type="email" name="email" class="form-control" 
                                   placeholder="faculty@college.edu" required>
                            <div class="form-hint">Official institutional email address</div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Date of Birth</label>
                                <input type="date" name="date_of_birth" class="form-control" 
                                       max="<?php echo date('Y-m-d', strtotime('-22 years')); ?>">
                                <div class="form-hint">Must be at least 22 years old</div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Gender</label>
                                <select name="gender" class="form-control">
                                    <option value="">Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                    <option value="Prefer not to say">Prefer not to say</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label required">Phone Number</label>
                                <input type="tel" name="phone" class="form-control" 
                                       placeholder="+1 (555) 123-4567" 
                                       pattern="[+0-9\s\-\(\)]{10,20}" required>
                                <div class="form-hint">Include country code, digits only</div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Alternate Phone</label>
                                <input type="tel" name="alternate_phone" class="form-control" 
                                       placeholder="+1 (555) 987-6543">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control" 
                                      placeholder="Enter complete address" rows="2"></textarea>
                        </div>
                    </div>

                    <!-- Section 2: Professional Information -->
                    <div class="form-section">
                        <h5><i class="fa fa-briefcase"></i> Professional Information</h5>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label required">Employee ID</label>
                                <input type="text" name="employee_id" class="form-control" 
                                       placeholder="EMP00123" 
                                       pattern="[A-Za-z0-9]{6,20}" 
                                       title="6-20 characters: letters and numbers only"
                                       required>
                                <div class="form-hint">Unique employee identification number</div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label required">Department</label>
                                <select name="department" class="form-control" required>
                                    <option value="">Select Department</option>
                                    <option value="Artificial Intelligence">Artificial Intelligence</option>
                                    <option value="Computer Science">Computer Science</option>
                                    <option value="Machine Learning">Machine Learning</option>
                                    <option value="Data Science">Data Science</option>
                                    <option value="Mathematics">Mathematics</option>
                                    <option value="Physics">Physics</option>
                                    <option value="Chemistry">Chemistry</option>
                                    <option value="Electrical Engineering">Electrical Engineering</option>
                                    <option value="Mechanical Engineering">Mechanical Engineering</option>
                                    <option value="Civil Engineering">Civil Engineering</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label required">Designation</label>
                                <select name="designation" class="form-control" required>
                                    <option value="">Select Designation</option>
                                    <option value="Professor">Professor</option>
                                    <option value="Associate Professor">Associate Professor</option>
                                    <option value="Assistant Professor">Assistant Professor</option>
                                    <option value="Senior Lecturer">Senior Lecturer</option>
                                    <option value="Lecturer">Lecturer</option>
                                    <option value="Visiting Faculty">Visiting Faculty</option>
                                    <option value="Adjunct Faculty">Adjunct Faculty</option>
                                    <option value="Research Scholar">Research Scholar</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Joining Date</label>
                                <input type="date" name="joining_date" class="form-control" 
                                       max="<?php echo date('Y-m-d'); ?>">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Highest Qualification</label>
                                <select name="highest_qualification" class="form-control">
                                    <option value="">Select Qualification</option>
                                    <option value="PhD">PhD</option>
                                    <option value="M.Tech">M.Tech / M.E.</option>
                                    <option value="M.Sc">M.Sc</option>
                                    <option value="B.Tech">B.Tech / B.E.</option>
                                    <option value="M.Phil">M.Phil</option>
                                    <option value="MBA">MBA</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Specialization</label>
                                <input type="text" name="specialization" class="form-control" 
                                       placeholder="Research/Teaching specialization">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Previous Institution</label>
                            <input type="text" name="previous_institution" class="form-control" 
                                   placeholder="Previous university/institution">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Research Interests</label>
                            <textarea name="research_interests" class="form-control" 
                                      placeholder="Areas of research interest (comma separated)" rows="2"></textarea>
                            <div class="form-hint">Separate multiple interests with commas</div>
                        </div>
                    </div>

                    <!-- Section 3: Academic Information -->
                    <div class="form-section">
                        <h5><i class="fa fa-graduation-cap"></i> Academic Information</h5>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Total Experience (Years)</label>
                                <input type="number" name="total_experience" class="form-control" 
                                       min="0" max="50" step="0.5"
                                       placeholder="e.g., 5.5">
                                <div class="form-hint">Years of teaching/research experience</div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Publications Count</label>
                                <input type="number" name="publications_count" class="form-control" 
                                       min="0" placeholder="Number of publications">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Subjects Expertise</label>
                            <textarea name="subjects_expertise" class="form-control" 
                                      placeholder="Subjects you can teach (comma separated)" rows="2"></textarea>
                            <div class="form-hint">Example: AI, Machine Learning, Data Structures, Algorithms</div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Office Room</label>
                                <input type="text" name="office_room" class="form-control" 
                                       placeholder="e.g., CS-302">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Office Hours</label>
                                <input type="text" name="office_hours" class="form-control" 
                                       placeholder="e.g., Mon-Wed 2-4 PM">
                            </div>
                        </div>
                    </div>

                    <!-- Section 4: Security Information -->
                    <div class="form-section">
                        <h5><i class="fa fa-lock"></i> Security Information</h5>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label required">Password</label>
                                <div class="password-input">
                                    <input type="password" id="password" name="password" class="form-control" 
                                           placeholder="Create a strong password" 
                                           pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d@$!%*?&]{8,}$"
                                           title="Minimum 8 characters with uppercase, lowercase, and number"
                                           required>
                                    <button type="button" class="toggle-password" onclick="togglePassword('password')">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                </div>
                                <div class="form-hint">Min 8 chars with uppercase, lowercase, and number</div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label required">Confirm Password</label>
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

                    <!-- Section 5: Additional Information -->
                    <div class="form-section">
                        <h5><i class="fa fa-info-circle"></i> Additional Information</h5>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Blood Group</label>
                                <select name="blood_group" class="form-control">
                                    <option value="">Select Blood Group</option>
                                    <option value="A+">A+</option>
                                    <option value="A-">A-</option>
                                    <option value="B+">B+</option>
                                    <option value="B-">B-</option>
                                    <option value="O+">O+</option>
                                    <option value="O-">O-</option>
                                    <option value="AB+">AB+</option>
                                    <option value="AB-">AB-</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Emergency Contact</label>
                                <input type="tel" name="emergency_contact" class="form-control" 
                                       placeholder="+1 (555) 123-4567">
                                <div class="form-hint">Contact in case of emergency</div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Medical Conditions</label>
                            <textarea name="medical_conditions" class="form-control" 
                                      placeholder="Any medical conditions or allergies" rows="2"></textarea>
                        </div>

                        <div class="form-group">
                            <label class="checkbox-label" style="display: flex; align-items: center; gap: 8px;">
                                <input type="checkbox" name="terms_accepted" required style="width: auto;">
                                <span>I confirm that all information provided is accurate and accept the terms of employment *</span>
                            </label>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <button type="reset" class="btn-secondary">
                            <i class="fa fa-undo"></i> Reset Form
                        </button>
                        <button type="submit" class="btn-primary">
                            <i class="fa fa-user-plus"></i> Create Student Account
                        </button>
                    </div>
                </form>
                </div> <!-- End card-body -->
            </div> <!-- End main-card -->

        </main>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('facultyForm');
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');
        const passwordMatchMessage = document.getElementById('passwordMatchMessage');
        
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
                    passwordMatchMessage.style.color = "#16a34a";
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
                alert('Please fix the password mismatch before submitting.');
                return false;
            }
            
            // Validate password strength
            const passwordValue = password.value;
            const strongRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d@$!%*?&]{8,}$/;
            
            if (!strongRegex.test(passwordValue)) {
                alert('Password must be at least 8 characters long and contain uppercase, lowercase letters and a number.');
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
            }, 500);
        });
        
        // Set max date for date of birth (minimum 22 years old)
        const dobField = document.querySelector('input[name="date_of_birth"]');
        if (dobField) {
            const maxDate = new Date();
            maxDate.setFullYear(maxDate.getFullYear() - 22);
            dobField.max = maxDate.toISOString().split('T')[0];
        }
        
        // Set max date for joining date (today or earlier)
        const joiningDateField = document.querySelector('input[name="joining_date"]');
        if (joiningDateField) {
            joiningDateField.max = new Date().toISOString().split('T')[0];
        }
    });
</script>

</body>
</html>