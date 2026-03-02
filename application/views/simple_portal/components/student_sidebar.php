<?php
// Determine active page if not set
if (!isset($active_page)) {
    $active_page = 'dashboard';
}

function get_active_class($page_name, $current_page) {
    return ($page_name === $current_page) ? 'active' : '';
}
?>

<!-- Sidebar -->
<aside class="sidebar">
    <div class="brand">
        <img src="<?php echo base_url('logo.png'); ?>" alt="SLAi Logo" class="brand-logo-img">
    </div>

    <div class="sidebar-nav">
        <div class="nav-section">
            <div class="nav-title">STUDENT ACCESS</div>
            <a href="<?php echo base_url('simple_portal'); ?>" class="nav-item <?php echo get_active_class('dashboard', $active_page); ?>">
                <i class="fa fa-tachometer-alt"></i>
                <span>DASHBOARD</span>
            </a>
            <a href="<?php echo base_url('simple_portal/student_resources'); ?>" class="nav-item <?php echo get_active_class('resources', $active_page); ?>">
                <i class="fa fa-book"></i>
                <span>RESOURCES</span>
            </a>
            <a href="<?php echo base_url('simple_portal/ai_chat'); ?>" class="nav-item <?php echo get_active_class('ai_chat', $active_page); ?>">
                <i class="fa fa-robot"></i>
                <span>HAWKAI</span>
            </a>
            <a href="<?php echo base_url('simple_portal/generate_mindmap'); ?>" class="nav-item <?php echo get_active_class('mindmap', $active_page); ?>">
                <i class="fa fa-project-diagram"></i>
                <span>MINDMAP</span>
            </a>
            <a href="<?php echo base_url('simple_portal/generate_flashcards'); ?>" class="nav-item <?php echo get_active_class('flashcards', $active_page); ?>">
                <i class="fa fa-layer-group"></i>
                <span>FLASHCARDS</span>
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-title">PUBLISHED CONTENT</div>
            <a href="<?php echo base_url('simple_portal/student_question_papers'); ?>" class="nav-item <?php echo get_active_class('question_papers', $active_page); ?>">
                <i class="fa fa-file-alt"></i>
                <span>QUESTION PAPERS</span>
            </a>
            <a href="<?php echo base_url('simple_portal/student_quizzes'); ?>" class="nav-item <?php echo get_active_class('quizzes', $active_page); ?>">
                <i class="fa fa-question-circle"></i>
                <span>QUIZZES</span>
            </a>
            <a href="<?php echo base_url('simple_portal/student_assignments'); ?>" class="nav-item <?php echo get_active_class('assignments', $active_page); ?>">
                <i class="fa fa-tasks"></i>
                <span>ASSIGNMENTS</span>
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-title">ACCOUNT</div>
            <a href="<?php echo base_url('simple_portal/settings'); ?>" class="nav-item <?php echo get_active_class('settings', $active_page); ?>">
                <i class="fa fa-cog"></i>
                <span>SETTINGS</span>
            </a>
            <a href="<?php echo base_url('simple_portal?action=logout'); ?>" class="nav-item logout-item">
                <i class="fa fa-sign-out-alt"></i>
                <span>LOGOUT</span>
            </a>
        </div>
    </div>
</aside>
