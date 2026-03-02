<?php
// application/helpers/navigation_helper.php
if (!function_exists('get_base_url')) {
    function get_base_url($folder = '') {
        $ci =& get_instance();
        $base = $ci->config->item('base_url');
        
        // Map folder to correct URL
        $folder_map = [
            'simple_portal' => 'simple_portal',
            'ai_buddy' => 'ai_buddy',
            'dashboard' => 'simple_portal'
        ];
        
        if (isset($folder_map[$folder])) {
            return $base . $folder_map[$folder];
        }
        
        return $base . $folder;
    }
}

if (!function_exists('nav_back_to_dashboard')) {
    function nav_back_to_dashboard() {
        // Check user role and return appropriate dashboard URL
        if (isset($_SESSION['user_role'])) {
            switch ($_SESSION['user_role']) {
                case 'admin':
                    return get_base_url('simple_portal');
                case 'faculty':
                    return get_base_url('faculty');
                case 'student':
                    return get_base_url('student');
                default:
                    return get_base_url('simple_portal');
            }
        }
        return get_base_url('simple_portal');
    }
}
?>