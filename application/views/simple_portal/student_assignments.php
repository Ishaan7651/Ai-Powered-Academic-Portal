<?php
// Helper function for formatting dates
if (!function_exists('timeAgo')) {
    function timeAgo($datetime) {
        $timestamp = strtotime($datetime);
        $diff = time() - $timestamp;
        
        if ($diff < 60) return 'Just now';
        if ($diff < 3600) return floor($diff / 60) . ' minutes ago';
        if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
        if ($diff < 604800) return floor($diff / 86400) . ' days ago';
        return date('M j, Y', $timestamp);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignments - SLAi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- Shared Student Sidebar CSS -->
    <?php include_once(APPPATH . 'views/simple_portal/components/student_sidebar_css.php'); ?>

    <style>
        /* Page-specific styles */
        :root {
            --orange: #114a7d;
        }

        .assignments-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
        }

        .assignment-card {
            background: var(--white);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .assignment-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(249, 115, 22, 0.15);
        }

        .assignment-header {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            margin-bottom: 15px;
        }

        .assignment-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--orange), #114a7d);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            flex-shrink: 0;
        }

        .assignment-info {
            flex: 1;
        }

        .assignment-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 5px;
            line-height: 1.3;
        }

        .assignment-subject {
            font-size: 13px;
            color: var(--orange);
            font-weight: 600;
        }

        .assignment-meta {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
            padding: 12px 0;
            border-top: 1px solid var(--border-color);
            border-bottom: 1px solid var(--border-color);
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: var(--text-light);
        }

        .meta-item i {
            color: var(--orange);
        }

        .assignment-footer {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 10px 18px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            flex: 1;
            justify-content: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--orange), #114a7d);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(31, 94, 168, 0.3);
        }

        .empty-state {
            text-align: center;
            padding: 80px 20px;
            background: var(--white);
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .empty-state i {
            font-size: 64px;
            color: var(--text-light);
            opacity: 0.5;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            font-size: 22px;
            color: var(--text-dark);
            margin-bottom: 10px;
        }

        .empty-state p {
            color: var(--text-light);
            font-size: 15px;
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-new {
            background: #e8f5e8;
            color: var(--success-green);
        }
    </style>
</head>
<body>

<div class="portal-container">
    <!-- Sidebar -->
    <!-- Sidebar -->
    <?php $this->load->view('simple_portal/components/student_sidebar', ['active_page' => 'assignments']); ?>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Topbar -->
        <div class="topbar">
            <div class="page-title">Assignments</div>
            <div class="user-profile" style="cursor: default;">
                <div class="user-avatar"><?php echo strtoupper(substr($username, 0, 1)); ?></div>
                <div class="user-info">
                    <div class="user-name"><?php echo $username; ?></div>
                    <div class="user-role">Student</div>
                </div>
            </div>
        </div>

        <div class="content-area">
            <div class="dashboard-header">
                <div class="header-title">
                    <h1>Academic Assignments</h1>
                    <p>Track your assignments and research tasks across all subjects.</p>
                </div>
            </div>

            <!-- Filter & Search Bar -->
            <div class="filter-bar" style="background: var(--white); padding: 25px; border-radius: 16px; margin-bottom: 35px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); display: flex; gap: 20px; align-items: center; border: 1px solid var(--border-color);">
                <div class="search-box" style="flex: 1; position: relative;">
                    <i class="fas fa-search" style="position: absolute; left: 18px; top: 50%; transform: translateY(-50%); color: var(--text-light); font-size: 16px;"></i>
                    <input type="text" placeholder="Search assignments..." id="assignmentSearch" onkeyup="filterAssignments()" 
                           style="width: 100%; padding: 14px 20px 14px 50px; border-radius: 12px; border: 1px solid var(--border-color); font-size: 15px; outline: none; transition: all 0.3s ease; background: #f8fafc;">
                </div>
                <div class="filter-group" style="display: flex; align-items: center; gap: 15px;">
                    <div class="filter-label" style="font-size: 14px; font-weight: 600; color: var(--text-dark);">Subject</div>
                    <select class="filter-select" id="subjectFilter" onchange="filterAssignments()" 
                            style="padding: 12px 20px; border-radius: 10px; border: 1px solid var(--border-color); font-size: 14px; outline: none; cursor: pointer; background: white; min-width: 180px;">
                        <option value="all">All Subjects</option>
                        <?php if (!empty($enrolled_subjects)): ?>
                            <?php foreach ($enrolled_subjects as $subject): ?>
                                <option value="<?php echo $subject->id; ?>">
                                    <?php echo htmlspecialchars($subject->subject_code . ' - ' . $subject->subject_name); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <div class="filter-label">Sort By</div>
                    <select class="filter-select" id="sortFilter" onchange="filterAssignments()">
                        <option value="newest">Newest First</option>
                        <option value="oldest">Oldest First</option>
                    </select>
                </div>
            </div>

            <!-- Assignments Grid -->
            <?php if (!empty($assignments)): ?>
                <div class="assignments-grid" id="assignmentsGrid">
                    <?php foreach ($assignments as $assignment): ?>
                        <div class="assignment-card" data-subject="<?php echo $assignment->subject_id; ?>" data-date="<?php echo strtotime($assignment->published_at); ?>">
                            <div class="assignment-header">
                                <div class="assignment-icon">
                                    <i class="fas fa-tasks"></i>
                                </div>
                                <div class="assignment-info">
                                    <div class="assignment-title"><?php echo htmlspecialchars($assignment->title); ?></div>
                                    <div class="assignment-subject">
                                        <?php echo htmlspecialchars($assignment->subject_code . ' - ' . $assignment->subject_name); ?>
                                    </div>
                                </div>
                                <?php if (strtotime($assignment->published_at) > strtotime('-7 days')): ?>
                                    <span class="badge badge-new">New</span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="assignment-meta">
                                <div class="meta-item">
                                    <i class="fas fa-file-word"></i>
                                    <span><?php echo isset($assignment->word_count) ? $assignment->word_count : '500+'; ?> Words</span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-calendar"></i>
                                    <span><?php echo timeAgo($assignment->published_at); ?></span>
                                </div>
                            </div>
                            
                            <div class="assignment-footer">
                                <a href="<?php echo base_url('simple_portal/view_assignment/' . $assignment->id); ?>" class="btn btn-primary">
                                    <i class="fas fa-eye"></i>
                                    View Assignment
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-tasks"></i>
                    <h3>No Assignments Available</h3>
                    <p>Your faculty hasn't published any assignments yet. Check back later!</p>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<script>
function filterAssignments() {
    const subjectFilter = document.getElementById('subjectFilter').value;
    const sortFilter = document.getElementById('sortFilter').value;
    const assignmentsGrid = document.getElementById('assignmentsGrid');
    const assignments = Array.from(assignmentsGrid.querySelectorAll('.assignment-card'));
    
    // Filter by subject
    assignments.forEach(assignment => {
        const assignmentSubject = assignment.getAttribute('data-subject');
        if (subjectFilter === '' || assignmentSubject === subjectFilter) {
            assignment.style.display = 'block';
        } else {
            assignment.style.display = 'none';
        }
    });
    
    // Sort assignments
    const visibleAssignments = assignments.filter(assignment => assignment.style.display !== 'none');
    
    visibleAssignments.sort((a, b) => {
        if (sortFilter === 'newest') {
            return parseInt(b.getAttribute('data-date')) - parseInt(a.getAttribute('data-date'));
        } else if (sortFilter === 'oldest') {
            return parseInt(a.getAttribute('data-date')) - parseInt(b.getAttribute('data-date'));
        }
        return 0;
    });
    
    // Re-append in sorted order
    visibleAssignments.forEach(assignment => {
        assignmentsGrid.appendChild(assignment);
    });
}
</script>

</body>
</html>
