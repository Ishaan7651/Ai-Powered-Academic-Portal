<?php
// Determine active page if not set
if (!isset($active_page)) {
    $active_page = 'dashboard';
}

function get_active_class_faculty($page_name, $current_page) {
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
            <div class="nav-title">FACULTY ACCESS</div>
            <a href="<?php echo base_url('simple_portal'); ?>" class="nav-item <?php echo get_active_class_faculty('dashboard', $active_page); ?>">
                <i class="fa fa-tachometer-alt"></i>
                <span>DASHBOARD</span>
            </a>
            <a href="<?php echo base_url('simple_portal/resources'); ?>" class="nav-item <?php echo get_active_class_faculty('resources', $active_page); ?>">
                <i class="fa fa-folder-open"></i>
                <span>MY RESOURCES</span>
            </a>
            <a href="<?php echo base_url('simple_portal/upload_resource'); ?>" class="nav-item <?php echo get_active_class_faculty('upload_resource', $active_page); ?>">
                <i class="fa fa-upload"></i>
                <span>UPLOAD RESOURCE</span>
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-title">AI TOOLS</div>
            <a href="<?php echo base_url('simple_portal/ai_chat'); ?>" class="nav-item <?php echo get_active_class_faculty('ai_chat', $active_page); ?>">
                <i class="fa fa-robot"></i>
                <span>HAWKAI</span>
            </a>
            <a href="<?php echo base_url('simple_portal/generate_assignment'); ?>" class="nav-item <?php echo get_active_class_faculty('ai_assignment', $active_page); ?>">
                <i class="fa fa-file-signature"></i>
                <span>AI ASSIGNMENT</span>
            </a>
            <a href="<?php echo base_url('simple_portal/generate_quiz'); ?>" class="nav-item <?php echo get_active_class_faculty('quiz_generator', $active_page); ?>">
                <i class="fa fa-question-circle"></i>
                <span>QUIZ GENERATOR</span>
            </a>
            <a href="<?php echo base_url('simple_portal/generate_question_paper'); ?>" class="nav-item <?php echo get_active_class_faculty('question_papers', $active_page); ?>">
                <i class="fa fa-file-alt"></i>
                <span>QUESTION PAPERS</span>
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-title">ACCOUNT</div>
            <a href="<?php echo base_url('simple_portal/settings'); ?>" class="nav-item <?php echo get_active_class_faculty('settings', $active_page); ?>">
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
