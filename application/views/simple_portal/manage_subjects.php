<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Subjects - SLAi</title>
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

        /* Form Container */
        .form-container {
            background: var(--white);
            border-radius: 18px;
            border: 1px solid var(--border-color);
            padding: 40px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        }

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

        /* Form Hints */
        .form-hint {
            font-size: 12px;
            color: var(--text-light);
            margin-top: 8px;
            line-height: 1.4;
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
            min-width: 180px;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-decoration: none;
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
            min-width: auto;
            border-radius: 10px;
        }

        /* Table Styles */
        .table-responsive {
            overflow-x: auto;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            background: var(--white);
        }

        .subject-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .subject-table thead {
            background: linear-gradient(to right, #f1f5f9, #e2e8f0);
        }

        .subject-table th {
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

        .subject-table td {
            padding: 22px 24px;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-dark);
            font-size: 15px;
            transition: all 0.3s ease;
        }

        .subject-table tbody tr {
            transition: all 0.3s ease;
        }

        .subject-table tbody tr:hover {
            background: linear-gradient(to right, rgba(248, 250, 252, 0.6), rgba(241, 245, 249, 0.4));
            transform: scale(1.002);
        }

        .subject-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Badges */
        .badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.3px;
            display: inline-block;
        }

        .badge-primary {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
        }

        .badge-success {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }

        .badge-secondary {
            background: linear-gradient(135deg, #dbeafe, #bfdbfe);
            color: #1e40af;
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

        .stat-icon.subjects { background: linear-gradient(135deg, #d97706, #b45309); }
        .stat-icon.resources { background: linear-gradient(135deg, #0891b2, #0e7490); }
        .stat-icon.faculty { background: linear-gradient(135deg, #059669, #047857); }

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
            margin: 0 auto;
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

        .quick-icon.dashboard { background: linear-gradient(135deg, #667eea, #764ba2); }
        .quick-icon.create-faculty { background: linear-gradient(135deg, #059669, #047857); }
        .quick-icon.create-student { background: linear-gradient(135deg, #0891b2, #0e7490); }
        .quick-icon.logout { background: linear-gradient(135deg, #d97706, #b45309); }

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
            
            .content-area {
                padding: 20px;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .form-container {
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
            
            .subject-table th,
            .subject-table td {
                padding: 16px;
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
    </style>
</head>
<body>

<div class="portal-container">

    <!-- Sidebar - Same as Admin Dashboard -->
    <?php $this->load->view('simple_portal/components/admin_sidebar', ['active_page' => 'manage_subjects']); ?>

    <!-- Main Content -->
    <div class="main-content">

        <!-- Topbar - Same as Admin Dashboard -->
        <header class="topbar">
            <div class="page-title">
                Manage Subjects
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
                    <h1>Manage Subjects</h1>
                    <p>Create, update, and manage course subjects in the SLAi academic system</p>
                </div>
                <a href="<?php echo base_url('simple_portal'); ?>" class="btn-secondary" style="text-decoration: none;">
                    <i class="fa fa-arrow-left"></i> Back to Dashboard
                </a>
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

            <!-- Stats Overview -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon subjects">
                            <i class="fa fa-book"></i>
                        </div>
                        <span class="badge badge-success">+5%</span>
                    </div>
                    <div class="stat-value"><?php echo count($subjects); ?></div>
                    <div class="stat-label">Total Subjects</div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon resources">
                            <i class="fa fa-file-alt"></i>
                        </div>
                        <span class="badge badge-success">+12%</span>
                    </div>
                    <div class="stat-value">
                        <?php 
                        $totalResources = 0;
                        foreach($subjects as $subject) {
                            $totalResources += isset($subject->resource_count) ? $subject->resource_count : 0;
                        }
                        echo $totalResources;
                        ?>
                    </div>
                    <div class="stat-label">Total Resources</div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon faculty">
                            <i class="fa fa-chalkboard-teacher"></i>
                        </div>
                        <span class="badge badge-success">+3%</span>
                    </div>
                    <div class="stat-value">
                        <?php
                        $uniqueFaculties = [];
                        foreach($subjects as $subject) {
                            if(isset($subject->faculty_count)) {
                                $uniqueFaculties[] = $subject->faculty_count;
                            }
                        }
                        echo count(array_unique($uniqueFaculties));
                        ?>
                    </div>
                    <div class="stat-label">Faculty Teaching</div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div style="margin-bottom: 30px;">
                <!-- Tabs -->
                <div style="display: flex; gap: 10px; margin-bottom: 25px; border-bottom: 2px solid var(--border-color);">
                    <button class="tab-btn active" onclick="switchTab('single')" style="padding: 12px 24px; background: none; border: none; border-bottom: 3px solid transparent; color: var(--text-light); font-weight: 600; cursor: pointer; transition: all 0.3s ease;">
                        <i class="fas fa-plus-circle"></i> Single Subject
                    </button>
                    <button class="tab-btn" onclick="switchTab('bulk')" style="padding: 12px 24px; background: none; border: none; border-bottom: 3px solid transparent; color: var(--text-light); font-weight: 600; cursor: pointer; transition: all 0.3s ease;">
                        <i class="fas fa-file-upload"></i> Bulk Upload
                    </button>
                    <button class="tab-btn" onclick="switchTab('list')" style="padding: 12px 24px; background: none; border: none; border-bottom: 3px solid transparent; color: var(--text-light); font-weight: 600; cursor: pointer; transition: all 0.3s ease;">
                        <i class="fas fa-list"></i> View All
                    </button>
                </div>

                <!-- Single Subject Tab -->
                <div id="single-tab" class="tab-content" style="display: block;">
                    <div class="form-grid" style="grid-template-columns: 1fr; gap: 30px;">
                        <div class="form-container">
                            <div class="form-header">
                                <h3><i class="fa fa-plus-circle"></i> Add New Subject</h3>
                                <p>Create a new course subject for the academic curriculum</p>
                            </div>
                            
                            <form method="POST">
                                <input type="hidden" name="action" value="add_subject">
                                
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label class="form-label">
                                            Subject Name
                                            <span class="required">* Required</span>
                                        </label>
                                        <input type="text" name="subject_name" class="form-control" 
                                               placeholder="e.g., Artificial Intelligence" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="form-label">
                                            Subject Code
                                            <span class="required">* Required</span>
                                        </label>
                                        <input type="text" name="subject_code" class="form-control" 
                                               placeholder="e.g., AI101" required>
                                        <div class="form-hint">Unique course code identifier</div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="form-label">Semester</label>
                                        <select name="semester" class="form-control">
                                            <option value="">Select Semester</option>
                                            <?php for ($i = 1; $i <= 8; $i++): ?>
                                                <option value="<?php echo $i; ?>">Semester <?php echo $i; ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="form-label">Credits</label>
                                        <input type="number" name="credits" class="form-control" 
                                               placeholder="e.g., 3" min="1" max="10" value="3">
                                    </div>
                                </div>
                                
                                <div style="margin-top: 25px;">
                                    <button type="submit" class="btn-primary" style="width: 100%;">
                                        <i class="fa fa-plus"></i> Add Subject
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Bulk Upload Tab -->
                <div id="bulk-tab" class="tab-content" style="display: none;">
                    <div class="form-container">
                        <!-- Template Download -->
                        <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 12px; padding: 20px; margin-bottom: 25px;">
                            <h4 style="color: var(--primary-blue); margin-bottom: 10px; font-size: 16px;">
                                <i class="fas fa-download"></i> Download Template
                            </h4>
                            <p style="color: var(--text-light); font-size: 14px; margin-bottom: 15px;">
                                Download the CSV template, fill in subject details, and upload it below.
                            </p>
                            <a href="<?php echo base_url('simple_portal/download_subjects_template'); ?>" class="btn-primary">
                                <i class="fas fa-file-csv"></i> Download CSV Template
                            </a>
                        </div>

                        <!-- Upload Form -->
                        <form method="POST" action="<?php echo base_url('simple_portal/bulk_upload_subjects'); ?>" enctype="multipart/form-data" id="bulkUploadForm">
                            <input type="hidden" name="action" value="bulk_upload">
                            
                            <div id="uploadArea" onclick="document.getElementById('fileInput').click()" style="border: 2px dashed var(--border-color); border-radius: 12px; padding: 40px; text-align: center; transition: all 0.3s ease; cursor: pointer;">
                                <div style="font-size: 48px; color: var(--text-light); margin-bottom: 15px;">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                </div>
                                <div style="font-size: 16px; color: var(--text-dark); margin-bottom: 8px;">Click to upload or drag and drop</div>
                                <div style="font-size: 14px; color: var(--text-light);">CSV files (Max 5MB)</div>
                                <input type="file" id="fileInput" name="subjects_file" style="display: none;" accept=".csv" required>
                            </div>

                            <div id="fileInfo" style="margin-top: 15px; display: none;">
                                <p><strong>Selected file:</strong> <span id="fileName"></span></p>
                            </div>

                            <div style="display: flex; gap: 15px; margin-top: 30px;">
                                <button type="button" class="btn-secondary" onclick="resetUpload()">
                                    <i class="fas fa-times"></i> Clear
                                </button>
                                <button type="submit" class="btn-primary" style="flex: 1;">
                                    <i class="fas fa-upload"></i> Upload Subjects
                                </button>
                            </div>
                        </form>

                        <!-- Instructions -->
                        <div style="margin-top: 30px; padding: 20px; background: #f8fafc; border-radius: 8px;">
                            <h4 style="margin-bottom: 15px; color: var(--text-dark);">CSV Format Instructions:</h4>
                            <ul style="color: var(--text-light); line-height: 1.8;">
                                <li><strong>Required columns:</strong> subject_code, subject_name, semester</li>
                                <li><strong>Optional columns:</strong> credits</li>
                                <li><strong>Subject Code:</strong> Must be unique (e.g., CS101, MATH201)</li>
                                <li><strong>Semester:</strong> Number between 1 and 8</li>
                                <li><strong>Credits:</strong> Number between 1 and 6 (defaults to 3)</li>
                                <li><strong>Example:</strong> CS101,Introduction to Computer Science,1,4</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- List Tab -->
                <div id="list-tab" class="tab-content" style="display: none;">
                    <div class="form-container">
                        <div class="form-header">
                            <h3><i class="fa fa-list"></i> Subjects List</h3>
                            <p>All available subjects in the system</p>
                        </div>
                        
                        <?php if (!empty($subjects)): ?>
                            <div class="table-responsive">
                                <table class="subject-table">
                                    <thead>
                                        <tr>
                                            <th>Subject</th>
                                            <th>Code</th>
                                            <th>Semester</th>
                                            <th>Credits</th>
                                            <th>Resources</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($subjects as $subject): ?>
                                            <tr>
                                                <td>
                                                    <div style="font-weight: 600; color: var(--text-dark);">
                                                        <?php echo htmlspecialchars($subject->subject_name ?? $subject->name); ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge badge-primary">
                                                        <?php echo htmlspecialchars($subject->subject_code ?? $subject->code ?? 'N/A'); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span style="color: var(--text-dark); font-weight: 500;">
                                                        Sem <?php echo htmlspecialchars($subject->semester ?? 'N/A'); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span style="color: var(--text-dark); font-weight: 500;">
                                                        <?php echo htmlspecialchars($subject->credits ?? '3'); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-success">
                                                        <?php echo isset($subject->resource_count) ? $subject->resource_count : 0; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if (isset($subject->resource_count) && $subject->resource_count == 0): ?>
                                                        <button class="btn-secondary btn-sm" 
                                                                onclick="deleteSubject(<?php echo $subject->id; ?>, '<?php echo htmlspecialchars($subject->subject_name ?? $subject->name); ?>')"
                                                                style="background: linear-gradient(135deg, #fee2e2, #fca5a5); color: #991b1b; border: none;">
                                                            <i class="fa fa-trash"></i> Delete
                                                        </button>
                                                    <?php else: ?>
                                                        <span class="badge badge-secondary" style="cursor: not-allowed; opacity: 0.7;">
                                                            <i class="fa fa-lock"></i> Has Resources
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fa fa-book-open"></i>
                                <h4>No Subjects Available</h4>
                                <p>Start by adding your first subject using the form.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <a href="<?php echo base_url('simple_portal'); ?>" class="quick-action-btn">
                    <div class="quick-icon dashboard">
                        <i class="fa fa-tachometer-alt"></i>
                    </div>
                    <div class="quick-label">Dashboard</div>
                    <div class="quick-desc">Return to main dashboard</div>
                </a>
                
                <a href="<?php echo base_url('simple_portal/create_faculty'); ?>" class="quick-action-btn">
                    <div class="quick-icon create-faculty">
                        <i class="fa fa-user-plus"></i>
                    </div>
                    <div class="quick-label">Create Faculty</div>
                    <div class="quick-desc">Add new faculty members</div>
                </a>
                
                <a href="<?php echo base_url('simple_portal/create_student'); ?>" class="quick-action-btn">
                    <div class="quick-icon create-student">
                        <i class="fa fa-graduation-cap"></i>
                    </div>
                    <div class="quick-label">Create Student</div>
                    <div class="quick-desc">Register new students</div>
                </a>
                
                <a href="<?php echo base_url('simple_portal?action=logout'); ?>" class="quick-action-btn">
                    <div class="quick-icon logout">
                        <i class="fa fa-sign-out-alt"></i>
                    </div>
                    <div class="quick-label">Logout</div>
                    <div class="quick-desc">Sign out from system</div>
                </a>
            </div>

        </main>
    </div>
</div>

<!-- Delete Confirmation Form -->
<form id="deleteForm" method="POST" style="display: none;">
    <input type="hidden" name="action" value="delete_subject">
    <input type="hidden" name="subject_id" id="deleteSubjectId">
</form>

<script>
    function switchTab(tab) {
        // Update tab buttons
        document.querySelectorAll('.tab-btn').forEach(t => {
            t.classList.remove('active');
            t.style.color = 'var(--text-light)';
            t.style.borderBottomColor = 'transparent';
        });
        event.target.classList.add('active');
        event.target.style.color = 'var(--success-green)';
        event.target.style.borderBottomColor = 'var(--success-green)';
        
        // Update tab content
        document.querySelectorAll('.tab-content').forEach(c => c.style.display = 'none');
        document.getElementById(tab + '-tab').style.display = 'block';
    }

    // File upload handling
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('fileInput');
    const fileInfo = document.getElementById('fileInfo');
    const fileName = document.getElementById('fileName');

    if (fileInput) {
        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                fileName.textContent = this.files[0].name;
                fileInfo.style.display = 'block';
            }
        });

        // Drag and drop
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.style.borderColor = 'var(--success-green)';
            this.style.background = 'rgba(120, 184, 63, 0.05)';
        });

        uploadArea.addEventListener('dragleave', function() {
            this.style.borderColor = 'var(--border-color)';
            this.style.background = 'transparent';
        });

        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            this.style.borderColor = 'var(--border-color)';
            this.style.background = 'transparent';
            
            if (e.dataTransfer.files.length > 0) {
                fileInput.files = e.dataTransfer.files;
                fileName.textContent = e.dataTransfer.files[0].name;
                fileInfo.style.display = 'block';
            }
        });
    }

    function resetUpload() {
        if (fileInput) {
            fileInput.value = '';
            fileInfo.style.display = 'none';
        }
    }

    function deleteSubject(subjectId, subjectName) {
        if (confirm(`Are you sure you want to delete the subject "${subjectName}"? This action cannot be undone.`)) {
            document.getElementById('deleteSubjectId').value = subjectId;
            document.getElementById('deleteForm').submit();
        }
    }
    
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
        
        document.querySelector('.content-area').insertBefore(notification, document.querySelector('.stats-grid'));
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 5000);
    }
    
    // Add animation to table rows
    document.addEventListener('DOMContentLoaded', function() {
        const tableRows = document.querySelectorAll('.subject-table tbody tr');
        tableRows.forEach((row, index) => {
            row.style.opacity = '0';
            row.style.transform = 'translateY(10px)';
            
            setTimeout(() => {
                row.style.transition = 'all 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
                row.style.opacity = '1';
                row.style.transform = 'translateY(0)';
            }, index * 50);
        });
    });
</script>

</body>
</html>