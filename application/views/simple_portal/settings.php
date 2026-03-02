<?php
// Helper to display flash messages
$success_msg = $this->session->flashdata('success');
$error_msg = $this->session->flashdata('error');

// Determine which sidebar to load
$sidebar_view = 'simple_portal/components/student_sidebar';
$sidebar_css = 'simple_portal/components/student_sidebar_css';

if ($user_role === 'faculty') {
    $sidebar_view = 'simple_portal/components/faculty_sidebar';
    $sidebar_css = 'simple_portal/components/faculty_sidebar_css';
} elseif ($user_role === 'admin') {
    $sidebar_view = 'simple_portal/components/admin_sidebar';
    $sidebar_css = 'simple_portal/components/admin_sidebar_css';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - AI Powered Academic Hub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <?php $this->load->view($sidebar_css); ?>
    <style>
        /* Ensure portal container and main content are properly styled */
        .portal-container {
            display: flex;
            min-height: 100vh;
            background: #f5f7fa;
        }
        
        .main-content {
            flex: 1;
            margin-left: 280px;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .topbar {
            background: var(--white, #ffffff);
            padding: 0 35px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--border-color, #e5e7eb);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .page-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-dark, #1e293b);
        }
        
        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-blue, #1f5ea8), #114a7d);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 18px;
        }
        
        .user-info {
            display: flex;
            flex-direction: column;
        }
        
        .user-name {
            font-weight: 600;
            color: var(--text-dark, #1e293b);
            font-size: 14px;
        }
        
        .user-role {
            font-size: 12px;
            color: var(--text-light, #64748b);
        }
        
        .content-area {
            flex: 1;
            padding: 35px;
            overflow-y: auto;
        }
        
        .dashboard-header {
            margin-bottom: 30px;
        }
        
        .header-title h1 {
            font-size: 32px;
            font-weight: 800;
            color: var(--text-dark, #1e293b);
            margin: 0 0 8px 0;
        }
        
        .header-title p {
            color: var(--text-light, #64748b);
            font-size: 16px;
            margin: 0;
        }
        
        /* Page-specific styles */
        .settings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 30px;
        }

        .settings-card {
            background: var(--white);
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border-color);
            overflow: hidden;
        }

        .card-header {
            padding: 20px 25px;
            background: var(--white);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .card-header i {
            font-size: 20px;
            color: var(--primary-blue);
        }

        .card-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-dark);
        }

        .card-body {
            padding: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-dark);
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-blue);
        }

        .form-control[readonly] {
            background-color: var(--gray-bg);
            cursor: not-allowed;
            color: var(--text-light);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-blue), #114a7d);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(31, 94, 168, 0.2);
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
    </style>
</head>
<body class="authenticated">

<div class="portal-container">
    <!-- Sidebar -->
    <?php $this->load->view($sidebar_view, ['active_page' => 'settings']); ?>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Topbar -->
        <div class="topbar">
            <div class="page-title">Settings</div>
            <div class="user-profile" style="cursor: default;">
                <div class="user-avatar"><?php echo strtoupper(substr($username ?? $this->session->userdata('username'), 0, 1)); ?></div>
                <div class="user-info">
                    <div class="user-name"><?php echo $username ?? $this->session->userdata('username'); ?></div>
                    <div class="user-role"><?php echo ucfirst($user_role); ?></div>
                </div>
            </div>
        </div>

        <div class="content-area">
            <div class="dashboard-header">
                <div class="header-title">
                    <h1>Account Settings</h1>
                    <p>Manage your account settings and profile information</p>
                </div>
            </div>

        <?php if ($success_msg): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i><?php echo $success_msg; ?>
            </div>
        <?php endif; ?>

        <?php if ($error_msg): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle me-2"></i><?php echo $error_msg; ?>
            </div>
        <?php endif; ?>

        <div class="settings-grid">
            <!-- Profile Information -->
            <div class="settings-card">
                <div class="card-header">
                    <i class="fas fa-user-circle"></i>
                    <h2 class="card-title">Profile Information</h2>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Role</label>
                        <input type="text" class="form-control" value="<?php echo ucfirst($user_role); ?>" readonly>
                    </div>
                    
                    <?php if ($user_role === 'student' && isset($student_data)): ?>
                        <?php if(isset($student_data['enrollment_year'])): ?>
                        <div class="form-group">
                            <label class="form-label">Enrollment Year</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($student_data['enrollment_year']); ?>" readonly>
                        </div>
                        <?php endif; ?>
                        <?php if(isset($student_data['current_semester'])): ?>
                        <div class="form-group">
                            <label class="form-label">Current Semester</label>
                            <input type="text" class="form-control" value="Semester <?php echo htmlspecialchars($student_data['current_semester']); ?>" readonly>
                        </div>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <?php if ($user_role === 'faculty' && isset($faculty_data)): ?>
                        <?php if(isset($faculty_data['department_name'])): ?>
                        <div class="form-group">
                            <label class="form-label">Department</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($faculty_data['department_name']); ?> (<?php echo htmlspecialchars($faculty_data['department_code']); ?>)" readonly>
                        </div>
                        <?php endif; ?>
                        <?php if(isset($faculty_data['designation'])): ?>
                        <div class="form-group">
                            <label class="form-label">Designation</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($faculty_data['designation']); ?>" readonly>
                        </div>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label class="form-label">Account Created</label>
                        <input type="text" class="form-control" value="<?php echo date('F j, Y', strtotime($user['created_at'])); ?>" readonly>
                    </div>
                </div>
            </div>

            <!-- Security Settings -->
            <div class="settings-card">
                <div class="card-header">
                    <i class="fas fa-shield-alt"></i>
                    <h2 class="card-title">Security & Password</h2>
                </div>
                <div class="card-body">
                    <?php echo form_open('simple_portal/settings_update_password'); ?>
                        <div class="form-group">
                            <label class="form-label" for="current_password">Current Password</label>
                            <input type="password" name="current_password" id="current_password" class="form-control" required placeholder="Enter current password">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="new_password">New Password</label>
                            <input type="password" name="new_password" id="new_password" class="form-control" required minlength="6" placeholder="Enter new password (min. 6 chars)">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="confirm_password">Confirm New Password</label>
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control" required minlength="6" placeholder="Confirm new password">
                        </div>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i> Update Password
                        </button>
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    </div>
</main>

</div><!-- End portal-container -->

</body>
</html>
