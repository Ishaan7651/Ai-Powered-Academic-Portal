<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Faculty - SLAi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <?php $this->load->view('simple_portal/components/admin_sidebar_css'); ?>
    <style>
        /* Main Content Styling Overrides for this page */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        }

        /* Topbar - Same as Admin Dashboard */
        .topbar {
            background: var(--white);
            padding: 0 35px;
            height: var(--topbar-height);
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

        /* Content Area */
        .content-area {
            padding: 35px;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
        }

        /* Dashboard Header */
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        .header-title h1 {
            font-size: 32px;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 12px;
            background: linear-gradient(135deg, var(--text-dark), #475569);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .header-title p {
            color: var(--text-light);
            font-size: 16px;
            line-height: 1.5;
            max-width: 600px;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
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

        .stat-icon.faculty { background: linear-gradient(135deg, #059669, #047857); }
        .stat-icon.subjects { background: linear-gradient(135deg, #d97706, #b45309); }
        .stat-icon.assignments { background: linear-gradient(135deg, #0891b2, #0e7490); }

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

        /* Main Card */
        .main-card {
            background: var(--white);
            border-radius: 18px;
            border: 1px solid var(--border-color);
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
            margin-bottom: 35px;
        }

        .card-header {
            padding: 28px 32px;
            border-bottom: 1px solid var(--border-color);
            background: linear-gradient(to right, #f8fafc, #f1f5f9);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-title {
            font-size: 22px;
            font-weight: 700;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .card-title i {
            color: var(--primary-blue);
            font-size: 20px;
        }

        .card-subtitle {
            color: var(--text-light);
            font-size: 14px;
            margin-top: 8px;
        }

        .card-body {
            padding: 32px;
        }

        /* Buttons */
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

        .btn-sm {
            padding: 10px 20px;
            font-size: 14px;
            border-radius: 10px;
        }

        /* Faculty Card */
        .faculty-card {
            background: var(--white);
            border-radius: 16px;
            border: 1px solid var(--border-color);
            margin-bottom: 25px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
        }

        .faculty-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            border-color: #cbd5e1;
        }

        .faculty-header {
            padding: 24px;
            border-bottom: 1px solid var(--border-color);
            background: linear-gradient(to right, #f8fafc, #f1f5f9);
        }

        .faculty-body {
            padding: 24px;
        }

        .faculty-name {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .faculty-info {
            color: var(--text-light);
            font-size: 14px;
            line-height: 1.5;
        }

        /* Subject Badge */
        .subject-badge {
            background: linear-gradient(135deg, #dbeafe, #bfdbfe);
            color: #1e40af;
            padding: 10px 16px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            margin: 6px;
            box-shadow: 0 2px 4px rgba(30, 64, 175, 0.1);
            border: 1px solid rgba(30, 64, 175, 0.2);
        }

        .subject-code {
            font-weight: 700;
            background: rgba(30, 64, 175, 0.1);
            padding: 4px 8px;
            border-radius: 6px;
        }

        .remove-btn {
            background: linear-gradient(135deg, #fee2e2, #fca5a5);
            color: #991b1b;
            border: none;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .remove-btn:hover {
            background: linear-gradient(135deg, #fca5a5, #f87171);
            transform: scale(1.1);
        }

        /* Assign Form */
        .assign-form {
            background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
            border-radius: 12px;
            padding: 20px;
            border: 1px solid rgba(14, 165, 233, 0.2);
        }

        .assign-form select {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--border-color);
            border-radius: 10px;
            font-size: 14px;
            color: var(--text-dark);
            background: var(--white);
            margin-bottom: 12px;
            transition: all 0.3s ease;
        }

        .assign-form select:focus {
            outline: none;
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 4px rgba(31, 94, 168, 0.15);
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

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 40px;
        }

        .empty-state i {
            font-size: 64px;
            color: #cbd5e1;
            margin-bottom: 20px;
        }

        .empty-state h4 {
            color: var(--text-dark);
            margin-bottom: 10px;
            font-weight: 600;
        }

        .empty-state p {
            color: var(--text-light);
            max-width: 400px;
            margin: 0 auto 25px;
        }

        /* Modal Styles */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            backdrop-filter: blur(4px);
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: var(--white);
            border-radius: 18px;
            padding: 40px;
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            animation: modalSlideIn 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .modal-header {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border-color);
        }

        .modal-header h3 {
            font-size: 22px;
            font-weight: 700;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .modal-header h3 i {
            color: var(--primary-blue);
            font-size: 20px;
        }

        .modal-close {
            position: absolute;
            top: 20px;
            right: 20px;
            background: none;
            border: none;
            font-size: 24px;
            color: var(--text-light);
            cursor: pointer;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .modal-close:hover {
            background: #f1f5f9;
            color: var(--text-dark);
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
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .form-label .required {
            color: #dc2626;
            font-size: 12px;
            font-weight: 600;
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

        .form-hint {
            font-size: 12px;
            color: var(--text-light);
            margin-top: 8px;
            line-height: 1.4;
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 20px;
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid var(--border-color);
        }

        /* Responsive Design */
        @media (max-width: 1400px) {
            .stats-grid {
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
            
            .modal-content {
                width: 95%;
                padding: 25px;
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
            
            .modal-content {
                padding: 20px;
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

        @keyframes modalSlideIn {
            from {
                transform: translateY(-20px) scale(0.95);
                opacity: 0;
            }
            to {
                transform: translateY(0) scale(1);
                opacity: 1;
            }
        }

        /* Grid Layout */
        .faculty-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
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
    </style>
</head>
<body>

<div class="portal-container">

    <!-- Sidebar - Same as Admin Dashboard -->
    <?php $this->load->view('simple_portal/components/admin_sidebar', ['active_page' => 'manage_faculty']); ?>

    <!-- Main Content -->
    <div class="main-content">

        <!-- Topbar - Same as Admin Dashboard -->
        <header class="topbar">
            <div class="page-title">
                Faculty Management
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
                    <h1>Faculty Management</h1>
                    <p>Manage faculty members and assign subjects to them for teaching</p>
                </div>
                <button class="btn-primary" onclick="openAddFacultyModal()">
                    <i class="fa fa-user-plus"></i> Add New Faculty
                </button>
            </div>

            <!-- Flash Messages -->
            <?php if ($this->session->flashdata('success')): ?>
                <div class="flash-message success">
                    <i class="fa fa-check-circle"></i>
                    <span><?php echo $this->session->flashdata('success'); ?></span>
                    <button class="close-btn" onclick="this.parentElement.remove()">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            <?php endif; ?>
            
            <?php if ($this->session->flashdata('error')): ?>
                <div class="flash-message error">
                    <i class="fa fa-exclamation-circle"></i>
                    <span><?php echo $this->session->flashdata('error'); ?></span>
                    <button class="close-btn" onclick="this.parentElement.remove()">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            <?php endif; ?>

            <!-- Stats Overview -->
            <?php
            $total_faculty = count($faculty_list);
            $total_subjects_assigned = 0;
            $total_assignments = 0;
            
            foreach ($faculty_list as $faculty) {
                $faculty_subjects = isset($faculty->subjects) ? count($faculty->subjects) : 0;
                $total_subjects_assigned += $faculty_subjects;
                $total_assignments += $faculty_subjects;
            }
            ?>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon faculty">
                            <i class="fa fa-chalkboard-teacher"></i>
                        </div>
                        <span style="color: #10b981; font-size: 14px; font-weight: 600;">Active</span>
                    </div>
                    <div class="stat-value"><?php echo $total_faculty; ?></div>
                    <div class="stat-label">Total Faculty Members</div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon subjects">
                            <i class="fa fa-book"></i>
                        </div>
                        <span style="color: #f59e0b; font-size: 14px; font-weight: 600;">Assigned</span>
                    </div>
                    <div class="stat-value"><?php echo $total_subjects_assigned; ?></div>
                    <div class="stat-label">Total Subject Assignments</div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon assignments">
                            <i class="fa fa-link"></i>
                        </div>
                        <span style="color: #0891b2; font-size: 14px; font-weight: 600;">Connections</span>
                    </div>
                    <div class="stat-value"><?php echo $total_assignments; ?></div>
                    <div class="stat-label">Faculty-Subject Links</div>
                </div>
            </div>

            <!-- Faculty List -->
            <div class="main-card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fa fa-chalkboard-teacher"></i>
                        Faculty Members & Subject Assignments
                    </div>
                    <div class="card-subtitle">Manage subject assignments for each faculty member</div>
                </div>
                <div class="card-body">
                    <?php if (!empty($faculty_list)): ?>
                        <div class="faculty-grid">
                            <?php foreach ($faculty_list as $faculty): ?>
                                <div class="faculty-card">
                                    <div class="faculty-header">
                                        <div class="faculty-name">
                                            <i class="fa fa-user-circle"></i>
                                            <?php echo htmlspecialchars($faculty->username); ?>
                                        </div>
                                        <div class="faculty-info">
                                            <div><strong>Email:</strong> <?php echo htmlspecialchars($faculty->email); ?></div>
                                            <?php if (!empty($faculty->employee_id)): ?>
                                                <div><strong>Employee ID:</strong> <?php echo htmlspecialchars($faculty->employee_id); ?></div>
                                            <?php endif; ?>
                                            <?php if (!empty($faculty->department_name)): ?>
                                                <div><strong>Department:</strong> <?php echo htmlspecialchars($faculty->department_name); ?> (<?php echo htmlspecialchars($faculty->department_code); ?>)</div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="faculty-body">
                                        <!-- Assigned Subjects -->
                                        <div style="margin-bottom: 25px;">
                                            <div class="section-title">
                                                <i class="fa fa-book"></i>
                                                <span>Assigned Subjects</span>
                                            </div>
                                            
                                            <?php if (!empty($faculty->subjects)): ?>
                                                <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                                                    <?php foreach ($faculty->subjects as $subject): ?>
                                                        <div class="subject-badge">
                                                            <span class="subject-code"><?php echo htmlspecialchars($subject->subject_code); ?></span>
                                                            <span><?php echo htmlspecialchars($subject->subject_name); ?></span>
                                                            <form method="post" action="<?php echo base_url('simple_portal/remove_subject_assignment'); ?>" style="display: inline;">
                                                                <input type="hidden" name="faculty_id" value="<?php echo $faculty->faculty_id; ?>">
                                                                <input type="hidden" name="subject_id" value="<?php echo $subject->id; ?>">
                                                                <button type="submit" class="remove-btn" 
                                                                        onclick="return confirm('Remove this subject assignment?')"
                                                                        title="Remove assignment">
                                                                    <i class="fa fa-times"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php else: ?>
                                                <div style="color: var(--text-light); text-align: center; padding: 20px; background: linear-gradient(135deg, #f8fafc, #f1f5f9); border-radius: 12px;">
                                                    <i class="fa fa-book-open" style="font-size: 32px; margin-bottom: 10px; display: block; color: #cbd5e1;"></i>
                                                    No subjects assigned yet
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <!-- Assign New Subject -->
                                        <div class="assign-form">
                                            <div class="section-title">
                                                <i class="fa fa-plus-circle"></i>
                                                <span>Assign New Subject</span>
                                            </div>
                                            
                                            <form method="post" action="<?php echo base_url('simple_portal/assign_subject'); ?>">
                                                <input type="hidden" name="faculty_id" value="<?php echo $faculty->faculty_id; ?>">
                                                
                                                <select name="subject_id" required>
                                                    <option value="">Select Subject to Assign</option>
                                                    <?php 
                                                    // Get unassigned subjects for this faculty
                                                    $CI =& get_instance();
                                                    $CI->db->select('s.*');
                                                    $CI->db->from('subjects s');
                                                    $CI->db->where('s.is_active', 1);
                                                    
                                                    if ($faculty->faculty_id) {
                                                        // Get currently assigned subject IDs
                                                        $CI->db->where("s.id NOT IN (SELECT subject_id FROM faculty_subjects WHERE faculty_id = " . $faculty->faculty_id . ")");
                                                    }
                                                    
                                                    $CI->db->order_by('s.semester', 'ASC');
                                                    $CI->db->order_by('s.subject_name', 'ASC');
                                                    $unassigned_subjects = $CI->db->get()->result();
                                                    ?>
                                                    
                                                    <?php if (!empty($unassigned_subjects)): ?>
                                                        <?php foreach ($unassigned_subjects as $subject): ?>
                                                            <option value="<?php echo $subject->id; ?>">
                                                                <?php echo htmlspecialchars($subject->subject_code); ?> - 
                                                                <?php echo htmlspecialchars($subject->subject_name); ?> 
                                                                (Sem <?php echo $subject->semester; ?>)
                                                            </option>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <option value="" disabled>No subjects available to assign</option>
                                                    <?php endif; ?>
                                                </select>
                                                
                                                <button type="submit" class="btn-primary btn-sm" style="width: 100%;">
                                                    <i class="fa fa-link"></i> Assign Subject
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fa fa-users"></i>
                            <h4>No Faculty Members Found</h4>
                            <p>Faculty members will appear here once users are assigned the faculty role.</p>
                            <button class="btn-primary" onclick="openAddFacultyModal()" style="margin-top: 20px;">
                                <i class="fa fa-user-plus"></i> Add First Faculty Member
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </main>
    </div>
</div>

<!-- Add Faculty Modal -->
<div class="modal-overlay" id="addFacultyModal">
    <div class="modal-content">
        <button class="modal-close" onclick="closeAddFacultyModal()">
            <i class="fa fa-times"></i>
        </button>
        
        <div class="modal-header">
            <h3><i class="fa fa-user-plus"></i> Add New Faculty</h3>
        </div>
        
        <form method="post" action="<?php echo base_url('simple_portal/add_faculty'); ?>">
            <div class="form-group">
                <label class="form-label">
                    Username
                    <span class="required">* Required</span>
                </label>
                <input type="text" name="username" class="form-control" 
                       placeholder="e.g., john.doe" required maxlength="50">
                <div class="form-hint">Will be used for login system</div>
            </div>
            
            <div class="form-group">
                <label class="form-label">
                    Email Address
                    <span class="required">* Required</span>
                </label>
                <input type="email" name="email" class="form-control" 
                       placeholder="e.g., john.doe@college.edu" required maxlength="100">
                <div class="form-hint">Official institutional email</div>
            </div>
            
            <div class="form-group">
                <label class="form-label">
                    Password
                    <span class="required">* Required</span>
                </label>
                <input type="password" name="password" class="form-control" 
                       placeholder="Create a strong password" required minlength="6">
                <div class="form-hint">Minimum 6 characters</div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Employee ID</label>
                <input type="text" name="employee_id" class="form-control" 
                       placeholder="e.g., FAC001" maxlength="20">
                <div class="form-hint">Unique employee identification number</div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Department</label>
                <select name="department" class="form-control">
                    <option value="">Select Department</option>
                    <option value="Computer Science">Computer Science</option>
                    <option value="Artificial Intelligence">Artificial Intelligence</option>
                    <option value="Machine Learning">Machine Learning</option>
                    <option value="Data Science">Data Science</option>
                    <option value="Mathematics">Mathematics</option>
                    <option value="Physics">Physics</option>
                    <option value="Chemistry">Chemistry</option>
                    <option value="General">General</option>
                </select>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn-secondary" onclick="closeAddFacultyModal()">
                    <i class="fa fa-times"></i> Cancel
                </button>
                <button type="submit" class="btn-primary">
                    <i class="fa fa-user-plus"></i> Add Faculty
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Modal Functions
    function openAddFacultyModal() {
        document.getElementById('addFacultyModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    function closeAddFacultyModal() {
        document.getElementById('addFacultyModal').style.display = 'none';
        document.body.style.overflow = 'auto';
    }
    
    // Close modal when clicking outside
    document.getElementById('addFacultyModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeAddFacultyModal();
        }
    });
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeAddFacultyModal();
        }
    });
    
    // Auto-hide flash messages after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
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
        
        // Add animation to faculty cards
        const facultyCards = document.querySelectorAll('.faculty-card');
        facultyCards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                card.style.transition = 'all 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });
    });
    
    // Confirm subject removal
    function confirmRemoveSubject(event) {
        if (!confirm('Are you sure you want to remove this subject assignment?')) {
            event.preventDefault();
            return false;
        }
        return true;
    }
</script>

</body>
</html>