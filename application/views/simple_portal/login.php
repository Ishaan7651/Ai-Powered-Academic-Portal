<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Powered Academic Hub - UAI</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --primary-blue: #4A76A8;
            --primary-dark: #1D4486;
            --primary-light: #6B8BC3;
            --success-green: #759B49;
            --success-green-dark: #658a39;
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
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 900px;
        }

        /* Header with Logo */
        .header-logo {
            text-align: center;
            margin-bottom: 40px;
        }

        .logo-image {
            max-width: 180px;
            height: auto;
            margin-bottom: 20px;
        }

        .portal-title {
            font-size: 28px;
            font-weight: 600;
            color: var(--primary-dark);
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }

        .portal-subtitle {
            font-size: 16px;
            color: var(--text-light);
            font-weight: 500;
        }

        .main-card {
            background: var(--white);
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            border: 1px solid var(--border-color);
        }

        /* Role Selection */
        .role-selection {
            padding: 40px;
        }

        .section-title {
            text-align: center;
            font-size: 24px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 30px;
        }

        .roles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .role-card {
            background: var(--white);
            border: 2px solid var(--border-color);
            border-radius: 12px;
            padding: 30px 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .role-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #4A76A8, #1D4486);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .role-card:hover::before {
            transform: scaleX(1);
        }

        .role-card:hover {
            border-color: #4A76A8;
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(74, 118, 168, 0.15);
        }

        .role-card.selected {
            border-color: #4A76A8;
            background: linear-gradient(135deg, #f8faff, #f0f7ff);
        }

        .role-card.selected::before {
            transform: scaleX(1);
        }

        .role-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 15px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: var(--white);
            transition: all 0.3s ease;
        }

        .role-card:hover .role-icon {
            transform: scale(1.1);
        }

        .role-icon.admin { background: linear-gradient(135deg, #1D4486, #4A76A8); }
        .role-icon.faculty { background: linear-gradient(135deg, #759B49, #8BAD5A); }
        .role-icon.student { background: linear-gradient(135deg, #4A76A8, #6B8BC3); }

        .role-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        .role-description {
            font-size: 14px;
            color: var(--text-light);
            line-height: 1.4;
        }

        /* Login Form */
        .login-form {
            background: #f8faff;
            padding: 40px;
            border-top: 1px solid var(--border-color);
            display: none;
        }

        .login-form.active {
            display: block;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border-color);
        }

        .login-title {
            font-size: 20px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        .login-subtitle {
            font-size: 14px;
            color: var(--text-light);
            font-weight: 500;
        }

        .role-indicator {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #1D4486, #4A76A8);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            margin-top: 10px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 8px;
            font-size: 14px;
            letter-spacing: 0.3px;
        }

        .form-input {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: #f9fbfd;
            color: var(--text-dark);
        }

        .form-input:focus {
            outline: none;
            border-color: #4A76A8;
            box-shadow: 0 0 0 3px rgba(74, 118, 168, 0.1);
        }

        .form-input::placeholder {
            color: #999;
            font-weight: normal;
        }

        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .btn {
            padding: 14px 24px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            flex: 1;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--success-green), var(--success-green-dark));
            color: var(--white);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #658a39, #557929);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(117, 155, 73, 0.3);
        }

        .btn-primary:active {
            transform: translateY(0);
            box-shadow: 0 2px 6px rgba(117, 155, 73, 0.3);
        }

        .btn-secondary {
            background: var(--white);
            color: var(--text-light);
            border: 2px solid var(--border-color);
        }

        .btn-secondary:hover {
            background: #f8faff;
            border-color: var(--success-green);
            color: var(--success-green);
        }

        /* Flash Messages */
        .alert {
            padding: 14px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            animation: slideDown 0.3s ease;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .alert-close {
            margin-left: auto;
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
            color: inherit;
            opacity: 0.7;
        }

        .alert-close:hover {
            opacity: 1;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .roles-grid {
                grid-template-columns: 1fr;
            }

            .role-selection,
            .login-form {
                padding: 30px 20px;
            }

            .portal-title {
                font-size: 24px;
            }

            .form-actions {
                flex-direction: column;
            }

            .credentials-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 15px;
            }

            .header-logo {
                margin-bottom: 30px;
            }

            .role-selection,
            .login-form {
                padding: 20px 15px;
            }
        }
    </style>
</head>
<body>

<div class="login-container">
    <!-- Header with Logo -->
    <div class="header-logo">
        <img src="https://www.universalai.in/wp-content/uploads/2020/03/UAi-B-G.png" alt="UAI Logo" class="logo-image">
        <div class="portal-title">AI Powered Academic Hub</div>
        <div class="portal-subtitle">Enterprise Identity Verification</div>
    </div>

    <!-- Main Card -->
    <div class="main-card">
        <!-- Flash Messages -->
        <?php if ($this->session->flashdata('message')): ?>
            <div class="alert alert-<?php echo $this->session->flashdata('message_type') === 'error' ? 'error' : 'success'; ?>">
                <i class="fas fa-<?php echo $this->session->flashdata('message_type') === 'error' ? 'exclamation-triangle' : 'check-circle'; ?>"></i>
                <?php echo $this->session->flashdata('message'); ?>
                <button type="button" class="alert-close" onclick="this.parentElement.style.display='none'">&times;</button>
            </div>
        <?php endif; ?>

        <!-- Role Selection -->
        <div class="role-selection">
            <h2 class="section-title">Choose Your Role</h2>
            
            <div class="roles-grid">
                <div class="role-card" onclick="selectRole('admin')">
                    <div class="role-icon admin">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div class="role-title">Administrator</div>
                    <div class="role-description">System Management & User Control</div>
                </div>

                <div class="role-card" onclick="selectRole('faculty')">
                    <div class="role-icon faculty">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <div class="role-title">Faculty</div>
                    <div class="role-description">Content Management & Resources</div>
                </div>

                <div class="role-card" onclick="selectRole('student')">
                    <div class="role-icon student">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="role-title">Student</div>
                    <div class="role-description">Learning Portal & Resources</div>
                </div>
            </div>
        </div>

        <!-- Login Form -->
        <div class="login-form" id="loginForm">
            <div class="login-header">
                <div class="login-title" id="formTitle">ADMIN PORTAL</div>
                <div class="login-subtitle" id="formSubtitle">Administrator Access</div>
                <div class="role-indicator" id="roleIndicator">
                    <i class="fas fa-user-shield"></i>
                    <span id="selectedRoleText">Administrator</span>
                </div>
            </div>

            <form method="POST" id="loginFormElement">
                <input type="hidden" name="action" value="login">
                <input type="hidden" name="role" id="selectedRole" value="">
                
                <div class="form-group">
                    <label class="form-label">USERNAME</label>
                    <input type="text" name="username" class="form-input" required placeholder="Enter your username">
                </div>
                
                <div class="form-group">
                    <label class="form-label">PASSWORD</label>
                    <input type="password" name="password" class="form-input" required placeholder="Enter your password">
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary" id="signInButton">
                        <i class="fas fa-sign-in-alt"></i>
                        SIGN IN
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="hideLogin()">
                        <i class="fas fa-arrow-left"></i>
                        Back to Roles
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let selectedRoleCard = null;

function selectRole(role) {
    // Remove previous selection
    if (selectedRoleCard) {
        selectedRoleCard.classList.remove('selected');
    }
    
    // Select new role
    const roleCard = event.currentTarget;
    roleCard.classList.add('selected');
    selectedRoleCard = roleCard;
    
    // Set role values
    document.getElementById('selectedRole').value = role;
    
    // Update form header based on role
    const roleTitles = {
        'admin': 'ADMIN PORTAL',
        'faculty': 'FACULTY PORTAL',
        'student': 'STUDENT PORTAL'
    };
    
    const roleSubtitles = {
        'admin': 'Administrator Access',
        'faculty': 'Faculty Access',
        'student': 'Student Access'
    };
    
    const roleIcons = {
        'admin': 'fa-user-shield',
        'faculty': 'fa-chalkboard-teacher',
        'student': 'fa-graduation-cap'
    };
    
    const roleNames = {
        'admin': 'Administrator',
        'faculty': 'Faculty',
        'student': 'Student'
    };
    
    document.getElementById('formTitle').textContent = roleTitles[role];
    document.getElementById('formSubtitle').textContent = roleSubtitles[role];
    document.getElementById('selectedRoleText').textContent = roleNames[role];
    
    // Update icon in role indicator
    const icon = document.querySelector('#roleIndicator i');
    icon.className = `fas ${roleIcons[role]}`;
    
    // Show form
    document.getElementById('loginForm').classList.add('active');
    
    // Smooth scroll to form
    setTimeout(() => {
        document.getElementById('loginForm').scrollIntoView({ 
            behavior: 'smooth', 
            block: 'nearest' 
        });
    }, 100);
}

function hideLogin() {
    document.getElementById('loginForm').classList.remove('active');
    if (selectedRoleCard) {
        selectedRoleCard.classList.remove('selected');
        selectedRoleCard = null;
    }
    document.getElementById('selectedRole').value = '';
}

// Add click effect to sign in button
document.getElementById('loginFormElement').addEventListener('submit', function(e) {
    const signInButton = document.getElementById('signInButton');
    
    // Add click animation
    signInButton.style.transform = 'scale(0.98)';
    signInButton.style.boxShadow = '0 2px 6px rgba(117, 155, 73, 0.3)';
    
    // Reset after 200ms
    setTimeout(() => {
        signInButton.style.transform = '';
        signInButton.style.boxShadow = '';
    }, 200);
});

// Auto-hide alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            if (alert.parentElement) {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                setTimeout(() => {
                    alert.style.display = 'none';
                }, 300);
            }
        }, 5000);
    });
});
</script>

</body>
</html>