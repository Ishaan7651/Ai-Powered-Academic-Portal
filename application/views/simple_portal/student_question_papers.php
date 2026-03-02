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
    <title>Question Papers - SLAi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <?php $this->load->view('simple_portal/components/student_sidebar_css'); ?>
    <style>
        /* Page-specific styles */
        .papers-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
        }

        .paper-card {
            background: var(--white);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .paper-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(31, 94, 168, 0.15);
        }

        .paper-header {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            margin-bottom: 15px;
        }

        .paper-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--primary-blue), #114a7d);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            flex-shrink: 0;
        }

        .paper-info {
            flex: 1;
        }

        .paper-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 5px;
            line-height: 1.3;
        }

        .paper-subject {
            font-size: 13px;
            color: var(--primary-blue);
            font-weight: 600;
        }

        .paper-meta {
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
            color: var(--primary-blue);
        }

        .paper-footer {
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
            background: linear-gradient(135deg, var(--primary-blue), #114a7d);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(31, 94, 168, 0.3);
        }

        .btn-secondary {
            background: var(--light-bg);
            color: var(--text-dark);
            border: 2px solid var(--border-color);
        }

        .btn-secondary:hover {
            background: var(--white);
            border-color: var(--primary-blue);
            color: var(--primary-blue);
        }

        .filter-bar {
            background: var(--white);
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .filter-group {
            flex: 1;
        }

        .filter-label {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-light);
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .filter-select {
            width: 100%;
            padding: 10px 14px;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .filter-select:focus {
            outline: none;
            border-color: var(--primary-blue);
        }

        .empty-state {
            text-align: center;
            padding: 80px 20px;
            background: var(--white);
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
    </style>
</head>
<body>

<div class="portal-container">
    <!-- Sidebar -->
    <!-- Sidebar -->
    <?php $this->load->view('simple_portal/components/student_sidebar', ['active_page' => 'question_papers']); ?>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Topbar -->
        <div class="topbar">
            <div class="page-title">Question Papers</div>
            <div class="user-profile" style="cursor: default;">
                <div class="user-avatar"><?php echo strtoupper(substr($username ?? $this->session->userdata('username'), 0, 1)); ?></div>
                <div class="user-info">
                    <div class="user-name"><?php echo $username ?? $this->session->userdata('username'); ?></div>
                    <div class="user-role">Student</div>
                </div>
            </div>
        </div>

        <div class="content-area">
            <div class="dashboard-header">
                <div class="header-title">
                    <h1>Question Papers</h1>
                    <p>Access question papers published by your faculty for enrolled subjects</p>
                </div>
            </div>

            <!-- Filter Bar -->
            <div class="filter-bar">
            <div class="filter-group">
                <div class="filter-label">Filter by Subject</div>
                <select class="filter-select" id="subjectFilter" onchange="filterPapers()">
                    <option value="">All Subjects</option>
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
                <select class="filter-select" id="sortFilter" onchange="filterPapers()">
                    <option value="newest">Newest First</option>
                    <option value="oldest">Oldest First</option>
                    <option value="marks">Total Marks</option>
                </select>
            </div>
        </div>

        <!-- Question Papers Grid -->
        <?php if (!empty($question_papers)): ?>
            <div class="papers-grid" id="papersGrid">
                <?php foreach ($question_papers as $paper): ?>
                    <div class="paper-card" data-subject="<?php echo $paper->subject_id; ?>" data-date="<?php echo strtotime($paper->published_at); ?>" data-marks="<?php echo $paper->total_marks; ?>">
                        <div class="paper-header">
                            <div class="paper-icon">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div class="paper-info">
                                <div class="paper-title"><?php echo htmlspecialchars($paper->title); ?></div>
                                <div class="paper-subject">
                                    <?php echo htmlspecialchars($paper->subject_code . ' - ' . $paper->subject_name); ?>
                                </div>
                            </div>
                            <?php if (strtotime($paper->published_at) > strtotime('-7 days')): ?>
                                <span class="badge badge-new">New</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="paper-meta">
                            <div class="meta-item">
                                <i class="fas fa-star"></i>
                                <span><?php echo $paper->total_marks; ?> Marks</span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-clock"></i>
                                <span><?php echo $paper->duration_minutes; ?> min</span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-calendar"></i>
                                <span><?php echo timeAgo($paper->published_at); ?></span>
                            </div>
                        </div>
                        
                        <div class="paper-footer">
                            <a href="<?php echo base_url('simple_portal/view_question_paper/' . $paper->id); ?>" class="btn btn-primary">
                                <i class="fas fa-eye"></i>
                                View Paper
                            </a>
                            <a href="<?php echo base_url('simple_portal/download_question_paper/' . $paper->id); ?>" class="btn btn-secondary">
                                <i class="fas fa-download"></i>
                                Download
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-file-alt"></i>
                <h3>No Question Papers Available</h3>
                <p>Your faculty hasn't published any question papers yet. Check back later!</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<script>
function filterPapers() {
    const subjectFilter = document.getElementById('subjectFilter').value;
    const sortFilter = document.getElementById('sortFilter').value;
    const papersGrid = document.getElementById('papersGrid');
    const papers = Array.from(papersGrid.querySelectorAll('.paper-card'));
    
    // Filter by subject
    papers.forEach(paper => {
        const paperSubject = paper.getAttribute('data-subject');
        if (subjectFilter === '' || paperSubject === subjectFilter) {
            paper.style.display = 'block';
        } else {
            paper.style.display = 'none';
        }
    });
    
    // Sort papers
    const visiblePapers = papers.filter(paper => paper.style.display !== 'none');
    
    visiblePapers.sort((a, b) => {
        if (sortFilter === 'newest') {
            return parseInt(b.getAttribute('data-date')) - parseInt(a.getAttribute('data-date'));
        } else if (sortFilter === 'oldest') {
            return parseInt(a.getAttribute('data-date')) - parseInt(b.getAttribute('data-date'));
        } else if (sortFilter === 'marks') {
            return parseInt(b.getAttribute('data-marks')) - parseInt(a.getAttribute('data-marks'));
        }
        return 0;
    });
    
    // Re-append in sorted order
    visiblePapers.forEach(paper => {
        papersGrid.appendChild(paper);
    });
}
</script>

</body>
</html>