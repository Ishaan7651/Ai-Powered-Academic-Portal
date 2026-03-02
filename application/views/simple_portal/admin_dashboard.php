<?php 
// Check if user is logged in and get username from session
$username = $this->session->userdata('username') ?? 'Admin';

// Get database instance for counts only (user data comes from controller)
$ci =& get_instance();
$ci->load->database();

try {
    // Get counts from database using CodeIgniter's database library
    // Only if not already provided by controller
    if (!isset($total_users)) {
        $total_users = $ci->db->count_all('users');
    }
    
    if (!isset($total_faculty)) {
        $total_faculty = $ci->db->where('role', 'faculty')
                              ->where('is_active', 1)
                              ->count_all_results('users');
    }
    
    if (!isset($total_students)) {
        $total_students = $ci->db->where('role', 'student')
                               ->where('is_active', 1)
                               ->count_all_results('users');
    }
    
    // Count subjects
    if (!isset($total_subjects)) {
        $total_subjects = 0;
        if ($ci->db->table_exists('subjects')) {
            $total_subjects = $ci->db->where('is_active', 1)
                                   ->count_all_results('subjects');
        }
    }
    
    // User data with departments is provided by controller via $all_users_data
    // No need to query again here
    
} catch (Exception $e) {
    // Use fallback values if database fails
    if (!isset($total_users)) $total_users = 0;
    if (!isset($total_faculty)) $total_faculty = 0;
    if (!isset($total_students)) $total_students = 0;
    if (!isset($total_subjects)) $total_subjects = 0;
    if (!isset($all_users_data)) $all_users_data = array();
    
    // Log error
    log_message('error', 'Database error in admin_dashboard: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal - AI Powered Academic Hub</title>
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
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
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

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 260px;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        }

        /* Topbar - Clean Design */
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

        .add-faculty-btn {
            background: linear-gradient(135deg, var(--success-green), #6ca736);
            color: var(--white);
            border: none;
            padding: 14px 28px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 12px rgba(120, 184, 63, 0.3);
        }

        .add-faculty-btn:hover {
            background: linear-gradient(135deg, #6ca736, #5a942f);
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(120, 184, 63, 0.4);
        }

        .add-faculty-btn:active {
            transform: translateY(-1px);
        }
        
        /* Stats Cards Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 25px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: var(--white);
            border-radius: 16px;
            padding: 28px;
            border: 1px solid var(--border-color);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-blue), var(--primary-light));
        }

        .stat-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
            border-color: #cbd5e1;
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: var(--white);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .stat-icon.users { background: linear-gradient(135deg, #667eea, #764ba2); }
        .stat-icon.faculty { background: linear-gradient(135deg, #059669, #047857); }
        .stat-icon.students { background: linear-gradient(135deg, #0891b2, #0e7490); }
        .stat-icon.subjects { background: linear-gradient(135deg, #d97706, #b45309); }

        .stat-trend {
            font-size: 14px;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 20px;
            background: rgba(34, 197, 94, 0.1);
            color: #16a34a;
        }

        .stat-value {
            font-size: 36px;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 8px;
            line-height: 1;
        }

        .stat-label {
            font-size: 15px;
            color: var(--text-light);
            font-weight: 500;
        }
        /* Table Container Styles */
.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    border-radius: 8px;
    position: relative;
}

/* Make the table take minimum width */
#usersTable {
    min-width: 100%;
    width: auto;
    white-space: nowrap;
}

/* Table headers should stick on scroll */
#usersTable thead {
    position: sticky;
    top: 0;
    z-index: 10;
    background: linear-gradient(to right, #f1f5f9, #e2e8f0);
}

/* Scrollbar styling */
.table-responsive::-webkit-scrollbar {
    height: 8px;
    width: 8px;
}

.table-responsive::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: linear-gradient(to right, #cbd5e1, #94a3b8);
    border-radius: 4px;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(to right, #94a3b8, #64748b);
}

/* Optional: Add scroll indicators */
.table-responsive {
    position: relative;
}

.table-responsive::before,
.table-responsive::after {
    content: '';
    position: absolute;
    top: 0;
    bottom: 0;
    width: 20px;
    pointer-events: none;
    z-index: 5;
    opacity: 0;
    transition: opacity 0.3s;
}

.table-responsive::before {
    left: 0;
    background: linear-gradient(to right, rgba(255, 255, 255, 0.8), transparent);
}

.table-responsive::after {
    right: 0;
    background: linear-gradient(to left, rgba(255, 255, 255, 0.8), transparent);
}

.table-responsive.scroll-start::after,
.table-responsive.scroll-end::before,
.table-responsive.scroll-middle::before,
.table-responsive.scroll-middle::after {
    opacity: 1;
}


        /* Override for Dashboard to have no padding in body (for table) */
        .main-card .card-body {
            padding: 0;
        }

        /* Table Styling */
        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        thead {
            background: linear-gradient(to right, #f1f5f9, #e2e8f0);
        }

        th {
            padding: 22px 24px;
            text-align: left;
            font-weight: 700;
            color: var(--text-dark);
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid var(--border-color);
            white-space: nowrap;
        }

        td {
            padding: 22px 24px;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-dark);
            font-size: 15px;
            transition: all 0.3s ease;
        }

        tbody tr {
            transition: all 0.3s ease;
        }

        tbody tr:hover {
            background: linear-gradient(to right, rgba(248, 250, 252, 0.6), rgba(241, 245, 249, 0.4));
            transform: scale(1.002);
        }

        .user-cell {
            display: flex;
            flex-direction: column;
        }

        .user-name-cell {
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 4px;
        }

        .user-title-cell {
            font-size: 13px;
            color: var(--text-light);
        }

        .role-badge {
            background: linear-gradient(135deg, #dbeafe, #bfdbfe);
            color: #1e40af;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.3px;
            display: inline-block;
            box-shadow: 0 2px 4px rgba(30, 64, 175, 0.1);
        }

        .actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .action-btn {
            background: none;
            border: none;
            color: var(--text-light);
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s ease;
            padding: 8px;
            border-radius: 8px;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .action-btn.edit {
            color: var(--primary-blue);
            background: rgba(59, 130, 246, 0.1);
        }

        .action-btn.delete {
            color: #dc2626;
            background: rgba(220, 38, 38, 0.1);
        }

        .action-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .action-btn.edit:hover {
            background: rgba(59, 130, 246, 0.2);
        }

        .action-btn.delete:hover {
            background: rgba(220, 38, 38, 0.2);
        }
        /* User Filter Tabs */
.user-filter-tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
}

.filter-btn {
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    background: #f1f5f9;
    color: var(--text-light);
    cursor: pointer;
    font-size: 13px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.filter-btn.active {
    background: var(--primary-blue);
    color: white;
}

.filter-btn:hover:not(.active) {
    background: #e2e8f0;
}

/* Role Badge Variations */
.role-badge.faculty-badge {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: white;
}

.role-badge.student-badge {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
}

.role-badge.admin-badge {
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    color: white;
}

/* Status Badge */
.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}

.active-status {
    background: rgba(34, 197, 94, 0.1);
    color: #16a34a;
}

.inactive-status {
    background: rgba(239, 68, 68, 0.1);
    color: #dc2626;
}
        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-top: 35px;
        }

        .quick-action-btn {
            background: var(--white);
            border: 2px solid var(--border-color);
            border-radius: 14px;
            padding: 28px 24px;
            text-decoration: none;
            color: var(--text-dark);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 16px;
            cursor: pointer;
            text-align: center;
        }

        .quick-action-btn:hover {
            background: linear-gradient(135deg, var(--white), #f8fafc);
            border-color: var(--primary-blue);
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(31, 94, 168, 0.15);
        }

        .quick-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            color: var(--white);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .quick-icon.create-faculty { background: linear-gradient(135deg, #667eea, #764ba2); }
        .quick-icon.create-student { background: linear-gradient(135deg, #059669, #047857); }
        .quick-icon.manage-subjects { background: linear-gradient(135deg, #0891b2, #0e7490); }
        .quick-icon.settings { background: linear-gradient(135deg, #d97706, #b45309); }

        .quick-label {
            font-weight: 700;
            font-size: 16px;
            color: var(--text-dark);
        }

        .quick-desc {
            font-size: 13px;
            color: var(--text-light);
            line-height: 1.4;
        }
@media (max-width: 768px) {
    /* Reduce table padding on mobile */
    #usersTable th,
    #usersTable td {
        padding: 16px 12px;
        font-size: 14px;
    }
    
    /* Make role badges smaller */
    .role-badge {
        padding: 4px 8px;
        font-size: 11px;
    }
    
    /* Adjust user cell layout */
    .user-cell {
        min-width: 150px;
    }
    
    .user-name-cell {
        font-size: 14px;
    }
    
    .user-id-cell span {
        font-size: 11px;
    }
    
    /* Make filter buttons smaller */
    .filter-btn {
        padding: 6px 12px;
        font-size: 12px;
    }
}

@media (max-width: 480px) {
    /* Even smaller on very small screens */
    #usersTable th,
    #usersTable td {
        padding: 12px 8px;
        font-size: 13px;
    }
    
    .user-cell {
        min-width: 120px;
    }
    
    /* Hide some columns on very small screens */
    #usersTable th:nth-child(4), /* Details column */
    #usersTable td:nth-child(4) {
        display: none;
    }
}
        /* Responsive Design */
        @media (max-width: 1400px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .quick-actions {
                grid-template-columns: repeat(2, 1fr);
            }
        }

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
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .quick-actions {
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
            
            .user-name {
                display: none;
            }
            
            table {
                display: block;
            }
            
            th, td {
                padding: 16px;
            }
        }

        @media (max-width: 480px) {
            .content-area {
                padding: 20px 15px;
            }
            
            .header-title h1 {
                font-size: 24px;
            }
            
            .stat-value {
                font-size: 28px;
            }
        }

        /* Scrollbar */
        .content-area::-webkit-scrollbar {
            width: 8px;
        }
        
        .content-area::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }
        
        .content-area::-webkit-scrollbar-thumb {
            background: linear-gradient(to bottom, #cbd5e1, #94a3b8);
            border-radius: 4px;
        }
        
        .content-area::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(to bottom, #94a3b8, #64748b);
        }
    </style>
</head>
<body>

<div class="portal-container">

   <!-- Sidebar -->
    <!-- Sidebar Component -->
    <?php $this->load->view('simple_portal/components/admin_sidebar', ['active_page' => 'dashboard']); ?>

    <!-- Main Content -->
    <div class="main-content">

        <!-- Topbar -->
        <header class="topbar">
            <div class="page-title">
                User Accounts Management
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

            <!-- Dashboard Header -->
            <div class="dashboard-header">
                <div class="header-title">
                    <h1>Admin Dashboard</h1>
                    <p>Manage user accounts, permissions, and system settings from a single interface</p>
                </div>
                <button class="add-faculty-btn" onclick="window.location.href='<?php echo base_url('simple_portal/create_faculty'); ?>'">
                    <i class="fa fa-user-plus"></i>
                    Add New Faculty
                </button>
            </div>

            <!-- Stats Overview -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon users">
                            <i class="fa fa-users"></i>
                        </div>
                        <span class="stat-trend">+12%</span>
                    </div>
                    <div class="stat-value"><?php echo $total_users; ?></div>
                    <div class="stat-label">Total Users</div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon faculty">
                            <i class="fa fa-chalkboard-teacher"></i>
                        </div>
                        <span class="stat-trend">+8%</span>
                    </div>
                    <div class="stat-value"><?php echo $total_faculty; ?></div>
                    <div class="stat-label">Faculty Members</div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon students">
                            <i class="fa fa-graduation-cap"></i>
                        </div>
                        <span class="stat-trend">+15%</span>
                    </div>
                    <div class="stat-value"><?php echo $total_students; ?></div>
                    <div class="stat-label">Students</div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon subjects">
                            <i class="fa fa-book"></i>
                        </div>
                        <span class="stat-trend">+5%</span>
                    </div>
                    <div class="stat-value"><?php echo $total_subjects; ?></div>
                    <div class="stat-label">Subjects</div>
                </div>
            </div>

           <!-- User Accounts Table -->
<div class="main-card">
    <div class="card-header">
        <div class="card-title">
            <i class="fa fa-users"></i>
            User Accounts (Faculty & Students)
        </div>
        <div class="card-subtitle">Manage all user accounts in the system</div>
    </div>
    <div class="card-body">
        <!-- User Filter Tabs -->
        <div class="user-filter-tabs" style="margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">
            <button class="filter-btn active" onclick="filterUsers('all')">All Users</button>
            <button class="filter-btn" onclick="filterUsers('faculty')">Faculty Only</button>
            <button class="filter-btn" onclick="filterUsers('student')">Students Only</button>
        </div>
        
        <div class="table-responsive">
            <table id="usersTable">
                <thead>
                    <tr>
                        <th>Name / ID</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Details</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="usersTableBody">
                    <?php if (!empty($all_users_data)): ?>
                        <?php foreach ($all_users_data as $user): ?>
                            <tr class="user-row" data-role="<?php echo $user['role']; ?>">
                                <td>
                                    <div class="user-cell">
                                        <div class="user-name-cell"><?php echo htmlspecialchars($user['username']); ?></div>
                                        <div class="user-id-cell">
                                            <?php if ($user['role'] === 'faculty' && !empty($user['employee_id'])): ?>
                                                <span style="color: var(--text-light); font-size: 12px;">ID: <?php echo htmlspecialchars($user['employee_id']); ?></span>
                                            <?php elseif ($user['role'] === 'student' && !empty($user['student_id'])): ?>
                                                <span style="color: var(--text-light); font-size: 12px;">ID: <?php echo htmlspecialchars($user['student_id']); ?></span>
                                            <?php else: ?>
                                                <span style="color: var(--text-light); font-size: 12px;">User ID: <?php echo $user['id']; ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <?php if ($user['role'] === 'faculty'): ?>
                                        <span class="role-badge faculty-badge">FACULTY</span>
                                    <?php elseif ($user['role'] === 'student'): ?>
                                        <span class="role-badge student-badge">STUDENT</span>
                                    <?php elseif ($user['role'] === 'admin'): ?>
                                        <span class="role-badge admin-badge">ADMIN</span>
                                    <?php else: ?>
                                        <span class="role-badge"><?php echo strtoupper($user['role']); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($user['role'] === 'faculty'): ?>
                                        <span style="font-size: 13px; color: var(--text-dark);">
                                            <?php echo !empty($user['department']) ? htmlspecialchars($user['department']) : 'No department'; ?>
                                        </span>
                                    <?php elseif ($user['role'] === 'student'): ?>
                                        <div style="display: flex; flex-direction: column; gap: 4px;">
                                            <?php if (!empty($user['department'])): ?>
                                                <span style="font-size: 13px; color: var(--text-dark);">
                                                    <?php echo htmlspecialchars($user['department']); ?>
                                                </span>
                                            <?php endif; ?>
                                            <?php if (!empty($user['current_semester'])): ?>
                                                <span style="font-size: 12px; color: var(--text-light);">
                                                    Semester: <?php echo $user['current_semester']; ?>
                                                </span>
                                            <?php endif; ?>
                                            <?php if (!empty($user['enrollment_year'])): ?>
                                                <span style="font-size: 12px; color: var(--text-light);">
                                                    Year: <?php echo $user['enrollment_year']; ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <span style="color: var(--text-light); font-size: 12px;">Administrator</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="status-badge active-status">
                                        <i class="fa fa-circle" style="color: #22c55e; font-size: 8px; margin-right: 5px;"></i>
                                        Active
                                    </span>
                                </td>
                                <td>
                                    <div class="actions">
                                        <?php if ($user['role'] !== 'admin'): ?>
                                            <button class="action-btn edit" onclick="editUser(<?php echo $user['id']; ?>)">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <button class="action-btn delete" onclick="deleteUser(<?php echo $user['id']; ?>)">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        <?php else: ?>
                                            <span style="color: var(--text-light); font-size: 12px;">Admin account</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px;">
                                <i class="fa fa-users fa-2x" style="color: #cbd5e1; margin-bottom: 15px;"></i>
                                <div style="color: var(--text-light); font-size: 14px;">No users found</div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <a href="<?php echo base_url('simple_portal/create_faculty'); ?>" class="quick-action-btn">
                    <div class="quick-icon create-faculty">
                        <i class="fa fa-user-plus"></i>
                    </div>
                    <div class="quick-label">Create Faculty</div>
                    <div class="quick-desc">Add new faculty members to the system</div>
                </a>
                
                <a href="<?php echo base_url('simple_portal/create_student'); ?>" class="quick-action-btn">
                    <div class="quick-icon create-student">
                        <i class="fa fa-graduation-cap"></i>
                    </div>
                    <div class="quick-label">Create Student</div>
                    <div class="quick-desc">Register new student accounts</div>
                </a>
                
                <a href="<?php echo base_url('simple_portal/manage_subjects'); ?>" class="quick-action-btn">
                    <div class="quick-icon manage-subjects">
                        <i class="fa fa-book"></i>
                    </div>
                    <div class="quick-label">Manage Subjects</div>
                    <div class="quick-desc">Add or modify course subjects</div>
                </a>
                
                <a href="#" class="quick-action-btn">
                    <div class="quick-icon settings">
                        <i class="fa fa-cogs"></i>
                    </div>
                    <div class="quick-label">System Settings</div>
                    <div class="quick-desc">Configure system preferences</div>
                </a>
            </div>

        </main>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Edit User</h2>
            <button class="close-modal" onclick="closeEditModal()">&times;</button>
        </div>
        <form id="editUserForm" method="POST" action="<?php echo base_url('simple_portal/update_user'); ?>">
            <input type="hidden" name="user_id" id="edit_user_id">
            <input type="hidden" name="user_role" id="edit_user_role">
            
            <div class="form-group">
                <label class="form-label">Username</label>
                <input type="text" class="form-control" id="edit_username" readonly style="background: #f1f5f9;">
            </div>

            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" id="edit_email" name="email" required>
            </div>

            <div class="form-group">
                <label class="form-label">New Password (leave blank to keep current)</label>
                <input type="password" class="form-control" id="edit_password" name="password" placeholder="Enter new password">
            </div>

            <div id="facultyFields" style="display: none;">
                <div class="form-group">
                    <label class="form-label">Departments (Select multiple)</label>
                    <select class="form-control" id="edit_department_ids" name="department_ids[]" multiple size="5" style="height: auto;">
                        <?php if (!empty($departments)): ?>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?php echo $dept->id; ?>"><?php echo htmlspecialchars($dept->name); ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <div class="form-hint" style="margin-top: 5px; font-size: 12px; color: #64748b;">Hold Ctrl (Windows) or Cmd (Mac) to select multiple departments</div>
                </div>
            </div>

            <div id="studentFields" style="display: none;">
                <div class="form-group">
                    <label class="form-label">Department</label>
                    <select class="form-control" id="edit_student_department_id" name="student_department_id">
                        <option value="">Select Department</option>
                        <?php if (!empty($departments)): ?>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?php echo $dept->id; ?>"><?php echo htmlspecialchars($dept->name); ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Current Semester</label>
                    <select class="form-control" id="edit_current_semester" name="current_semester">
                        <?php for ($i = 1; $i <= 8; $i++): ?>
                            <option value="<?php echo $i; ?>">Semester <?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Enrollment Year</label>
                    <input type="number" class="form-control" id="edit_enrollment_year" name="enrollment_year" min="2000" max="2100">
                </div>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fa fa-save"></i> Update User
            </button>
        </form>
    </div>
</div>

<style>
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    align-items: center;
    justify-content: center;
}

.modal.active {
    display: flex;
}

.modal-content {
    background: white;
    border-radius: 12px;
    padding: 30px;
    max-width: 500px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.modal-title {
    font-size: 24px;
    font-weight: 700;
    color: #1e293b;
}

.close-modal {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #64748b;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    font-weight: 600;
    color: #334155;
    margin-bottom: 8px;
}

.form-control {
    width: 100%;
    padding: 10px 15px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
}

.btn-submit {
    width: 100%;
    padding: 12px;
    background: linear-gradient(135deg, var(--primary-blue), var(--primary-dark));
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}
</style>

<script>
    // Interactive functions
    function editUser(userId) {
        // Fetch user data via AJAX
        fetch('<?php echo base_url("simple_portal/get_user_data/"); ?>' + userId)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const user = data.user;
                    
                    // Populate form
                    document.getElementById('edit_user_id').value = user.id;
                    document.getElementById('edit_user_role').value = user.role;
                    document.getElementById('edit_username').value = user.username;
                    document.getElementById('edit_email').value = user.email;
                    document.getElementById('edit_password').value = '';
                    
                    // Show/hide role-specific fields
                    document.getElementById('facultyFields').style.display = 'none';
                    document.getElementById('studentFields').style.display = 'none';
                    
                    if (user.role === 'faculty') {
                        document.getElementById('facultyFields').style.display = 'block';
                        // Clear previous selections
                        const deptSelect = document.getElementById('edit_department_ids');
                        for (let option of deptSelect.options) {
                            option.selected = false;
                        }
                        // Select the faculty's departments
                        if (user.department_ids && user.department_ids.length > 0) {
                            user.department_ids.forEach(deptId => {
                                for (let option of deptSelect.options) {
                                    if (option.value == deptId) {
                                        option.selected = true;
                                    }
                                }
                            });
                        }
                    } else if (user.role === 'student') {
                        document.getElementById('studentFields').style.display = 'block';
                        if (user.department_id) {
                            document.getElementById('edit_student_department_id').value = user.department_id;
                        }
                        if (user.current_semester) {
                            document.getElementById('edit_current_semester').value = user.current_semester;
                        }
                        if (user.enrollment_year) {
                            document.getElementById('edit_enrollment_year').value = user.enrollment_year;
                        }
                    }
                    
                    // Open modal
                    document.getElementById('editUserModal').classList.add('active');
                } else {
                    alert('Error loading user data: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to load user data');
            });
    }

    function closeEditModal() {
        document.getElementById('editUserModal').classList.remove('active');
    }

    // Close modal when clicking outside
    document.getElementById('editUserModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeEditModal();
        }
    });


    function deleteUser(userId) {
        if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
            // In real implementation, you would make an AJAX call here
            const row = event.target.closest('tr');
            row.style.opacity = '0.5';
            row.style.transform = 'translateX(-10px)';
            
            setTimeout(() => {
                row.style.display = 'none';
                showNotification('User deleted successfully', 'success');
            }, 500);
        }
    }
    // User filtering functionality
function filterUsers(role) {
    const rows = document.querySelectorAll('.user-row');
    const filterBtns = document.querySelectorAll('.filter-btn');
    
    // Update active button
    filterBtns.forEach(btn => {
        btn.classList.remove('active');
        if (btn.textContent.toLowerCase().includes(role)) {
            btn.classList.add('active');
        }
    });
    
    // Filter rows
    rows.forEach(row => {
        if (role === 'all') {
            row.style.display = '';
        } else {
            if (row.getAttribute('data-role') === role) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    });
    // Add scroll indicators to table
function initTableScroll() {
    const tableContainer = document.querySelector('.table-responsive');
    const table = document.getElementById('usersTable');
    
    if (!tableContainer || !table) return;
    
    function updateScrollIndicators() {
        const scrollLeft = tableContainer.scrollLeft;
        const maxScroll = tableContainer.scrollWidth - tableContainer.clientWidth;
        
        // Remove all classes first
        tableContainer.classList.remove('scroll-start', 'scroll-end', 'scroll-middle');
        
        if (maxScroll <= 0) {
            // No scroll needed
            return;
        }
        
        if (scrollLeft <= 0) {
            tableContainer.classList.add('scroll-start');
        } else if (scrollLeft >= maxScroll) {
            tableContainer.classList.add('scroll-end');
        } else {
            tableContainer.classList.add('scroll-middle');
        }
    }
    
    // Initialize
    updateScrollIndicators();
    
    // Update on scroll
    tableContainer.addEventListener('scroll', updateScrollIndicators);
    
    // Update on window resize
    window.addEventListener('resize', updateScrollIndicators);
}

// Call this function when page loads
document.addEventListener('DOMContentLoaded', function() {
    initTableScroll();
});



    // Update table message if no results
    const visibleRows = Array.from(rows).filter(row => row.style.display !== 'none');
    const noResultsRow = document.getElementById('noResultsRow');
    
    if (visibleRows.length === 0) {
        if (!noResultsRow) {
            const tbody = document.getElementById('usersTableBody');
            const noResults = document.createElement('tr');
            noResults.id = 'noResultsRow';
            noResults.innerHTML = `
                <td colspan="6" style="text-align: center; padding: 40px;">
                    <i class="fa fa-search fa-2x" style="color: #cbd5e1; margin-bottom: 15px;"></i>
                    <div style="color: var(--text-light); font-size: 14px;">No ${role} users found</div>
                </td>
            `;
            tbody.appendChild(noResults);
        }
    } else if (noResultsRow) {
        noResultsRow.remove();
    }
}

// Enhanced delete function
function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        const row = event.target.closest('tr');
        const userRole = row.getAttribute('data-role');
        
        // Show loading state
        row.style.opacity = '0.5';
        row.style.pointerEvents = 'none';
        
        // In a real implementation, you would make an AJAX call here
        // For now, simulate deletion
        setTimeout(() => {
            row.remove();
            showNotification(`${userRole.charAt(0).toUpperCase() + userRole.slice(1)} deleted successfully`, 'success');
            
            // Update stats
            updateStatsAfterDeletion(userRole);
        }, 500);
    }
}

// Function to update stats after deletion
function updateStatsAfterDeletion(role) {
    const statCards = {
        'faculty': document.querySelector('.stat-card:nth-child(2) .stat-value'),
        'student': document.querySelector('.stat-card:nth-child(3) .stat-value'),
        'all': document.querySelector('.stat-card:nth-child(1) .stat-value')
    };
    
    if (statCards[role]) {
        const currentValue = parseInt(statCards[role].textContent);
        statCards[role].textContent = currentValue - 1;
    }
    
    // Update total users
    if (statCards.all) {
        const currentTotal = parseInt(statCards.all.textContent);
        statCards.all.textContent = currentTotal - 1;
    }
}
    // Notification function
    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = 'notification';
        notification.innerHTML = `
            <span>${message}</span>
            <button onclick="this.parentElement.remove()">×</button>
        `;
        
        // Add styles
        notification.style.cssText = `
            position: fixed;
            top: 30px;
            right: 30px;
            padding: 16px 24px;
            border-radius: 12px;
            color: white;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 1000;
            animation: slideIn 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            background: linear-gradient(135deg, ${type === 'success' ? '#059669, #047857' : '#dc2626, #b91c1c'});
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 5000);
    }

    // Add CSS for animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        .notification button {
            background: none;
            border: none;
            color: white;
            font-size: 22px;
            cursor: pointer;
            padding: 0;
            margin: 0;
            line-height: 1;
            opacity: 0.8;
            transition: opacity 0.3s;
        }
        
        .notification button:hover {
            opacity: 1;
        }
    `;
    document.head.appendChild(style);
</script>

</body>
</html>