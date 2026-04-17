<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Chat - Select Semester</title>
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

    .select-semester-container {
        max-width: 1200px;
        margin: 40px auto;
        padding: 0 20px;
        margin-left: var(--sidebar-width);
    }

    .page-header {
        text-align: center;
        margin-bottom: 40px;
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

    .semesters-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 40px;
    }

    .semester-card {
        background: var(--white);
        border: 2px solid var(--border-color);
        border-radius: 12px;
        padding: 30px 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        text-decoration: none;
        color: inherit;
        display: block;
    }

    .semester-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-blue), var(--primary-dark));
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .semester-card:hover::before {
        transform: scaleX(1);
    }

    .semester-card:hover {
        border-color: var(--primary-blue);
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(74, 118, 168, 0.15);
        color: inherit;
    }

    .semester-card.locked {
        opacity: 0.5;
        cursor: not-allowed;
        background: #f5f5f5;
    }

    .semester-card.locked:hover {
        transform: none;
        border-color: var(--border-color);
        box-shadow: none;
    }

    .semester-icon {
        width: 60px;
        height: 60px;
        margin: 0 auto 15px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        color: var(--white);
        background: linear-gradient(135deg, var(--primary-blue), var(--primary-dark));
    }

    .semester-card.locked .semester-icon {
        background: #ccc;
    }

    .semester-title {
        font-size: 20px;
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 8px;
    }

    .semester-count {
        font-size: 14px;
        color: var(--text-light);
    }

    .semester-status {
        margin-top: 10px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }

    .status-available {
        background: #d1fae5;
        color: #065f46;
    }

    .status-locked {
        background: #fee2e2;
        color: #991b1b;
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
        .semesters-grid {
            grid-template-columns: repeat(2, 1fr);
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

<div class="select-semester-container">
    <a href="<?php echo base_url('simple_portal'); ?>" class="back-button">
        <i class="fas fa-arrow-left"></i>
        Back to Dashboard
    </a>

    <div class="page-header">
        <h1><i class="fas fa-comments"></i> HawkAI - Select Semester</h1>
        <p>Choose your semester to view available subjects</p>
    </div>

    <div class="semesters-grid">
        <?php
        // Group subjects by semester
        $subjects_by_semester = [];
        foreach ($enrolled_subjects as $subject) {
            $sem = $subject->semester ?? 'Unknown';
            if (!isset($subjects_by_semester[$sem])) {
                $subjects_by_semester[$sem] = [];
            }
            $subjects_by_semester[$sem][] = $subject;
        }
        ksort($subjects_by_semester);

        // Show all 8 semesters
        for ($sem = 1; $sem <= 8; $sem++):
            $has_subjects = isset($subjects_by_semester[$sem]);
            $subject_count = $has_subjects ? count($subjects_by_semester[$sem]) : 0;
            $is_locked = !$has_subjects;
        ?>
            <?php if ($is_locked): ?>
                <div class="semester-card locked">
                    <div class="semester-icon">
                        <i class="fas fa-lock"></i>
                    </div>
                    <div class="semester-title">Semester <?php echo $sem; ?></div>
                    <div class="semester-count">0 subjects</div>
                    <div class="semester-status status-locked">Locked</div>
                </div>
            <?php else: ?>
                <a href="<?php echo base_url('simple_portal/select_subject_for_chat?semester=' . $sem); ?>" class="semester-card">
                    <div class="semester-icon">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <div class="semester-title">Semester <?php echo $sem; ?></div>
                    <div class="semester-count"><?php echo $subject_count; ?> subject<?php echo $subject_count > 1 ? 's' : ''; ?></div>
                    <div class="semester-status status-available">Available</div>
                </a>
            <?php endif; ?>
        <?php endfor; ?>
    </div>
</div>

</body>
</html>
