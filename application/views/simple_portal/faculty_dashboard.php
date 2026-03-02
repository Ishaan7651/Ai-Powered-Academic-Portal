<?php
// Check if user is logged in and get user data
$username = $this->session->userdata('username') ?? 'Faculty';
$user_id = $this->session->userdata('user_id');
$role = $this->session->userdata('role');

// Get database instance
$ci =& get_instance();
$ci->load->database();

// Initialize variables
$total_resources = 0;
$recent_uploads = 0;
$ai_assignments = 0;
$total_students = 0;
$recent_activities = [];
$assigned_subjects = [];
$faculty_data = [];

try {
    // Get faculty details - CORRECTED: faculty table has user_id
    if ($user_id) {
        $faculty_query = $ci->db->query("
            SELECT f.*, u.email 
            FROM faculty f 
            JOIN users u ON f.user_id = u.id 
            WHERE f.user_id = ?
        ", array($user_id));
        
        $faculty_data = $faculty_query->row_array() ?? [];
        $faculty_id = isset($faculty_data['id']) ? $faculty_data['id'] : null;
    }

    // Get stats from database
    if ($user_id) {
        // 1. Total Resources - CORRECTED: uploaded_by is user_id in resources table
        $total_resources = $ci->db->where('uploaded_by', $user_id)
                                ->where('is_active', 1)
                                ->count_all_results('resources');
        
        // 2. Recent Uploads (last 7 days)
        $seven_days_ago = date('Y-m-d H:i:s', strtotime('-7 days'));
        $recent_uploads = $ci->db->where('uploaded_by', $user_id)
                                ->where('created_at >=', $seven_days_ago)
                                ->where('is_active', 1)
                                ->count_all_results('resources');
        
        // 3. AI Assignments & Quizzes created - CORRECTED: using user_id from session
        $ai_quizzes = $ci->db->where('user_id', $user_id)
                            ->count_all_results('ai_quizzes');
        $ai_papers = $ci->db->where('user_id', $user_id)
                           ->count_all_results('ai_question_papers');
        $ai_chats = $ci->db->where('user_id', $user_id)
                          ->count_all_results('ai_chat_sessions');
        $ai_assignments = $ai_quizzes + $ai_papers + $ai_chats;
        
        // 4. Total Students in faculty's subjects - CORRECTED based on your schema
        $subject_ids = [];
        if ($faculty_id) {
            // Get subjects assigned to this faculty
            $subjects_query = $ci->db->query("
                SELECT subject_id 
                FROM faculty_subjects 
                WHERE faculty_id = ?
            ", array($faculty_id));
            
            $subject_ids = array_column($subjects_query->result_array(), 'subject_id');
        }
        
        // Count students enrolled in those semesters
        if (!empty($subject_ids)) {
            // Get semesters for these subjects
            $semester_query = $ci->db->select('semester')
                                   ->from('subjects')
                                   ->where_in('id', $subject_ids)
                                   ->group_by('semester')
                                   ->get();
            
            $semesters = array_column($semester_query->result_array(), 'semester');
            
            if (!empty($semesters)) {
                // Count students in those semesters
                $total_students = $ci->db->where_in('current_semester', $semesters)
                                       ->where('user_id IN (SELECT id FROM users WHERE role = "student" AND is_active = 1)')
                                       ->count_all_results('students');
            }
        }
        
        // Get assigned subjects with resource counts
        if ($faculty_id) {
            $assigned_subjects = $ci->db->query("
                SELECT 
                    s.id as subject_id,
                    s.subject_code, 
                    s.subject_name, 
                    s.semester, 
                    s.credits,
                    COUNT(r.id) as resource_count
                FROM subjects s
                JOIN faculty_subjects fs ON s.id = fs.subject_id
                LEFT JOIN resources r ON s.id = r.subject_id AND r.uploaded_by = ? AND r.is_active = 1
                WHERE fs.faculty_id = ?
                AND s.is_active = 1
                GROUP BY s.id, s.subject_code, s.subject_name, s.semester, s.credits
                ORDER BY s.semester, s.subject_name
            ", array($user_id, $faculty_id))->result_array();
        }
        
        // Get recent activities (resources uploaded by this faculty)
        $recent_resources = $ci->db->query("
            SELECT 
                r.id,
                r.title,
                CONCAT('Uploaded resource: ', r.title) as description,
                s.subject_name,
                r.created_at,
                'upload' as type,
                'upload' as icon
            FROM resources r
            LEFT JOIN subjects s ON r.subject_id = s.id
            WHERE r.uploaded_by = ?
            AND r.is_active = 1
            ORDER BY r.created_at DESC
            LIMIT 5
        ", array($user_id))->result_array();
        
        foreach ($recent_resources as $resource) {
            $recent_activities[] = array(
                'title' => $resource['title'],
                'description' => $resource['description'],
                'subject' => $resource['subject_name'],
                'time' => time_ago($resource['created_at']),
                'type' => 'upload',
                'icon' => 'upload'
            );
        }
        
        // Get recent AI activities - SIMPLIFIED (no subject join)
        $recent_quizzes = $ci->db->select('id, title, created_at')
                                ->from('ai_quizzes')
                                ->where('user_id', $user_id)
                                ->order_by('created_at', 'DESC')
                                ->limit(2)
                                ->get()
                                ->result_array();

        $recent_papers = $ci->db->select('id, title, created_at')
                               ->from('ai_question_papers')
                               ->where('user_id', $user_id)
                               ->order_by('created_at', 'DESC')
                               ->limit(2)
                               ->get()
                               ->result_array();

        // Combine them
        foreach ($recent_quizzes as $quiz) {
            $recent_activities[] = array(
                'title' => $quiz['title'],
                'description' => 'Created quiz: ' . $quiz['title'],
                'time' => time_ago($quiz['created_at']),
                'type' => 'ai',
                'icon' => 'question-circle'
            );
        }

        foreach ($recent_papers as $paper) {
            $recent_activities[] = array(
                'title' => $paper['title'],
                'description' => 'Generated paper: ' . $paper['title'],
                'time' => time_ago($paper['created_at']),
                'type' => 'ai',
                'icon' => 'file-alt'
            );
        }
        
        // Sort activities by time (most recent first)
        usort($recent_activities, function($a, $b) {
            return strtotime($b['time']) - strtotime($a['time']);
        });
        
        // Keep only the 5 most recent activities
        $recent_activities = array_slice($recent_activities, 0, 5);
        
    }
    
} catch (Exception $e) {
    log_message('error', 'Database error in faculty_dashboard: ' . $e->getMessage());
    // Set fallback values
    $total_resources = 0;
    $recent_uploads = 0;
    $ai_assignments = 0;
    $total_students = 0;
    $assigned_subjects = [];
    $recent_activities = [];
}

// Helper function for time ago
function time_ago($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}

// For debugging - uncomment if needed
// echo "User ID: " . $user_id . "<br>";
// echo "Faculty ID: " . $faculty_id . "<br>";
// echo "Total Resources: " . $total_resources . "<br>";
// echo "Assigned Subjects: " . count($assigned_subjects) . "<br>";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Dashboard - SLAi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <?php $this->load->view('simple_portal/components/faculty_sidebar_css'); ?>
    <style>
        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        }

        /* Topbar - Premium Design */
        .topbar {
            height: var(--topbar-height);
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            padding: 0 2.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            box-shadow: var(--shadow-md);
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .page-info {
            display: flex;
            flex-direction: column;
            gap: 0.375rem;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--gray-900);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            letter-spacing: -0.025em;
        }

        .page-title i {
            color: var(--primary-blue);
            background: rgba(59, 130, 246, 0.1);
            padding: 0.5rem;
            border-radius: 10px;
            font-size: 1.25rem;
        }

        .breadcrumb {
            font-size: 0.875rem;
            color: var(--gray-500);
            font-weight: 500;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        /* Search Bar - Premium */
        .search-container {
            position: relative;
            width: 360px;
        }

        .search-bar {
            width: 100%;
            padding: 0.875rem 1.25rem 0.875rem 3.25rem;
            border: 2px solid var(--gray-200);
            border-radius: 14px;
            font-size: 0.9375rem;
            color: var(--gray-800);
            background: var(--white);
            transition: all var(--transition-base);
            box-shadow: var(--shadow-sm);
        }

        .search-bar:focus {
            outline: none;
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15), var(--shadow-md);
            transform: translateY(-1px);
        }

        .search-icon {
            position: absolute;
            left: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-400);
            font-size: 1.125rem;
            transition: color var(--transition-fast);
        }

        .search-bar:focus + .search-icon {
            color: var(--primary-blue);
        }

        /* Notification Bell */
        .notification-bell {
            position: relative;
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--white);
            color: var(--gray-600);
            cursor: pointer;
            transition: all var(--transition-base);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--gray-200);
        }

        .notification-bell:hover {
            background: var(--gray-50);
            color: var(--gray-700);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
            border-color: var(--gray-300);
        }

        .notification-count {
            position: absolute;
            top: -6px;
            right: -6px;
            background: linear-gradient(135deg, var(--error), #dc2626);
            color: white;
            font-size: 0.75rem;
            font-weight: 800;
            min-width: 22px;
            height: 22px;
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 6px;
            border: 3px solid var(--white);
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.4);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        /* User Profile */
        .user-profile {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem 1rem 0.75rem 1.25rem;
            border-radius: 16px;
            background: var(--white);
            cursor: pointer;
            transition: all var(--transition-base);
            border: 1px solid var(--gray-200);
            min-width: 220px;
            box-shadow: var(--shadow-sm);
        }

        .user-profile:hover {
            background: var(--gray-50);
            border-color: var(--gray-300);
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .user-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: var(--gradient-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 800;
            font-size: 1.125rem;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
            flex-shrink: 0;
            border: 3px solid var(--white);
        }

        .user-info {
            flex: 1;
            min-width: 0;
        }

        .user-name {
            font-weight: 700;
            font-size: 0.9375rem;
            color: var(--gray-900);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 0.125rem;
        }

        .user-role {
            font-size: 0.8125rem;
            color: var(--gray-600);
            background: rgba(37, 99, 235, 0.1);
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            display: inline-block;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .user-dropdown {
            color: var(--gray-400);
            font-size: 0.875rem;
            transition: transform var(--transition-fast);
        }

        .user-profile:hover .user-dropdown {
            transform: translateY(2px);
        }

        /* Content Area */
        .content-area {
            flex: 1;
            padding: 2.5rem;
            overflow-y: auto;
            background: transparent;
        }

        /* Welcome Banner - Premium */
        .welcome-banner {
            background: linear-gradient(135deg, var(--primary-blue), var(--primary-light));
            border-radius: 24px;
            padding: 3rem;
            color: white;
            margin-bottom: 2.5rem;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-xl);
            animation: fadeIn 0.8s ease-out;
        }

        .welcome-banner::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            width: 40%;
            background: linear-gradient(135deg, transparent, rgba(255, 255, 255, 0.15));
            transform: skewX(-20deg);
        }

        .welcome-banner::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        }

        .welcome-content {
            position: relative;
            z-index: 1;
            max-width: 700px;
        }

        .welcome-title {
            font-size: 2.5rem;
            font-weight: 900;
            margin-bottom: 1rem;
            line-height: 1.2;
            letter-spacing: -0.025em;
            background: linear-gradient(45deg, #ffffff, #e0f2fe);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .welcome-subtitle {
            font-size: 1.125rem;
            opacity: 0.95;
            margin-bottom: 2rem;
            line-height: 1.6;
            font-weight: 400;
        }

        .welcome-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        /* Stats Grid - Premium Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .stat-card {
            background: var(--white);
            border-radius: 20px;
            padding: 2rem;
            border: 1px solid var(--gray-200);
            transition: all var(--transition-base);
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-md);
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-2xl);
            border-color: var(--primary-blue);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: var(--gradient-primary);
            opacity: 0;
            transition: opacity var(--transition-fast);
        }

        .stat-card:hover::before {
            opacity: 1;
        }

        .stat-content {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .stat-icon {
            width: 72px;
            height: 72px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: var(--white);
            flex-shrink: 0;
            box-shadow: var(--shadow-lg);
            transition: all var(--transition-base);
        }

        .stat-card:hover .stat-icon {
            transform: scale(1.1) rotate(5deg);
        }

        .stat-icon.resources { background: var(--gradient-purple); }
        .stat-icon.uploads { background: var(--gradient-success); }
        .stat-icon.assignments { background: var(--gradient-warning); }
        .stat-icon.students { background: var(--gradient-primary); }

        .stat-info {
            flex: 1;
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 900;
            color: var(--gray-900);
            line-height: 1;
            margin-bottom: 0.5rem;
            font-feature-settings: "tnum";
        }

        .stat-label {
            font-size: 1rem;
            color: var(--gray-600);
            font-weight: 600;
            margin-bottom: 0.75rem;
        }

        .stat-trend {
            font-size: 0.875rem;
            font-weight: 700;
            padding: 0.375rem 0.875rem;
            border-radius: 20px;
            background: rgba(34, 197, 94, 0.15);
            color: var(--success);
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            border: 1px solid rgba(34, 197, 94, 0.2);
        }

        .stat-trend i {
            font-size: 0.75rem;
        }

        /* Quick Actions - Premium Grid */
        .quick-actions-section {
            margin-bottom: 2.5rem;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.75rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--gray-100);
        }

        .section-title {
            font-size: 1.75rem;
            font-weight: 900;
            color: var(--gray-900);
            display: flex;
            align-items: center;
            gap: 1rem;
            letter-spacing: -0.025em;
        }

        .section-title i {
            color: var(--primary-blue);
            background: rgba(59, 130, 246, 0.1);
            padding: 0.75rem;
            border-radius: 14px;
            font-size: 1.5rem;
        }

        .action-filters {
            display: flex;
            gap: 0.5rem;
            background: var(--gray-100);
            padding: 0.375rem;
            border-radius: 14px;
            border: 1px solid var(--gray-200);
        }

        .filter-btn {
            padding: 0.625rem 1.25rem;
            border: none;
            border-radius: 10px;
            background: transparent;
            color: var(--gray-600);
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: all var(--transition-fast);
        }

        .filter-btn.active {
            background: var(--white);
            color: var(--primary-blue);
            box-shadow: var(--shadow-md);
        }

        .filter-btn:hover:not(.active) {
            color: var(--gray-800);
            background: var(--gray-200);
        }

        .quick-actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .quick-action-card {
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: 20px;
            padding: 2rem;
            text-decoration: none;
            color: var(--gray-800);
            transition: all var(--transition-base);
            display: flex;
            align-items: center;
            gap: 1.5rem;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-md);
        }

        .quick-action-card:hover {
            transform: translateY(-6px) scale(1.02);
            border-color: var(--primary-blue);
            box-shadow: var(--shadow-2xl);
            color: var(--gray-900);
        }

        .quick-action-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: var(--gradient-primary);
            opacity: 0;
            transition: opacity var(--transition-fast);
        }

        .quick-action-card:hover::before {
            opacity: 1;
        }

        .quick-icon {
            width: 64px;
            height: 64px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            color: var(--white);
            flex-shrink: 0;
            box-shadow: var(--shadow-lg);
            transition: all var(--transition-base);
        }

        .quick-action-card:hover .quick-icon {
            transform: scale(1.1) rotate(5deg);
        }

        .quick-icon.upload { background: var(--gradient-purple); }
        .quick-icon.resources { background: var(--gradient-success); }
        .quick-icon.ai { background: var(--gradient-warning); }
        .quick-icon.quiz { background: linear-gradient(135deg, #ec4899, #d946ef); }
        .quick-icon.paper { background: linear-gradient(135deg, #0891b2, #0e7490); }
        .quick-icon.chat { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .quick-icon.students { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
        .quick-icon.analytics { background: linear-gradient(135deg, #1e40af, #1e3a8a); }

        .quick-action-info {
            flex: 1;
        }

        .quick-action-title {
            font-weight: 800;
            font-size: 1.125rem;
            margin-bottom: 0.5rem;
            color: var(--gray-900);
        }

        .quick-action-desc {
            font-size: 0.875rem;
            color: var(--gray-600);
            line-height: 1.5;
        }

        .quick-action-arrow {
            color: var(--gray-400);
            font-size: 1.125rem;
            transition: all var(--transition-fast);
            opacity: 0;
            transform: translateX(-10px);
        }

        .quick-action-card:hover .quick-action-arrow {
            opacity: 1;
            transform: translateX(0);
            color: var(--primary-blue);
        }

        /* Recent Activity - Premium */
        .recent-activity-section {
            background: var(--white);
            border-radius: 24px;
            border: 1px solid var(--gray-200);
            overflow: hidden;
            box-shadow: var(--shadow-lg);
            margin-bottom: 2.5rem;
        }

        .activity-header {
            padding: 2rem 2.5rem;
            border-bottom: 1px solid var(--gray-200);
            background: linear-gradient(to right, var(--gray-50), var(--gray-100));
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .activity-header h3 {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--gray-900);
            display: flex;
            align-items: center;
            gap: 1rem;
            letter-spacing: -0.025em;
        }

        .activity-header h3 i {
            color: var(--primary-blue);
            background: rgba(59, 130, 246, 0.1);
            padding: 0.75rem;
            border-radius: 12px;
            font-size: 1.25rem;
        }

        .refresh-btn {
            background: var(--white);
            border: 2px solid var(--gray-300);
            color: var(--gray-700);
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-size: 0.875rem;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all var(--transition-base);
        }
                    .refresh-btn:hover {
                background: var(--gray-50);
                border-color: var(--primary-blue);
                color: var(--primary-blue);
                transform: translateY(-2px);
                box-shadow: var(--shadow-md);
            }

            .refresh-btn i {
                font-size: 1rem;
                transition: transform var(--transition-fast);
            }

            .refresh-btn:hover i {
                transform: rotate(90deg);
            }

            .activity-list {
                padding: 1rem 0;
            }

            .activity-item {
                display: flex;
                align-items: center;
                padding: 1.5rem 2.5rem;
                border-bottom: 1px solid var(--gray-100);
                transition: all var(--transition-fast);
                cursor: pointer;
                position: relative;
            }

            .activity-item:last-child {
                border-bottom: none;
            }

            .activity-item:hover {
                background: linear-gradient(to right, var(--gray-50), transparent);
                padding-left: 3rem;
            }

            .activity-item:hover::before {
                content: '';
                position: absolute;
                left: 0;
                top: 0;
                bottom: 0;
                width: 4px;
                background: var(--gradient-primary);
                border-radius: 0 4px 4px 0;
            }

            .activity-icon {
                width: 52px;
                height: 52px;
                border-radius: 14px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.25rem;
                color: var(--white);
                margin-right: 1.5rem;
                flex-shrink: 0;
                box-shadow: var(--shadow-md);
                transition: transform var(--transition-fast);
            }

            .activity-item:hover .activity-icon {
                transform: scale(1.1);
            }

            .activity-icon.upload { background: var(--gradient-purple); }
            .activity-icon.ai { background: var(--gradient-warning); }
            .activity-icon.resource { background: var(--gradient-success); }

            .activity-content {
                flex: 1;
                min-width: 0;
            }

            .activity-title {
                font-weight: 700;
                color: var(--gray-900);
                margin-bottom: 0.375rem;
                font-size: 1rem;
            }

            .activity-description {
                font-size: 0.875rem;
                color: var(--gray-600);
                margin-bottom: 0.375rem;
                line-height: 1.5;
            }

            .activity-time {
                font-size: 0.8125rem;
                color: var(--gray-500);
                display: flex;
                align-items: center;
                gap: 0.5rem;
                font-weight: 500;
            }

            .activity-time i {
                font-size: 0.75rem;
                color: var(--gray-400);
            }

            /* Assigned Subjects - Premium Cards */
            .subjects-section {
                margin-top: 2.5rem;
            }

            .subjects-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
                gap: 1.5rem;
            }

            .subject-card {
                background: var(--white);
                border: 1px solid var(--gray-200);
                border-radius: 20px;
                padding: 2rem;
                transition: all var(--transition-base);
                position: relative;
                overflow: hidden;
                box-shadow: var(--shadow-md);
                backdrop-filter: blur(10px);
                background: rgba(255, 255, 255, 0.98);
            }

            .subject-card:hover {
                transform: translateY(-6px);
                box-shadow: var(--shadow-2xl);
                border-color: var(--primary-blue);
            }

            .subject-card::before {
                content: '';
                position: absolute;
                left: 0;
                top: 0;
                height: 100%;
                width: 6px;
                background: var(--gradient-primary);
                opacity: 0;
                transition: opacity var(--transition-fast);
            }

            .subject-card:hover::before {
                opacity: 1;
            }

            .subject-header {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                margin-bottom: 1.25rem;
            }

            .subject-code {
                font-size: 0.875rem;
                font-weight: 800;
                color: var(--gray-700);
                background: var(--gray-100);
                padding: 0.5rem 1rem;
                border-radius: 20px;
                border: 1px solid var(--gray-200);
                letter-spacing: 0.5px;
            }

            .subject-semester {
                font-size: 0.75rem;
                color: var(--primary-blue);
                background: rgba(59, 130, 246, 0.1);
                padding: 0.5rem 1rem;
                border-radius: 20px;
                font-weight: 700;
                border: 1px solid rgba(59, 130, 246, 0.2);
            }

            .subject-title {
                font-size: 1.375rem;
                font-weight: 900;
                color: var(--gray-900);
                margin-bottom: 1rem;
                line-height: 1.3;
                letter-spacing: -0.025em;
            }

            .subject-meta {
                display: flex;
                gap: 1.5rem;
                margin-bottom: 1.5rem;
                padding-bottom: 1.5rem;
                border-bottom: 1px solid var(--gray-100);
            }

            .subject-meta-item {
                display: flex;
                align-items: center;
                gap: 0.5rem;
                font-size: 0.875rem;
                color: var(--gray-600);
                font-weight: 500;
            }

            .subject-meta-item i {
                color: var(--primary-blue);
                font-size: 1rem;
                background: rgba(59, 130, 246, 0.1);
                padding: 0.5rem;
                border-radius: 10px;
            }

            .subject-actions {
                display: flex;
                gap: 1rem;
            }

            .subject-btn {
                flex: 1;
                padding: 0.875rem 1.5rem;
                border-radius: 12px;
                font-size: 0.875rem;
                font-weight: 700;
                cursor: pointer;
                transition: all var(--transition-base);
                text-align: center;
                text-decoration: none;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 0.75rem;
                letter-spacing: 0.5px;
            }

            .subject-btn.primary {
                background: var(--gradient-primary);
                color: white;
                border: none;
                box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
            }

            .subject-btn.primary:hover {
                transform: translateY(-3px);
                box-shadow: 0 8px 25px rgba(37, 99, 235, 0.4);
            }

            .subject-btn.secondary {
                background: var(--white);
                color: var(--gray-700);
                border: 2px solid var(--gray-300);
            }

            .subject-btn.secondary:hover {
                background: var(--gray-50);
                border-color: var(--primary-blue);
                color: var(--primary-blue);
                transform: translateY(-3px);
                box-shadow: var(--shadow-md);
            }

            /* Empty States */
            .empty-state {
                text-align: center;
                padding: 4rem 2rem;
                background: var(--white);
                border-radius: 24px;
                border: 2px dashed var(--gray-300);
                box-shadow: var(--shadow-sm);
            }

            .empty-state-icon {
                font-size: 4rem;
                color: var(--gray-300);
                margin-bottom: 1.5rem;
                opacity: 0.7;
            }

            .empty-state-title {
                font-size: 1.5rem;
                font-weight: 800;
                color: var(--gray-700);
                margin-bottom: 0.75rem;
            }

            .empty-state-description {
                font-size: 1rem;
                color: var(--gray-600);
                max-width: 400px;
                margin: 0 auto 2rem;
                line-height: 1.6;
            }

            /* Buttons - Premium */
            .btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 0.75rem;
                padding: 1rem 2rem;
                border-radius: 14px;
                font-weight: 700;
                font-size: 0.9375rem;
                cursor: pointer;
                transition: all var(--transition-base);
                border: none;
                text-decoration: none;
                position: relative;
                overflow: hidden;
                letter-spacing: 0.5px;
            }

            .btn::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
                transition: left var(--transition-base);
            }

            .btn:hover::before {
                left: 100%;
            }

            .btn-primary {
                background: var(--gradient-success);
                color: white;
                box-shadow: 0 4px 20px rgba(120, 184, 63, 0.4);
            }

            .btn-primary:hover {
                transform: translateY(-3px);
                box-shadow: 0 8px 30px rgba(120, 184, 63, 0.5);
            }

            .btn-secondary {
                background: var(--white);
                color: var(--primary-blue);
                border: 2px solid var(--primary-blue);
                box-shadow: 0 4px 15px rgba(37, 99, 235, 0.15);
            }

            .btn-secondary:hover {
                background: var(--primary-blue);
                color: white;
                transform: translateY(-3px);
                box-shadow: 0 8px 25px rgba(37, 99, 235, 0.3);
            }

            /* Responsive Design */
            @media (max-width: 1400px) {
                .sidebar {
                    width: 240px;
                }
                
                .main-content {
                    margin-left: 240px;
                }
                
                .stats-grid {
                    grid-template-columns: repeat(2, 1fr);
                }
            }

            @media (max-width: 1200px) {
                .quick-actions-grid,
                .subjects-grid {
                    grid-template-columns: repeat(2, 1fr);
                }
            }

            @media (max-width: 1024px) {
                .sidebar {
                    width: 80px;
                    padding: 2rem 0.5rem;
                }
                
                .brand-section {
                    padding: 0 0.5rem 1.5rem;
                    display: flex;
                    justify-content: center;
                }
                
                .brand-text, .nav-item span, .nav-title, .subject-badge {
                    display: none;
                }
                
                .nav-item i {
                    margin-right: 0;
                    font-size: 1.5rem;
                }
                
                .nav-item {
                    padding: 1rem;
                    justify-content: center;
                    margin-bottom: 0.5rem;
                }
                
                .main-content {
                    margin-left: 80px;
                }
                
                .search-container {
                    width: 300px;
                }
            }

            @media (max-width: 768px) {
                .topbar {
                    padding: 0 1.5rem;
                    height: 70px;
                }
                
                .content-area {
                    padding: 1.5rem;
                }
                
                .welcome-banner {
                    padding: 2rem;
                }
                
                .welcome-title {
                    font-size: 2rem;
                }
                
                .stats-grid,
                .quick-actions-grid,
                .subjects-grid {
                    grid-template-columns: 1fr;
                    gap: 1rem;
                }
                
                .section-header {
                    flex-direction: column;
                    align-items: flex-start;
                    gap: 1rem;
                }
                
                .search-container {
                    width: 100%;
                    max-width: 300px;
                }
                
                .user-info {
                    display: none;
                }
                
                .user-profile {
                    min-width: auto;
                    padding: 0.5rem;
                }
                
                .notification-bell {
                    width: 44px;
                    height: 44px;
                }
            }

            @media (max-width: 480px) {
                .topbar {
                    padding: 0 1rem;
                }
                
                .content-area {
                    padding: 1rem;
                }
                
                .welcome-banner {
                    padding: 1.5rem;
                }
                
                .welcome-title {
                    font-size: 1.75rem;
                }
                
                .welcome-actions {
                    flex-direction: column;
                    width: 100%;
                }
                
                .welcome-actions .btn {
                    width: 100%;
                }
                
                .subject-actions {
                    flex-direction: column;
                }
                
                .action-filters {
                    flex-wrap: wrap;
                    width: 100%;
                    justify-content: center;
                }
                
                .activity-header,
                .activity-item {
                    padding-left: 1rem;
                    padding-right: 1rem;
                }
            }

            /* Animations */
            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            @keyframes slideInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px) scale(0.95);
                }
                to {
                    opacity: 1;
                    transform: translateY(0) scale(1);
                }
            }

            .welcome-banner {
                animation: fadeIn 0.8s ease-out forwards;
            }

            .stat-card, .quick-action-card, .subject-card {
                animation: slideInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
                opacity: 0;
                transform: translateY(20px);
            }

            .stat-card:nth-child(1) { animation-delay: 0.1s; }
            .stat-card:nth-child(2) { animation-delay: 0.2s; }
            .stat-card:nth-child(3) { animation-delay: 0.3s; }
            .stat-card:nth-child(4) { animation-delay: 0.4s; }

            .quick-action-card:nth-child(1) { animation-delay: 0.2s; }
            .quick-action-card:nth-child(2) { animation-delay: 0.3s; }
            .quick-action-card:nth-child(3) { animation-delay: 0.4s; }
            .quick-action-card:nth-child(4) { animation-delay: 0.5s; }
            .quick-action-card:nth-child(5) { animation-delay: 0.6s; }
            .quick-action-card:nth-child(6) { animation-delay: 0.7s; }
            .quick-action-card:nth-child(7) { animation-delay: 0.8s; }
            .quick-action-card:nth-child(8) { animation-delay: 0.9s; }

            /* Scrollbar Styling */
            .content-area::-webkit-scrollbar {
                width: 10px;
            }
            
            .content-area::-webkit-scrollbar-track {
                background: var(--gray-100);
                border-radius: 10px;
                margin: 4px;
            }
            
            .content-area::-webkit-scrollbar-thumb {
                background: linear-gradient(to bottom, var(--gray-300), var(--gray-400));
                border-radius: 10px;
                border: 2px solid var(--white);
            }
            
            .content-area::-webkit-scrollbar-thumb:hover {
                background: linear-gradient(to bottom, var(--gray-400), var(--gray-500));
            }

            /* Notification Modal */
            .modal-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.7);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 2000;
                animation: fadeIn 0.3s ease-out;
                backdrop-filter: blur(10px);
            }

            .modal-content {
                background: var(--white);
                border-radius: 24px;
                width: 90%;
                max-width: 500px;
                max-height: 80vh;
                overflow: hidden;
                box-shadow: var(--shadow-2xl);
                animation: slideInUp 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                border: 1px solid var(--gray-200);
            }

            /* Notification Popup */
            .notification-popup {
                position: fixed;
                top: 100px;
                right: 30px;
                padding: 1.25rem 1.5rem;
                border-radius: 16px;
                color: white;
                font-weight: 600;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 1rem;
                z-index: 2000;
                animation: slideInRight 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                background: var(--gradient-primary);
                box-shadow: var(--shadow-xl);
                min-width: 300px;
                max-width: 400px;
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.2);
            }

            @keyframes slideInRight {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }

            @keyframes slideOutRight {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(100%);
                    opacity: 0;
                }
            }

            /* User Menu */
            .user-menu {
                position: absolute;
                top: 90px;
                right: 32px;
                background: var(--white);
                border-radius: 20px;
                box-shadow: var(--shadow-2xl);
                min-width: 220px;
                z-index: 2000;
                border: 1px solid var(--gray-200);
                animation: slideInDown 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                overflow: hidden;
                backdrop-filter: blur(10px);
            }

            @keyframes slideInDown {
                from {
                    transform: translateY(-20px);
                    opacity: 0;
                }
                to {
                    transform: translateY(0);
                    opacity: 1;
                }
            }

            /* Loading Spinner */
            .loading-spinner {
                width: 40px;
                height: 40px;
                border: 4px solid var(--gray-200);
                border-top: 4px solid var(--primary-blue);
                border-radius: 50%;
                animation: spin 1s linear infinite;
            }

            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }

            /* Utility Classes */
            .text-primary { color: var(--primary-blue); }
            .text-success { color: var(--success); }
            .text-warning { color: var(--warning); }
            .text-error { color: var(--error); }
            .text-info { color: var(--info); }

            .bg-primary { background: var(--primary-blue); }
            .bg-success { background: var(--success); }
            .bg-warning { background: var(--warning); }
            .bg-error { background: var(--error); }
            .bg-info { background: var(--info); }

            .mt-1 { margin-top: 0.25rem; }
            .mt-2 { margin-top: 0.5rem; }
            .mt-3 { margin-top: 1rem; }
            .mt-4 { margin-top: 1.5rem; }
            .mt-5 { margin-top: 2rem; }

            .mb-1 { margin-bottom: 0.25rem; }
            .mb-2 { margin-bottom: 0.5rem; }
            .mb-3 { margin-bottom: 1rem; }
            .mb-4 { margin-bottom: 1.5rem; }
            .mb-5 { margin-bottom: 2rem; }

            .hidden { display: none; }
            .flex { display: flex; }
            .items-center { align-items: center; }
            .justify-between { justify-content: space-between; }
        </style>
    </head>
    <body>

    <div class="portal-container">

        <!-- Sidebar -->
        <?php $this->load->view('simple_portal/components/faculty_sidebar', ['active_page' => 'dashboard', 'assigned_subjects' => $assigned_subjects]); ?>

        <!-- Main Content -->
        <div class="main-content">

            <!-- Topbar -->
            <header class="topbar">
                <div class="page-info">
                    <div class="page-title">
                        <i class="fas fa-chalkboard-teacher"></i>
                        Faculty Dashboard
                    </div>
                    <div class="breadcrumb">Welcome to your teaching workspace</div>
                </div>

                <div class="topbar-actions">
                    <div class="search-container">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" class="search-bar" placeholder="Search resources, students, assignments..." 
                               id="globalSearch" autocomplete="off">
                    </div>

                    <div class="notification-bell" onclick="toggleNotifications()">
                        <i class="fas fa-bell"></i>
                        <span class="notification-count"><?php echo min($recent_uploads + $ai_assignments, 9); ?></span>
                    </div>

                    <div class="user-profile" onclick="showUserMenu()">
                        <div class="user-avatar">
                            <?php echo strtoupper(substr($username, 0, 1)); ?>
                        </div>
                        <div class="user-info">
                            <div class="user-name"><?php echo htmlspecialchars($username); ?></div>
                            <div class="user-role">FACULTY</div>
                        </div>
                        <i class="fas fa-chevron-down user-dropdown"></i>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <main class="content-area">

                <!-- Welcome Banner -->
                <div class="welcome-banner">
                    <div class="welcome-content">
                        <h1 class="welcome-title">Welcome back, <?php echo htmlspecialchars($username); ?>! 👋</h1>
                        <p class="welcome-subtitle">
                            <?php 
                            if (isset($faculty_data['department'])) {
                                echo '<strong>Department:</strong> ' . htmlspecialchars($faculty_data['department']);
                            }
                            if (isset($faculty_data['employee_id'])) {
                                echo ' • <strong>Employee ID:</strong> ' . htmlspecialchars($faculty_data['employee_id']);
                            }
                            ?>
                        </p>
                        <div class="welcome-actions">
                            <a href="<?php echo base_url('simple_portal/upload_resource'); ?>" class="btn btn-primary">
                                <i class="fas fa-upload"></i> Upload Resource
                            </a>
                            <button onclick="generateQuickAssignment()" class="btn btn-secondary">
                                <i class="fas fa-robot"></i> Quick AI Task
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Stats Overview -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-content">
                            <div class="stat-icon resources">
                                <i class="fas fa-folder-open"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-value"><?php echo $total_resources; ?></div>
                                <div class="stat-label">Total Resources</div>
                                <?php if ($recent_uploads > 0): ?>
                                <div class="stat-trend">
                                    <i class="fas fa-arrow-up"></i>
                                    <?php echo $recent_uploads; ?> new this week
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-content">
                            <div class="stat-icon uploads">
                                <i class="fas fa-upload"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-value"><?php echo $recent_uploads; ?></div>
                                <div class="stat-label">Recent Uploads</div>
                                <div class="stat-trend">
                                    <i class="fas fa-calendar-week"></i>
                                    Last 7 days
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-content">
                            <div class="stat-icon assignments">
                                <i class="fas fa-robot"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-value"><?php echo $ai_assignments; ?></div>
                                <div class="stat-label">AI Activities</div>
                                <div class="stat-trend">
                                    <i class="fas fa-magic"></i>
                                    Quizzes, Papers & Chat
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-content">
                            <div class="stat-icon students">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-value"><?php echo $total_students; ?></div>
                                <div class="stat-label">Total Students</div>
                                <?php if (!empty($assigned_subjects)): ?>
                                <div class="stat-trend">
                                    <i class="fas fa-book"></i>
                                    <?php echo count($assigned_subjects); ?> subjects
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="quick-actions-section">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fas fa-bolt"></i>
                            Quick Actions
                        </h2>
                        <div class="action-filters">
                            <button class="filter-btn active" onclick="filterActions('all')">All</button>
                            <button class="filter-btn" onclick="filterActions('resources')">Resources</button>
                            <button class="filter-btn" onclick="filterActions('ai')">AI Tools</button>
                            <button class="filter-btn" onclick="filterActions('manage')">Management</button>
                        </div>
                    </div>

                    <div class="quick-actions-grid">
                        <a href="<?php echo base_url('simple_portal/upload_resource'); ?>" class="quick-action-card" data-category="resources">
                            <div class="quick-icon upload">
                                <i class="fas fa-upload"></i>
                            </div>
                            <div class="quick-action-info">
                                <div class="quick-action-title">Upload Resource</div>
                                <div class="quick-action-desc">Share study materials and notes with students</div>
                            </div>
                            <i class="fas fa-chevron-right quick-action-arrow"></i>
                        </a>

                        <a href="<?php echo base_url('simple_portal/resources'); ?>" class="quick-action-card" data-category="resources">
                            <div class="quick-icon resources">
                                <i class="fas fa-folder-open"></i>
                            </div>
                            <div class="quick-action-info">
                                <div class="quick-action-title">Manage Resources</div>
                                <div class="quick-action-desc">View, organize, and edit your uploaded resources</div>
                            </div>
                            <i class="fas fa-chevron-right quick-action-arrow"></i>
                        </a>

                        <a href="<?php echo base_url('simple_portal/generate_assignment'); ?>" class="quick-action-card" data-category="ai">
                            <div class="quick-icon ai">
                                <i class="fas fa-robot"></i>
                            </div>
                            <div class="quick-action-info">
                                <div class="quick-action-title">AI Assignment</div>
                                <div class="quick-action-desc">Generate assignments using AI technology</div>
                            </div>
                            <i class="fas fa-chevron-right quick-action-arrow"></i>
                        </a>

                        <a href="<?php echo base_url('ai_buddy/generate_quiz'); ?>" class="quick-action-card" data-category="ai">
                            <div class="quick-icon quiz">
                                <i class="fas fa-question-circle"></i>
                            </div>
                            <div class="quick-action-info">
                                <div class="quick-action-title">Quiz Generator</div>
                                <div class="quick-action-desc">Create interactive quizzes for students</div>
                            </div>
                            <i class="fas fa-chevron-right quick-action-arrow"></i>
                        </a>

                        <a href="<?php echo base_url('ai_buddy/generate_question_paper'); ?>" class="quick-action-card" data-category="ai">
                            <div class="quick-icon paper">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div class="quick-action-info">
                                <div class="quick-action-title">Question Papers</div>
                                <div class="quick-action-desc">Generate exam papers with custom settings</div>
                            </div>
                            <i class="fas fa-chevron-right quick-action-arrow"></i>
                        </a>

                        <a href="<?php echo base_url('ai_buddy/ai_chat'); ?>" class="quick-action-card" data-category="ai">
                            <div class="quick-icon chat">
                                <i class="fas fa-comments"></i>
                            </div>
                            <div class="quick-action-info">
                                <div class="quick-action-title">AI Chat</div>
                                <div class="quick-action-desc">Chat with AI about your resources and content</div>
                            </div>
                            <i class="fas fa-chevron-right quick-action-arrow"></i>
                        </a>

                        <button class="quick-action-card" data-category="manage" onclick="showAnalytics()">
                            <div class="quick-icon analytics">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div class="quick-action-info">
                                <div class="quick-action-title">Analytics</div>
                                <div class="quick-action-desc">View detailed resource usage statistics</div>
                            </div>
                            <i class="fas fa-chevron-right quick-action-arrow"></i>
                        </button>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="recent-activity-section">
                    <div class="activity-header">
                        <h3>
                            <i class="fas fa-history"></i>
                            Recent Activity
                        </h3>
                        <button class="refresh-btn" onclick="refreshActivities()">
                            <i class="fas fa-sync-alt"></i>
                            Refresh
                        </button>
                    </div>
                    <div class="activity-list">
                        <?php if (!empty($recent_activities)): ?>
                            <?php foreach ($recent_activities as $activity): ?>
                            <div class="activity-item" onclick="viewActivityDetails()">
                                <div class="activity-icon <?php echo $activity['icon']; ?>">
                                    <i class="fas fa-<?php echo $activity['icon']; ?>"></i>
                                </div>
                                <div class="activity-content">
                                    <div class="activity-title"><?php echo htmlspecialchars($activity['title']); ?></div>
                                    <div class="activity-description"><?php echo htmlspecialchars($activity['description']); ?></div>
                                    <div class="activity-time">
                                        <i class="fas fa-clock"></i>
                                        <?php echo $activity['time']; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="empty-state" style="margin: 2rem; border: none;">
                                <div class="empty-state-icon">
                                    <i class="fas fa-history"></i>
                                </div>
                                <h3 class="empty-state-title">No Recent Activity</h3>
                                <p class="empty-state-description">
                                    Start by uploading resources or generating assignments to see your activity here.
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Assigned Subjects -->
                <?php if (!empty($assigned_subjects)): ?>
                <div class="subjects-section">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fas fa-book"></i>
                            My Assigned Subjects
                        </h2>
                        <a href="<?php echo base_url('simple_portal/manage_subjects'); ?>" class="btn btn-secondary">
                            <i class="fas fa-cog"></i> Manage Subjects
                        </a>
                    </div>

                    <div class="subjects-grid">
                        <?php foreach ($assigned_subjects as $subject): ?>
                        <div class="subject-card">
                            <div class="subject-header">
                                <span class="subject-code"><?php echo htmlspecialchars($subject['subject_code']); ?></span>
                                <span class="subject-semester">Semester <?php echo $subject['semester']; ?></span>
                            </div>
                            <h3 class="subject-title"><?php echo htmlspecialchars($subject['subject_name']); ?></h3>
                            <div class="subject-meta">
                                <div class="subject-meta-item">
                                    <i class="fas fa-graduation-cap"></i>
                                    <span><?php echo $subject['credits']; ?> Credits</span>
                                </div>
                                <div class="subject-meta-item">
                                    <i class="fas fa-file-alt"></i>
                                    <span><?php echo $subject['resource_count']; ?> Resources</span>
                                </div>
                            </div>
                            <div class="subject-actions">
                                <a href="<?php echo base_url('simple_portal/subject_resources/' . urlencode($subject['subject_code'])); ?>" 
                                   class="subject-btn primary">
                                    <i class="fas fa-eye"></i> View Resources
                                </a>
                                <button onclick="generateSubjectAssignment('<?php echo addslashes($subject['subject_code']); ?>')" 
                                        class="subject-btn secondary">
                                    <i class="fas fa-robot"></i> AI Assignment
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <h3 class="empty-state-title">No Subjects Assigned</h3>
                    <p class="empty-state-description">
                        You haven't been assigned any subjects yet. Please contact your department administrator.
                    </p>
                    <a href="<?php echo base_url('simple_portal/manage_subjects'); ?>" class="btn btn-primary mt-4">
                        <i class="fas fa-cog"></i> Request Subjects
                    </a>
                </div>
                <?php endif; ?>

            </main>
        </div>
    </div>

    <script>
        // Global Variables
        let activeModal = null;
        let userMenu = null;

        // Initialize page animations
        document.addEventListener('DOMContentLoaded', function() {
            // Animate cards on scroll
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.animationPlayState = 'running';
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            // Observe all cards
            document.querySelectorAll('.stat-card, .quick-action-card, .subject-card').forEach(card => {
                observer.observe(card);
            });

            // Initialize search
            initializeSearch();
        });

        // Search functionality
        function initializeSearch() {
            const searchInput = document.getElementById('globalSearch');
            if (!searchInput) return;

            let searchTimeout;
            
            searchInput.addEventListener('input', function(e) {
                clearTimeout(searchTimeout);
                const searchTerm = e.target.value.trim();
                
                if (searchTerm.length >= 2) {
                    searchTimeout = setTimeout(() => {
                        performSearch(searchTerm);
                    }, 300);
                }
            });

            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    const searchTerm = this.value.trim();
                    if (searchTerm.length > 0) {
                        performSearch(searchTerm);
                    }
                }
            });
        }

        function performSearch(term) {
            showNotification(`Searching for "${term}"...`, 'info');
            
            // Simulate search results
            setTimeout(() => {
                const results = [
                    'Resources: 5 matches found',
                    'Students: 2 matches found',
                    'Assignments: 3 matches found'
                ].join('<br>');
                
                showModal('Search Results', `
                    <div style="padding: 1.5rem;">
                        <div style="margin-bottom: 1rem; color: var(--gray-600);">
                            Showing results for: <strong>${term}</strong>
                        </div>
                        <div style="color: var(--gray-700);">
                            ${results}
                        </div>
                    </div>
                `);
            }, 500);
        }

        // Filter quick actions
        function filterActions(category) {
            const cards = document.querySelectorAll('.quick-action-card');
            const buttons = document.querySelectorAll('.filter-btn');
            
            // Update active button
            buttons.forEach(btn => {
                btn.classList.remove('active');
                if (btn.textContent.toLowerCase().includes(category)) {
                    btn.classList.add('active');
                }
            });
            
            // Filter cards with animation
            cards.forEach(card => {
                if (category === 'all') {
                    card.style.display = 'flex';
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, 50);
                } else {
                    if (card.getAttribute('data-category') === category) {
                        card.style.display = 'flex';
                        setTimeout(() => {
                            card.style.opacity = '1';
                            card.style.transform = 'translateY(0)';
                        }, 50);
                    } else {
                        card.style.opacity = '0';
                        card.style.transform = 'translateY(20px)';
                        setTimeout(() => {
                            card.style.display = 'none';
                        }, 300);
                    }
                }
            });
        }

        // Notification system
        function toggleNotifications() {
            const notifications = [
                { 
                    title: 'New Resource Uploaded', 
                    message: 'You successfully uploaded "Deep Learning Notes.pdf"', 
                    time: '2 hours ago', 
                    type: 'success',
                    icon: 'upload'
                },
                { 
                    title: 'AI Quiz Generated', 
                    message: 'Quiz for CS101 - Computer Science has been created', 
                    time: '1 day ago', 
                    type: 'info',
                    icon: 'question-circle'
                },
                { 
                    title: 'Student Activity', 
                    message: '5 students accessed your resources today', 
                    time: '2 days ago', 
                    type: 'warning',
                    icon: 'users'
                }
            ];
            
            const notificationCount = <?php echo min($recent_uploads + $ai_assignments, 9); ?>;
            
            let notificationHTML = `
                <div style="max-height: 400px; overflow-y: auto;">
                    <div style="padding: 1.5rem; border-bottom: 1px solid var(--gray-200); background: var(--gray-50);">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <h4 style="margin: 0; font-size: 1.125rem; color: var(--gray-900); font-weight: 800;">Notifications</h4>
                            <button onclick="markAllAsRead()" style="background: none; border: none; color: var(--primary-blue); cursor: pointer; font-size: 0.875rem; font-weight: 600; padding: 0.5rem; border-radius: 6px;">
                                Mark all as read
                            </button>
                        </div>
                        <div style="font-size: 0.875rem; color: var(--gray-600); margin-top: 0.5rem;">
                            ${notificationCount} unread notifications
                        </div>
                    </div>
            `;
            
            notifications.forEach((notification, index) => {
                notificationHTML += `
                    <div style="padding: 1.25rem; border-bottom: 1px solid var(--gray-100); cursor: pointer; transition: background 0.3s;" 
                         onmouseover="this.style.background='var(--gray-50)'" 
                         onmouseout="this.style.background='transparent'"
                         onclick="viewNotification(${index})">
                        <div style="display: flex; align-items: flex-start; gap: 1rem;">
                            <div style="width: 36px; height: 36px; border-radius: 10px; background: var(--primary-blue); color: white; display: flex; align-items: center; justify-content: center; font-size: 1rem;">
                                <i class="fas fa-${notification.icon}"></i>
                            </div>
                            <div style="flex: 1;">
                                <div style="font-weight: 700; color: var(--gray-900); margin-bottom: 0.25rem;">${notification.title}</div>
                                <div style="font-size: 0.875rem; color: var(--gray-600); margin-bottom: 0.5rem;">${notification.message}</div>
                                <div style="font-size: 0.75rem; color: var(--gray-500);">${notification.time}</div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            notificationHTML += `</div>`;
            
            showModal('Notifications', notificationHTML);
        }

        function markAllAsRead() {
            document.querySelector('.notification-count').style.display = 'none';
            showNotification('All notifications marked as read', 'success');
            closeModal();
        }

        function viewNotification(index) {
            const notifications = [
                'Resource uploaded successfully and is now available for students.',
                'Quiz has been generated and saved to your dashboard.',
                'Students are actively engaging with your materials.'
            ];
            
            showModal('Notification Details', `
                <div style="padding: 1.5rem;">
                    <div style="font-size: 1.125rem; font-weight: 700; color: var(--gray-900); margin-bottom: 1rem;">
                        Notification Details
                    </div>
                    <div style="color: var(--gray-700); line-height: 1.6; margin-bottom: 1.5rem;">
                        ${notifications[index]}
                    </div>
                    <button onclick="closeModal()" style="width: 100%; padding: 0.875rem; background: var(--primary-blue); color: white; border: none; border-radius: 10px; font-weight: 600; cursor: pointer; transition: background 0.3s;">
                        Close
                    </button>
                </div>
            `);
        }

        // AI Assignment generation
        function generateQuickAssignment() {
            const subjects = <?php echo json_encode(array_column($assigned_subjects ?? [], 'subject_code')); ?>;
            
            if (subjects.length === 0) {
                showNotification('No subjects assigned to generate assignments', 'error');
                return;
            }
            
            let promptHTML = `
                <div style="padding: 1.5rem;">
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--gray-700);">
                            Select Subject
                        </label>
                        <select id="subjectSelect" style="width: 100%; padding: 0.875rem; border: 2px solid var(--gray-300); border-radius: 10px; font-size: 0.9375rem; background: var(--white); color: var(--gray-800);">
                            <option value="">-- Select Subject --</option>
            `;
            
            subjects.forEach(subject => {
                promptHTML += `<option value="${subject}">${subject}</option>`;
            });
            
            promptHTML += `
                        </select>
                    </div>
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--gray-700);">
                            Assignment Type
                        </label>
                        <select id="assignmentType" style="width: 100%; padding: 0.875rem; border: 2px solid var(--gray-300); border-radius: 10px; font-size: 0.9375rem; background: var(--white); color: var(--gray-800);">
                            <option value="quiz">AI Quiz</option>
                            <option value="question_paper">Question Paper</option>
                            <option value="assignment">Regular Assignment</option>
                        </select>
                    </div>
                    <div style="display: flex; gap: 1rem;">
                        <button onclick="closeModal()" style="flex: 1; padding: 0.875rem; background: var(--gray-200); color: var(--gray-700); border: none; border-radius: 10px; font-weight: 600; cursor: pointer; transition: background 0.3s;">
                            Cancel
                        </button>
                        <button onclick="submitQuickAssignment()" style="flex: 1; padding: 0.875rem; background: linear-gradient(135deg, var(--accent-green), #5a942f); color: white; border: none; border-radius: 10px; font-weight: 600; cursor: pointer; transition: all 0.3s;">
                            Generate
                        </button>
                    </div>
                </div>
            `;
            
            showModal('Generate AI Assignment', promptHTML);
        }

        function generateSubjectAssignment(subjectCode) {
            if (!subjectCode) {
                showNotification('No subject selected', 'error');
                return;
            }
            
            showNotification(`Generating assignment for ${subjectCode}...`, 'info');
            
            // Redirect to assignment generator with subject parameter
            setTimeout(() => {
                window.location.href = '<?php echo base_url("simple_portal/generate_assignment"); ?>?subject=' + encodeURIComponent(subjectCode);
            }, 1000);
        }

        function submitQuickAssignment() {
            const subject = document.getElementById('subjectSelect')?.value;
            const type = document.getElementById('assignmentType')?.value;
            
            if (!subject) {
                showNotification('Please select a subject', 'error');
                return;
            }
            
            let redirectUrl = '';
            switch(type) {
                case 'quiz':
                    redirectUrl = '<?php echo base_url("ai_buddy/generate_quiz"); ?>?subject=' + encodeURIComponent(subject);
                    break;
                case 'question_paper':
                    redirectUrl = '<?php echo base_url("ai_buddy/generate_question_paper"); ?>?subject=' + encodeURIComponent(subject);
                    break;
                case 'assignment':
                    redirectUrl = '<?php echo base_url("simple_portal/generate_assignment"); ?>?subject=' + encodeURIComponent(subject);
                    break;
            }
            
            closeModal();
            showNotification(`Generating ${type.replace('_', ' ')} for ${subject}...`, 'info');
            
            setTimeout(() => {
                window.location.href = redirectUrl;
            }, 1500);
        }

        // Analytics modal
        function showAnalytics() {
            const stats = {
                resources: <?php echo $total_resources; ?>,
                students: <?php echo $total_students; ?>,
                aiActivities: <?php echo $ai_assignments; ?>,
                recentUploads: <?php echo $recent_uploads; ?>,
                subjects: <?php echo count($assigned_subjects); ?>
            };
            
            const analyticsHTML = `
                <div style="padding: 1.5rem;">
                    <div style="margin-bottom: 1.5rem;">
                        <div style="font-size: 1.125rem; font-weight: 700; color: var(--gray-900); margin-bottom: 1rem;">
                            Analytics Dashboard
                        </div>
                        <div style="color: var(--gray-600); font-size: 0.875rem;">
                            Overview of your teaching activities and resource usage
                        </div>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
                        <div style="background: linear-gradient(135deg, rgba(139, 92, 246, 0.1), rgba(139, 92, 246, 0.05)); padding: 1.25rem; border-radius: 12px; text-align: center; border: 1px solid rgba(139, 92, 246, 0.2);">
                            <div style="font-size: 1.75rem; font-weight: 800; color: var(--gray-900); margin-bottom: 0.5rem;">${stats.resources}</div>
                            <div style="font-size: 0.75rem; color: var(--gray-600); font-weight: 600;">Total Resources</div>
                        </div>
                        <div style="background: linear-gradient(135deg, rgba(37, 99, 235, 0.1), rgba(37, 99, 235, 0.05)); padding: 1.25rem; border-radius: 12px; text-align: center; border: 1px solid rgba(37, 99, 235, 0.2);">
                            <div style="font-size: 1.75rem; font-weight: 800; color: var(--gray-900); margin-bottom: 0.5rem;">${stats.students}</div>
                            <div style="font-size: 0.75rem; color: var(--gray-600); font-weight: 600;">Students</div>
                        </div>
                    </div>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <div style="font-size: 0.875rem; font-weight: 600; color: var(--gray-700); margin-bottom: 0.75rem;">Detailed Statistics</div>
                        <div style="background: var(--gray-50); padding: 1.25rem; border-radius: 12px; border: 1px solid var(--gray-200);">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--gray-200);">
                                <span style="font-size: 0.875rem; color: var(--gray-700);">AI Activities</span>
                                <span style="font-weight: 700; color: var(--gray-900);">${stats.aiActivities}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--gray-200);">
                                <span style="font-size: 0.875rem; color: var(--gray-700);">Recent Uploads (7 days)</span>
                                <span style="font-weight: 700; color: var(--gray-900);">${stats.recentUploads}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-size: 0.875rem; color: var(--gray-700);">Assigned Subjects</span>
                                <span style="font-weight: 700; color: var(--gray-900);">${stats.subjects}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div style="font-size: 0.75rem; color: var(--gray-500); text-align: center; padding-top: 1rem; border-top: 1px solid var(--gray-200);">
                        Last updated: Just now • Auto-refreshes every 5 minutes
                    </div>
                </div>
            `;
            
            showModal('Analytics Dashboard', analyticsHTML);
        }

        // Activity functions
        function refreshActivities() {
            const refreshBtn = event.target.closest('.refresh-btn');
            const icon = refreshBtn.querySelector('i');
            
            // Show loading animation
            icon.style.animation = 'spin 1s linear infinite';
            refreshBtn.disabled = true;
            
            // Simulate API call
            setTimeout(() => {
                showNotification('Activities refreshed successfully', 'success');
                icon.style.animation = '';
                refreshBtn.disabled = false;
            }, 1500);
        }

        function viewActivityDetails() {
            showModal('Activity Details', `
                <div style="padding: 1.5rem;">
                    <div style="font-size: 1.125rem; font-weight: 700; color: var(--gray-900); margin-bottom: 1rem;">
                        Activity Details
                    </div>
                    <div style="color: var(--gray-700); line-height: 1.6; margin-bottom: 1.5rem;">
                        Detailed information about this activity. You can view the resource, edit it, or see student engagement statistics.
                    </div>
                    <div style="display: flex; gap: 1rem;">
                        <button onclick="closeModal()" style="flex: 1; padding: 0.875rem; background: var(--gray-200); color: var(--gray-700); border: none; border-radius: 10px; font-weight: 600; cursor: pointer; transition: background 0.3s;">
                            Close
                        </button>
                        <button onclick="viewResource()" style="flex: 1; padding: 0.875rem; background: var(--primary-blue); color: white; border: none; border-radius: 10px; font-weight: 600; cursor: pointer; transition: background 0.3s;">
                            View Resource
                        </button>
                    </div>
                </div>
            `);
        }

        function viewResource() {
            showNotification('Opening resource details...', 'info');
            closeModal();
        }

        // Modal system
        function showModal(title, content) {
            // Remove existing modal
            if (activeModal) {
                document.body.removeChild(activeModal);
            }
            
            activeModal = document.createElement('div');
            activeModal.className = 'modal-overlay';
            
            const modalContent = document.createElement('div');
            modalContent.className = 'modal-content';
            
            modalContent.innerHTML = `
                <div style="padding: 1.5rem 2rem; border-bottom: 1px solid var(--gray-200); display: flex; justify-content: space-between; align-items: center; background: var(--gray-50);">
                    <h3 style="margin: 0; font-size: 1.25rem; font-weight: 800; color: var(--gray-900);">${title}</h3>
                    <button onclick="closeModal()" style="background: none; border: none; font-size: 1.5rem; color: var(--gray-500); cursor: pointer; padding: 0; line-height: 1; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 8px; transition: background 0.3s;" 
                            onmouseover="this.style.background='var(--gray-200)'" 
                            onmouseout="this.style.background='transparent'">
                        &times;
                    </button>
                </div>
                ${content}
            `;
            
            activeModal.appendChild(modalContent);
            document.body.appendChild(activeModal);
            
            // Add keypress listener for ESC
            activeModal.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') closeModal();
            });
            
            // Close on backdrop click
            activeModal.addEventListener('click', function(e) {
                if (e.target === activeModal) closeModal();
            });
            
            // Focus modal
            activeModal.setAttribute('tabindex', '0');
            activeModal.focus();
        }

        function closeModal() {
            if (activeModal) {
                activeModal.style.animation = 'fadeOut 0.3s ease-out forwards';
                setTimeout(() => {
                    if (activeModal && activeModal.parentElement) {
                        document.body.removeChild(activeModal);
                        activeModal = null;
                    }
                }, 300);
            }
        }

        // User menu
        function showUserMenu() {
            // Remove existing menu
            if (userMenu) {
                document.body.removeChild(userMenu);
                userMenu = null;
                return;
            }
            
            userMenu = document.createElement('div');
            userMenu.className = 'user-menu';
            
            userMenu.innerHTML = `
                <a href="<?php echo base_url('simple_portal/profile'); ?>" 
                   style="display: flex; align-items: center; padding: 1rem 1.5rem; text-decoration: none; color: var(--gray-800); transition: background 0.3s; border-bottom: 1px solid var(--gray-100);"
                   onmouseover="this.style.background='var(--gray-50)'" 
                   onmouseout="this.style.background='transparent'">
                    <i class="fas fa-user" style="width: 20px; margin-right: 1rem; color: var(--gray-600);"></i>
                    <span style="font-size: 0.875rem; font-weight: 600;">Profile</span>
                </a>
                <a href="<?php echo base_url('simple_portal/settings'); ?>" 
                   style="display: flex; align-items: center; padding: 1rem 1.5rem; text-decoration: none; color: var(--gray-800); transition: background 0.3s; border-bottom: 1px solid var(--gray-100);"
                   onmouseover="this.style.background='var(--gray-50)'" 
                   onmouseout="this.style.background='transparent'">
                    <i class="fas fa-cog" style="width: 20px; margin-right: 1rem; color: var(--gray-600);"></i>
                    <span style="font-size: 0.875rem; font-weight: 600;">Settings</span>
                </a>
                <a href="<?php echo base_url('simple_portal?action=logout'); ?>" 
                   style="display: flex; align-items: center; padding: 1rem 1.5rem; text-decoration: none; color: #ef4444; transition: background 0.3s;"
                   onmouseover="this.style.background='rgba(239, 68, 68, 0.1)'" 
                   onmouseout="this.style.background='transparent'">
                    <i class="fas fa-sign-out-alt" style="width: 20px; margin-right: 1rem;"></i>
                    <span style="font-size: 0.875rem; font-weight: 600;">Logout</span>
                </a>
            `;
            
            document.body.appendChild(userMenu);
            
            // Close menu when clicking outside
            setTimeout(() => {
                const closeMenu = (e) => {
                    if (userMenu && !userMenu.contains(e.target) && !document.querySelector('.user-profile').contains(e.target)) {
                        document.body.removeChild(userMenu);
                        userMenu = null;
                        document.removeEventListener('click', closeMenu);
                    }
                };
                document.addEventListener('click', closeMenu);
            }, 0);
        }

        // Notification system
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = 'notification-popup';
            
            notification.innerHTML = `
                <span style="font-size: 0.9375rem; flex: 1;">${message}</span>
                <button onclick="this.parentElement.remove()" 
                        style="background: none; border: none; color: rgba(255, 255, 255, 0.8); font-size: 1.25rem; cursor: pointer; padding: 0; line-height: 1; transition: color 0.3s;"
                        onmouseover="this.style.color='white'" 
                        onmouseout="this.style.color='rgba(255, 255, 255, 0.8)'">
                    &times;
                </button>
            `;
            
            // Set notification color based on type
            switch(type) {
                case 'success':
                    notification.style.background = 'linear-gradient(135deg, var(--success), #059669)';
                    break;
                case 'error':
                    notification.style.background = 'linear-gradient(135deg, var(--error), #dc2626)';
                    break;
                case 'warning':
                    notification.style.background = 'linear-gradient(135deg, var(--warning), #d97706)';
                    break;
                default:
                    notification.style.background = 'linear-gradient(135deg, var(--info), #2563eb)';
            }
            
            document.body.appendChild(notification);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.style.animation = 'slideOutRight 0.4s ease-out forwards';
                    setTimeout(() => {
                        if (notification.parentElement) notification.remove();
                    }, 400);
                }
            }, 5000);
        }

        // Add missing CSS for animations
        const style = document.createElement('style');
        style.textContent = `
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
            
            @keyframes fadeOut {
                from { opacity: 1; }
                to { opacity: 0; }
            }
        `;
        document.head.appendChild(style);
    </script>

    </body>
    </html>