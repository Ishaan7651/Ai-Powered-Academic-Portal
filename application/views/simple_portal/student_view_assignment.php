<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($assignment->title); ?> - SLAi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- Shared Student Sidebar CSS -->
    <?php include_once(APPPATH . 'views/simple_portal/components/student_sidebar_css.php'); ?>

    <style>
        .assignment-container {
            background: var(--white);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            margin-bottom: 30px;
            border: 1px solid var(--border-color);
        }

        .assignment-header {
            background: linear-gradient(135deg, var(--primary-blue), var(--primary-light));
            color: white;
            padding: 45px 40px;
            position: relative;
        }

        .assignment-title {
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 12px;
            line-height: 1.2;
        }

        .assignment-subject {
            font-size: 16px;
            opacity: 0.9;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .meta-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .meta-item i {
            font-size: 20px;
            opacity: 0.8;
        }

        .meta-text {
            display: flex;
            flex-direction: column;
        }

        .meta-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.7;
        }

        .meta-value {
            font-size: 15px;
            font-weight: 600;
        }

        .assignment-content {
            padding: 40px;
        }

        .section {
            margin-bottom: 40px;
        }

        .section-title {
            font-size: 22px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .section-title i {
            color: var(--primary-blue);
        }

        .objective-list {
            list-style: none;
            display: grid;
            gap: 12px;
        }

        .objective-item {
            display: flex;
            gap: 12px;
            padding: 15px 20px;
            background: #f8fafc;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            font-size: 15px;
            line-height: 1.5;
        }

        .objective-item i {
            color: var(--success-green);
            margin-top: 3px;
        }

        .tasks-container {
            display: grid;
            gap: 25px;
        }

        .task-card {
            background: var(--white);
            border-radius: 16px;
            border: 1px solid var(--border-color);
            padding: 25px;
            transition: all 0.3s ease;
        }

        .task-card:hover {
            border-color: var(--primary-blue);
            box-shadow: 0 4px 20px rgba(31, 94, 168, 0.08);
        }

        .task-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px dashed var(--border-color);
        }

        .task-number {
            font-size: 13px;
            font-weight: 700;
            color: var(--primary-blue);
            background: rgba(31, 94, 168, 0.1);
            padding: 4px 12px;
            border-radius: 20px;
            text-transform: uppercase;
        }

        .task-words {
            font-size: 13px;
            color: var(--text-light);
            font-weight: 600;
        }

        .task-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 12px;
        }

        .task-desc {
            font-size: 15px;
            line-height: 1.6;
            color: var(--text-dark);
            opacity: 0.85;
        }

        .guidelines-box {
            background: #fffbeb;
            border: 1px solid #fef3c7;
            border-radius: 15px;
            padding: 25px;
        }

        .guideline-item {
            display: flex;
            gap: 12px;
            margin-bottom: 12px;
            font-size: 14px;
            color: #92400e;
        }

        .guideline-item i {
            margin-top: 3px;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--text-light);
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 25px;
            transition: color 0.3s ease;
        }

        .back-link:hover {
            color: var(--primary-blue);
        }

        @media (max-width: 768px) {
            .assignment-header { padding: 30px 25px; }
            .assignment-content { padding: 25px; }
            .assignment-title { font-size: 24px; }
        }
    </style>
</head>
<body>

<div class="portal-container">
    <!-- Sidebar -->
    <?php $this->load->view('simple_portal/components/student_sidebar', ['active_page' => 'assignments']); ?>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Topbar -->
        <div class="topbar">
            <div class="page-title">Assignment View</div>
            <div class="user-profile" style="cursor: default;">
                <div class="user-avatar"><?php echo strtoupper(substr($username, 0, 1)); ?></div>
                <div class="user-info">
                    <div class="user-name"><?php echo $username; ?></div>
                    <div class="user-role">Student</div>
                </div>
            </div>
        </div>

        <div class="content-area">
            <a href="<?php echo base_url('simple_portal/student_assignments'); ?>" class="back-link">
                <i class="fas fa-arrow-left"></i>
                Back to Assignments
            </a>

            <div class="assignment-container">
                <div class="assignment-header">
                    <h1 class="assignment-title"><?php echo htmlspecialchars($assignment->title); ?></h1>
                    <div class="assignment-subject">
                        <i class="fas fa-book"></i>
                        <?php echo htmlspecialchars($assignment->subject_code . ' - ' . $assignment->subject_name); ?>
                    </div>

                    <div class="meta-grid">
                        <div class="meta-item">
                            <i class="fas fa-layer-group"></i>
                            <div class="meta-text">
                                <span class="meta-label">Type</span>
                                <span class="meta-value"><?php echo ucfirst(str_replace('_', ' ', $assignment->assignment_type)); ?></span>
                            </div>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-signal"></i>
                            <div class="meta-text">
                                <span class="meta-label">Difficulty</span>
                                <span class="meta-value"><?php echo ucfirst($assignment->difficulty); ?></span>
                            </div>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-file-word"></i>
                            <div class="meta-text">
                                <span class="meta-label">Total Word Count</span>
                                <span class="meta-value"><?php echo $assignment->word_count; ?> Words</span>
                            </div>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-calendar-alt"></i>
                            <div class="meta-text">
                                <span class="meta-label">Due Date</span>
                                <span class="meta-value"><?php echo isset($assignment->parsed_data['assignment']['due_date']) ? $assignment->parsed_data['assignment']['due_date'] : 'In ' . $assignment->due_weeks . ' weeks'; ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="assignment-content">
                    <?php if (isset($assignment->parsed_data['assignment'])): ?>
                        <?php $data = $assignment->parsed_data['assignment']; ?>
                        
                        <!-- Description -->
                        <div class="section">
                            <div class="section-title">
                                <i class="fas fa-info-circle"></i>
                                Overview
                            </div>
                            <p style="line-height: 1.7; color: var(--text-dark); opacity: 0.9;">
                                <?php echo htmlspecialchars($data['description']); ?>
                            </p>
                        </div>

                        <!-- Objectives -->
                        <?php if (!empty($data['objectives'])): ?>
                        <div class="section">
                            <div class="section-title">
                                <i class="fas fa-bullseye"></i>
                                Learning Objectives
                            </div>
                            <div class="objective-list">
                                <?php foreach ($data['objectives'] as $objective): ?>
                                    <div class="objective-item">
                                        <i class="fas fa-check-circle"></i>
                                        <?php echo htmlspecialchars($objective); ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Tasks -->
                        <?php if (!empty($data['tasks'])): ?>
                        <div class="section">
                            <div class="section-title">
                                <i class="fas fa-tasks"></i>
                                Assignment Tasks
                            </div>
                            <div class="tasks-container">
                                <?php foreach ($data['tasks'] as $task): ?>
                                    <div class="task-card">
                                        <div class="task-header">
                                            <span class="task-number">Task <?php echo $task['task_number']; ?></span>
                                            <span class="task-words"><?php echo $task['word_count']; ?> Words</span>
                                        </div>
                                        <div class="task-title"><?php echo htmlspecialchars($task['task_title']); ?></div>
                                        <div class="task-desc"><?php echo htmlspecialchars($task['description']); ?></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Submission Guidelines -->
                        <?php if (!empty($data['submission_guidelines'])): ?>
                        <div class="section" style="margin-bottom: 0;">
                            <div class="section-title">
                                <i class="fas fa-file-upload"></i>
                                Submission Guidelines
                            </div>
                            <div class="guidelines-box">
                                <?php foreach ($data['submission_guidelines'] as $guideline): ?>
                                    <div class="guideline-item">
                                        <i class="fas fa-exclamation-circle"></i>
                                        <span><?php echo htmlspecialchars($guideline); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-exclamation-triangle"></i>
                            <h3>Unable to Load Content</h3>
                            <p>There was an error parsing the assignment data. Please contact your faculty if this persists.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
</div>

</body>
</html>
