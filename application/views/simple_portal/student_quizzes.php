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
    <title>Quizzes - SLAi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <?php $this->load->view('simple_portal/components/student_sidebar_css'); ?>
    <style>
        /* Page-specific styles */
        .subject-filters {
            display: flex;
            gap: 12px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 10px 20px;
            border-radius: 12px;
            background: var(--white);
            border: 1px solid var(--border-color);
            color: var(--text-light);
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .filter-btn:hover {
            border-color: var(--primary-blue);
            color: var(--primary-blue);
            transform: translateY(-2px);
        }

        .filter-btn.active {
            background: var(--primary-blue);
            color: white;
            border-color: var(--primary-blue);
            box-shadow: 0 4px 12px rgba(31, 94, 168, 0.2);
        }

        .quizzes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 25px;
        }

        .quiz-card {
            background: var(--white);
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            border: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            gap: 20px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .quiz-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 30px rgba(31, 94, 168, 0.1);
        }

        .quiz-subject {
            height: 32px;
            padding: 0 14px;
            background: rgba(31, 94, 168, 0.08);
            color: var(--primary-blue);
            border-radius: 8px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            width: fit-content;
        }

        .quiz-info h3 {
            font-size: 20px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 15px;
            line-height: 1.3;
        }

        .quiz-meta {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-light);
            font-size: 13px;
        }

        .meta-item i {
            color: var(--primary-blue);
            width: 16px;
        }

        .quiz-footer {
            margin-top: auto;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .difficulty {
            font-weight: 700;
            font-size: 12px;
            text-transform: uppercase;
            padding: 4px 10px;
            border-radius: 20px;
        }

        .diff-easy { color: #16a34a; background: #f0fdf4; }
        .diff-medium { color: #ca8a04; background: #fefce8; }
        .diff-hard { color: #dc2626; background: #fef2f2; }

        .btn-start {
            padding: 10px 20px;
            background: linear-gradient(135deg, var(--primary-blue), var(--primary-light));
            color: white;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .btn-start:hover {
            box-shadow: 0 4px 12px rgba(31, 94, 168, 0.3);
            transform: scale(1.05);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 20px;
            border: 1px dashed var(--border-color);
        }

        .empty-icon {
            font-size: 48px;
            color: var(--text-light);
            opacity: 0.3;
            margin-bottom: 20px;
        }

        .empty-state h2 {
            font-size: 24px;
            color: var(--text-dark);
            margin-bottom: 10px;
        }

        .empty-state p {
            color: var(--text-light);
        }

        @media (max-width: 768px) {
            .quizzes-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<div class="portal-container">
    <!-- Sidebar -->
    <!-- Sidebar -->
    <?php $this->load->view('simple_portal/components/student_sidebar', ['active_page' => 'quizzes']); ?>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Topbar -->
            <div class="topbar">
                <div class="page-title">Quizzes</div>
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
                        <h1>Assessments & Quizzes</h1>
                        <p>Enhance your knowledge through curriculum-aligned assessments</p>
                    </div>
                </div>

                <!-- Filter Bar -->
                <div class="subject-filters">
                    <button class="filter-btn active" onclick="filterBySubject('', this)">All Subjects</button>
                    <?php if (!empty($enrolled_subjects)): ?>
                        <?php foreach ($enrolled_subjects as $subject): ?>
                            <button class="filter-btn" 
                                    onclick="filterBySubject('<?php echo $subject->id; ?>', this)">
                                <?php echo htmlspecialchars($subject->subject_code); ?>
                            </button>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Quizzes Grid -->
                <?php if (!empty($quizzes)): ?>
                    <div class="quizzes-grid" id="quizzesGrid">
                        <?php foreach ($quizzes as $quiz): ?>
                            <div class="quiz-card" 
                                 data-subject="<?php echo $quiz->subject_id; ?>" 
                                 data-date="<?php echo strtotime($quiz->published_at); ?>" 
                                 data-questions="<?php echo isset($quiz->num_questions) ? $quiz->num_questions : 0; ?>">
                                
                                <div class="quiz-subject">
                                    <?php echo htmlspecialchars($quiz->subject_code); ?>
                                </div>
                                
                                <div class="quiz-info">
                                    <h3><?php echo htmlspecialchars($quiz->title); ?></h3>
                                    <div class="quiz-meta">
                                        <div class="meta-item">
                                            <i class="fas fa-list-ul"></i>
                                            <span><?php echo isset($quiz->num_questions) ? $quiz->num_questions : 0; ?> Qs</span>
                                        </div>
                                        <div class="meta-item">
                                            <i class="fas fa-clock"></i>
                                            <span><?php echo (isset($quiz->num_questions) ? $quiz->num_questions : 10) * 2; ?> min</span>
                                        </div>
                                        <div class="meta-item">
                                            <i class="fas fa-calendar-alt"></i>
                                            <span><?php echo timeAgo($quiz->published_at); ?></span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="quiz-footer">
                                    <span class="difficulty diff-<?php echo strtolower($quiz->difficulty); ?>">
                                        <?php echo $quiz->difficulty; ?>
                                    </span>
                                    <a href="<?php echo base_url('simple_portal/take_quiz/' . $quiz->id); ?>" class="btn-start">
                                        <i class="fas fa-play"></i> Start
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <h2>No Quizzes Available</h2>
                        <p>Your faculty hasn't published any quizzes for your enrolled subjects yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
    function filterBySubject(subjectId, btn) {
        // Update active button
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        const grid = document.getElementById('quizzesGrid');
        if (!grid) return;

        const cards = grid.querySelectorAll('.quiz-card');
        
        cards.forEach(card => {
            if (subjectId === '' || card.getAttribute('data-subject') === subjectId) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }
    </script>
</body>
</html>
