<?php
// Debug script to check user data with departments
require_once('system/core/CodeIgniter.php');

$CI =& get_instance();
$CI->load->database();

echo "<h2>Debug: Users with Departments</h2>";

// Get all users
$CI->db->select('u.id, u.username, u.email, u.role');
$CI->db->from('users u');
$CI->db->order_by('u.role', 'ASC');
$all_users = $CI->db->get()->result_array();

echo "<pre>";
foreach ($all_users as $user) {
    echo "\n=== User: {$user['username']} (ID: {$user['id']}, Role: {$user['role']}) ===\n";
    
    if ($user['role'] === 'faculty') {
        // Get faculty info with departments
        $CI->db->select('f.id as faculty_id, f.employee_id, GROUP_CONCAT(d.name SEPARATOR ", ") as departments');
        $CI->db->from('faculty f');
        $CI->db->join('faculty_departments fd', 'f.id = fd.faculty_id', 'left');
        $CI->db->join('departments d', 'fd.department_id = d.id', 'left');
        $CI->db->where('f.user_id', $user['id']);
        $CI->db->group_by('f.id');
        $faculty_info = $CI->db->get()->row_array();
        
        echo "Faculty Info: ";
        print_r($faculty_info);
        
        if ($faculty_info) {
            echo "Departments: " . ($faculty_info['departments'] ?: 'No department') . "\n";
        }
    } elseif ($user['role'] === 'student') {
        // Get student info with department
        $CI->db->select('s.student_id, s.current_semester, s.enrollment_year, s.department_id, d.name as department');
        $CI->db->from('students s');
        $CI->db->join('departments d', 's.department_id = d.id', 'left');
        $CI->db->where('s.user_id', $user['id']);
        $student_info = $CI->db->get()->row_array();
        
        echo "Student Info: ";
        print_r($student_info);
        
        if ($student_info) {
            echo "Department: " . ($student_info['department'] ?: 'No department') . "\n";
        }
    }
}
echo "</pre>";
?>
