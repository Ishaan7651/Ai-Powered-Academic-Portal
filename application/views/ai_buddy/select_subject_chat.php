<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Chat - Select Subject</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <?php 
    // Load appropriate sidebar CSS based on user role
    if ($user_role === 'student') {
        $this->load->view('simple_portal/components/student_sidebar_css');
    } else {
        $this->load->view('simple_portal/components/faculty_sidebar_css');
    }
    ?>

<style>
    :root {
        --primary-blue: #4A76A8;
        --primary-dark: #1D4486;
        --success-green: #759B49;
        --light-bg: #eef2f7;
        --white: #ffffff;
        --text-dark: #333333;
        --text-light: #666666;
        --border-color: #e0e0e0;
    }

    body {
        background: var(--light-bg) !important;
    }

    .select-subject-container {
        max-width: 100%;
        margin: 0;
        padding: 30px;
        margin-left: var(--sidebar-width);
        min-height: 100vh;
    }

    .page-header {
        text-align: center;
        margin-bottom: 40px;
        padding: 20px;
    }

    .page-header h1 {
        font-size: 32px;
        font-weight: 600;
        color: var(--primary-dark);
        margin-bottom: 10px;
    }

    .page-header p {
        font-size: 16px;
        color: var(--text-light);
    }

    .semester-section {
        background: var(--white);
        border-radius: 12px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        max-width: 1400px;
        margin-left: auto;
        margin-right: auto;
        margin-bottom: 30px;
    }

    .semester-header {
        font-size: 20px;
        font-weight: 600;
        color: var(--primary-dark);
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid var(--border-color);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .semester-badge {
        background: linear-gradient(135deg, var(--primary-blue), var(--primary-dark));
        color: white;
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 500;
    }

    .subjects-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
    }

    .subject-card {
        background: var(--white);
        border: 2px solid var(--border-color);
        border-radius: 12px;
        padding: 24px;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .subject-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--success-green), var(--primary-blue));
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .subject-card:hover {
        border-color: var(--primary-blue);
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(74, 118, 168, 0.15);
    }

    .subject-card:hover::before {
        transform: scaleX(1);
    }

    .subject-code {
        font-size: 14px;
        font-weight: 600;
        color: var(--primary-blue);
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .subject-name {
        font-size: 18px;
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 12px;
        line-height: 1.4;
    }

    .subject-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, var(--success-green), #8BAD5A);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
        margin-bottom: 15px;
    }

    .no-subjects {
        text-align: center;
        padding: 40px;
        color: var(--text-light);
    }

    .no-subjects i {
        font-size: 48px;
        color: var(--border-color);
        margin-bottom: 15px;
    }

    .back-button {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        background: var(--white);
        border: 2px solid var(--border-color);
        border-radius: 8px;
        color: var(--text-dark);
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
        margin-bottom: 20px;
    }

    .back-button:hover {
        border-color: var(--primary-blue);
        color: var(--primary-blue);
        transform: translateX(-5px);
    }

    @media (max-width: 768px) {
        .subjects-grid {
            grid-template-columns: 1fr;
        }

        .page-header h1 {
            font-size: 24px;
        }
    }
</style>
</head>
<body>

<?php 
// Load appropriate sidebar based on user role
if ($user_role === 'student') {
    $this->load->view('simple_portal/components/student_sidebar', ['active_page' => 'ai_chat']);
} else {
    $this->load->view('simple_portal/components/faculty_sidebar', ['active_page' => 'ai_chat']);
}
?>

<div class="select-subject-container">
    <a href="<?php echo base_url('simple_portal/select_semester_for_chat'); ?>" class="back-button">
        <i class="fas fa-arrow-left"></i>
        Back to Semesters
    </a>

    <div class="page-header">
        <h1><i class="fas fa-comments"></i> HawkAI - Select Subject</h1>
        <p>Semester <?php echo isset($semester) ? $semester : ''; ?> - Choose a subject to start chatting</p>
    </div>

    <?php if (empty($enrolled_subjects)): ?>
        <div class="semester-section">
            <div class="no-subjects">
                <i class="fas fa-inbox"></i>
                <h3>No Subjects Found</h3>
                <p>You are not enrolled in any subjects for this semester.</p>
            </div>
        </div>
    <?php else: ?>
        <div class="semester-section">
            <div class="subjects-grid">
                    <?php foreach ($enrolled_subjects as $subject): ?>
                        <a href="<?php echo base_url('simple_portal/start_subject_chat?subject_id=' . $subject->id); ?>" 
                           class="subject-card" style="text-decoration: none;">
                            <div class="subject-icon">
                                <i class="fas fa-book"></i>
                            </div>
                            <div class="subject-code"><?php echo htmlspecialchars($subject->subject_code); ?></div>
                            <div class="subject-name"><?php echo htmlspecialchars($subject->subject_name); ?></div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
