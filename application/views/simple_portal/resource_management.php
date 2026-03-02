<?php
// Helper function for formatting file size
if (!function_exists('formatFileSize')) {
    function formatFileSize($bytes) {
        if ($bytes == 0) return '0 Bytes';
        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes) / log($k));
        return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resource Management - AI Powered Academic Hub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <?php $this->load->view('simple_portal/components/faculty_sidebar_css'); ?>
    
    <style>
        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Topbar */
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
            z-index: 100;
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
            flex: 1;
            padding: 35px;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
            overflow-y: auto;
        }

        /* Header Section */
        .header-section {
            margin-bottom: 30px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .page-header h1 {
            font-size: 32px;
            font-weight: 800;
            color: var(--text-dark);
            background: linear-gradient(135deg, var(--text-dark), #475569);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1.2;
        }

        .page-subtitle {
            color: var(--text-light);
            font-size: 16px;
            line-height: 1.5;
            margin-top: 10px;
        }

        .action-buttons {
            display: flex;
            gap: 12px;
        }

        .btn {
            padding: 14px 28px;
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
            background: linear-gradient(135deg, var(--primary-blue), var(--primary-light));
            color: var(--white);
        }

        .btn-secondary:hover {
            background: linear-gradient(135deg, var(--primary-light), var(--primary-blue));
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(31, 94, 168, 0.4);
        }

        /* Search Bar */
        .search-container {
            position: relative;
            width: 350px;
        }

        .search-bar {
            width: 100%;
            padding: 14px 16px 14px 48px;
            border: 2px solid var(--border-color);
            border-radius: 12px;
            font-size: 15px;
            color: var(--text-dark);
            background: var(--white);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .search-bar:focus {
            outline: none;
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 4px rgba(31, 94, 168, 0.15);
        }

        .search-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
            font-size: 16px;
        }

        /* Filters */
        .filters-container {
            background: var(--white);
            border-radius: 16px;
            border: 1px solid var(--border-color);
            padding: 20px;
            margin-bottom: 24px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .filter-row {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .filter-group {
            flex: 1;
            min-width: 200px;
        }

        .filter-label {
            display: block;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 8px;
            font-size: 14px;
        }

        .filter-select {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--border-color);
            border-radius: 10px;
            font-size: 14px;
            color: var(--text-dark);
            background: var(--white);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .filter-select:focus {
            outline: none;
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 4px rgba(31, 94, 168, 0.15);
        }

        .reset-filters {
            background: var(--white);
            color: var(--text-light);
            border: 2px solid var(--border-color);
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .reset-filters:hover {
            background: var(--light-bg);
            border-color: var(--error-red);
            color: var(--error-red);
        }

        /* Resources Grid */
        .resources-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 24px;
            margin-bottom: 35px;
        }

        .resource-card {
            background: var(--white);
            border-radius: 16px;
            border: 1px solid var(--border-color);
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .resource-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
            border-color: var(--primary-blue);
        }

        .resource-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-blue), var(--primary-light));
        }

        .resource-header {
            padding: 24px;
            border-bottom: 1px solid var(--border-color);
            position: relative;
        }

        .resource-type {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: var(--white);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .resource-type.pdf { background: linear-gradient(135deg, #ef4444, #dc2626); }
        .resource-type.ppt, .resource-type.pptx { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .resource-type.xls, .resource-type.xlsx { background: linear-gradient(135deg, var(--success-green), #5a942f); }
        .resource-type.csv { background: linear-gradient(135deg, #3b82f6, #2563eb); }
        .resource-type.epub { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
        .resource-type.doc, .resource-type.docx { background: linear-gradient(135deg, #0891b2, #0e7490); }
        .resource-type.txt { background: linear-gradient(135deg, #6b7280, #4b5563); }
        .resource-type.weblink { background: linear-gradient(135deg, var(--purple), var(--pink)); }

        .resource-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 8px;
            line-height: 1.3;
            padding-right: 60px;
        }

        .resource-description {
            font-size: 14px;
            color: var(--text-light);
            line-height: 1.5;
            margin-bottom: 16px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .resource-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 16px;
        }

        .resource-tag {
            background: var(--light-bg);
            color: var(--text-dark);
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .resource-tag i {
            font-size: 14px;
        }

        .resource-body {
            padding: 20px 24px;
        }

        .resource-info {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
            margin-bottom: 20px;
        }

        .info-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .info-label {
            font-size: 12px;
            color: var(--text-light);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-value {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-dark);
        }

        .resource-actions {
            display: flex;
            gap: 12px;
        }

        .action-btn {
            flex: 1;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .action-btn.download {
            background: linear-gradient(135deg, var(--primary-blue), var(--primary-light));
            color: white;
        }

        .action-btn.download:hover {
            background: linear-gradient(135deg, var(--primary-light), var(--primary-blue));
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(31, 94, 168, 0.3);
        }

        .action-btn.view {
            background: var(--white);
            color: var(--text-dark);
            border: 2px solid var(--border-color);
        }

        .action-btn.view:hover {
            background: var(--light-bg);
            border-color: var(--primary-blue);
            color: var(--primary-blue);
            transform: translateY(-2px);
        }

        /* Empty State */
        .empty-state {
            background: var(--white);
            border-radius: 18px;
            border: 1px solid var(--border-color);
            padding: 60px 40px;
            text-align: center;
            margin-bottom: 35px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        }

        .empty-state-icon {
            font-size: 64px;
            color: #cbd5e1;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 12px;
        }

        .empty-state p {
            color: var(--text-light);
            font-size: 16px;
            line-height: 1.5;
            max-width: 500px;
            margin: 0 auto 30px;
        }

        /* Stats Overview */
        .stats-overview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--white);
            border-radius: 14px;
            padding: 24px;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
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

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: var(--white);
            margin-bottom: 16px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .stat-icon.total { background: linear-gradient(135deg, #667eea, #764ba2); }
        .stat-icon.pdf { background: linear-gradient(135deg, #ef4444, #dc2626); }
        .stat-icon.ppt { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .stat-icon.link { background: linear-gradient(135deg, var(--purple), var(--pink)); }

        .stat-value {
            font-size: 32px;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 4px;
            line-height: 1;
        }

        .stat-label {
            font-size: 14px;
            color: var(--text-light);
            font-weight: 500;
        }

        /* Flash Messages */
        .flash-message {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideIn 0.3s ease-out;
            border: 1px solid transparent;
        }

        .flash-success {
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
            border-color: #10b981;
            color: #065f46;
        }

        .flash-error {
            background: linear-gradient(135deg, #fee2e2, #fca5a5);
            border-color: #ef4444;
            color: #991b1b;
        }

        .flash-close {
            margin-left: auto;
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: inherit;
            opacity: 0.7;
            transition: opacity 0.3s ease;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
        }

        .flash-close:hover {
            opacity: 1;
            background: rgba(0, 0, 0, 0.1);
        }

        /* Quick Actions */
        .quick-actions {
            margin-top: 35px;
        }

        .quick-actions h3 {
            font-size: 20px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .quick-actions h3 i {
            color: var(--primary-blue);
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }

        .quick-action-btn {
            background: var(--white);
            border: 2px solid var(--border-color);
            border-radius: 14px;
            padding: 24px 20px;
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

        .quick-icon.upload { background: linear-gradient(135deg, #667eea, #764ba2); }
        .quick-icon.dashboard { background: linear-gradient(135deg, var(--success-green), #5a942f); }
        .quick-icon.ai { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .quick-icon.profile { background: linear-gradient(135deg, #0891b2, #0e7490); }

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
        @media (max-width: 1200px) {
            .resources-grid {
                grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
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
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }
            
            .topbar {
                padding: 0 20px;
                height: 70px;
            }
            
            .content-area {
                padding: 20px;
            }
            
            .search-container {
                width: 100%;
                max-width: 300px;
            }
            
            .filter-row {
                flex-direction: column;
            }
            
            .filter-group {
                min-width: 100%;
            }
            
            .resources-grid {
                grid-template-columns: 1fr;
            }
            
            .action-buttons {
                width: 100%;
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
            
            .page-header h1 {
                font-size: 24px;
            }
            
            .resource-actions {
                flex-direction: column;
            }
            
            .stats-overview {
                grid-template-columns: 1fr;
            }
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .stats-overview, .resource-card, .quick-action-btn {
            animation: fadeIn 0.5s ease-out forwards;
            opacity: 0;
        }

        .stats-overview .stat-card:nth-child(1) { animation-delay: 0.1s; }
        .stats-overview .stat-card:nth-child(2) { animation-delay: 0.2s; }
        .stats-overview .stat-card:nth-child(3) { animation-delay: 0.3s; }
        .stats-overview .stat-card:nth-child(4) { animation-delay: 0.4s; }

        .flash-message {
            animation: slideIn 0.3s ease-out;
        }
    </style>
</head>
<body>

<div class="portal-container">


    <!-- Sidebar -->
    <?php $this->load->view('simple_portal/components/faculty_sidebar', ['active_page' => 'resources']); ?>

    <!-- Main Content -->
    <div class="main-content">

        <!-- Topbar -->
        <header class="topbar">
            <div class="page-title">
                Resource Management
            </div>

            <div class="search-container">
                <i class="fa fa-search search-icon"></i>
                <input type="text" class="search-bar" placeholder="Search resources by title, subject, or type..." 
                       id="resourceSearch" autocomplete="off">
            </div>

            <div class="user-profile">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($username, 0, 1)); ?>
                </div>
                <div class="user-info">
                    <div class="user-name"><?php echo htmlspecialchars($username); ?></div>
                    <div class="user-role">FACULTY</div>
                </div>
            </div>
        </header>

        <!-- Content Area -->
        <main class="content-area">

            <!-- Header Section -->
            <div class="header-section">
                <div class="page-header">
                    <div>
                        <h1><?php echo isset($filter_subject) ? 'Resources: ' . htmlspecialchars($filter_subject) : 'My Resources'; ?></h1>
                        <p class="page-subtitle"><?php echo isset($filter_subject) ? 'Manage resources for this subject.' : 'Manage and organize your teaching materials, notes, and learning resources'; ?></p>
                    </div>
                    <div class="action-buttons">
                        <?php if (isset($filter_subject)): ?>
                        <a href="<?php echo base_url('simple_portal/resources'); ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to All
                        </a>
                        <?php endif; ?>
                        <a href="<?php echo base_url('simple_portal/upload_resource'); ?>" class="btn btn-primary">
                            <i class="fa fa-plus"></i> Upload New Resource
                        </a>
                        <?php if (!isset($filter_subject)): ?>
                        <a href="<?php echo base_url('simple_portal'); ?>" class="btn btn-secondary">
                            <i class="fa fa-tachometer-alt"></i> Dashboard
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Flash Messages -->
            <?php if ($this->session->flashdata('message')): ?>
                <div class="flash-message flash-<?php echo $this->session->flashdata('message_type'); ?>">
                    <i class="fa fa-<?php echo $this->session->flashdata('message_type') === 'error' ? 'exclamation-triangle' : 'check-circle'; ?>"></i>
                    <span><?php echo $this->session->flashdata('message'); ?></span>
                    <button type="button" class="flash-close" onclick="this.parentElement.style.display='none'">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            <?php endif; ?>

            <?php if (!empty($resources)): ?>
                <!-- Statistics Overview -->
                <div class="stats-overview">
                    <?php
                    // Calculate statistics
                    $total_resources = count($resources);
                    $pdf_count = 0;
                    $ppt_count = 0;
                    $link_count = 0;
                    
                    foreach ($resources as $resource) {
                        if ($resource->file_type === 'pdf') $pdf_count++;
                        if (in_array($resource->file_type, ['ppt', 'pptx'])) $ppt_count++;
                        if ($resource->file_type === 'weblink') $link_count++;
                    }
                    ?>
                    
                    <div class="stat-card">
                        <div class="stat-icon total">
                            <i class="fa fa-folder-open"></i>
                        </div>
                        <div class="stat-value"><?php echo $total_resources; ?></div>
                        <div class="stat-label">Total Resources</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon pdf">
                            <i class="fa fa-file-pdf"></i>
                        </div>
                        <div class="stat-value"><?php echo $pdf_count; ?></div>
                        <div class="stat-label">PDF Documents</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon ppt">
                            <i class="fa fa-file-powerpoint"></i>
                        </div>
                        <div class="stat-value"><?php echo $ppt_count; ?></div>
                        <div class="stat-label">Presentations</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon link">
                            <i class="fa fa-link"></i>
                        </div>
                        <div class="stat-value"><?php echo $link_count; ?></div>
                        <div class="stat-label">Web Links</div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="filters-container">
                    <div class="filter-row">
                        <div class="filter-group">
                            <label class="filter-label">Subject</label>
                            <select class="filter-select" id="subjectFilter">
                                <option value="">All Subjects</option>
                                <?php 
                                // Get unique subjects
                                $unique_subjects = [];
                                foreach ($resources as $resource) {
                                    $subject_key = $resource->subject_id . '-' . ($resource->subject_name ?: 'Subject ' . $resource->subject_id);
                                    if (!isset($unique_subjects[$subject_key])) {
                                        $unique_subjects[$subject_key] = $resource;
                                        echo '<option value="' . htmlspecialchars($subject_key) . '">' . 
                                             htmlspecialchars($resource->subject_name ?: 'Subject ' . $resource->subject_id) . 
                                             '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label class="filter-label">Semester</label>
                            <select class="filter-select" id="semesterFilter">
                                <option value="">All Semesters</option>
                                <?php 
                                // Get unique semesters
                                $unique_semesters = [];
                                foreach ($resources as $resource) {
                                    if (!in_array($resource->semester, $unique_semesters)) {
                                        $unique_semesters[] = $resource->semester;
                                        echo '<option value="' . $resource->semester . '">Semester ' . $resource->semester . '</option>';
                                    }
                                }
                                sort($unique_semesters);
                                ?>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label class="filter-label">Resource Type</label>
                            <select class="filter-select" id="typeFilter">
                                <option value="">All Types</option>
                                <?php 
                                // Get unique file types
                                $unique_types = [];
                                foreach ($resources as $resource) {
                                    $file_type = $resource->file_type;
                                    if (!in_array($file_type, $unique_types)) {
                                        $unique_types[] = $file_type;
                                        $display_name = strtoupper($file_type);
                                        if ($file_type === 'weblink') $display_name = 'Web Link';
                                        echo '<option value="' . $file_type . '">' . $display_name . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        
                        <button type="button" class="reset-filters" onclick="resetFilters()">
                            <i class="fa fa-redo"></i>
                            Reset Filters
                        </button>
                    </div>
                </div>

                <!-- Resources Grid -->
                <div class="resources-grid" id="resourcesGrid">
                    <?php foreach ($resources as $resource): ?>
                        <div class="resource-card" data-subject="<?php echo htmlspecialchars($resource->subject_id . '-' . ($resource->subject_name ?: 'Subject ' . $resource->subject_id)); ?>" 
                             data-semester="<?php echo $resource->semester; ?>" data-type="<?php echo $resource->file_type; ?>">
                            <div class="resource-header">
                                <div class="resource-type <?php echo $resource->file_type; ?>">
                                    <i class="fa fa-<?php 
                                        echo $resource->file_type === 'pdf' ? 'file-pdf' : 
                                               (in_array($resource->file_type, ['ppt', 'pptx']) ? 'file-powerpoint' :
                                               (in_array($resource->file_type, ['xls', 'xlsx', 'csv']) ? 'file-excel' :
                                               (in_array($resource->file_type, ['doc', 'docx']) ? 'file-word' :
                                               ($resource->file_type === 'epub' ? 'book' :
                                               ($resource->file_type === 'txt' ? 'file-alt' :
                                               ($resource->file_type === 'weblink' ? 'link' : 'file')))))); ?>"></i>
                                </div>
                                <h3 class="resource-title"><?php echo htmlspecialchars($resource->title); ?></h3>
                                <?php if ($resource->description): ?>
                                    <p class="resource-description"><?php echo htmlspecialchars($resource->description); ?></p>
                                <?php endif; ?>
                                
                                <div class="resource-meta">
                                    <span class="resource-tag">
                                        <i class="fa fa-book"></i>
                                        <?php echo htmlspecialchars($resource->subject_name ?: 'Subject ' . $resource->subject_id); ?>
                                    </span>
                                    <span class="resource-tag">
                                        <i class="fa fa-graduation-cap"></i>
                                        Semester <?php echo $resource->semester; ?>
                                    </span>
                                    <span class="resource-tag">
                                        <i class="fa fa-calendar"></i>
                                        <?php echo date('M j, Y', strtotime($resource->created_at ?: $resource->upload_date)); ?>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="resource-body">
                                <div class="resource-info">
                                    <div class="info-item">
                                        <span class="info-label">Type</span>
                                        <span class="info-value">
                                            <?php 
                                            $type_name = strtoupper($resource->file_type);
                                            if ($resource->file_type === 'weblink') $type_name = 'Web Link';
                                            echo $type_name;
                                            ?>
                                        </span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Size</span>
                                        <span class="info-value">
                                            <?php 
                                            if ($resource->file_type === 'weblink') {
                                                echo 'Web Link';
                                            } else {
                                                echo isset($resource->file_size) ? formatFileSize($resource->file_size) : 'N/A';
                                            }
                                            ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="resource-actions">
                                    <a href="<?php echo base_url('simple_portal/download_resource/' . $resource->id); ?>" 
                                       class="action-btn download">
                                        <i class="fa fa-download"></i>
                                        <?php echo $resource->file_type === 'weblink' ? 'Visit Link' : 'Download'; ?>
                                    </a>
                                    <?php if ($resource->file_type !== 'weblink'): ?>
                                        <button class="action-btn view" onclick="previewResource(<?php echo $resource->id; ?>)">
                                            <i class="fa fa-eye"></i>
                                            Preview
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

            <?php else: ?>
                <!-- Empty State -->
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fa fa-folder-open"></i>
                    </div>
                    <h3>No Resources Yet</h3>
                    <p>You haven't uploaded any resources yet. Start sharing your teaching materials with students by uploading your first resource.</p>
                    <div class="action-buttons" style="justify-content: center;">
                        <a href="<?php echo base_url('simple_portal/upload_resource'); ?>" class="btn btn-primary">
                            <i class="fa fa-plus"></i> Upload Your First Resource
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <h3><i class="fa fa-bolt"></i> Quick Actions</h3>
                <div class="actions-grid">
                    <a href="<?php echo base_url('simple_portal'); ?>" class="quick-action-btn">
                        <div class="quick-icon dashboard">
                            <i class="fa fa-tachometer-alt"></i>
                        </div>
                        <div class="quick-label">Dashboard</div>
                        <div class="quick-desc">Return to main dashboard</div>
                    </a>
                    
                    <a href="<?php echo base_url('simple_portal/upload_resource'); ?>" class="quick-action-btn">
                        <div class="quick-icon upload">
                            <i class="fa fa-upload"></i>
                        </div>
                        <div class="quick-label">Upload Resource</div>
                        <div class="quick-desc">Share new learning materials</div>
                    </a>
                    
                    <a href="<?php echo base_url('simple_portal/generate_assignment'); ?>" class="quick-action-btn">
                        <div class="quick-icon ai">
                            <i class="fa fa-robot"></i>
                        </div>
                        <div class="quick-label">AI Assignment</div>
                        <div class="quick-desc">Generate assignments using AI</div>
                    </a>
                    
                    <a href="<?php echo base_url('simple_portal/profile'); ?>" class="quick-action-btn">
                        <div class="quick-icon profile">
                            <i class="fa fa-user"></i>
                        </div>
                        <div class="quick-label">Profile</div>
                        <div class="quick-desc">View and edit your profile</div>
                    </a>
                </div>
            </div>

        </main>
    </div>
</div>

<script>
    // Format file size function
    function formatFileSize(bytes) {
        if (typeof bytes !== 'number' || bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Resource filtering
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('resourceSearch');
        const subjectFilter = document.getElementById('subjectFilter');
        const semesterFilter = document.getElementById('semesterFilter');
        const typeFilter = document.getElementById('typeFilter');
        const resourceCards = document.querySelectorAll('.resource-card');
        
        // Search functionality
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                filterResources();
            });
        }
        
        // Filter functionality
        if (subjectFilter) subjectFilter.addEventListener('change', filterResources);
        if (semesterFilter) semesterFilter.addEventListener('change', filterResources);
        if (typeFilter) typeFilter.addEventListener('change', filterResources);
        
        function filterResources() {
            const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';
            const selectedSubject = subjectFilter ? subjectFilter.value : '';
            const selectedSemester = semesterFilter ? semesterFilter.value : '';
            const selectedType = typeFilter ? typeFilter.value : '';
            
            let visibleCount = 0;
            
            resourceCards.forEach(card => {
                const title = card.querySelector('.resource-title').textContent.toLowerCase();
                const description = card.querySelector('.resource-description') ? 
                    card.querySelector('.resource-description').textContent.toLowerCase() : '';
                const subject = card.getAttribute('data-subject');
                const semester = card.getAttribute('data-semester');
                const type = card.getAttribute('data-type');
                
                // Check search term
                const matchesSearch = !searchTerm || 
                    title.includes(searchTerm) || 
                    description.includes(searchTerm);
                
                // Check filters
                const matchesSubject = !selectedSubject || subject === selectedSubject;
                const matchesSemester = !selectedSemester || semester === selectedSemester;
                const matchesType = !selectedType || type === selectedType;
                
                // Show/hide card
                if (matchesSearch && matchesSubject && matchesSemester && matchesType) {
                    card.style.display = 'block';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });
            
            // Show message if no resources match
            const emptyState = document.querySelector('.empty-state');
            const statsOverview = document.querySelector('.stats-overview');
            const filtersContainer = document.querySelector('.filters-container');
            
            if (visibleCount === 0 && resourceCards.length > 0) {
                if (!emptyState) {
                    const message = document.createElement('div');
                    message.className = 'empty-state';
                    message.innerHTML = `
                        <div class="empty-state-icon">
                            <i class="fa fa-search"></i>
                        </div>
                        <h3>No Matching Resources</h3>
                        <p>No resources found matching your search criteria. Try adjusting your filters.</p>
                        <button class="btn btn-secondary" onclick="resetFilters()">
                            <i class="fa fa-redo"></i> Reset Filters
                        </button>
                    `;
                    
                    if (statsOverview && filtersContainer) {
                        statsOverview.style.display = 'none';
                        filtersContainer.style.display = 'none';
                    }
                    
                    const resourcesGrid = document.getElementById('resourcesGrid');
                    if (resourcesGrid) {
                        resourcesGrid.style.display = 'none';
                        resourcesGrid.parentNode.insertBefore(message, resourcesGrid);
                    }
                }
            } else {
                if (emptyState && emptyState.querySelector('.fa-search')) {
                    emptyState.remove();
                }
                if (statsOverview) statsOverview.style.display = 'grid';
                if (filtersContainer) filtersContainer.style.display = 'block';
                
                const resourcesGrid = document.getElementById('resourcesGrid');
                if (resourcesGrid) resourcesGrid.style.display = 'grid';
            }
        }
        
        // Reset filters
        window.resetFilters = function() {
            if (searchInput) searchInput.value = '';
            if (subjectFilter) subjectFilter.value = '';
            if (semesterFilter) semesterFilter.value = '';
            if (typeFilter) typeFilter.value = '';
            filterResources();
        };
        
        // Resource preview
        window.previewResource = function(resourceId) {
            // Open resource preview in new tab
            window.open('<?php echo base_url("simple_portal/preview_resource/"); ?>' + resourceId, '_blank');
        };
        
        // Auto-hide flash messages after 5 seconds
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
        
        // Add animation delays for resource cards
        resourceCards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.05}s`;
        });
    });
</script>

</body>
</html>