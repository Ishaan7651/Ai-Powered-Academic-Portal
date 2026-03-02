<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'AI Powered Academic Hub'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            min-height: 100vh; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .card { 
            border-radius: 15px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.1); 
            border: none;
        }
        .dashboard-card:hover { 
            transform: translateY(-3px); 
            transition: all 0.3s ease; 
        }
        .main-content {
            padding-top: 20px;
            padding-bottom: 40px;
        }
    </style>
    
    <!-- Session Manager Script -->
    <?php if (isset($user_data) && !empty($user_data)): ?>
    <script src="<?php echo base_url('assets/js/session-manager.js'); ?>"></script>
    <?php endif; ?>
</head>
<body class="<?php echo (isset($user_data) && !empty($user_data)) ? 'authenticated' : ''; ?>">

<nav class="navbar navbar-dark navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo base_url(); ?>">
            <i class="fas fa-graduation-cap me-2"></i>AI Powered Academic Hub
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <?php if (isset($user_role) && $user_role == 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo base_url('admin/dashboard'); ?>">
                            <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo base_url('admin/users'); ?>">
                            <i class="fas fa-users me-1"></i>Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo base_url('admin/faculty'); ?>">
                            <i class="fas fa-chalkboard-teacher me-1"></i>Faculty
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo base_url('admin/subjects'); ?>">
                            <i class="fas fa-book me-1"></i>Subjects
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
            
            <div class="navbar-nav">
                <span class="navbar-text me-3">
                    Welcome, <?php echo isset($user_data['username']) ? htmlspecialchars($user_data['username']) : 'User'; ?>
                    <?php if (isset($user_role)): ?>
                        <span class="badge bg-light text-dark ms-2"><?php echo ucfirst($user_role); ?></span>
                    <?php endif; ?>
                </span>
                <a class="nav-link" href="<?php echo base_url('logout'); ?>">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </div>
</nav>

<div class="main-content"><?php // Content will be inserted here ?>