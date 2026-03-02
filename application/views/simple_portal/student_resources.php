<?php
// Check if user is logged in and get username from session
$username = $this->session->userdata('username') ?? 'Student';
$user_id = $this->session->userdata('user_id');

// Get database instance
$ci =& get_instance();
$ci->load->database();

// DEBUG: Check what semester we're getting
echo "<!-- DEBUG: Current URL: " . current_url() . " -->\n";
echo "<!-- DEBUG: Semester param received: " . (isset($semester_param) ? $semester_param : 'NOT SET') . " -->\n";

// Get semester from URL segment - FIXED METHOD
$semester = $this->uri->segment(3); // simple_portal/student_resources/1 -> segment(3) = 1

if (empty($semester)) {
    // If no semester in URL, get student's current semester
    try {
        $student_query = $ci->db->query("
            SELECT s.current_semester 
            FROM students s 
            WHERE s.user_id = ?
        ", [$user_id]);
        
        $student_data = $student_query->row_array();
        $semester = $student_data['current_semester'] ?? 1;
    } catch (Exception $e) {
        $semester = 1;
    }
}

$current_semester = $semester; // For now, we'll use the same

try {
    // Get student data
    $student_query = $ci->db->query("
        SELECT s.* 
        FROM students s 
        WHERE s.user_id = ?
    ", [$user_id]);
    
    $student_data = $student_query->row_array();
    $current_semester = $student_data['current_semester'] ?? 1;
    
    // If semester is not valid, set to current semester
    if ($semester < 1 || $semester > $current_semester) {
        $semester = $current_semester;
    }
    
    echo "<!-- DEBUG: Final semester to use: " . $semester . " -->\n";
    echo "<!-- DEBUG: Current semester: " . $current_semester . " -->\n";
    
    // Get subject_id from URL
    $subject_id = $ci->input->get('subject_id');
    
    // Get subjects for this semester
    $subjects_query = $ci->db->query("
        SELECT * FROM subjects 
        WHERE semester = ? 
        ORDER BY subject_name ASC
    ", [$semester]);
    $semester_subjects = $subjects_query->result_array();
    
    // Build query for resources
    $sql = "
        SELECT r.*, 
               s.subject_name, 
               s.subject_code,
               u.username as uploaded_by_name
        FROM resources r
        LEFT JOIN subjects s ON r.subject_id = s.id
        LEFT JOIN users u ON r.uploaded_by = u.id
        WHERE r.is_active = 1 
        AND r.semester = ?
    ";
    
    $query_params = [$semester];
    
    // Add subject filter if selected
    if (!empty($subject_id)) {
        $sql .= " AND r.subject_id = ? ";
        $query_params[] = $subject_id;
    }
    
    $sql .= " ORDER BY r.created_at DESC";
    
    // Execute resources query
    $resources_query = $ci->db->query($sql, $query_params);
    
    $resources = $resources_query->result_array();
    
    echo "<!-- DEBUG: Found " . count($resources) . " resources for semester " . $semester . " -->\n";
    
    // Get all resources count
    $total_resources_query = $ci->db->query("
        SELECT COUNT(*) as total 
        FROM resources r
        WHERE r.is_active = 1 
        AND r.semester <= ?
    ", [$current_semester]);
    
    $total_resources = $total_resources_query->row()->total;
    
    // Get all semesters count
    $all_semesters = array();
    for ($i = 1; $i <= 8; $i++) {
        $sem_count_query = $ci->db->query("
            SELECT COUNT(*) as total 
            FROM resources r
            WHERE r.is_active = 1 
            AND r.semester = ?
        ", [$i]);
        
        $all_semesters[$i] = array(
            'total' => $sem_count_query->row()->total,
            'is_current' => ($i == $current_semester),
            'is_available' => ($i <= $current_semester)
        );
    }
    
} catch (Exception $e) {
    echo "<!-- DEBUG: Database error: " . $e->getMessage() . " -->\n";
    $current_semester = 1;
    $semester = 1;
    $resources = array();
    $total_resources = 0;
    $all_semesters = array();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Resources - SLAi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- Shared Student Sidebar CSS -->
    <?php include_once(APPPATH . 'views/simple_portal/components/student_sidebar_css.php'); ?>

    <style>
        /* Resource Specific Styles */
        .resources-container {
            padding: 20px 0;
        }

        .resources-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
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

        .stats-badge {
            background: linear-gradient(135deg, var(--success-green), #6ca736);
            color: var(--white);
            border: none;
            padding: 14px 28px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 12px rgba(120, 184, 63, 0.3);
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

        .stat-icon.total { background: linear-gradient(135deg, #667eea, #764ba2); }
        .stat-icon.current { background: linear-gradient(135deg, #059669, #047857); }
        .stat-icon.semester { background: linear-gradient(135deg, #0891b2, #0e7490); }
        .stat-icon.progress { background: linear-gradient(135deg, #d97706, #b45309); }

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

        /* Main Content Card */
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

        /* Semester Selection Grid */
        .semester-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .semester-card {
            background: var(--white);
            border: 2px solid var(--border-color);
            border-radius: 12px;
            padding: 25px;
            text-align: center;
            text-decoration: none;
            color: var(--text-dark);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .semester-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-blue), var(--primary-light));
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .semester-card.active::before {
            transform: scaleX(1);
            background: var(--primary-blue);
        }

        .semester-card.available:hover::before {
            transform: scaleX(1);
        }

        .semester-card.available:hover {
            border-color: var(--primary-blue);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(31, 94, 168, 0.15);
        }

        .semester-card.active {
            border-color: var(--primary-blue);
            background: linear-gradient(135deg, #f0f9ff, #ecfdf5);
        }

        .semester-card.locked {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .semester-icon {
            width: 50px;
            height: 50px;
            margin: 0 auto 15px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: var(--white);
        }

        .semester-card.available .semester-icon {
            background: linear-gradient(135deg, var(--primary-blue), var(--primary-light));
        }

        .semester-card.active .semester-icon {
            background: linear-gradient(135deg, var(--primary-blue), var(--primary-light));
        }

        .semester-card.locked .semester-icon {
            background: linear-gradient(135deg, #9ca3af, #6b7280);
        }

        .semester-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .semester-count {
            font-size: 13px;
            color: var(--text-light);
            margin-bottom: 8px;
        }

        .semester-status {
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 500;
        }

        .semester-status.current {
            background: #dcfce7;
            color: #166534;
        }

        .semester-status.available {
            background: #dbeafe;
            color: #1e40af;
        }

        .semester-status.locked {
            background: #f3f4f6;
            color: #6b7280;
        }

        /* Resources Grid */
        .resources-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 25px;
            margin-top: 20px;
        }

        .resource-card {
            background: var(--white);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .resource-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
            border-color: var(--primary-blue);
        }

        .resource-header {
            padding: 25px;
            border-bottom: 1px solid var(--border-color);
            background: linear-gradient(to right, #f8fafc, #f1f5f9);
        }

        .resource-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 10px;
            line-height: 1.3;
        }

        .resource-meta {
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 13px;
            color: var(--text-light);
        }

        .file-type-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            color: white;
        }

        .file-type-pdf { background: linear-gradient(135deg, #dc2626, #ef4444); }
        .file-type-doc { background: linear-gradient(135deg, #2563eb, #3b82f6); }
        .file-type-ppt { background: linear-gradient(135deg, #d97706, #f59e0b); }
        .file-type-xls { background: linear-gradient(135deg, #059669, #10b981); }
        .file-type-img { background: linear-gradient(135deg, #7c3aed, #8b5cf6); }
        .file-type-zip { background: linear-gradient(135deg, #475569, #64748b); }

        .resource-body {
            padding: 25px;
        }

        .resource-description {
            color: var(--text-light);
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 20px;
        }

        .resource-details {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .detail-item i {
            width: 16px;
            color: var(--primary-blue);
            font-size: 14px;
        }

        .detail-label {
            font-size: 12px;
            color: var(--text-light);
            margin-bottom: 2px;
        }

        .detail-value {
            font-size: 14px;
            font-weight: 500;
            color: var(--text-dark);
        }

        .resource-footer {
            padding: 20px 25px;
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(to right, #f8fafc, #f1f5f9);
        }

        .resource-size {
            font-size: 13px;
            color: var(--text-light);
        }

        .resource-actions {
            display: flex;
            gap: 10px;
        }

        .action-btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .action-btn.download {
            background: linear-gradient(135deg, var(--primary-blue), var(--primary-light));
            color: var(--white);
        }

        .action-btn.view {
            background: linear-gradient(135deg, #6B8BC3, var(--primary-blue));
            color: var(--white);
        }

        .action-btn.ai-buddy {
            background: linear-gradient(135deg, var(--success-green), #8bad5a);
            color: var(--white);
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .action-btn.download:hover {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary-blue));
        }

        .action-btn.view:hover {
            background: linear-gradient(135deg, var(--primary-blue), var(--primary-dark));
        }

        .action-btn.ai-buddy:hover {
            background: linear-gradient(135deg, #6ca736, var(--success-green));
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-light);
        }
        
        .empty-state i {
            font-size: 64px;
            color: #cbd5e1;
            margin-bottom: 20px;
        }
        
        .empty-state h3 {
            font-size: 20px;
            color: var(--text-dark);
            margin-bottom: 10px;
        }
        
        .empty-state p {
            margin-bottom: 20px;
        }
        
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: linear-gradient(135deg, var(--primary-blue), var(--primary-light));
            color: var(--white);
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .back-btn:hover {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary-blue));
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(31, 94, 168, 0.2);
        }

        /* Notification Alert */
        .notification {
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 15px;
            background: linear-gradient(135deg, #dbeafe, #eff6ff);
            border: 1px solid #93c5fd;
        }
        
        .notification i {
            font-size: 20px;
            color: #1e40af;
        }
        
        .notification-content {
            flex: 1;
        }
        
        .notification-title {
            font-weight: 600;
            color: #1e40af;
            margin-bottom: 5px;
        }
        
        .notification-text {
            color: #475569;
            font-size: 14px;
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
            
            .user-name {
                display: none;
            }
            
            .semester-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .resources-grid {
                grid-template-columns: 1fr;
            }
            
            .resource-details {
                grid-template-columns: 1fr;
            }
            
            .resource-footer {
                flex-direction: column;
                gap: 15px;
                align-items: stretch;
            }
            
            .resource-actions {
                justify-content: center;
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
            
            .semester-grid {
                grid-template-columns: 1fr;
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
    <!-- Sidebar -->
    <?php $this->load->view('simple_portal/components/student_sidebar', ['active_page' => 'resources']); ?>

    <!-- Main Content -->
    <div class="main-content">

        <!-- Topbar -->
        <header class="topbar">
            <div class="page-title">
                Learning Resources
            </div>

            <div class="user-profile">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($username, 0, 1)); ?>
                </div>
                <div class="user-info">
                    <div class="user-name"><?php echo htmlspecialchars($username); ?></div>
                    <div class="user-role">STUDENT</div>
                </div>
            </div>
        </header>

        <!-- Content Area -->
        <main class="content-area">

            <!-- Dashboard Header -->
            <div class="dashboard-header">
                <div class="header-title">
                    <h1>Learning Resources</h1>
                    <p>Access study materials, lecture notes, and resources for your courses</p>
                </div>
                <div class="stats-badge">
                    <i class="fa fa-graduation-cap"></i>
                    Semester <?php echo $current_semester; ?> • <?php echo $total_resources; ?> Total Resources
                </div>
            </div>

            <!-- Stats Overview -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon total">
                            <i class="fa fa-database"></i>
                        </div>
                        <span class="stat-trend">All Semesters</span>
                    </div>
                    <div class="stat-value"><?php echo $total_resources; ?></div>
                    <div class="stat-label">Total Resources Available</div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon current">
                            <i class="fa fa-calendar-check"></i>
                        </div>
                        <span class="stat-trend">Current Semester</span>
                    </div>
                    <div class="stat-value"><?php echo isset($all_semesters[$current_semester]['total']) ? $all_semesters[$current_semester]['total'] : 0; ?></div>
                    <div class="stat-label">Current Semester Resources</div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon semester">
                            <i class="fa fa-book-open"></i>
                        </div>
                        <span class="stat-trend">Selected Semester</span>
                    </div>
                    <div class="stat-value"><?php echo count($resources); ?></div>
                    <div class="stat-label">Semester <?php echo $semester; ?> Resources</div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon progress">
                            <i class="fa fa-chart-line"></i>
                        </div>
                        <span class="stat-trend">Access Level</span>
                    </div>
                    <div class="stat-value"><?php echo $current_semester; ?>/8</div>
                    <div class="stat-label">Semesters Unlocked</div>
                </div>
            </div>

            <!-- Semester Selection Card -->
            <div class="main-card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fa fa-calendar-alt"></i>
                        Select Semester
                    </div>
                    <div class="card-subtitle">Choose a semester to view its available resources</div>
                </div>
                <div class="card-body">
                    <?php if ($current_semester < 8): ?>
                        <div class="notification">
                            <i class="fa fa-info-circle"></i>
                            <div class="notification-content">
                                <div class="notification-title">Access Information</div>
                                <div class="notification-text">
                                    You can access materials from Semester 1 to <?php echo $current_semester; ?>. 
                                    Future semester materials will be available as you progress.
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="semester-grid">
                        <?php for ($i = 1; $i <= 8; $i++): 
                            $is_current = ($i == $current_semester);
                            $is_selected = ($i == $semester);
                            $is_available = ($i <= $current_semester);
                            $is_locked = ($i > $current_semester);
                            
                            if ($is_selected) {
                                $card_class = 'active';
                                $status_class = 'current';
                                $status_text = 'Selected';
                            } elseif ($is_current) {
                                $card_class = 'available';
                                $status_class = 'current';
                                $status_text = 'Current';
                            } elseif ($is_available) {
                                $card_class = 'available';
                                $status_class = 'available';
                                $status_text = 'Available';
                            } else {
                                $card_class = 'locked';
                                $status_class = 'locked';
                                $status_text = 'Locked';
                            }
                        ?>
                            <?php if ($is_available): ?>
                                <a href="<?php echo base_url('simple_portal/student_resources/' . $i); ?>" class="semester-card <?php echo $card_class; ?>">
                            <?php else: ?>
                                <div class="semester-card <?php echo $card_class; ?>">
                            <?php endif; ?>
                                <div class="semester-icon">
                                    <i class="fa fa-book"></i>
                                </div>
                                <div class="semester-title">Semester <?php echo $i; ?></div>
                                <div class="semester-count">
                                    <?php echo isset($all_semesters[$i]['total']) ? $all_semesters[$i]['total'] . ' resources' : '0 resources'; ?>
                                </div>
                                <div class="semester-status <?php echo $status_class; ?>">
                                    <?php echo $status_text; ?>
                                </div>
                            <?php if ($is_available): ?>
                                </a>
                            <?php else: ?>
                                </div>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>

            <!-- Subject Selection Card -->
            <?php if (!empty($semester_subjects)): ?>
            <div class="main-card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fa fa-book-open"></i>
                        Filter by Subject
                    </div>
                    <div class="card-subtitle">Select a subject to view specific resources</div>
                </div>
                <div class="card-body">
                    <div class="semester-grid" style="grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));">
                        <!-- All Subjects Option -->
                        <a href="<?php echo base_url('simple_portal/student_resources/' . $semester); ?>" 
                           class="semester-card <?php echo empty($subject_id) ? 'active' : 'available'; ?>">
                            <div class="semester-icon">
                                <i class="fa fa-th-list"></i>
                            </div>
                            <div class="semester-title">All Subjects</div>
                            <div class="semester-count">View all resources</div>
                            <div class="semester-status <?php echo empty($subject_id) ? 'current' : 'available'; ?>">
                                <?php echo empty($subject_id) ? 'Selected' : 'View All'; ?>
                            </div>
                        </a>

                        <?php foreach ($semester_subjects as $subj): 
                            $is_active = ($subject_id == $subj['id']);
                        ?>
                            <a href="<?php echo base_url('simple_portal/student_resources/' . $semester . '?subject_id=' . $subj['id']); ?>" 
                               class="semester-card <?php echo $is_active ? 'active' : 'available'; ?>">
                                <div class="semester-icon" style="background: linear-gradient(135deg, #059669, #047857);">
                                    <i class="fa fa-book"></i>
                                </div>
                                <div class="semester-title"><?php echo htmlspecialchars($subj['subject_name']); ?></div>
                                <div class="semester-count"><?php echo htmlspecialchars($subj['subject_code']); ?></div>
                                <div class="semester-status <?php echo $is_active ? 'current' : 'available'; ?>">
                                    <?php echo $is_active ? 'Selected' : 'View'; ?>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Resources Card -->
            <div class="main-card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fa fa-folder-open"></i>
                        Semester <?php echo $semester; ?> Resources
                        <?php if (count($resources) > 0): ?>
                            <span style="font-size: 14px; color: var(--text-light); margin-left: 10px;">
                                (<?php echo count($resources); ?> resources available)
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="card-subtitle">
                        <?php if ($semester == $current_semester): ?>
                            Current semester study materials and resources
                        <?php else: ?>
                            Archived materials from Semester <?php echo $semester; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (!empty($resources)): ?>
                        <div class="resources-grid">
                            <?php foreach ($resources as $resource): 
                                // Extract file extension from file_path
                                $file_path = $resource['file_path'];
                                $file_type = 'pdf'; // default
                                if (!empty($file_path)) {
                                    $path_info = pathinfo($file_path);
                                    if (isset($path_info['extension'])) {
                                        $file_type = strtolower($path_info['extension']);
                                    }
                                }
                                
                                // Map file types to CSS classes
                                $file_type_class = 'file-type-pdf';
                                if (in_array($file_type, ['doc', 'docx'])) $file_type_class = 'file-type-doc';
                                elseif (in_array($file_type, ['ppt', 'pptx'])) $file_type_class = 'file-type-ppt';
                                elseif (in_array($file_type, ['xls', 'xlsx', 'csv'])) $file_type_class = 'file-type-xls';
                                elseif (in_array($file_type, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg'])) $file_type_class = 'file-type-img';
                                elseif (in_array($file_type, ['zip', 'rar', '7z', 'tar', 'gz'])) $file_type_class = 'file-type-zip';
                                elseif (in_array($file_type, ['txt', 'rtf'])) $file_type_class = 'file-type-pdf'; // Default to PDF style for text files
                            ?>
                                <div class="resource-card">
                                    <div class="resource-header">
                                        <div class="resource-title"><?php echo htmlspecialchars($resource['title']); ?></div>
                                        <div class="resource-meta">
                                            <span class="file-type-badge <?php echo $file_type_class; ?>">
                                                <?php echo strtoupper($file_type); ?>
                                            </span>
                                            <span>
                                                <?php echo date('M d, Y', strtotime($resource['created_at'])); ?>
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="resource-body">
                                        <?php if (!empty($resource['description'])): ?>
                                            <div class="resource-description">
                                                <?php echo htmlspecialchars($resource['description']); ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="resource-details">
                                            <div class="detail-item">
                                                <i class="fa fa-book"></i>
                                                <div>
                                                    <div class="detail-label">Subject</div>
                                                    <div class="detail-value"><?php echo htmlspecialchars($resource['subject_name'] ?: 'General'); ?></div>
                                                </div>
                                            </div>
                                            
                                            <div class="detail-item">
                                                <i class="fa fa-user"></i>
                                                <div>
                                                    <div class="detail-label">Uploaded By</div>
                                                    <div class="detail-value"><?php echo htmlspecialchars($resource['uploaded_by_name'] ?: 'Faculty'); ?></div>
                                                </div>
                                            </div>
                                            
                                            <div class="detail-item">
                                                <i class="fa fa-code"></i>
                                                <div>
                                                    <div class="detail-label">Subject Code</div>
                                                    <div class="detail-value"><?php echo htmlspecialchars($resource['subject_code'] ?: 'N/A'); ?></div>
                                                </div>
                                            </div>
                                            
                                            <div class="detail-item">
                                                <i class="fa fa-clock"></i>
                                                <div>
                                                    <div class="detail-label">Upload Date</div>
                                                    <div class="detail-value"><?php echo date('M d, Y', strtotime($resource['created_at'])); ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="resource-footer">
                                        <div class="resource-size">
                                            <?php if (isset($resource['file_size']) && $resource['file_size'] > 0): ?>
                                                <?php 
                                                $size = $resource['file_size'];
                                                if ($size < 1024) {
                                                    $size_text = $size . ' bytes';
                                                } elseif ($size < 1048576) {
                                                    $size_text = round($size / 1024, 1) . ' KB';
                                                } else {
                                                    $size_text = round($size / 1048576, 1) . ' MB';
                                                }
                                                ?>
                                                <i class="fa fa-hdd"></i> <?php echo $size_text; ?>
                                            <?php else: ?>
                                                <i class="fa fa-hdd"></i> Size: N/A
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="resource-actions">
                                            <?php if ($file_type !== 'weblink'): ?>
                                                <button class="action-btn view" onclick="previewResource(<?php echo $resource['id']; ?>)" style="background: linear-gradient(135deg, #6B8BC3, var(--primary-blue)); color: white; border: none; cursor: pointer;">
                                                    <i class="fa fa-eye"></i>
                                                    Preview
                                                </button>
                                            <?php endif; ?>
                                            <?php if ($file_type === 'pdf'): ?>
                                                <a href="<?php echo base_url('simple_portal/start_subject_chat?subject_id=' . $resource['subject_id']); ?>" 
                                                   class="action-btn ai-buddy">
                                                    <i class="fa fa-robot"></i>
                                                    HawkAI
                                                </a>
                                            <?php endif; ?>
                                            <a href="<?php echo base_url('simple_portal/download_resource/' . $resource['id']); ?>" 
                                               class="action-btn download">
                                                <i class="fa fa-download"></i>
                                                Download
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fa fa-folder-open"></i>
                            <h3>No Resources Available</h3>
                            <p>No resources have been uploaded for Semester <?php echo $semester; ?> yet.</p>
                            <div style="display: flex; gap: 10px; justify-content: center;">
                                <?php if ($semester != $current_semester): ?>
                                    <a href="<?php echo base_url('simple_portal/student_resources/' . $current_semester); ?>" class="back-btn">
                                        <i class="fa fa-arrow-left"></i>
                                        View Current Semester
                                    </a>
                                <?php endif; ?>
                                <a href="<?php echo base_url('simple_portal'); ?>" class="back-btn">
                                    <i class="fa fa-home"></i>
                                    Back to Dashboard
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    // Initialize interactive features
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DEBUG: Page loaded');
        console.log('DEBUG: Selected semester from URL: <?php echo $semester; ?>');
        console.log('DEBUG: Current semester: <?php echo $current_semester; ?>');
        console.log('DEBUG: Resources found: <?php echo count($resources); ?>');
        
        // Debug all semester links
        const semesterLinks = document.querySelectorAll('.semester-card[href]');
        console.log('DEBUG: Found ' + semesterLinks.length + ' semester links');
        semesterLinks.forEach((link, index) => {
            console.log('DEBUG: Link ' + (index + 1) + ': ' + link.href);
        });
        
        // Test if clicking works
        semesterLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                console.log('DEBUG: Clicked on: ' + this.href);
                console.log('DEBUG: This semester: ' + this.querySelector('.semester-title').textContent);
            });
        });
        
        // Add hover effects
        const statCards = document.querySelectorAll('.stat-card');
        statCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-8px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
        
        // Resource card animations
        const resourceCards = document.querySelectorAll('.resource-card');
        resourceCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    });

    // Resource preview function
    function previewResource(resourceId) {
        window.open('<?php echo base_url("simple_portal/preview_resource/"); ?>' + resourceId, '_blank');
    }
</script>

</body>
</html>