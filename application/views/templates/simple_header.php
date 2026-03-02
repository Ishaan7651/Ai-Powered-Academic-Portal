<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title . ' - ' : ''; ?>AI Powered Academic Hub</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            min-height: 100vh; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .card { 
            border-radius: 15px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.1); 
            border: none;
        }
        .btn-primary { 
            background: linear-gradient(45deg, #667eea, #764ba2); 
            border: none; 
        }
        .btn-primary:hover {
            background: linear-gradient(45deg, #5a6fd8, #6a4190);
            transform: translateY(-1px);
        }
        .navbar { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; 
        }
        .role-btn {
            transition: all 0.3s ease;
            min-height: 200px;
        }
        .role-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }
        .dashboard-card {
            transition: all 0.3s ease;
        }
        .dashboard-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
    </style>
    
    <!-- Session Manager Script -->
    <?php if (isset($logged_in) && $logged_in): ?>
    <script src="<?php echo base_url('assets/js/session-manager.js'); ?>"></script>
    <?php endif; ?>
</head>
<body class="<?php echo (isset($logged_in) && $logged_in) ? 'authenticated' : ''; ?>">

<?php if (isset($logged_in) && $logged_in): ?>
    <!-- Navigation for logged in users -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="<?php echo base_url('portal'); ?>">
                <i class="fas fa-graduation-cap me-2"></i>
                AI Powered Academic Hub
            </a>
            
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">
                    <i class="fas fa-user-circle me-1"></i>
                    Welcome, <?php echo $this->session->userdata('username'); ?>
                    <span class="badge bg-light text-dark ms-2">
                        <?php echo ucfirst($user_role); ?>
                    </span>
                </span>
                <a class="nav-link" href="<?php echo base_url('portal?action=logout'); ?>">
                    <i class="fas fa-sign-out-alt me-1"></i> Logout
                </a>
            </div>
        </div>
    </nav>
<?php endif; ?>

<div class="container mt-4">
    <!-- Flash Messages -->
    <?php if ($this->session->flashdata('message')): ?>
        <div class="alert alert-<?php echo $this->session->flashdata('message_type'); ?> alert-dismissible fade show" role="alert">
            <?php echo $this->session->flashdata('message'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>