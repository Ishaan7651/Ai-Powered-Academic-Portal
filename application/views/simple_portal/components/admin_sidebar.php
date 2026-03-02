<?php
// Determine active page if not set
if (!isset($active_page)) {
    $active_page = 'dashboard';
}

function get_active_class_admin($page_name, $current_page) {
    return ($page_name === $current_page) ? 'active' : '';
}
?>
<!-- Sidebar -->
<aside class="sidebar">
    <div class="brand">
        <img src="<?php echo base_url('logo.png'); ?>" alt="SLAi Logo" class="brand-logo-img">
    </div>

    <div class="sidebar-nav">
        <!-- Admin Access Section -->
        <div class="nav-section">
            <div class="nav-title">ADMIN ACCESS</div>
            <a href="<?php echo base_url('simple_portal'); ?>" class="nav-item <?php echo get_active_class_admin('dashboard', $active_page); ?>">
                <i class="fa fa-user"></i>
                <span>USER ACCOUNTS</span>
            </a>
            <a href="<?php echo base_url('simple_portal/create_faculty'); ?>" class="nav-item <?php echo get_active_class_admin('create_faculty', $active_page); ?>">
                <i class="fa fa-user-plus"></i>
                <span>CREATE FACULTY</span>
            </a>
            <a href="<?php echo base_url('simple_portal/create_student'); ?>" class="nav-item <?php echo get_active_class_admin('create_student', $active_page); ?>">
                <i class="fa fa-user-plus"></i>
                <span>CREATE STUDENT</span>
            </a>
        </div>

        <!-- Quick Links Section -->
        <div class="nav-section">
            <div class="nav-title">QUICK LINKS</div>
            <a href="<?php echo base_url('simple_portal/manage_subjects'); ?>" class="nav-item <?php echo get_active_class_admin('manage_subjects', $active_page); ?>">
                <i class="fa fa-book"></i>
                <span>MANAGE SUBJECTS</span>
            </a>
            <a href="<?php echo base_url('simple_portal/manage_departments'); ?>" class="nav-item <?php echo get_active_class_admin('manage_departments', $active_page); ?>">
                <i class="fa fa-building"></i>
                <span>MANAGE DEPARTMENTS</span>
            </a>
            <a href="<?php echo base_url('simple_portal/manage_faculty'); ?>" class="nav-item <?php echo get_active_class_admin('manage_faculty', $active_page); ?>">
                <i class="fa fa-chalkboard-teacher"></i>
                <span>FACULTY MANAGEMENT</span>
            </a>
        </div>

        <!-- Account Section -->
        <div class="nav-section">
            <div class="nav-title">ACCOUNT</div>
            <a href="<?php echo base_url('simple_portal/settings'); ?>" class="nav-item <?php echo get_active_class_admin('settings', $active_page); ?>">
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
