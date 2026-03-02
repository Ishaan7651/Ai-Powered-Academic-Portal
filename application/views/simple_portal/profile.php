<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - SLAi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <?php if (isset($user_role) && $user_role === 'faculty'): ?>
        <?php $this->load->view('simple_portal/components/faculty_sidebar_css'); ?>
    <?php else: ?>
        <?php $this->load->view('simple_portal/components/student_sidebar_css'); ?>
    <?php endif; ?>
    <style>
        /* Main Content Styling Overrides for this page */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        }

        /* Page specific structure alignment */
        .content-container {
            padding: 35px;
        }
        
        .profile-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
            max-width: 800px;
            margin: 0 auto;
        }
        
        .profile-header {
            display: flex;
            align-items: center;
            gap: 30px;
            margin-bottom: 40px;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 30px;
        }
        
        .big-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-blue), var(--primary-light));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            color: white;
            font-weight: 700;
        }
        
        .profile-info h2 {
            font-size: 24px;
            margin-bottom: 5px;
            color: var(--text-dark);
        }
        
        .profile-role {
            display: inline-block;
            padding: 5px 15px;
            background: rgba(31, 94, 168, 0.1);
            color: var(--primary-blue);
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 30px;
        }
        
        .info-group label {
            display: block;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-light);
            margin-bottom: 8px;
            font-weight: 600;
        }
        
        .info-value {
            font-size: 16px;
            color: var(--text-dark);
            font-weight: 500;
            padding: 12px;
            background: #f8fafc;
            border-radius: 8px;
            border: 1px solid var(--border-color);
        }
    </style>
</head>
<body>

<div class="portal-container">
    <!-- Sidebar -->
    <?php 
    if (isset($user_role) && $user_role === 'faculty') {
        $this->load->view('simple_portal/components/faculty_sidebar', ['active_page' => 'profile']);
    } else {
        // Fallback or student sidebar
         $this->load->view('simple_portal/components/student_sidebar', ['active_page' => 'profile']);
    }
    ?>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Topbar -->
        <div class="topbar">
            <div class="page-title">My Profile</div>
            
            <div class="user-profile">
                <div class="user-avatar">
                   <?php echo strtoupper(substr($user_data['username'], 0, 1)); ?>
                </div>
                <div class="user-info">
                    <div class="user-name"><?php echo htmlspecialchars($user_data['username']); ?></div>
                    <div class="user-role"><?php echo ucfirst($user_role); ?></div>
                </div>
            </div>
        </div>
        
        <div class="content-container">
            <div class="profile-card">
                <div class="profile-header">
                    <div class="big-avatar">
                        <?php echo strtoupper(substr($user_data['username'], 0, 1)); ?>
                    </div>
                    <div class="profile-info">
                        <h2><?php echo htmlspecialchars($user_data['username']); ?></h2>
                        <span class="profile-role"><?php echo ucfirst($user_role); ?></span>
                    </div>
                </div>
                
                <div class="info-grid">
                    <div class="info-group">
                        <label>Username</label>
                        <div class="info-value"><?php echo htmlspecialchars($user_data['username']); ?></div>
                    </div>
                    <div class="info-group">
                        <label>User ID</label>
                        <div class="info-value">#<?php echo $user_data['user_id']; ?></div>
                    </div>
                    <div class="info-group">
                        <label>Role</label>
                        <div class="info-value"><?php echo ucfirst($user_role); ?></div>
                    </div>
                    <div class="info-group">
                        <label>Account Status</label>
                        <div class="info-value" style="color: var(--success-green);">Active</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
