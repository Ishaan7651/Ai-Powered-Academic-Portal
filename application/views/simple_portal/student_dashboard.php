<?php
// Check if user is logged in and get username from session
$username = $this->session->userdata('username') ?? 'Student';
$user_id = $this->session->userdata('user_id');

// Get database instance
$ci =& get_instance();
$ci->load->database();

try {
    // Get student data
    $student_query = $ci->db->query("
        SELECT s.*, u.email 
        FROM students s 
        JOIN users u ON s.user_id = u.id 
        WHERE s.user_id = ? AND u.is_active = 1
    ", [$user_id]);
    
    $student_data = $student_query->row_array();
    $current_semester = isset($student_data['current_semester']) ? $student_data['current_semester'] : 1;
    $enrollment_year = isset($student_data['enrollment_year']) ? $student_data['enrollment_year'] : date('Y');
    $student_id = isset($student_data['student_id']) ? $student_data['student_id'] : 'N/A';
    $student_db_id = isset($student_data['id']) ? $student_data['id'] : null; // This is the actual database ID for enrollments
    
    // Count total resources available for student's semester and previous semesters
    $total_resources = 0;
    if ($ci->db->table_exists('resources')) {
        for ($i = 1; $i <= $current_semester; $i++) {
            $ci->db->where('semester', $i);
            $ci->db->where('is_active', 1);
            $total_resources += $ci->db->count_all_results('resources');
        }
    }
    
    // Count AI chat sessions for this student
    $total_chats = 0;
    if ($ci->db->table_exists('ai_chat_sessions')) {
        $ci->db->where('user_id', $user_id);
        $ci->db->where('is_active', 1);
        $total_chats = $ci->db->count_all_results('ai_chat_sessions');
    }

    // Calculate Course Progress (Quizzes Completed / Total Quizzes)
    $course_progress = 0;
    $total_quizzes = 0;
    $attempted_quizzes = 0;
    
    if ($ci->db->table_exists('ai_quizzes') && $ci->db->table_exists('subjects') && $ci->db->table_exists('student_enrollments') && $student_db_id) {
        // Count total published quizzes for student's enrolled subjects
        $total_quizzes_query = $ci->db->query("
            SELECT COUNT(q.id) as count
            FROM ai_quizzes q
            JOIN subjects s ON q.subject_id = s.id
            JOIN student_enrollments se ON q.subject_id = se.subject_id
            WHERE q.is_published = 1 
            AND se.student_id = ?
        ", [$student_db_id]);
        $total_quizzes = $total_quizzes_query->row()->count;
        
        // Count unique quizzes attempted by student
        $attempted_query = $ci->db->query("
            SELECT COUNT(DISTINCT qa.quiz_id) as count
            FROM quiz_attempts qa
            JOIN ai_quizzes q ON qa.quiz_id = q.id
            JOIN subjects s ON q.subject_id = s.id
            JOIN student_enrollments se ON q.subject_id = se.subject_id
            WHERE qa.student_id = ? 
            AND se.student_id = ?
        ", [$user_id, $student_db_id]);
        $attempted_quizzes = $attempted_query->row()->count;
        
        if ($total_quizzes > 0) {
            $course_progress = round(($attempted_quizzes / $total_quizzes) * 100, 1);
        }
    }
    
    // Get recent AI chat sessions
    $recent_chats = array();
    if ($ci->db->table_exists('ai_chat_sessions') && $ci->db->table_exists('resources')) {
        $recent_chats_query = $ci->db->query("
            SELECT acs.*, r.title as resource_title 
            FROM ai_chat_sessions acs 
            LEFT JOIN resources r ON acs.resource_id = r.id 
            WHERE acs.user_id = ? 
            AND acs.is_active = 1 
            ORDER BY acs.updated_at DESC 
            LIMIT 5
        ", [$user_id]);
        
        $recent_chats = $recent_chats_query->result_array();
    }
    
    // Get recent resources
    $recent_resources = array();
    if ($ci->db->table_exists('resources')) {
        $ci->db->select('r.*, s.subject_name, s.subject_code');
        $ci->db->from('resources r');
        $ci->db->join('subjects s', 'r.subject_id = s.id', 'left');
        $ci->db->where('r.is_active', 1);
        $ci->db->where('r.semester <=', $current_semester);
        $ci->db->order_by('r.created_at', 'DESC');
        $ci->db->limit(8);
        $recent_resources = $ci->db->get()->result_array();
    }
    
} catch (Exception $e) {
    // Use fallback values if database fails
    $student_data = array();
    $current_semester = 1;
    $enrollment_year = date('Y');
    $student_id = 'N/A';
    $total_resources = 0;
    $total_chats = 0;
    $course_progress = 0; 
    $recent_chats = array();
    $recent_resources = array();
    
    // Log error
    log_message('error', 'Database error in student_dashboard: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal - SLAi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <?php $this->load->view('simple_portal/components/student_sidebar_css'); ?>
    <style>
        .semester-badge {
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

        .stat-icon.resources { background: linear-gradient(135deg, #667eea, #764ba2); }
        .stat-icon.chats { background: linear-gradient(135deg, #059669, #047857); }
        .stat-icon.progress { background: linear-gradient(135deg, #d97706, #b45309); }
        .stat-icon.semester { background: linear-gradient(135deg, #0891b2, #0e7490); }

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

        /* Recent Items Grid */
        .items-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .item-card {
            background: var(--white);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 20px;
            transition: all 0.3s ease;
            display: flex;
            align-items: flex-start;
            gap: 15px;
        }

        .item-card:hover {
            border-color: var(--primary-blue);
            box-shadow: 0 8px 20px rgba(31, 94, 168, 0.1);
            transform: translateY(-3px);
        }

        .item-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: var(--white);
            flex-shrink: 0;
        }

        .item-icon.resource { background: linear-gradient(135deg, var(--primary-blue), var(--primary-light)); }
        .item-icon.chat { background: linear-gradient(135deg, var(--success-green), #8bad5a); }

        .item-content {
            flex: 1;
        }

        .item-title {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 5px;
            font-size: 15px;
        }

        .item-subtitle {
            color: var(--text-light);
            font-size: 13px;
            margin-bottom: 8px;
        }

        .item-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
            color: var(--text-light);
        }

        .item-badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-pdf { background: rgba(239, 68, 68, 0.1); color: #dc2626; }
        .badge-active { background: rgba(34, 197, 94, 0.1); color: #16a34a; }

        /* Semester Grid */
        .semester-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
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

        .semester-card.available:hover::before {
            transform: scaleX(1);
        }

        .semester-card.available:hover {
            border-color: var(--primary-blue);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(31, 94, 168, 0.15);
        }

        .semester-card.current {
            border-color: var(--success-green);
            background: linear-gradient(135deg, #f0f9ff, #ecfdf5);
        }

        .semester-card.current::before {
            background: var(--success-green);
            transform: scaleX(1);
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

        .semester-card.current .semester-icon {
            background: linear-gradient(135deg, var(--success-green), #8BAD5A);
        }

        .semester-card.locked .semester-icon {
            background: linear-gradient(135deg, #9ca3af, #6b7280);
        }

        .semester-title {
            font-size: 16px;
            font-weight: 600;
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

        /* Quick Actions */
        .quick-actions {
            margin-top: 35px;
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .action-btn {
            background: var(--white);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 25px;
            text-align: center;
            text-decoration: none;
            color: var(--text-dark);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }

        .action-btn:hover {
            background: var(--primary-blue);
            color: var(--white);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(31, 94, 168, 0.2);
            border-color: var(--primary-blue);
        }

        .action-btn i {
            font-size: 28px;
        }

        .action-btn span {
            font-weight: 600;
            font-size: 15px;
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
            
            .actions-grid {
                grid-template-columns: 1fr;
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
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: var(--text-light);
        }
        
        .empty-state i {
            font-size: 48px;
            color: #cbd5e1;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<div class="portal-container">

    <!-- Sidebar -->
    <!-- Sidebar -->
    <?php $this->load->view('simple_portal/components/student_sidebar', ['active_page' => 'dashboard']); ?>

    <!-- Main Content -->
    <div class="main-content">

        <!-- Topbar -->
        <header class="topbar">
            <div class="page-title">
                Student Dashboard
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
                    <h1>Welcome back, <?php echo htmlspecialchars($username); ?>!</h1>
                    <p>Track your learning progress, access resources, and interact with AI Buddy</p>
                </div>
                <div class="semester-badge">
                    <i class="fa fa-graduation-cap"></i>
                    Semester <?php echo $current_semester; ?> • Student ID: <?php echo $student_id; ?>
                </div>
            </div>

            <!-- Stats Overview -->
            <div class="stats-grid">
                <a href="<?php echo base_url('simple_portal/student_resources'); ?>" class="stat-card" style="text-decoration: none; display: block;">
                    <div class="stat-header">
                        <div class="stat-icon resources">
                            <i class="fa fa-book-open"></i>
                        </div>
                        <span class="stat-trend"><?php echo $total_resources; ?> total</span>
                    </div>
                    <div class="stat-value"><?php echo $total_resources; ?></div>
                    <div class="stat-label">Available Resources</div>
                </a>

                <a href="<?php echo base_url('simple_portal/ai_chat'); ?>" class="stat-card" style="text-decoration: none; display: block;">
                    <div class="stat-header">
                        <div class="stat-icon chats">
                            <i class="fa fa-comments"></i>
                        </div>
                        <span class="stat-trend"><?php echo $total_chats; ?> created</span>
                    </div>
                    <div class="stat-value"><?php echo $total_chats; ?></div>
                    <div class="stat-label">AI Chat Sessions</div>
                </a>

                <a href="<?php echo base_url('simple_portal/student_quizzes'); ?>" class="stat-card" style="text-decoration: none; display: block;">
                    <div class="stat-header">
                        <div class="stat-icon progress">
                            <i class="fa fa-chart-line"></i>
                        </div>
                        <span class="stat-trend"><?php echo $attempted_quizzes; ?>/<?php echo $total_quizzes; ?> quizzes</span>
                    </div>
                    <div class="stat-value"><?php echo $course_progress; ?>%</div>
                    <div class="stat-label">Course Progress</div>
                </a>

                <a href="#" class="stat-card" style="text-decoration: none; display: block; cursor: default;">
                    <div class="stat-header">
                        <div class="stat-icon semester">
                            <i class="fa fa-calendar-alt"></i>
                        </div>
                        <span class="stat-trend"><?php echo date('Y') - $enrollment_year; ?> years</span>
                    </div>
                    <div class="stat-value"><?php echo $current_semester; ?>/8</div>
                    <div class="stat-label">Semester Progress</div>
                </a>
            </div>

            <!-- Published Content Overview -->
            <div class="main-card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fa fa-graduation-cap"></i>
                        Published Content
                    </div>
                    <div class="card-subtitle">Access question papers, quizzes, and assignments published by your faculty</div>
                </div>
                <div class="card-body">
                    <div class="actions-grid">
                        <a href="<?php echo base_url('simple_portal/student_question_papers'); ?>" class="action-btn" style="background: linear-gradient(135deg, #dbeafe, #bfdbfe); border-color: #3b82f6;">
                            <i class="fa fa-file-alt" style="color: #1e40af;"></i>
                            <span style="color: #1e40af;">Question Papers</span>
                        </a>
                        <a href="<?php echo base_url('simple_portal/student_quizzes'); ?>" class="action-btn" style="background: linear-gradient(135deg, #f3e8ff, #e9d5ff); border-color: #8b5cf6;">
                            <i class="fa fa-question-circle" style="color: #6d28d9;"></i>
                            <span style="color: #6d28d9;">Quizzes</span>
                        </a>
                        <a href="<?php echo base_url('simple_portal/student_assignments'); ?>" class="action-btn" style="background: linear-gradient(135deg, #ffedd5, #fed7aa); border-color: #f97316;">
                            <i class="fa fa-tasks" style="color: #c2410c;"></i>
                            <span style="color: #c2410c;">Assignments</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Resources -->
            <div class="main-card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fa fa-clock-rotate-left"></i>
                        Recent Resources
                    </div>
                    <div class="card-subtitle">Your recently accessed learning materials</div>
                </div>
                <div class="card-body">
                    <?php if (!empty($recent_resources)): ?>
                        <div class="items-grid">
                            <?php foreach ($recent_resources as $resource): ?>
                                <a href="<?php echo base_url('simple_portal/download_resource/' . $resource['id']); ?>" class="item-card">
                                    <div class="item-icon resource">
                                        <i class="fa fa-file-pdf"></i>
                                    </div>
                                    <div class="item-content">
                                        <div class="item-title"><?php echo htmlspecialchars($resource['title']); ?></div>
                                        <div class="item-subtitle">
                                            <?php echo isset($resource['subject_name']) ? htmlspecialchars($resource['subject_name']) : 'No Subject'; ?>
                                            • Sem <?php echo $resource['semester']; ?>
                                        </div>
                                        <div class="item-meta">
                                            <span><?php echo date('M d, Y', strtotime($resource['created_at'])); ?></span>
                                            <span class="item-badge badge-pdf">PDF</span>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fa fa-book-open"></i>
                            <h3>No resources available yet</h3>
                            <p>Start by exploring resources from your current semester</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Semester Access -->
            <div class="main-card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fa fa-graduation-cap"></i>
                        Semester Access
                    </div>
                    <div class="card-subtitle">Access resources and materials by semester</div>
                </div>
                <div class="card-body">
                    <div class="semester-grid">
                        <?php for ($i = 1; $i <= 8; $i++): 
                            $is_current = ($i == $current_semester);
                            $is_available = ($i <= $current_semester);
                            $is_locked = ($i > $current_semester);
                            
                            if ($is_current) {
                                $card_class = 'current';
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

            <!-- Recent AI Chats -->
            <?php if (!empty($recent_chats)): ?>
            <div class="main-card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fa fa-robot"></i>
                        Recent AI Chat Sessions
                    </div>
                    <div class="card-subtitle">Continue your conversations with AI Buddy</div>
                </div>
                <div class="card-body">
                    <div class="items-grid">
                        <?php foreach ($recent_chats as $chat): ?>
                            <a href="<?php echo base_url('simple_portal/ai_chat/' . $chat['id']); ?>" class="item-card">
                                <div class="item-icon chat">
                                    <i class="fa fa-robot"></i>
                                </div>
                                <div class="item-content">
                                    <div class="item-title"><?php echo !empty($chat['session_name']) ? htmlspecialchars($chat['session_name']) : 'Untitled Chat'; ?></div>
                                    <div class="item-subtitle">
                                        <?php echo !empty($chat['resource_title']) ? htmlspecialchars($chat['resource_title']) : 'General Discussion'; ?>
                                    </div>
                                    <div class="item-meta">
                                        <span><?php echo date('M d, Y', strtotime($chat['updated_at'])); ?></span>
                                        <span class="item-badge badge-active">Active</span>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <h3 class="section-title">Quick Actions</h3>
                <div class="actions-grid">
                    <a href="<?php echo base_url('simple_portal/student_resources'); ?>" class="action-btn">
                        <i class="fa fa-book"></i>
                        <span>Browse Resources</span>
                    </a>
                    <a href="<?php echo base_url('simple_portal/student_question_papers'); ?>" class="action-btn">
                        <i class="fa fa-file-alt"></i>
                        <span>Question Papers</span>
                    </a>
                    <a href="<?php echo base_url('simple_portal/student_quizzes'); ?>" class="action-btn">
                        <i class="fa fa-question-circle"></i>
                        <span>Quizzes</span>
                    </a>
                    <a href="<?php echo base_url('simple_portal/student_assignments'); ?>" class="action-btn">
                        <i class="fa fa-tasks"></i>
                        <span>Assignments</span>
                    </a>
                    <a href="<?php echo base_url('simple_portal/ai_chat'); ?>" class="action-btn">
                        <i class="fa fa-robot"></i>
                        <span>Start AI Chat</span>
                    </a>
                    <a href="<?php echo base_url('simple_portal/student_quizzes'); ?>" class="action-btn">
                        <i class="fa fa-chart-line"></i>
                        <span>Progress Report</span>
                    </a>
                </div>
            </div>

        </main>
    </div>
</div>

<script>
    // Initialize interactive features
    document.addEventListener('DOMContentLoaded', function() {
        // Add hover effects to stat cards
        const statCards = document.querySelectorAll('.stat-card');
        statCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-8px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });

        // Semester card interactions
        const semesterCards = document.querySelectorAll('.semester-card.available');
        semesterCards.forEach(card => {
            card.addEventListener('click', function(e) {
                if (!this.classList.contains('locked')) {
                    // Add click animation
                    this.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        this.style.transform = '';
                    }, 150);
                }
            });
        });

        // Action button animations
        const actionButtons = document.querySelectorAll('.action-btn');
        actionButtons.forEach(button => {
            button.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-4px) scale(1.02)';
            });
            
            button.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Item card animations
        const itemCards = document.querySelectorAll('.item-card');
        itemCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-4px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });

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
                background: linear-gradient(135deg, ${type === 'success' ? '#059669, #047857' : '#0ea5e9, #0284c7'});
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

        // Simulate loading data
        setTimeout(() => {
            // This would be replaced with actual AJAX calls in production
            console.log('Dashboard loaded successfully');
        }, 500);
    });

    // Function to refresh dashboard data
    function refreshDashboardData() {
        console.log('Refreshing dashboard data...');
        // In production, this would make an AJAX call to update stats
        showNotification('Dashboard data refreshed', 'info');
    }
</script>

</body>
</html>