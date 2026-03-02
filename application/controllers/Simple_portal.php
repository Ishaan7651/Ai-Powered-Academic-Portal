<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Enhanced Simple Portal Controller
 * Includes advanced features from the spec while maintaining simplicity
 */
class Simple_portal extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->library('upload');
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->helper('security');
        $this->load->helper('file');
        $this->load->database();

        // Load models if they exist
        if (file_exists(APPPATH . 'models/User_model.php')) {
            $this->load->model('User_model');
        }
        if (file_exists(APPPATH . 'models/Resource_model.php')) {
            $this->load->model('Resource_model');
        }
        if (file_exists(APPPATH . 'models/Faculty_model.php')) {
            $this->load->model('Faculty_model');
        }
        if (file_exists(APPPATH . 'models/Student_model.php')) {
            $this->load->model('Student_model');
        }
        if (file_exists(APPPATH . 'models/Subject_model.php')) {
            $this->load->model('Subject_model');
        }

        // Load Gemini service if available
        if (file_exists(APPPATH . 'libraries/Gemini_service.php')) {
            $this->load->library('Gemini_service');
        }

        // Ensure upload directories exist
        $this->ensure_upload_directories();
    }

    /**
     * Main portal page
     */
    public function index()
    {
        // Handle logout - force complete session destruction
        if ($this->input->get('action') === 'logout') {
            $this->session->sess_destroy();
            // Also clear all session data manually
            $_SESSION = array();
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(
                    session_name(),
                    '',
                    time() - 42000,
                    $params["path"],
                    $params["domain"],
                    $params["secure"],
                    $params["httponly"]
                );
            }
            $this->session->set_flashdata('message_type', 'success');
            $this->session->set_flashdata('message', 'Logged out successfully.');
            redirect('simple_portal');
            return;
        }

        // Force logout for testing - remove this after fixing
        if ($this->input->get('force_logout') === '1') {
            $this->session->sess_destroy();
            $_SESSION = array();
            redirect('simple_portal');
            return;
        }

        // Check if logged in
        $logged_in = $this->session->userdata('logged_in') === TRUE;

        if ($logged_in) {
            $this->show_dashboard();
        } else {
            // Handle login
            if ($this->input->post('action') === 'login') {
                $this->process_login();
            } else {
                $this->show_login();
            }
        }
    }

    /**
     * Show login page
     */
    private function show_login()
    {
        $data = array(
            'logged_in' => false,
            'user_role' => null
        );

        echo $this->load->view('simple_portal/login', $data, TRUE);
    }

    /**
     * Process login
     */
    private function process_login()
    {
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        $role = $this->input->post('role');

        if ($username && $password && $role) {
            $this->db->where('username', $username);
            $this->db->where('role', $role);
            $this->db->where('is_active', 1);
            $query = $this->db->get('users');

            if ($query->num_rows() == 1) {
                $user = $query->row();

                if (password_verify($password, $user->password_hash)) {
                    $this->session->set_userdata(array(
                        'user_id' => $user->id,
                        'username' => $user->username,
                        'role' => $user->role,
                        'logged_in' => TRUE
                    ));

                    redirect('simple_portal');
                    return;
                }
            }
        }

        $this->session->set_flashdata('message_type', 'error');
        $this->session->set_flashdata('message', 'Invalid credentials.');
        redirect('simple_portal');
    }

    /**
     * Create Faculty User (Simplified)
     */
    public function create_faculty()
    {
        // Check if admin is logged in
        if ($this->session->userdata('role') !== 'admin') {
            redirect('simple_portal');
            return;
        }

        if ($this->input->post('action') === 'create_faculty') {
            $username = trim($this->input->post('username'));
            $email = trim($this->input->post('email'));
            $password = $this->input->post('password');
            $employee_id = trim($this->input->post('employee_id'));
            $department_ids = $this->input->post('department_ids'); // Array of department IDs

            if ($username && $email && $password) {
                // Check if username exists
                $this->db->where('username', $username);
                $existing = $this->db->get('users');

                if ($existing->num_rows() == 0) {
                    // Create user
                    $user_data = array(
                        'username' => $username,
                        'email' => $email,
                        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                        'role' => 'faculty',
                        'is_active' => 1
                    );

                    $this->db->insert('users', $user_data);
                    $user_id = $this->db->insert_id();

                    // Create faculty record
                    if ($this->db->table_exists('faculty')) {
                        $primary_dept_id = (!empty($department_ids) && is_array($department_ids)) ? $department_ids[0] : null;
                        
                        $faculty_data = array(
                            'user_id' => $user_id,
                            'employee_id' => !empty($employee_id) ? $employee_id : null,
                            'department_id' => $primary_dept_id // Use first selected department as primary
                        );
                        $this->db->insert('faculty', $faculty_data);
                        $faculty_id = $this->db->insert_id();
                        
                        // Insert into faculty_departments junction table
                        if (!empty($department_ids) && is_array($department_ids) && $this->db->table_exists('faculty_departments')) {
                            foreach ($department_ids as $dept_id) {
                                if (!empty($dept_id)) {
                                    $this->db->insert('faculty_departments', [
                                        'faculty_id' => $faculty_id,
                                        'department_id' => $dept_id
                                    ]);
                                }
                            }
                        }
                    }

                    $this->session->set_flashdata('message_type', 'success');
                    $this->session->set_flashdata('message', 'Faculty created successfully! Username: ' . $username);
                } else {
                    $this->session->set_flashdata('message_type', 'error');
                    $this->session->set_flashdata('message', 'Username already exists.');
                }
            } else {
                $this->session->set_flashdata('message_type', 'error');
                $this->session->set_flashdata('message', 'Username, email, and password are required.');
            }

            redirect('simple_portal/create_faculty');
            return;
        }

        // Show create faculty form
        // Get departments from database
        $departments = array();
        if ($this->db->table_exists('departments')) {
            $this->db->order_by('name', 'ASC');
            $departments = $this->db->get('departments')->result();
        }

        $data = array(
            'logged_in' => true,
            'user_role' => 'admin',
            'username' => $this->session->userdata('username'),
            'departments' => $departments
        );

        echo $this->load->view('simple_portal/create_faculty_simplified', $data, TRUE);
    }

    /**
     * Download Faculty CSV Template
     */
    public function download_faculty_template()
    {
        // Check if admin is logged in
        if ($this->session->userdata('role') !== 'admin') {
            redirect('simple_portal');
            return;
        }

        $this->load->helper('download');

        $csv_content = "username,email,password,employee_id,department\n";
        $csv_content .= "dr.smith,smith@college.edu,SecurePass24,FAC2401,Computer Science\n";
        $csv_content .= "prof.johnson,johnson@college.edu,MyPass2024,FAC2402,Information Technology\n";
        $csv_content .= "dr.williams,williams@college.edu,Faculty@2024,FAC2403,Electronics\n";
        $csv_content .= "# Note: Department can be department name (e.g., 'Computer Science') or code (e.g., 'CS')\n";

        force_download('faculty_template.csv', $csv_content);
    }

    /**
     * Bulk Upload Faculty from CSV/Excel
     */
    public function bulk_upload_faculty()
    {
        // Check if admin is logged in
        if ($this->session->userdata('role') !== 'admin') {
            redirect('simple_portal');
            return;
        }

        if (!isset($_FILES['faculty_file']) || $_FILES['faculty_file']['error'] !== UPLOAD_ERR_OK) {
            $this->session->set_flashdata('message_type', 'error');
            $this->session->set_flashdata('message', 'Please select a file to upload.');
            redirect('simple_portal/create_faculty');
            return;
        }

        $file = $_FILES['faculty_file'];
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        // Only allow CSV files
        if ($file_ext !== 'csv') {
            $this->session->set_flashdata('message_type', 'error');
            $this->session->set_flashdata('message', 'Only CSV files are supported. Please use the template.');
            redirect('simple_portal/create_faculty');
            return;
        }

        // Parse CSV file
        $csv_data = array_map('str_getcsv', file($file['tmp_name']));
        $headers = array_map('trim', array_shift($csv_data)); // Remove header row

        // Validate headers
        $required_headers = ['username', 'email', 'password'];
        $optional_headers = ['employee_id', 'department'];
        $missing_headers = array_diff($required_headers, $headers);

        if (!empty($missing_headers)) {
            $this->session->set_flashdata('message_type', 'error');
            $this->session->set_flashdata('message', 'Missing required columns: ' . implode(', ', $missing_headers));
            redirect('simple_portal/create_faculty');
            return;
        }

        // Load departments for lookup
        $departments_map = array();
        if ($this->db->table_exists('departments')) {
            $depts = $this->db->get('departments')->result();
            foreach ($depts as $dept) {
                // Map both name and code to ID for flexible matching
                $departments_map[strtolower($dept->name)] = $dept->id;
                $departments_map[strtolower($dept->code)] = $dept->id;
            }
        }

        $success_count = 0;
        $error_count = 0;
        $errors = array();

        foreach ($csv_data as $row_num => $row) {
            // Skip empty rows
            if (empty(array_filter($row))) {
                continue;
            }

            // Map row to associative array
            $faculty = array_combine($headers, $row);

            $username = trim($faculty['username']);
            $email = trim($faculty['email']);
            $password = trim($faculty['password']);
            $employee_id = isset($faculty['employee_id']) ? trim($faculty['employee_id']) : null;
            $department = isset($faculty['department']) ? trim($faculty['department']) : null;

            // Look up department_id from department name/code
            $department_id = null;
            if (!empty($department)) {
                $dept_key = strtolower($department);
                if (isset($departments_map[$dept_key])) {
                    $department_id = $departments_map[$dept_key];
                }
            }

            // Validate data
            if (empty($username) || empty($email) || empty($password)) {
                $errors[] = "Row " . ($row_num + 2) . ": Missing required fields";
                $error_count++;
                continue;
            }

            if (strlen($password) < 6) {
                $errors[] = "Row " . ($row_num + 2) . ": Password must be at least 6 characters";
                $error_count++;
                continue;
            }

            // Check if username exists
            $this->db->where('username', $username);
            $existing = $this->db->get('users');

            if ($existing->num_rows() > 0) {
                $errors[] = "Row " . ($row_num + 2) . ": Username '{$username}' already exists";
                $error_count++;
                continue;
            }

            // Create user
            $user_data = array(
                'username' => $username,
                'email' => $email,
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                'role' => 'faculty',
                'is_active' => 1
            );

            if ($this->db->insert('users', $user_data)) {
                $user_id = $this->db->insert_id();

                // Create faculty record
                if ($this->db->table_exists('faculty')) {
                    $faculty_data = array(
                        'user_id' => $user_id,
                        'employee_id' => !empty($employee_id) ? $employee_id : null,
                        'department_id' => $department_id // Use department_id instead of department string
                    );
                    $this->db->insert('faculty', $faculty_data);
                }

                $success_count++;
            } else {
                $errors[] = "Row " . ($row_num + 2) . ": Database error";
                $error_count++;
            }
        }

        // Set flash message
        if ($success_count > 0) {
            $message = "Successfully created {$success_count} faculty member(s).";
            if ($error_count > 0) {
                $message .= " {$error_count} error(s) occurred.";
            }
            $this->session->set_flashdata('message_type', 'success');
            $this->session->set_flashdata('message', $message);

            if (!empty($errors)) {
                $this->session->set_flashdata('errors', $errors);
            }
        } else {
            $this->session->set_flashdata('message_type', 'error');
            $this->session->set_flashdata('message', 'No faculty members were created. Please check your file.');

            if (!empty($errors)) {
                $this->session->set_flashdata('errors', $errors);
            }
        }

        redirect('simple_portal/create_faculty');
    }
    /**
     * Create Student User (Simplified)
     */
    public function create_student()
    {
        // Check if admin is logged in
        if ($this->session->userdata('role') !== 'admin') {
            redirect('simple_portal');
            return;
        }

        if ($this->input->post('action') === 'create_student') {
            $username = trim($this->input->post('username'));
            $email = trim($this->input->post('email'));
            $password = $this->input->post('password');
            $student_id = trim($this->input->post('student_id'));
            $current_semester = $this->input->post('current_semester');
            $enrollment_year = $this->input->post('enrollment_year') ?: date('Y');
            $department_id = $this->input->post('department_id');

            if ($username && $email && $password && $current_semester && $department_id) {
                // Check if username exists
                $this->db->where('username', $username);
                $existing = $this->db->get('users');

                if ($existing->num_rows() == 0) {
                    // Create user
                    $user_data = array(
                        'username' => $username,
                        'email' => $email,
                        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                        'role' => 'student',
                        'is_active' => 1
                    );

                    $this->db->insert('users', $user_data);
                    $user_id = $this->db->insert_id();

                    // Create student record
                    if ($this->db->table_exists('students')) {
                        $student_data = array(
                            'user_id' => $user_id,
                            'student_id' => !empty($student_id) ? $student_id : null,
                            'department_id' => $department_id,
                            'current_semester' => $current_semester,
                            'enrollment_year' => $enrollment_year
                        );
                        $this->db->insert('students', $student_data);
                    }

                    $this->session->set_flashdata('message_type', 'success');
                    $this->session->set_flashdata('message', 'Student created successfully! Username: ' . $username);
                } else {
                    $this->session->set_flashdata('message_type', 'error');
                    $this->session->set_flashdata('message', 'Username already exists.');
                }
            } else {
                $this->session->set_flashdata('message_type', 'error');
                $this->session->set_flashdata('message', 'Username, email, password, department, and semester are required.');
            }

            redirect('simple_portal/create_student');
            return;
        }

        // Get departments for dropdown
        $departments = array();
        if ($this->db->table_exists('departments')) {
            $departments = $this->db->get('departments')->result();
        }

        // Show create student form
        $data = array(
            'logged_in' => true,
            'user_role' => 'admin',
            'username' => $this->session->userdata('username'),
            'departments' => $departments
        );

        echo $this->load->view('simple_portal/create_student_simplified', $data, TRUE);
    }

    /**
     * Download Student CSV Template
     */
    public function download_student_template()
    {
        // Check if admin is logged in
        if ($this->session->userdata('role') !== 'admin') {
            redirect('simple_portal');
            return;
        }

        $this->load->helper('download');

        $csv_content = "username,email,password,current_semester,department_id,student_id,enrollment_year\n";
        $csv_content .= "# Department IDs: 1=BTech AI and ML, 2=BSc Psychology, 3=BTech Sound Engineering, 4=BBA, 5=MBA\n";
        $csv_content .= "john.doe,john@college.edu,password123,1,1,STU001,2025\n";
        $csv_content .= "jane.smith,jane@college.edu,password456,2,1,STU002,2025\n";
        $csv_content .= "mike.wilson,mike@college.edu,password789,4,2,STU003,2025\n";

        force_download('student_template.csv', $csv_content);
    }

    /**
     * Bulk Upload Students from CSV/Excel
     */
    public function bulk_upload_students()
    {
        // Check if admin is logged in
        if ($this->session->userdata('role') !== 'admin') {
            redirect('simple_portal');
            return;
        }

        if (!isset($_FILES['student_file']) || $_FILES['student_file']['error'] !== UPLOAD_ERR_OK) {
            $this->session->set_flashdata('message_type', 'error');
            $this->session->set_flashdata('message', 'Please select a file to upload.');
            redirect('simple_portal/create_student');
            return;
        }

        $file = $_FILES['student_file'];
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        // Only allow CSV files for now
        if ($file_ext !== 'csv') {
            $this->session->set_flashdata('message_type', 'error');
            $this->session->set_flashdata('message', 'Only CSV files are supported. Please use the template.');
            redirect('simple_portal/create_student');
            return;
        }

        // Parse CSV file
        $csv_data = array_map('str_getcsv', file($file['tmp_name']));
        $headers = array_map('trim', array_shift($csv_data)); // Remove header row

        // Validate headers
        $required_headers = ['username', 'email', 'password', 'current_semester', 'department_id'];
        $optional_headers = ['student_id', 'enrollment_year'];
        $missing_headers = array_diff($required_headers, $headers);

        if (!empty($missing_headers)) {
            $this->session->set_flashdata('message_type', 'error');
            $this->session->set_flashdata('message', 'Missing required columns: ' . implode(', ', $missing_headers));
            redirect('simple_portal/create_student');
            return;
        }

        $success_count = 0;
        $error_count = 0;
        $errors = array();

        foreach ($csv_data as $row_num => $row) {
            // Skip empty rows and comment lines
            if (empty(array_filter($row)) || (isset($row[0]) && strpos($row[0], '#') === 0)) {
                continue;
            }

            // Map row to associative array
            $student = array_combine($headers, $row);

            $username = trim($student['username']);
            $email = trim($student['email']);
            $password = trim($student['password']);
            $current_semester = isset($student['current_semester']) ? trim($student['current_semester']) : '';
            $department_id = isset($student['department_id']) ? trim($student['department_id']) : '';
            $student_id = isset($student['student_id']) ? trim($student['student_id']) : null;
            $enrollment_year = isset($student['enrollment_year']) ? trim($student['enrollment_year']) : date('Y');

            // Validate data
            if (empty($username) || empty($email) || empty($password) || empty($current_semester) || empty($department_id)) {
                $errors[] = "Row " . ($row_num + 2) . ": Missing required fields";
                $error_count++;
                continue;
            }

            if (strlen($password) < 6) {
                $errors[] = "Row " . ($row_num + 2) . ": Password must be at least 6 characters";
                $error_count++;
                continue;
            }

            if (!is_numeric($current_semester) || $current_semester < 1 || $current_semester > 8) {
                $errors[] = "Row " . ($row_num + 2) . ": Current semester must be between 1 and 8";
                $error_count++;
                continue;
            }

            if (!is_numeric($department_id) || $department_id < 1 || $department_id > 5) {
                $errors[] = "Row " . ($row_num + 2) . ": Department ID must be between 1 and 5";
                $error_count++;
                continue;
            }

            // Check if username exists
            $this->db->where('username', $username);
            $existing = $this->db->get('users');

            if ($existing->num_rows() > 0) {
                $errors[] = "Row " . ($row_num + 2) . ": Username '{$username}' already exists";
                $error_count++;
                continue;
            }

            // Create user
            $user_data = array(
                'username' => $username,
                'email' => $email,
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                'role' => 'student',
                'is_active' => 1
            );

            if ($this->db->insert('users', $user_data)) {
                $user_id = $this->db->insert_id();

                // Create student record
                if ($this->db->table_exists('students')) {
                    $student_data = array(
                        'user_id' => $user_id,
                        'student_id' => !empty($student_id) ? $student_id : null,
                        'department_id' => $department_id,
                        'current_semester' => $current_semester,
                        'enrollment_year' => $enrollment_year
                    );
                    $this->db->insert('students', $student_data);
                }

                $success_count++;
            } else {
                $errors[] = "Row " . ($row_num + 2) . ": Database error";
                $error_count++;
            }
        }

        // Set flash message
        if ($success_count > 0) {
            $message = "Successfully created {$success_count} student(s).";
            if ($error_count > 0) {
                $message .= " {$error_count} error(s) occurred.";
            }
            $this->session->set_flashdata('message_type', 'success');
            $this->session->set_flashdata('message', $message);

            if (!empty($errors)) {
                $this->session->set_flashdata('errors', $errors);
            }
        } else {
            $this->session->set_flashdata('message_type', 'error');
            $this->session->set_flashdata('message', 'No students were created. Please check your file.');

            if (!empty($errors)) {
                $this->session->set_flashdata('errors', $errors);
            }
        }

        redirect('simple_portal/create_student');
    }

    /**
     * Manage Subjects (Admin only)
     */
    public function manage_subjects()
    {
        // Check if admin is logged in
        if ($this->session->userdata('role') !== 'admin') {
            redirect('simple_portal');
            return;
        }

        if ($this->input->post('action') === 'add_subject') {
            $this->add_subject();
            return;
        }

        if ($this->input->post('action') === 'delete_subject') {
            $this->delete_subject();
            return;
        }

        $data = array(
            'logged_in' => true,
            'user_role' => 'admin',
            'username' => $this->session->userdata('username'),
            'subjects' => $this->get_subjects()
        );

        echo $this->load->view('simple_portal/manage_subjects', $data, TRUE);
    }

    /**
     * Add new subject
     */
    private function add_subject()
    {
        $this->form_validation->set_rules('subject_name', 'Subject Name', 'required|trim|max_length[200]');
        $this->form_validation->set_rules('subject_code', 'Subject Code', 'required|trim|max_length[20]|is_unique[subjects.subject_code]');
        $this->form_validation->set_rules('semester', 'Semester', 'numeric');
        $this->form_validation->set_rules('credits', 'Credits', 'numeric');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('message_type', 'error');
            $this->session->set_flashdata('message', validation_errors());
            redirect('simple_portal/manage_subjects');
            return;
        }

        $subject_data = array(
            'subject_name' => $this->input->post('subject_name'),
            'subject_code' => $this->input->post('subject_code'),
            'semester' => $this->input->post('semester') ?: 1,
            'credits' => $this->input->post('credits') ?: 3,
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s')
        );

        if ($this->create_subject($subject_data)) {
            $this->session->set_flashdata('message_type', 'success');
            $this->session->set_flashdata('message', 'Subject added successfully.');
        } else {
            $this->session->set_flashdata('message_type', 'error');
            $this->session->set_flashdata('message', 'Failed to add subject. Subject code might already exist.');
        }

        redirect('simple_portal/manage_subjects');
    }
    /**
     * Delete subject
     */
    private function delete_subject()
    {
        $subject_id = $this->input->post('subject_id');

        if ($subject_id && $this->db->table_exists('subjects')) {
            try {
                // Check if subject has any resources
                $has_resources = false;
                if ($this->db->table_exists('resources')) {
                    $this->db->where('subject_id', $subject_id);
                    $this->db->where('is_active', 1);
                    $has_resources = $this->db->count_all_results('resources') > 0;
                }

                // Check if subject has any faculty assignments
                $has_faculty = false;
                if ($this->db->table_exists('faculty_subjects')) {
                    $this->db->where('subject_id', $subject_id);
                    $has_faculty = $this->db->count_all_results('faculty_subjects') > 0;
                }

                if ($has_resources || $has_faculty) {
                    $this->session->set_flashdata('message_type', 'error');
                    $this->session->set_flashdata('message', 'Cannot delete subject. It has resources or faculty assignments.');
                } else {
                    // Soft delete - set is_active to 0
                    $this->db->where('id', $subject_id);
                    if ($this->db->update('subjects', array('is_active' => 0))) {
                        $this->session->set_flashdata('message_type', 'success');
                        $this->session->set_flashdata('message', 'Subject deleted successfully.');
                    } else {
                        $this->session->set_flashdata('message_type', 'error');
                        $this->session->set_flashdata('message', 'Failed to delete subject.');
                    }
                }
            } catch (Exception $e) {
                $this->session->set_flashdata('message_type', 'error');
                $this->session->set_flashdata('message', 'Database error: ' . $e->getMessage());
            }
        }

        redirect('simple_portal/manage_subjects');
    }

    /**
     * Download Subjects CSV Template
     */
    public function download_subjects_template()
    {
        // Check if admin is logged in
        if ($this->session->userdata('role') !== 'admin') {
            redirect('simple_portal');
            return;
        }

        $this->load->helper('download');

        $csv_content = "subject_code,subject_name,semester,credits,department_id\n";
        $csv_content .= "# Department IDs: 1=BTech AI and ML, 2=BSc Psychology, 3=BTech Sound Engineering, 4=BBA, 5=MBA\n";
        $csv_content .= "CS101,Introduction to Computer Science,1,4,1\n";
        $csv_content .= "MATH201,Calculus II,2,3,1\n";
        $csv_content .= "AI301,Artificial Intelligence,3,4,1\n";
        $csv_content .= "PSY101,Introduction to Psychology,1,3,2\n";
        $csv_content .= "AUDIO201,Audio Engineering Basics,2,4,3\n";

        force_download('subjects_template.csv', $csv_content);
    }

    /**
     * Bulk Upload Subjects from CSV
     */
    public function bulk_upload_subjects()
    {
        // Check if admin is logged in
        if ($this->session->userdata('role') !== 'admin') {
            redirect('simple_portal');
            return;
        }

        if (!isset($_FILES['subjects_file']) || $_FILES['subjects_file']['error'] !== UPLOAD_ERR_OK) {
            $this->session->set_flashdata('message_type', 'error');
            $this->session->set_flashdata('message', 'Please select a file to upload.');
            redirect('simple_portal/manage_subjects');
            return;
        }

        $file = $_FILES['subjects_file'];
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        // Only allow CSV files
        if ($file_ext !== 'csv') {
            $this->session->set_flashdata('message_type', 'error');
            $this->session->set_flashdata('message', 'Only CSV files are supported. Please use the template.');
            redirect('simple_portal/manage_subjects');
            return;
        }

        // Parse CSV file
        $csv_data = array_map('str_getcsv', file($file['tmp_name']));
        $headers = array_map('trim', array_shift($csv_data)); // Remove header row

        // Validate headers
        $required_headers = ['subject_code', 'subject_name', 'semester', 'department_id'];
        $optional_headers = ['credits'];
        $missing_headers = array_diff($required_headers, $headers);

        if (!empty($missing_headers)) {
            $this->session->set_flashdata('message_type', 'error');
            $this->session->set_flashdata('message', 'Missing required columns: ' . implode(', ', $missing_headers));
            redirect('simple_portal/manage_subjects');
            return;
        }

        $success_count = 0;
        $error_count = 0;
        $errors = array();

        foreach ($csv_data as $row_num => $row) {
            // Skip empty rows and comment lines
            if (empty(array_filter($row)) || (isset($row[0]) && strpos($row[0], '#') === 0)) {
                continue;
            }

            // Map row to associative array
            $subject = array_combine($headers, $row);

            $subject_code = trim($subject['subject_code']);
            $subject_name = trim($subject['subject_name']);
            $semester = trim($subject['semester']);
            $department_id = isset($subject['department_id']) ? trim($subject['department_id']) : '';
            $credits = isset($subject['credits']) ? trim($subject['credits']) : 3;

            // Validate data
            if (empty($subject_code) || empty($subject_name) || empty($semester) || empty($department_id)) {
                $errors[] = "Row " . ($row_num + 2) . ": Missing required fields";
                $error_count++;
                continue;
            }

            if (!is_numeric($semester) || $semester < 1 || $semester > 8) {
                $errors[] = "Row " . ($row_num + 2) . ": Semester must be between 1 and 8";
                $error_count++;
                continue;
            }

            if (!is_numeric($department_id) || $department_id < 1 || $department_id > 5) {
                $errors[] = "Row " . ($row_num + 2) . ": Department ID must be between 1 and 5";
                $error_count++;
                continue;
            }

            if (!is_numeric($credits) || $credits < 1 || $credits > 6) {
                $errors[] = "Row " . ($row_num + 2) . ": Credits must be between 1 and 6";
                $error_count++;
                continue;
            }

            // Check if subject_code exists
            $this->db->where('subject_code', $subject_code);
            $existing = $this->db->get('subjects');

            if ($existing->num_rows() > 0) {
                $errors[] = "Row " . ($row_num + 2) . ": Subject code '{$subject_code}' already exists";
                $error_count++;
                continue;
            }

            // Create subject
            $subject_data = array(
                'subject_code' => $subject_code,
                'subject_name' => $subject_name,
                'semester' => $semester,
                'credits' => $credits,
                'department_id' => $department_id,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            );

            if ($this->db->insert('subjects', $subject_data)) {
                $success_count++;
            } else {
                $errors[] = "Row " . ($row_num + 2) . ": Database error";
                $error_count++;
            }
        }

        // Set flash message
        if ($success_count > 0) {
            $message = "Successfully created {$success_count} subject(s).";
            if ($error_count > 0) {
                $message .= " {$error_count} error(s) occurred.";
            }
            $this->session->set_flashdata('message_type', 'success');
            $this->session->set_flashdata('message', $message);

            if (!empty($errors)) {
                $this->session->set_flashdata('errors', $errors);
            }
        } else {
            $this->session->set_flashdata('message_type', 'error');
            $this->session->set_flashdata('message', 'No subjects were created. Please check your file.');

            if (!empty($errors)) {
                $this->session->set_flashdata('errors', $errors);
            }
        }

        redirect('simple_portal/manage_subjects');
    }
    /**
     * Show dashboard based on role
     */
    private function show_dashboard()
    {
        $role = $this->session->userdata('role');
        $username = $this->session->userdata('username');

        $data = array(
            'logged_in' => true,
            'user_role' => $role,
            'username' => $username
        );

        switch ($role) {
            case 'admin':
                $data['total_users'] = $this->db->count_all('users');
                $data['total_faculty'] = $this->count_users_by_role('faculty');
                $data['total_students'] = $this->count_users_by_role('student');
                $data['total_subjects'] = $this->count_subjects();
                
                // Load departments for dropdowns
                if ($this->db->table_exists('departments')) {
                    $this->db->order_by('name', 'ASC');
                    $data['departments'] = $this->db->get('departments')->result();
                } else {
                    $data['departments'] = array();
                }
                
                // Load all users with their department information
                $data['all_users_data'] = $this->get_all_users_with_departments();
                
                echo $this->load->view('simple_portal/admin_dashboard', $data, TRUE);
                break;
            case 'faculty':
                $faculty_id = $this->session->userdata('user_id');
                $data['total_resources'] = $this->count_resources_by_faculty($faculty_id);
                echo $this->load->view('simple_portal/faculty_dashboard', $data, TRUE);
                break;
            case 'student':
                $student_id = $this->session->userdata('user_id');
                $data['current_semester'] = $this->get_student_semester($student_id);
                $data['available_semesters'] = range(1, $data['current_semester']);
                echo $this->load->view('simple_portal/student_dashboard', $data, TRUE);
                break;
            default:
                $this->session->sess_destroy();
                redirect('simple_portal');
        }
    }

    // ========================================
    // RESOURCE MANAGEMENT FEATURES
    // ========================================

    /**
     * Faculty Resource Management
     */
    public function resources()
    {
        if ($this->session->userdata('role') !== 'faculty') {
            redirect('simple_portal');
            return;
        }

        $faculty_id = $this->session->userdata('user_id');
        $data = array(
            'logged_in' => true,
            'user_role' => 'faculty',
            'username' => $this->session->userdata('username'),
            'resources' => $this->get_resources_by_faculty($faculty_id)
        );

        echo $this->load->view('simple_portal/resource_management', $data, TRUE);
    }

    /**
     * Upload Resource
     */
    public function upload_resource()
    {
        if ($this->session->userdata('role') !== 'faculty') {
            redirect('simple_portal');
            return;
        }

        if ($this->input->post('action') === 'upload_resource') {
            $this->process_resource_upload();
            return;
        }

        // Get only subjects assigned to this faculty
        $faculty_id = $this->session->userdata('user_id');
        $data = array(
            'logged_in' => true,
            'user_role' => 'faculty',
            'username' => $this->session->userdata('username'),
            'subjects' => $this->get_faculty_subjects($faculty_id) // Only show assigned subjects
        );

        echo $this->load->view('simple_portal/upload_resource', $data, TRUE);
    }

    /**
     * Process resource upload
     */
    private function process_resource_upload()
    {
        // Validate form data
        $this->form_validation->set_rules('title', 'Title', 'required|trim|max_length[255]');
        $this->form_validation->set_rules('description', 'Description', 'trim');
        $this->form_validation->set_rules('subject_id', 'Subject', 'required|numeric');
        $this->form_validation->set_rules('semester', 'Semester', 'required|numeric');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('message_type', 'error');
            $this->session->set_flashdata('message', validation_errors());
            redirect('simple_portal/upload_resource');
            return;
        }

        // Security check: Verify faculty is assigned to this subject
        $faculty_id = $this->session->userdata('user_id');
        $subject_id = $this->input->post('subject_id');
        if (!$this->is_faculty_teaching_subject($faculty_id, $subject_id)) {
            $this->session->set_flashdata('message_type', 'error');
            $this->session->set_flashdata('message', 'You can only upload resources to subjects assigned to you.');
            redirect('simple_portal/upload_resource');
            return;
        }

        // Prepare resource data
        $resource_data = array(
            'title' => $this->input->post('title'),
            'description' => $this->input->post('description'),
            'subject_id' => $this->input->post('subject_id'),
            'semester' => $this->input->post('semester'),
            'uploaded_by' => $this->session->userdata('user_id'),
            'created_at' => date('Y-m-d H:i:s')
        );

        // Handle web link
        if ($this->input->post('resource_type') === 'weblink') {
            $resource_data['file_type'] = 'weblink';
            $resource_data['file_path'] = $this->input->post('web_url');
            $resource_data['file_size'] = 0;
            $resource_data['original_filename'] = 'Web Link';
        } else {
            // Handle file upload
            $upload_result = $this->handle_file_upload();

            if ($upload_result['success']) {
                $resource_data['file_type'] = $upload_result['file_type'];
                $resource_data['file_path'] = $upload_result['file_path'];
                $resource_data['file_size'] = $upload_result['file_size'];
                $resource_data['original_filename'] = $upload_result['original_filename'];
            } else {
                $this->session->set_flashdata('message_type', 'error');
                $this->session->set_flashdata('message', $upload_result['error']);
                redirect('simple_portal/upload_resource');
                return;
            }
        }

        // Save to database
        if ($this->create_resource($resource_data)) {
            $this->session->set_flashdata('message_type', 'success');
            $this->session->set_flashdata('message', 'Resource uploaded successfully.');
        } else {
            $this->session->set_flashdata('message_type', 'error');
            $this->session->set_flashdata('message', 'Failed to save resource to database.');
        }

        redirect('simple_portal/resources');
    }

    /**
     * Handle file upload with comprehensive error checking
     */
    private function handle_file_upload()
    {
        // Check if file was uploaded
        if (empty($_FILES['resource_file']['name'])) {
            return array('success' => false, 'error' => 'Please select a file to upload.');
        }

        $file = $_FILES['resource_file'];

        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $error_messages = array(
                UPLOAD_ERR_INI_SIZE => 'File is too large (exceeds server limit)',
                UPLOAD_ERR_FORM_SIZE => 'File is too large (exceeds form limit)',
                UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
                UPLOAD_ERR_NO_FILE => 'No file was uploaded',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
                UPLOAD_ERR_EXTENSION => 'Upload stopped by extension'
            );

            $error = isset($error_messages[$file['error']]) ? $error_messages[$file['error']] : 'Unknown upload error';
            return array('success' => false, 'error' => $error);
        }

        // Validate file type
        $allowed_types = array('pdf', 'ppt', 'pptx', 'xls', 'xlsx', 'csv', 'epub', 'doc', 'docx', 'txt');
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($file_ext, $allowed_types)) {
            return array(
                'success' => false,
                'error' => 'File type not allowed. Supported formats: ' . implode(', ', $allowed_types)
            );
        }

        // Check file size (100MB limit)
        $max_size = 100 * 1024 * 1024; // 100MB in bytes
        if ($file['size'] > $max_size) {
            return array('success' => false, 'error' => 'File is too large. Maximum size is 100MB.');
        }

        // Ensure upload directory exists
        $upload_dir = './uploads/resources/';
        if (!is_dir($upload_dir)) {
            if (!mkdir($upload_dir, 0755, true)) {
                return array('success' => false, 'error' => 'Failed to create upload directory.');
            }
        }

        // Check if directory is writable
        if (!is_writable($upload_dir)) {
            return array('success' => false, 'error' => 'Upload directory is not writable.');
        }

        // Generate unique filename
        $new_filename = 'resource_' . time() . '_' . uniqid() . '.' . $file_ext;
        $destination = $upload_dir . $new_filename;

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return array(
                'success' => true,
                'file_type' => $file_ext,
                'file_path' => 'uploads/resources/' . $new_filename,
                'file_size' => round($file['size'] / 1024), // Size in KB
                'original_filename' => $file['name']
            );
        } else {
            return array('success' => false, 'error' => 'Failed to move uploaded file.');
        }
    }

    /**
     * Student Resource Access
     */
    public function student_resources($semester = null)
    {
        if ($this->session->userdata('role') !== 'student') {
            redirect('simple_portal');
            return;
        }

        $student_id = $this->session->userdata('user_id');
        $current_semester = $this->get_student_semester($student_id);
        
        // Get student's department
        $this->db->select('department_id');
        $this->db->from('students');
        $this->db->where('user_id', $student_id);
        $student = $this->db->get()->row();
        $department_id = $student ? $student->department_id : null;

        if (!$semester) {
            $semester = $current_semester;
        }

        // Check if student can access this semester
        if ($semester > $current_semester) {
            $this->session->set_flashdata('message_type', 'error');
            $this->session->set_flashdata('message', 'You cannot access future semester materials.');
            redirect('simple_portal');
            return;
        }

        $data = array(
            'logged_in' => true,
            'user_role' => 'student',
            'username' => $this->session->userdata('username'),
            'semester' => $semester,
            'current_semester' => $current_semester,
            'resources' => $this->get_resources_by_semester_and_department($semester, $department_id)
        );

        echo $this->load->view('simple_portal/student_resources', $data, TRUE);
    }

    /**
     * Download Resource
     */
    public function download_resource($resource_id)
    {
        if (!$this->session->userdata('logged_in')) {
            redirect('simple_portal');
            return;
        }

        $resource = $this->get_resource($resource_id);

        if (!$resource) {
            $this->session->set_flashdata('message_type', 'error');
            $this->session->set_flashdata('message', 'Resource not found.');
            redirect('simple_portal');
            return;
        }

        // Check permissions for students
        if ($this->session->userdata('role') === 'student') {
            $student_id = $this->session->userdata('user_id');
            $current_semester = $this->get_student_semester($student_id);

            if ($resource->semester > $current_semester) {
                $this->session->set_flashdata('message_type', 'error');
                $this->session->set_flashdata('message', 'You cannot access this resource.');
                redirect('simple_portal');
                return;
            }
        }

        // Handle web links
        if ($resource->file_type === 'weblink') {
            redirect($resource->file_path);
            return;
        }

        // Handle file downloads
        $file_path = FCPATH . $resource->file_path;

        if (!file_exists($file_path)) {
            $this->session->set_flashdata('message_type', 'error');
            $this->session->set_flashdata('message', 'File not found on server.');
            redirect('simple_portal');
            return;
        }

        // Load download helper
        $this->load->helper('download');

        // Determine filename
        $filename = !empty($resource->original_filename) ? $resource->original_filename : ($resource->title . '.' . $resource->file_type);

        // Clean filename for security
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);

        try {
            // Get file contents
            $file_data = file_get_contents($file_path);

            if ($file_data === false) {
                throw new Exception('Failed to read file');
            }

            // Force download
            force_download($filename, $file_data);

        } catch (Exception $e) {
            log_message('error', 'Download error for resource ' . $resource_id . ': ' . $e->getMessage());
            $this->session->set_flashdata('message_type', 'error');
            $this->session->set_flashdata('message', 'Failed to download file. Please try again.');
            redirect('simple_portal');
        }
    }

    /**
     * Preview Resource (view in browser)
     */
    public function preview_resource($resource_id)
    {
        if (!$this->session->userdata('logged_in')) {
            redirect('simple_portal');
            return;
        }

        $resource = $this->get_resource($resource_id);

        if (!$resource) {
            show_404();
            return;
        }

        // Check permissions for students
        if ($this->session->userdata('role') === 'student') {
            $student_id = $this->session->userdata('user_id');
            $current_semester = $this->get_student_semester($student_id);

            if ($resource->semester > $current_semester) {
                $this->session->set_flashdata('message_type', 'error');
                $this->session->set_flashdata('message', 'You cannot access this resource.');
                redirect('simple_portal');
                return;
            }
        }

        // Handle web links
        if ($resource->file_type === 'weblink') {
            redirect($resource->file_path);
            return;
        }

        // Handle file preview
        $file_path = FCPATH . $resource->file_path;

        if (!file_exists($file_path)) {
            show_404();
            return;
        }

        // Set appropriate content type based on file type
        $mime_types = array(
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'txt' => 'text/plain',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation'
        );

        $file_type = strtolower($resource->file_type);
        $content_type = isset($mime_types[$file_type]) ? $mime_types[$file_type] : 'application/octet-stream';

        // Set headers for inline display
        header('Content-Type: ' . $content_type);
        header('Content-Disposition: inline; filename="' . basename($resource->original_filename ?: $resource->title) . '"');
        header('Content-Length: ' . filesize($file_path));
        header('Cache-Control: public, must-revalidate, max-age=0');
        header('Pragma: public');
        header('Expires: 0');

        // Output file
        readfile($file_path);
        exit;
    }

    // ========================================
    // AI FEATURES (if Gemini service available)
    // ========================================

    /**
     * Generate AI Assignment
     */
    public function generate_assignment()
    {
        if ($this->session->userdata('role') !== 'faculty') {
            redirect('simple_portal');
            return;
        }

        // Load AI_service if not already loaded
        if (!isset($this->ai_service)) {
            $this->load->library('AI_service');
        }

        if ($this->input->post('action') === 'generate_assignment') {
            $this->process_ai_assignment();
            return;
        }

        $data = array(
            'logged_in' => true,
            'user_role' => 'faculty',
            'user_data' => $this->session->userdata(),
            'username' => $this->session->userdata('username'),
            'resources' => $this->Resource_model->get_all_resources(),
            'subjects' => $this->Subject_model->get_all_subjects(),
            'selected_resource_id' => null
        );

        echo $this->load->view('simple_portal/generate_assignment', $data, TRUE);
    }

    /**
     * Process assignment generation (AJAX endpoint for new UI)
     */
    public function process_assignment_generation()
    {
        header('Content-Type: application/json');

        if ($this->session->userdata('role') !== 'faculty') {
            echo json_encode(['success' => false, 'error' => 'Access denied. Assignment generation is available for faculty only.']);
            return;
        }

        // Load AI_buddy_model for assignment operations
        $this->load->model('AI_buddy_model');
        $this->load->library('AI_service');

        try {
            if (!$this->input->post()) {
                echo json_encode(['success' => false, 'error' => 'Invalid request']);
                return;
            }

            $resource_ids = $this->input->post('resource_ids');
            if (empty($resource_ids) || !is_array($resource_ids)) {
                echo json_encode(['success' => false, 'error' => 'Please select at least one resource']);
                return;
            }

            $title = $this->input->post('title') ?: 'Generated Assignment';
            $type = $this->input->post('type') ?: 'research';
            $difficulty = $this->input->post('difficulty') ?: 'medium';
            $word_count = $this->input->post('word_count') ?: 1000;
            $due_weeks = $this->input->post('due_weeks') ?: 2;

            // Get content from all selected resources
            $combined_content = '';
            $resource_titles = [];

            foreach ($resource_ids as $resource_id) {
                $resource = $this->Resource_model->get_resource($resource_id);
                if (!$resource) {
                    echo json_encode(['success' => false, 'error' => 'Resource not found: ID ' . $resource_id]);
                    return;
                }

                $file_path = FCPATH . $resource->file_path;
                if (!file_exists($file_path)) {
                    echo json_encode(['success' => false, 'error' => 'Resource file not found: ' . $resource->file_path]);
                    return;
                }

                // Extract content
                $ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
                if ($resource->file_type === 'pdf' || $ext === 'pdf') {
                    $content = $this->ai_service->extract_pdf_text($file_path);
                } elseif (in_array($ext, ['docx', 'doc'])) {
                    $content = $this->ai_service->extract_docx_text($file_path);
                } elseif ($ext === 'pptx') {
                    $content = $this->ai_service->extract_pptx_text($file_path);
                } elseif ($ext === 'ppt') {
                    $content = $this->ai_service->extract_ppt_text($file_path);
                } else {
                    $content = file_get_contents($file_path);
                }

                if (!empty(trim($content))) {
                    $combined_content .= "\n\n=== " . $resource->title . " ===\n" . $content;
                    $resource_titles[] = $resource->title;
                }
            }

            if (empty(trim($combined_content))) {
                echo json_encode(['success' => false, 'error' => 'Could not extract content from any selected files']);
                return;
            }

            // Limit content size
            if (strlen($combined_content) > 10000) {
                $combined_content = substr($combined_content, 0, 10000);
            }

            // Generate assignment
            $ai_response = $this->ai_service->generate_assignment($combined_content, $type, $difficulty, $word_count, $due_weeks, $title);

            if ($ai_response['success']) {
                // Save assignment
                $assignment_data = [
                    'user_id' => $this->session->userdata('user_id'),
                    'resource_id' => $resource_ids[0],
                    'title' => $title,
                    'type' => $type,
                    'difficulty' => $difficulty,
                    'word_count' => $word_count,
                    'assignment_data' => $ai_response['content']
                ];

                if ($this->AI_buddy_model->create_assignment($assignment_data)) {
                    $assignment_id = $this->db->insert_id();
                    echo json_encode([
                        'success' => true,
                        'assignment_id' => $assignment_id,
                        'assignment_data' => json_decode($ai_response['content'], true)
                    ]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Failed to save assignment to database']);
                }
            } else {
                echo json_encode(['success' => false, 'error' => $ai_response['error'] ?? 'AI generation failed']);
            }
        } catch (Exception $e) {
            log_message('error', 'Assignment generation error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
        }
    }

    /**
     * Process AI assignment generation
     */
    private function process_ai_assignment()
    {
        $topics = $this->input->post('topics');
        $difficulty = $this->input->post('difficulty');
        $question_count = $this->input->post('question_count');

        if ($topics && $difficulty) {
            try {
                $result = $this->gemini_service->generate_assignment(
                    explode(',', $topics),
                    array(
                        'difficulty' => $difficulty,
                        'question_count' => $question_count ?: 5
                    )
                );

                if ($result['success']) {
                    $data = array(
                        'logged_in' => true,
                        'user_role' => 'faculty',
                        'username' => $this->session->userdata('username'),
                        'generated_assignment' => $result['content']
                    );

                    echo $this->load->view('simple_portal/assignment_result', $data, TRUE);
                    return;
                } else {
                    $this->session->set_flashdata('message_type', 'error');
                    $this->session->set_flashdata('message', 'Failed to generate assignment: ' . $result['error']);
                }
            } catch (Exception $e) {
                $this->session->set_flashdata('message_type', 'error');
                $this->session->set_flashdata('message', 'AI service error: ' . $e->getMessage());
            }
        }

        redirect('simple_portal/generate_assignment');
    }

    /**
     * Generate Quiz page (Faculty only)
     */
    public function generate_quiz()
    {
        if ($this->session->userdata('role') !== 'faculty') {
            redirect('simple_portal');
            return;
        }

        $data = array(
            'logged_in' => true,
            'user_role' => 'faculty',
            'user_data' => $this->session->userdata(),
            'username' => $this->session->userdata('username'),
            'resources' => $this->Resource_model->get_all_resources(),
            'subjects' => $this->Subject_model->get_all_subjects(),
            'selected_resource_id' => null
        );

        echo $this->load->view('simple_portal/generate_quiz', $data, TRUE);
    }

    /**
     * Process quiz generation (Faculty only)
     */
    public function process_quiz_generation()
    {
        header('Content-Type: application/json');

        if ($this->session->userdata('role') !== 'faculty') {
            echo json_encode(['success' => false, 'error' => 'Access denied. Quiz generation is available for faculty only.']);
            return;
        }

        // Load AI_buddy_model for quiz operations
        $this->load->model('AI_buddy_model');
        $this->load->library('AI_service');

        try {
            if (!$this->input->post()) {
                echo json_encode(['success' => false, 'error' => 'Invalid request']);
                return;
            }

            $resource_ids = $this->input->post('resource_ids');
            if (empty($resource_ids) || !is_array($resource_ids)) {
                echo json_encode(['success' => false, 'error' => 'Please select at least one resource']);
                return;
            }

            $num_questions = $this->input->post('num_questions') ?: 10;
            $difficulty = $this->input->post('difficulty') ?: 'medium';
            $title = $this->input->post('title') ?: 'Generated Quiz';

            // Get content from all selected resources
            $combined_content = '';
            $resource_titles = [];

            foreach ($resource_ids as $resource_id) {
                $resource = $this->Resource_model->get_resource($resource_id);
                if (!$resource) {
                    echo json_encode(['success' => false, 'error' => 'Resource not found: ID ' . $resource_id]);
                    return;
                }

                $file_path = FCPATH . $resource->file_path;
                if (!file_exists($file_path)) {
                    echo json_encode(['success' => false, 'error' => 'Resource file not found: ' . $resource->file_path]);
                    return;
                }

                // Extract content
                $ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
                if ($resource->file_type === 'pdf' || $ext === 'pdf') {
                    $content = $this->ai_service->extract_pdf_text($file_path);
                } elseif (in_array($ext, ['docx', 'doc'])) {
                    $content = $this->ai_service->extract_docx_text($file_path);
                } elseif ($ext === 'pptx') {
                    $content = $this->ai_service->extract_pptx_text($file_path);
                } elseif ($ext === 'ppt') {
                    $content = $this->ai_service->extract_ppt_text($file_path);
                } else {
                    $content = file_get_contents($file_path);
                }

                if (!empty(trim($content))) {
                    $combined_content .= "\n\n=== " . $resource->title . " ===\n" . $content;
                    $resource_titles[] = $resource->title;
                }
            }

            if (empty(trim($combined_content))) {
                echo json_encode(['success' => false, 'error' => 'Could not extract content from any selected files']);
                return;
            }

            // Limit content size
            if (strlen($combined_content) > 15000) {
                $combined_content = substr($combined_content, 0, 15000) . '...';
            }

            // Generate quiz
            $ai_response = $this->ai_service->generate_quiz($combined_content, $num_questions, $difficulty, $title);

            if ($ai_response['success']) {
                // Save quiz
                $quiz_data = [
                    'user_id' => $this->session->userdata('user_id'),
                    'resource_id' => $resource_ids[0],
                    'title' => $title,
                    'difficulty' => $difficulty,
                    'num_questions' => $num_questions,
                    'quiz_data' => $ai_response['content']
                ];

                if ($this->AI_buddy_model->create_quiz($quiz_data)) {
                    $quiz_id = $this->db->insert_id();
                    echo json_encode([
                        'success' => true,
                        'quiz_id' => $quiz_id,
                        'quiz_data' => json_decode($ai_response['content'], true)
                    ]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Failed to save quiz to database']);
                }
            } else {
                echo json_encode(['success' => false, 'error' => $ai_response['error'] ?? 'AI generation failed']);
            }
        } catch (Exception $e) {
            log_message('error', 'Quiz generation error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
        }
    }

    /**
     * Generate Question Paper page (Faculty only)
     */
    public function generate_question_paper()
    {
        if ($this->session->userdata('role') !== 'faculty') {
            redirect('simple_portal');
            return;
        }

        $data = array(
            'logged_in' => true,
            'user_role' => 'faculty',
            'user_data' => $this->session->userdata(),
            'username' => $this->session->userdata('username'),
            'resources' => $this->Resource_model->get_all_resources(),
            'subjects' => $this->Subject_model->get_all_subjects(),
            'selected_resource_id' => null
        );

        echo $this->load->view('simple_portal/generate_question_paper', $data, TRUE);
    }

    /**
     * Process question paper generation (Faculty only)
     */
    public function process_question_paper_generation()
    {
        header('Content-Type: application/json');
        set_time_limit(60);

        if ($this->session->userdata('role') !== 'faculty') {
            echo json_encode(['success' => false, 'error' => 'Access denied. Question paper generation is available for faculty only.']);
            return;
        }

        // Load AI_buddy_model for question paper operations
        $this->load->model('AI_buddy_model');
        $this->load->library('AI_service');

        try {
            if (!$this->input->post()) {
                echo json_encode(['success' => false, 'error' => 'Invalid request']);
                return;
            }

            $resource_ids = $this->input->post('resource_ids');
            if (empty($resource_ids) || !is_array($resource_ids)) {
                echo json_encode(['success' => false, 'error' => 'Please select at least one resource']);
                return;
            }

            $title = $this->input->post('title') ?: 'Generated Question Paper';
            $subject_id = $this->input->post('subject_id');
            $total_marks = $this->input->post('total_marks') ?: 100;
            $duration_minutes = $this->input->post('duration_minutes') ?: 180;
            $sections = $this->input->post('sections') ?: [];

            // Get content from all selected resources
            $combined_content = '';
            $resource_titles = [];

            foreach ($resource_ids as $resource_id) {
                $resource = $this->Resource_model->get_resource($resource_id);
                if (!$resource) {
                    echo json_encode(['success' => false, 'error' => 'Resource not found: ID ' . $resource_id]);
                    return;
                }

                $file_path = FCPATH . $resource->file_path;
                if (!file_exists($file_path)) {
                    echo json_encode(['success' => false, 'error' => 'Resource file not found: ' . $resource->file_path]);
                    return;
                }

                // Extract content
                $ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
                if ($resource->file_type === 'pdf' || $ext === 'pdf') {
                    $content = $this->ai_service->extract_pdf_text($file_path);
                } elseif (in_array($ext, ['docx', 'doc'])) {
                    $content = $this->ai_service->extract_docx_text($file_path);
                } elseif ($ext === 'pptx') {
                    $content = $this->ai_service->extract_pptx_text($file_path);
                } elseif ($ext === 'ppt') {
                    $content = $this->ai_service->extract_ppt_text($file_path);
                } else {
                    $content = file_get_contents($file_path);
                }

                if (!empty(trim($content))) {
                    $combined_content .= "\n\n=== " . $resource->title . " ===\n" . $content;
                    $resource_titles[] = $resource->title;
                }
            }

            if (empty(trim($combined_content))) {
                echo json_encode(['success' => false, 'error' => 'Could not extract content from any selected files']);
                return;
            }

            // Limit content size
            if (strlen($combined_content) > 8000) {
                $combined_content = substr($combined_content, 0, 8000);
            }

            // Prepare configuration
            $config = [
                'title' => $title,
                'total_marks' => $total_marks,
                'duration_minutes' => $duration_minutes,
                'sections' => $sections
            ];

            // Generate question paper
            $ai_response = $this->ai_service->generate_question_paper($combined_content, $config);

            if ($ai_response['success']) {
                // Save question paper
                $qp_data = [
                    'user_id' => $this->session->userdata('user_id'),
                    'resource_id' => $resource_ids[0],
                    'subject_id' => $subject_id,
                    'title' => $title,
                    'total_marks' => $total_marks,
                    'duration_minutes' => $duration_minutes,
                    'paper_data' => $ai_response['content']
                ];

                if ($this->AI_buddy_model->create_question_paper($qp_data)) {
                    $qp_id = $this->db->insert_id();
                    echo json_encode([
                        'success' => true,
                        'paper_id' => $qp_id,
                        'paper_data' => json_decode($ai_response['content'], true)
                    ]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Failed to save question paper to database']);
                }
            } else {
                echo json_encode(['success' => false, 'error' => $ai_response['error'] ?? 'AI generation failed']);
            }
        } catch (Exception $e) {
            log_message('error', 'Question paper generation error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
        }
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    /**
     * Ensure upload directories exist
     */
    private function ensure_upload_directories()
    {
        $directories = array(
            './uploads/',
            './uploads/resources/',
            './uploads/assignments/',
            './uploads/question_papers/'
        );

        foreach ($directories as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);

                // Create .htaccess for security
                $htaccess_content = "Options -Indexes\n";
                $htaccess_content .= "deny from all\n";
                $htaccess_content .= "<Files ~ \"\\.(pdf|ppt|pptx|xls|xlsx|csv|epub)$\">\n";
                $htaccess_content .= "    allow from all\n";
                $htaccess_content .= "</Files>\n";

                file_put_contents($dir . '.htaccess', $htaccess_content);
            }
        }
    }

    /**
     * Count users by role
     */
    private function count_users_by_role($role)
    {
        $this->db->where('role', $role);
        $this->db->where('is_active', 1);
        return $this->db->count_all_results('users');
    }

    /**
     * Get all users with their department information
     */
    private function get_all_users_with_departments()
    {
        $users = [];
        
        // Get all users
        $this->db->select('u.id, u.username, u.email, u.role, u.is_active');
        $this->db->from('users u');
        $this->db->order_by('u.role', 'ASC');
        $this->db->order_by('u.username', 'ASC');
        $all_users = $this->db->get()->result_array();
        
        foreach ($all_users as $user) {
            if ($user['role'] === 'faculty') {
                // Get faculty info with departments
                $this->db->select('f.id as faculty_id, f.employee_id, GROUP_CONCAT(d.name SEPARATOR ", ") as departments');
                $this->db->from('faculty f');
                $this->db->join('faculty_departments fd', 'f.id = fd.faculty_id', 'left');
                $this->db->join('departments d', 'fd.department_id = d.id', 'left');
                $this->db->where('f.user_id', $user['id']);
                $this->db->group_by('f.id');
                $faculty_info = $this->db->get()->row_array();
                
                if ($faculty_info) {
                    $user['employee_id'] = $faculty_info['employee_id'];
                    $user['department'] = $faculty_info['departments'] ?: 'No department';
                } else {
                    $user['employee_id'] = null;
                    $user['department'] = 'No department';
                }
            } elseif ($user['role'] === 'student') {
                // Get student info with department
                $this->db->select('s.student_id, s.current_semester, s.enrollment_year, d.name as department');
                $this->db->from('students s');
                $this->db->join('departments d', 's.department_id = d.id', 'left');
                $this->db->where('s.user_id', $user['id']);
                $student_info = $this->db->get()->row_array();
                
                if ($student_info) {
                    $user['student_id'] = $student_info['student_id'];
                    $user['current_semester'] = $student_info['current_semester'];
                    $user['enrollment_year'] = $student_info['enrollment_year'];
                    $user['department'] = $student_info['department'] ?: 'No department';
                } else {
                    $user['student_id'] = null;
                    $user['current_semester'] = null;
                    $user['enrollment_year'] = null;
                    $user['department'] = 'No department';
                }
            }
            
            $users[] = $user;
        }
        
        return $users;
    }

    /**
     * Count resources by faculty
     */
    private function count_resources_by_faculty($faculty_id)
    {
        if ($this->db->table_exists('resources')) {
            try {
                $this->db->where('uploaded_by', $faculty_id);
                return $this->db->count_all_results('resources');
            } catch (Exception $e) {
                log_message('error', 'Database error in count_resources_by_faculty: ' . $e->getMessage());
                return 0;
            }
        }
        return 0;
    }

    /**
     * Get student current semester
     */
    private function get_student_semester($student_id)
    {
        if ($this->db->table_exists('students')) {
            $this->db->select('current_semester');
            $this->db->where('user_id', $student_id);
            $query = $this->db->get('students');

            if ($query->num_rows() > 0) {
                return $query->row()->current_semester;
            }
        }
        return 1; // Default to semester 1
    }

    /**
     * Get resources by faculty
     */
    private function get_resources_by_faculty($faculty_id)
    {
        if ($this->db->table_exists('resources')) {
            try {
                // Simple query first, then try to add subject info
                $this->db->select('*');
                $this->db->from('resources');
                $this->db->where('uploaded_by', $faculty_id);
                $this->db->order_by('created_at', 'DESC');
                $resources = $this->db->get()->result();

                // Add subject_name field to each resource
                foreach ($resources as $resource) {
                    $resource->subject_name = 'Subject ' . $resource->subject_id;
                    $resource->upload_date = $resource->created_at; // For compatibility
                }

                return $resources;
            } catch (Exception $e) {
                log_message('error', 'Database error in get_resources_by_faculty: ' . $e->getMessage());
                return array();
            }
        }
        return array();
    }

    /**
     * Get resources by semester
     */
    private function get_resources_by_semester($semester)
    {
        if ($this->db->table_exists('resources')) {
            try {
                $this->db->select('*');
                $this->db->from('resources');
                $this->db->where('semester', $semester);
                $this->db->order_by('created_at', 'DESC');
                $resources = $this->db->get()->result();

                // Add additional fields to each resource
                foreach ($resources as $resource) {
                    $resource->subject_name = 'Subject ' . $resource->subject_id;
                    $resource->uploaded_by_name = 'Faculty';
                    $resource->upload_date = $resource->created_at; // For compatibility

                    // Try to get actual username if possible
                    if ($this->db->table_exists('users')) {
                        try {
                            $this->db->select('username');
                            $this->db->where('id', $resource->uploaded_by);
                            $user = $this->db->get('users')->row();
                            if ($user) {
                                $resource->uploaded_by_name = $user->username;
                            }
                        } catch (Exception $e) {
                            // Keep default value
                        }
                    }
                }

                return $resources;
            } catch (Exception $e) {
                log_message('error', 'Database error in get_resources_by_semester: ' . $e->getMessage());
                return array();
            }
        }
        return array();
    }

    /**
     * Get resources by semester and department (for students)
     */
    private function get_resources_by_semester_and_department($semester, $department_id = null)
    {
        if ($this->db->table_exists('resources') && $this->db->table_exists('subjects')) {
            try {
                $this->db->select('r.*, s.subject_name, s.subject_code, s.department_id');
                $this->db->from('resources r');
                $this->db->join('subjects s', 'r.subject_id = s.id', 'left');
                $this->db->where('r.semester', $semester);
                $this->db->where('r.is_active', 1);
                
                // Filter by department if provided
                if ($department_id) {
                    $this->db->where('s.department_id', $department_id);
                }
                
                $this->db->order_by('r.created_at', 'DESC');
                $resources = $this->db->get()->result();

                // Add additional fields to each resource
                foreach ($resources as $resource) {
                    if (empty($resource->subject_name)) {
                        $resource->subject_name = 'Subject ' . $resource->subject_id;
                    }
                    $resource->uploaded_by_name = 'Faculty';
                    $resource->upload_date = $resource->created_at; // For compatibility

                    // Try to get actual username if possible
                    if ($this->db->table_exists('users')) {
                        try {
                            $this->db->select('username');
                            $this->db->where('id', $resource->uploaded_by);
                            $user = $this->db->get('users')->row();
                            if ($user) {
                                $resource->uploaded_by_name = $user->username;
                            }
                        } catch (Exception $e) {
                            // Keep default value
                        }
                    }
                }

                return $resources;
            } catch (Exception $e) {
                log_message('error', 'Database error in get_resources_by_semester_and_department: ' . $e->getMessage());
                return array();
            }
        }
        return array();
    }

    /**
     * Get single resource
     */
    private function get_resource($resource_id)
    {
        if ($this->db->table_exists('resources')) {
            $this->db->where('id', $resource_id);
            $query = $this->db->get('resources');

            if ($query->num_rows() > 0) {
                return $query->row();
            }
        }
        return null;
    }

    /**
     * Create resource
     */
    private function create_resource($resource_data)
    {
        if ($this->db->table_exists('resources')) {
            return $this->db->insert('resources', $resource_data);
        }
        return false;
    }

    /**
     * Get all subjects with additional info
     */
    private function get_all_subjects()
    {
        if ($this->db->table_exists('subjects')) {
            try {
                $this->db->select('*, subjects.id as id');
                $this->db->from('subjects');
                $this->db->where('is_active', 1);
                $this->db->order_by('semester', 'ASC');
                $this->db->order_by('subject_name', 'ASC');
                $subjects = $this->db->get()->result();

                // Add resource count for each subject
                foreach ($subjects as $subject) {
                    $subject->name = $subject->subject_name; // For compatibility
                    $subject->code = $subject->subject_code; // For compatibility
                    $subject->resource_count = 0;

                    // Count resources for this subject
                    if ($this->db->table_exists('resources')) {
                        $this->db->where('subject_id', $subject->id);
                        $this->db->where('is_active', 1);
                        $subject->resource_count = $this->db->count_all_results('resources');
                    }

                    // Count faculty assigned to this subject
                    $subject->faculty_count = 0;
                    if ($this->db->table_exists('faculty_subjects')) {
                        $this->db->where('subject_id', $subject->id);
                        $subject->faculty_count = $this->db->count_all_results('faculty_subjects');
                    }
                }

                return $subjects;
            } catch (Exception $e) {
                log_message('error', 'Database error in get_all_subjects: ' . $e->getMessage());
                return array();
            }
        }

        return array();
    }
    /**
     * Get all active subjects for dropdown
     */
    private function get_subjects()
    {
        if ($this->db->table_exists('subjects')) {
            try {
                $this->db->select('id, subject_code, subject_name, semester');
                $this->db->from('subjects');
                $this->db->where('is_active', 1);
                $this->db->order_by('semester', 'ASC');
                $this->db->order_by('subject_name', 'ASC');
                return $this->db->get()->result();
            } catch (Exception $e) {
                log_message('error', 'Database error in get_subjects: ' . $e->getMessage());
                return array();
            }
        }
        return array();
    }
    /**
     * Create subject in database
     */
    private function create_subject($subject_data)
    {
        if ($this->db->table_exists('subjects')) {
            try {
                // Check if subject code already exists
                $this->db->where('subject_code', $subject_data['subject_code']);
                $existing = $this->db->get('subjects');

                if ($existing->num_rows() > 0) {
                    return false; // Subject code already exists
                }

                return $this->db->insert('subjects', $subject_data);
            } catch (Exception $e) {
                log_message('error', 'Database error in create_subject: ' . $e->getMessage());
                return false;
            }
        }
        return false;
    }

    /**
     * Count subjects
     */
    private function count_subjects()
    {
        if ($this->db->table_exists('subjects')) {
            try {
                return $this->db->count_all('subjects');
            } catch (Exception $e) {
                log_message('error', 'Database error in count_subjects: ' . $e->getMessage());
                return 0;
            }
        }
        return 0;
    }

    /**
     * Manage Faculty (Admin only)
     */
    public function manage_faculty()
    {
        // Check if admin is logged in
        if ($this->session->userdata('role') !== 'admin') {
            redirect('simple_portal');
            return;
        }

        $data = array(
            'logged_in' => true,
            'user_role' => 'admin',
            'username' => $this->session->userdata('username')
        );

        // Get all faculty with their subject assignments
        $data['faculty_list'] = $this->get_all_faculty_with_subjects();

        echo $this->load->view('simple_portal/manage_faculty', $data, TRUE);
    }

    /**
     * Assign subject to faculty
     */
    public function assign_subject()
    {
        if ($this->session->userdata('role') !== 'admin') {
            redirect('simple_portal');
            return;
        }

        if ($this->input->post()) {
            $faculty_id = $this->input->post('faculty_id');
            $subject_id = $this->input->post('subject_id');

            if ($this->assign_subject_to_faculty($faculty_id, $subject_id)) {
                $this->session->set_flashdata('success', 'Subject assigned successfully!');
            } else {
                $this->session->set_flashdata('error', 'Subject is already assigned to this faculty or assignment failed.');
            }
        }

        redirect('simple_portal/manage_faculty');
    }

    /**
     * Remove subject assignment
     */
    public function remove_subject_assignment()
    {
        if ($this->session->userdata('role') !== 'admin') {
            redirect('simple_portal');
            return;
        }

        if ($this->input->post()) {
            $faculty_id = $this->input->post('faculty_id');
            $subject_id = $this->input->post('subject_id');

            if ($this->remove_faculty_subject_assignment($faculty_id, $subject_id)) {
                $this->session->set_flashdata('success', 'Subject assignment removed successfully!');
            } else {
                $this->session->set_flashdata('error', 'Failed to remove subject assignment.');
            }
        }

        redirect('simple_portal/manage_faculty');
    }

    /**
     * Add new faculty
     */
    public function add_faculty()
    {
        if ($this->session->userdata('role') !== 'admin') {
            redirect('simple_portal');
            return;
        }

        if ($this->input->post()) {
            $this->form_validation->set_rules('username', 'Username', 'required|trim|max_length[50]');
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email|max_length[100]');
            $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
            $this->form_validation->set_rules('employee_id', 'Employee ID', 'trim|max_length[20]');
            $this->form_validation->set_rules('department', 'Department', 'trim|max_length[100]');

            if ($this->form_validation->run() === TRUE) {
                // Check if username or email already exists
                $this->db->where('username', $this->input->post('username'));
                $this->db->or_where('email', $this->input->post('email'));
                $existing_user = $this->db->get('users')->row();

                if ($existing_user) {
                    $this->session->set_flashdata('error', 'Username or email already exists.');
                } else {
                    // Create user account
                    $user_data = [
                        'username' => $this->input->post('username'),
                        'email' => $this->input->post('email'),
                        'password_hash' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
                        'role' => 'faculty',
                        'is_active' => 1,
                        'created_at' => date('Y-m-d H:i:s')
                    ];

                    $this->db->insert('users', $user_data);
                    $user_id = $this->db->insert_id();

                    if ($user_id) {
                        // Create faculty record
                        $faculty_data = [
                            'user_id' => $user_id,
                            'employee_id' => $this->input->post('employee_id'),
                            'department' => $this->input->post('department'),
                            'created_at' => date('Y-m-d H:i:s')
                        ];

                        $this->db->insert('faculty', $faculty_data);
                        $this->session->set_flashdata('success', 'Faculty member added successfully!');
                    } else {
                        $this->session->set_flashdata('error', 'Failed to create user account.');
                    }
                }
            } else {
                $this->session->set_flashdata('error', 'Validation failed: ' . validation_errors());
            }
        }

        redirect('simple_portal/manage_faculty');
    }

    /**
     * Get all faculty with their subject assignments
     */
    private function get_all_faculty_with_subjects()
    {
        $faculty_list = [];

        // Get all faculty users with department info
        $this->db->select('u.id as user_id, u.username, u.email, f.id as faculty_id, f.employee_id, f.department_id, d.name as department_name, d.code as department_code');
        $this->db->from('users u');
        $this->db->join('faculty f', 'u.id = f.user_id', 'left');
        $this->db->join('departments d', 'f.department_id = d.id', 'left');
        $this->db->where('u.role', 'faculty');
        $this->db->where('u.is_active', 1);
        $faculty_query = $this->db->get();

        foreach ($faculty_query->result() as $faculty) {
            $faculty_data = (array) $faculty;

            // Get assigned subjects for this faculty
            $faculty_data['subjects'] = [];
            if ($faculty->faculty_id) {
                $this->db->select('s.id, s.subject_name, s.subject_code, s.semester');
                $this->db->from('subjects s');
                $this->db->join('faculty_subjects fs', 's.id = fs.subject_id');
                $this->db->where('fs.faculty_id', $faculty->faculty_id);
                $subjects_query = $this->db->get();
                $faculty_data['subjects'] = $subjects_query->result();
            }

            $faculty_list[] = (object) $faculty_data;
        }

        return $faculty_list;
    }

    /**
     * Get unassigned subjects for a faculty
     */
    private function get_unassigned_subjects($faculty_id)
    {
        $this->db->select('s.*');
        $this->db->from('subjects s');
        $this->db->where('s.id NOT IN (SELECT subject_id FROM faculty_subjects WHERE faculty_id = ?)', $faculty_id, FALSE);
        $this->db->where('s.is_active', 1);
        return $this->db->get()->result();
    }

    /**
     * Assign subject to faculty
     */
    private function assign_subject_to_faculty($faculty_id, $subject_id)
    {
        // Check if assignment already exists
        $this->db->where('faculty_id', $faculty_id);
        $this->db->where('subject_id', $subject_id);
        $existing = $this->db->get('faculty_subjects')->row();

        if ($existing) {
            return false; // Already assigned
        }

        // Create assignment
        $assignment_data = [
            'faculty_id' => $faculty_id,
            'subject_id' => $subject_id,
            'assigned_at' => date('Y-m-d H:i:s')
        ];

        return $this->db->insert('faculty_subjects', $assignment_data);
    }

    /**
     * Remove faculty subject assignment
     */
    private function remove_faculty_subject_assignment($faculty_id, $subject_id)
    {
        $this->db->where('faculty_id', $faculty_id);
        $this->db->where('subject_id', $subject_id);
        return $this->db->delete('faculty_subjects');
    }

    // ========================================
    // AI CHAT FUNCTIONALITY
    // ========================================

    /**
     * AI Chat Interface
     */
    public function ai_chat($session_id = null)
    {
        if (!$this->session->userdata('logged_in')) {
            redirect('simple_portal');
            return;
        }

        // For both students and faculty, redirect to semester selection if no session
        if (!$session_id) {
            redirect('simple_portal/select_semester_for_chat');
            return;
        }

        // Load AI Buddy model if available
        if (file_exists(APPPATH . 'models/AI_buddy_model.php')) {
            $this->load->model('AI_buddy_model');
        } else {
            $this->session->set_flashdata('message_type', 'error');
            $this->session->set_flashdata('message', 'AI Chat functionality is not available. Please contact administrator.');
            redirect('simple_portal');
            return;
        }

        $data = array(
            'logged_in' => true,
            'user_role' => $this->session->userdata('role'),
            'username' => $this->session->userdata('username')
        );

        if ($session_id) {
            // Load existing session
            $session = $this->AI_buddy_model->get_chat_session($session_id);

            if (!$session || $session->user_id != $this->session->userdata('user_id')) {
                $this->session->set_flashdata('message_type', 'error');
                $this->session->set_flashdata('message', 'Chat session not found.');
                redirect('simple_portal/ai_chat');
                return;
            }

            $data['session'] = $session;
            $data['messages'] = $this->AI_buddy_model->get_chat_messages($session_id);
        } else {
            $data['session'] = null;
            $data['messages'] = [];
        }

        // Get available resources
        $data['resources'] = $this->get_available_resources_for_chat();

        echo $this->load->view('simple_portal/chat', $data, TRUE);
    }

    /**
     * Create new AI chat session
     */
    public function create_ai_chat_session()
    {
        if (!$this->session->userdata('logged_in')) {
            redirect('simple_portal');
            return;
        }

        if (!$this->input->post()) {
            redirect('simple_portal/ai_chat');
            return;
        }

        // Load AI Buddy model
        if (file_exists(APPPATH . 'models/AI_buddy_model.php')) {
            $this->load->model('AI_buddy_model');
        } else {
            $this->session->set_flashdata('message_type', 'error');
            $this->session->set_flashdata('message', 'AI Chat functionality is not available.');
            redirect('simple_portal');
            return;
        }

        $resource_id = $this->input->post('resource_id');
        $session_name = $this->input->post('session_name') ?: 'New Chat Session';

        $session_data = [
            'user_id' => $this->session->userdata('user_id'),
            'resource_id' => $resource_id ?: null,
            'session_name' => $session_name
        ];

        if ($this->AI_buddy_model->create_chat_session($session_data)) {
            $session_id = $this->db->insert_id();
            $this->session->set_flashdata('message_type', 'success');
            $this->session->set_flashdata('message', 'Chat session created successfully!');
            redirect('simple_portal/ai_chat/' . $session_id);
        } else {
            $this->session->set_flashdata('message_type', 'error');
            $this->session->set_flashdata('message', 'Failed to create chat session.');
            redirect('simple_portal/ai_chat');
        }
    }

    /**
     * Select semester for chat - Step 1 (For both students and faculty)
     */
    public function select_semester_for_chat()
    {
        if (!$this->session->userdata('logged_in')) {
            redirect('simple_portal');
            return;
        }

        $user_role = $this->session->userdata('role');
        $user_id = $this->session->userdata('user_id');

        if ($user_role === 'student') {
            // Get student's enrolled subjects
            $enrolled_subjects = $this->get_student_enrolled_subjects($user_id);
        } else {
            // For faculty, get subjects directly using user_id (get_faculty_subjects handles the conversion)
            $enrolled_subjects = $this->get_faculty_subjects($user_id);
        }

        $data = array(
            'logged_in' => true,
            'user_role' => $user_role,
            'username' => $this->session->userdata('username'),
            'enrolled_subjects' => $enrolled_subjects,
            'user_data' => $this->session->userdata()
        );

        $this->load->view('ai_buddy/select_semester_chat', $data);
    }

    /**
     * Select subject for chat - Step 2 (For both students and faculty)
     */
    public function select_subject_for_chat()
    {
        if (!$this->session->userdata('logged_in')) {
            redirect('simple_portal');
            return;
        }

        $semester = $this->input->get('semester');
        if (!$semester) {
            // No semester specified, redirect to semester selection
            redirect('simple_portal/select_semester_for_chat');
            return;
        }

        $user_role = $this->session->userdata('role');
        $user_id = $this->session->userdata('user_id');

        if ($user_role === 'student') {
            // Get student's enrolled subjects for the selected semester
            $enrolled_subjects = $this->get_student_enrolled_subjects($user_id);
        } else {
            // For faculty, get subjects directly using user_id (get_faculty_subjects handles the conversion)
            $enrolled_subjects = $this->get_faculty_subjects($user_id);
        }

        // Filter by semester
        $semester_subjects = array_filter($enrolled_subjects, function ($subject) use ($semester) {
            return $subject->semester == $semester;
        });

        $data = array(
            'logged_in' => true,
            'user_role' => $user_role,
            'username' => $this->session->userdata('username'),
            'enrolled_subjects' => $semester_subjects,
            'semester' => $semester,
            'user_data' => $this->session->userdata()
        );

        $this->load->view('ai_buddy/select_subject_chat', $data);
    }

    /**
     * Start chat with selected subject (For both students and faculty)
     */
    public function start_subject_chat()
        {
            if (!$this->session->userdata('logged_in')) {
                redirect('simple_portal');
                return;
            }

            $subject_id = $this->input->get('subject_id');
            if (!$subject_id) {
                $this->session->set_flashdata('message_type', 'error');
                $this->session->set_flashdata('message', 'Please select a subject.');
                redirect('simple_portal/select_semester_for_chat');
                return;
            }

            $user_role = $this->session->userdata('role');
            $user_id = $this->session->userdata('user_id');

            // Debug: Log the received subject_id
            log_message('info', 'AI Chat: Received subject_id from URL = ' . $subject_id);

            // Load models first
            $this->load->model('Subject_model');
            $this->load->model('Resource_model');

            // Get subject details to retrieve semester
            $subject = $this->Subject_model->get_subject($subject_id);
            $semester = $subject ? $subject->semester : null;

            // Verify user has access to this subject
            if ($user_role === 'student') {
                if (!$this->is_student_enrolled_in_subject($user_id, $subject_id)) {
                    $this->session->set_flashdata('message_type', 'error');
                    $this->session->set_flashdata('message', 'You are not enrolled in this subject.');
                    if ($semester) {
                        redirect('simple_portal/select_subject_for_chat?semester=' . $semester);
                    } else {
                        redirect('simple_portal/select_semester_for_chat');
                    }
                    return;
                }
            } else {
                // For faculty, verify they teach this subject using user_id
                if (!$this->is_faculty_teaching_subject($user_id, $subject_id)) {
                    $this->session->set_flashdata('message_type', 'error');
                    $this->session->set_flashdata('message', 'You do not teach this subject.');
                    if ($semester) {
                        redirect('simple_portal/select_subject_for_chat?semester=' . $semester);
                    } else {
                        redirect('simple_portal/select_semester_for_chat');
                    }
                    return;
                }
            }

            // Debug: Log what get_subject returned
            log_message('info', 'AI Chat: get_subject returned - ID: ' . ($subject ? $subject->id : 'NULL') . ', Code: ' . ($subject ? $subject->subject_code : 'NULL'));

            // Get resources for this subject only
            $resources = $this->Resource_model->get_resources_by_subject($subject_id);

            // Debug logging
            log_message('info', 'AI Chat: Subject ID parameter = ' . $subject_id);
            log_message('info', 'AI Chat: Subject object ID = ' . ($subject ? $subject->id : 'NULL'));
            log_message('info', 'AI Chat: Subject = ' . ($subject ? $subject->subject_code : 'NULL'));
            log_message('info', 'AI Chat: Resources count = ' . count($resources));

            $data = array(
                'logged_in' => true,
                'user_role' => $user_role,
                'username' => $this->session->userdata('username'),
                'user_data' => $this->session->userdata(),
                'resources' => $resources,
                'subject' => $subject,
                'session' => null,
                'messages' => [],
                'semester' => $semester
            );

            $this->load->view('ai_buddy/chat_with_subject', $data);
        }


    /**
     * Mindmap generation page (Student only)
     */
    public function generate_mindmap()
    {
        if (!$this->session->userdata('logged_in')) {
            redirect('simple_portal');
            return;
        }

        // Only for students
        if ($this->session->userdata('role') !== 'student') {
            $this->session->set_flashdata('message_type', 'error');
            $this->session->set_flashdata('message', 'Mindmap generation is available for students only.');
            redirect('simple_portal');
            return;
        }

        $student_id = $this->session->userdata('user_id');

        // Get student's enrolled subjects
        $enrolled_subjects = $this->get_student_enrolled_subjects($student_id);

        $data = array(
            'logged_in' => true,
            'user_role' => 'student',
            'username' => $this->session->userdata('username'),
            'user_data' => $this->session->userdata(),
            'enrolled_subjects' => $enrolled_subjects
        );

        $this->load->view('simple_portal/generate_mindmap', $data);
    }

    /**
     * Process mindmap generation (AJAX)
     */
    public function process_mindmap_generation()
    {
        header('Content-Type: application/json');

        if (!$this->session->userdata('logged_in')) {
            echo json_encode(['success' => false, 'error' => 'Not logged in']);
            return;
        }

        // Only for students
        if ($this->session->userdata('role') !== 'student') {
            echo json_encode(['success' => false, 'error' => 'Access denied']);
            return;
        }

        try {
            if (!$this->input->post()) {
                echo json_encode(['success' => false, 'error' => 'Invalid request']);
                return;
            }

            $resource_ids_json = $this->input->post('resource_ids');
            $subject_id = $this->input->post('subject_id');

            // Decode JSON if it's a string
            if (is_string($resource_ids_json)) {
                $resource_ids = json_decode($resource_ids_json, true);
            } else {
                $resource_ids = $resource_ids_json;
            }

            if (empty($resource_ids) || !is_array($resource_ids)) {
                echo json_encode(['success' => false, 'error' => 'Please select at least one resource']);
                return;
            }

            // Load models
            $this->load->model('Resource_model');
            $this->load->model('Subject_model');
            $this->load->library('AI_service');

            // Get subject details
            $subject = $this->Subject_model->get_subject($subject_id);
            $subject_name = $subject ? $subject->subject_name : 'Subject';

            // Get content from all selected resources
            $combined_content = '';
            $resource_titles = [];

            foreach ($resource_ids as $resource_id) {
                $resource = $this->Resource_model->get_resource($resource_id);
                if (!$resource) {
                    continue;
                }

                $file_path = FCPATH . $resource->file_path;
                if (!file_exists($file_path)) {
                    continue;
                }

                // Extract content
                $ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
                if ($resource->file_type === 'pdf' || $ext === 'pdf') {
                    $content = $this->ai_service->extract_pdf_text($file_path);
                } elseif (in_array($ext, ['docx', 'doc'])) {
                    $content = $this->ai_service->extract_docx_text($file_path);
                } elseif ($ext === 'pptx') {
                    $content = $this->ai_service->extract_pptx_text($file_path);
                } elseif ($ext === 'ppt') {
                    $content = $this->ai_service->extract_ppt_text($file_path);
                } else {
                    $content = file_get_contents($file_path);
                }

                if (!empty(trim($content))) {
                    $combined_content .= "\n\n=== " . $resource->title . " ===\n" . $content;
                    $resource_titles[] = $resource->title;
                }
            }

            // Check if any content was extracted
            if (empty(trim($combined_content))) {
                echo json_encode(['success' => false, 'error' => 'Could not extract content from selected files']);
                return;
            }

            log_message('info', 'Mindmap generation: Combined content from ' . count($resource_titles) . ' resources, total length: ' . strlen($combined_content));

            // Generate mindmap
            $ai_response = $this->ai_service->generate_mindmap($combined_content, $subject_name);

            log_message('info', 'Mindmap AI response success: ' . ($ai_response['success'] ? 'YES' : 'NO'));
            if ($ai_response['success']) {
                log_message('info', 'Mindmap AI raw content length: ' . strlen($ai_response['content']));
                log_message('info', 'Mindmap AI raw content preview: ' . substr($ai_response['content'], 0, 500));
            } else {
                log_message('error', 'Mindmap AI error: ' . ($ai_response['error'] ?? 'Unknown error'));
            }

            if ($ai_response['success']) {
                // Clean and validate JSON
                $cleaned_json = $this->clean_ai_json_response($ai_response['content']);

                if ($cleaned_json === false) {
                    log_message('error', 'Mindmap: Failed to clean JSON response');
                    echo json_encode([
                        'success' => false,
                        'error' => 'Failed to parse AI response. Please try again.'
                    ]);
                    return;
                }

                log_message('info', 'Mindmap cleaned JSON: ' . $cleaned_json);

                // Validate JSON
                $mindmap_data = json_decode($cleaned_json, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    log_message('error', 'Mindmap JSON decode error: ' . json_last_error_msg());
                    echo json_encode([
                        'success' => false,
                        'error' => 'Invalid mindmap data: ' . json_last_error_msg()
                    ]);
                    return;
                }

                log_message('info', 'Mindmap data structure: central_topic=' . ($mindmap_data['central_topic'] ?? 'MISSING') . ', branches=' . (isset($mindmap_data['branches']) ? count($mindmap_data['branches']) : 'MISSING'));

                echo json_encode([
                    'success' => true,
                    'mindmap_data' => $mindmap_data,
                    'subject_name' => $subject_name,
                    'resources_used' => $resource_titles
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'error' => $ai_response['error'] ?? 'Failed to generate mindmap'
                ]);
            }

        } catch (Exception $e) {
            log_message('error', 'Mindmap generation error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
        }
    }

    /**
     * Flashcard generation page (Student only)
     */
    public function generate_flashcards()
    {
        if (!$this->session->userdata('logged_in')) {
            redirect('simple_portal');
            return;
        }

        // Only for students
        if ($this->session->userdata('role') !== 'student') {
            $this->session->set_flashdata('message_type', 'error');
            $this->session->set_flashdata('message', 'Flashcard generation is available for students only.');
            redirect('simple_portal');
            return;
        }

        $student_id = $this->session->userdata('user_id');

        // Get student's enrolled subjects
        $enrolled_subjects = $this->get_student_enrolled_subjects($student_id);

        $data = array(
            'logged_in' => true,
            'user_role' => 'student',
            'username' => $this->session->userdata('username'),
            'user_data' => $this->session->userdata(),
            'enrolled_subjects' => $enrolled_subjects
        );

        $this->load->view('simple_portal/generate_flashcards', $data);
    }

    /**
     * Process flashcard generation (AJAX)
     */
    public function process_flashcard_generation()
    {
        header('Content-Type: application/json');

        if (!$this->session->userdata('logged_in')) {
            echo json_encode(['success' => false, 'error' => 'Not logged in']);
            return;
        }

        // Only for students
        if ($this->session->userdata('role') !== 'student') {
            echo json_encode(['success' => false, 'error' => 'Access denied']);
            return;
        }

        try {
            if (!$this->input->post()) {
                echo json_encode(['success' => false, 'error' => 'Invalid request']);
                return;
            }

            $resource_ids_json = $this->input->post('resource_ids');
            $subject_id = $this->input->post('subject_id');
            $num_cards = $this->input->post('num_cards') ?: 15;

            // Decode JSON if it's a string
            if (is_string($resource_ids_json)) {
                $resource_ids = json_decode($resource_ids_json, true);
            } else {
                $resource_ids = $resource_ids_json;
            }

            if (empty($resource_ids) || !is_array($resource_ids)) {
                echo json_encode(['success' => false, 'error' => 'Please select at least one resource']);
                return;
            }

            // Load models
            $this->load->model('Resource_model');
            $this->load->model('Subject_model');
            $this->load->library('AI_service');

            // Get subject details
            $subject = $this->Subject_model->get_subject($subject_id);
            $subject_name = $subject ? $subject->subject_name : 'Subject';

            // Get content from all selected resources
            $combined_content = '';
            $resource_titles = [];

            foreach ($resource_ids as $resource_id) {
                $resource = $this->Resource_model->get_resource($resource_id);
                if (!$resource) {
                    continue;
                }

                $file_path = FCPATH . $resource->file_path;
                if (!file_exists($file_path)) {
                    continue;
                }

                // Extract content
                $ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
                if ($resource->file_type === 'pdf' || $ext === 'pdf') {
                    $content = $this->ai_service->extract_pdf_text($file_path);
                } elseif (in_array($ext, ['docx', 'doc'])) {
                    $content = $this->ai_service->extract_docx_text($file_path);
                } elseif ($ext === 'pptx') {
                    $content = $this->ai_service->extract_pptx_text($file_path);
                } elseif ($ext === 'ppt') {
                    $content = $this->ai_service->extract_ppt_text($file_path);
                } else {
                    $content = file_get_contents($file_path);
                }

                if (!empty(trim($content))) {
                    $combined_content .= "\n\n=== " . $resource->title . " ===\n" . $content;
                    $resource_titles[] = $resource->title;
                }
            }

            // Check if any content was extracted
            if (empty(trim($combined_content))) {
                echo json_encode(['success' => false, 'error' => 'Could not extract content from selected files']);
                return;
            }

            log_message('info', 'Flashcard generation: Combined content from ' . count($resource_titles) . ' resources, total length: ' . strlen($combined_content));

            // Generate flashcards
            $ai_response = $this->ai_service->generate_flashcards($combined_content, $subject_name, $num_cards);

            if ($ai_response['success']) {
                // Clean and validate JSON
                $cleaned_json = $this->clean_ai_json_response($ai_response['content']);

                if ($cleaned_json === false) {
                    echo json_encode([
                        'success' => false,
                        'error' => 'Failed to parse AI response. Please try again.'
                    ]);
                    return;
                }

                // Validate JSON
                $flashcard_data = json_decode($cleaned_json, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    echo json_encode([
                        'success' => false,
                        'error' => 'Invalid flashcard data: ' . json_last_error_msg()
                    ]);
                    return;
                }

                echo json_encode([
                    'success' => true,
                    'flashcard_data' => $flashcard_data,
                    'subject_name' => $subject_name,
                    'resources_used' => $resource_titles
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'error' => $ai_response['error'] ?? 'Failed to generate flashcards'
                ]);
            }

        } catch (Exception $e) {
            log_message('error', 'Flashcard generation error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
        }
    }

    /**
     * Send AI chat message (AJAX)
     */
    public function send_ai_chat_message()
    {
        header('Content-Type: application/json');

        if (!$this->session->userdata('logged_in')) {
            echo json_encode(['success' => false, 'error' => 'Not logged in']);
            return;
        }

        if (!$this->input->post()) {
            echo json_encode(['success' => false, 'error' => 'Invalid request']);
            return;
        }

        // Load required libraries and models
        if (file_exists(APPPATH . 'models/AI_buddy_model.php')) {
            $this->load->model('AI_buddy_model');
        } else {
            echo json_encode(['success' => false, 'error' => 'AI Chat functionality not available']);
            return;
        }

        if (file_exists(APPPATH . 'libraries/AI_service.php')) {
            $this->load->library('AI_service');
        } else {
            echo json_encode(['success' => false, 'error' => 'AI Service not available']);
            return;
        }

        $session_id = $this->input->post('session_id');
        $message = $this->input->post('message');
        $resource_id = $this->input->post('resource_id');

        // If no session_id, create a new session
        if (!$session_id || $session_id == 0) {
            $session_data = [
                'user_id' => $this->session->userdata('user_id'),
                'resource_id' => $resource_id ?: null,
                'session_name' => 'Chat Session - ' . date('Y-m-d H:i:s')
            ];

            if ($this->AI_buddy_model->create_chat_session($session_data)) {
                $session_id = $this->db->insert_id();
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to create chat session']);
                return;
            }
        }

        // Verify session ownership
        $session = $this->AI_buddy_model->get_chat_session($session_id);
        if (!$session || $session->user_id != $this->session->userdata('user_id')) {
            echo json_encode(['success' => false, 'error' => 'Invalid session']);
            return;
        }

        // Save user message
        $this->AI_buddy_model->add_chat_message([
            'session_id' => $session_id,
            'role' => 'user',
            'message' => $message
        ]);

        // Get document context if resource is attached
        $context = '';
        if ($session->resource_id) {
            $resource = $this->get_resource($session->resource_id);
            if ($resource && file_exists(FCPATH . $resource->file_path)) {
                log_message('error', 'AI Chat: Found resource file: ' . $resource->file_path . ', type: ' . $resource->file_type);
                $ext = strtolower(pathinfo($resource->file_path, PATHINFO_EXTENSION));
                if ($resource->file_type === 'pdf' || $ext === 'pdf') {
                    // Use AI_service PDF extraction method
                    log_message('error', 'AI Chat: Calling AI service PDF extraction');
                    $context = $this->ai_service->extract_pdf_text(FCPATH . $resource->file_path);
                    log_message('error', 'AI Chat: PDF extraction returned ' . strlen($context) . ' characters');
                } elseif (in_array($ext, ['docx', 'doc'])) {
                    // Use AI_service DOCX extraction method
                    $context = $this->ai_service->extract_docx_text(FCPATH . $resource->file_path);
                    log_message('error', 'AI Chat: DOCX extraction returned ' . strlen($context) . ' characters');
                } elseif ($ext === 'pptx') {
                    // Use AI_service PPTX extraction method (XML-based format)
                    $context = $this->ai_service->extract_pptx_text(FCPATH . $resource->file_path);
                    log_message('error', 'AI Chat: PPTX extraction returned ' . strlen($context) . ' characters');
                } elseif ($ext === 'ppt') {
                    // Use AI_service PPT extraction method (binary OLE format)
                    $context = $this->ai_service->extract_ppt_text(FCPATH . $resource->file_path);
                    log_message('error', 'AI Chat: PPT extraction returned ' . strlen($context) . ' characters');
                } else {
                    $context = file_get_contents(FCPATH . $resource->file_path);
                    log_message('error', 'AI Chat: Read non-PDF/DOCX/PPTX file, ' . strlen($context) . ' characters');
                }
            } else {
                log_message('error', 'AI Chat: Resource file not found or no resource attached');
            }
        }

        // Get recent conversation history
        $recent_messages = $this->AI_buddy_model->get_recent_messages($session_id, 10);

        // Log message count for debugging
        log_message('info', 'AI Chat: Retrieved ' . count($recent_messages) . ' recent messages for session ' . $session_id);

        // Build system prompt with context
        $system_prompt = "You are an AI teaching assistant helping with educational content.";
        if ($context && strlen(trim($context)) > 20) {
            $system_prompt .= "\n\nDocument Context:\n" . substr($context, 0, 3000);
            log_message('info', 'AI Chat: Using document context, length: ' . strlen($context));
        } else {
            $system_prompt .= "\n\nNote: No specific document context is available. Please provide general educational assistance.";
            log_message('info', 'AI Chat: No document context available');
        }

        // Get AI response
        log_message('info', 'AI Chat: Calling AI service with ' . count($recent_messages) . ' messages');
        $ai_response = $this->ai_service->chat($recent_messages, $system_prompt);

        if ($ai_response['success']) {
            // Save AI response
            $this->AI_buddy_model->add_chat_message([
                'session_id' => $session_id,
                'role' => 'assistant',
                'message' => $ai_response['content']
            ]);

            // Log usage if table exists
            if ($this->db->table_exists('ai_usage_logs')) {
                $this->AI_buddy_model->log_usage([
                    'user_id' => $this->session->userdata('user_id'),
                    'feature_type' => 'chat',
                    'resource_id' => $session->resource_id,
                    'tokens_used' => $ai_response['tokens'] ?? 0
                ]);
            }

            // Update session timestamp
            $this->AI_buddy_model->update_chat_session($session_id, ['updated_at' => date('Y-m-d H:i:s')]);

            echo json_encode([
                'success' => true,
                'message' => $ai_response['content'],
                'session_id' => $session_id
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => $ai_response['error'] ?? 'Failed to get AI response'
            ]);
        }
    }

    /**
     * Get available resources for chat based on user role
     */
    private function get_available_resources_for_chat()
    {
        $resources = [];

        if (!$this->db->table_exists('resources')) {
            return $resources;
        }

        $user_role = $this->session->userdata('role');

        if ($user_role === 'faculty') {
            // Faculty can see all resources
            $this->db->select('r.*, s.subject_name, s.subject_code');
            $this->db->from('resources r');
            $this->db->join('subjects s', 'r.subject_id = s.id', 'left');
            $this->db->where('r.is_active', 1);
            $this->db->order_by('r.created_at', 'DESC');
            $resources = $this->db->get()->result();
        } else if ($user_role === 'student') {
            // Students can see resources for their semester and below
            $student_semester = $this->get_student_current_semester();

            $this->db->select('r.*, s.subject_name, s.subject_code');
            $this->db->from('resources r');
            $this->db->join('subjects s', 'r.subject_id = s.id', 'left');
            $this->db->where('r.is_active', 1);
            $this->db->where('s.semester <=', $student_semester);
            $this->db->order_by('r.created_at', 'DESC');
            $resources = $this->db->get()->result();
        }

        return $resources;
    }

    /**
     * Get student's current semester
     */
    private function get_student_current_semester()
    {
        if (!$this->db->table_exists('students')) {
            return 1;
        }

        $user_id = $this->session->userdata('user_id');
        $this->db->select('current_semester');
        $this->db->from('students');
        $this->db->where('user_id', $user_id);
        $result = $this->db->get()->row();

        return $result ? $result->current_semester : 1;
    }

    /**
     * Simple PDF text extraction
     */
    private function extract_pdf_text($file_path)
    {
        // Simple text extraction - you can enhance this with proper PDF libraries
        $text = '';

        try {
            // Try using shell command if available
            if (function_exists('shell_exec')) {
                $output = shell_exec("pdftotext '$file_path' -");
                if ($output) {
                    $text = $output;
                }
            }

            // Fallback: basic text extraction
            if (empty($text)) {
                $content = file_get_contents($file_path);
                if (strpos($content, 'stream') !== false) {
                    // Very basic PDF text extraction
                    preg_match_all('/\(([^)]+)\)/', $content, $matches);
                    $text = implode(' ', $matches[1]);
                }
            }
        } catch (Exception $e) {
            log_message('error', 'PDF extraction failed: ' . $e->getMessage());
        }

        return $text;
    }

    /**
     * AI Features Dashboard
     */
    public function ai_features()
    {
        if ($this->session->userdata('role') !== 'faculty') {
            redirect('simple_portal');
            return;
        }

        $user_id = $this->session->userdata('user_id');

        // Get usage statistics
        $stats = array();

        if ($this->db->table_exists('ai_question_papers')) {
            $this->db->where('user_id', $user_id);
            $stats['question_papers'] = $this->db->count_all_results('ai_question_papers');
        } else {
            $stats['question_papers'] = 0;
        }

        if ($this->db->table_exists('ai_assignments')) {
            $this->db->where('user_id', $user_id);
            $stats['assignments'] = $this->db->count_all_results('ai_assignments');
        } else {
            $stats['assignments'] = 0;
        }

        if ($this->db->table_exists('ai_quizzes')) {
            $this->db->where('user_id', $user_id);
            $stats['quizzes'] = $this->db->count_all_results('ai_quizzes');
        } else {
            $stats['quizzes'] = 0;
        }

        if ($this->db->table_exists('ai_chat_sessions')) {
            $this->db->where('user_id', $user_id);
            $stats['chat_sessions'] = $this->db->count_all_results('ai_chat_sessions');
        } else {
            $stats['chat_sessions'] = 0;
        }

        $data = array(
            'logged_in' => true,
            'user_role' => 'faculty',
            'username' => $this->session->userdata('username'),
            'stats' => $stats
        );

        echo $this->load->view('simple_portal/ai_features', $data, TRUE);
    }

    // ========================================
    // STUDENT PUBLISHED CONTENT VIEWS
    // ========================================

    /**
     * Student Question Papers View
     */
    public function student_question_papers()
    {
        if ($this->session->userdata('role') !== 'student') {
            redirect('simple_portal');
            return;
        }

        $student_id = $this->session->userdata('user_id');

        // Get student's enrolled subjects
        $enrolled_subjects = $this->get_student_enrolled_subjects($student_id);

        // Get published question papers for enrolled subjects
        $question_papers = $this->get_published_question_papers_for_student($student_id);

        $data = array(
            'logged_in' => true,
            'user_role' => 'student',
            'username' => $this->session->userdata('username'),
            'enrolled_subjects' => $enrolled_subjects,
            'question_papers' => $question_papers
        );

        echo $this->load->view('simple_portal/student_question_papers', $data, TRUE);
    }

    /**
     * Student Quizzes View
     */
    public function student_quizzes()
    {
        if ($this->session->userdata('role') !== 'student') {
            redirect('simple_portal');
            return;
        }

        $student_id = $this->session->userdata('user_id');

        // Get student's enrolled subjects
        $enrolled_subjects = $this->get_student_enrolled_subjects($student_id);

        // Get published quizzes for enrolled subjects
        $quizzes = $this->get_published_quizzes_for_student($student_id);

        $data = array(
            'logged_in' => true,
            'user_role' => 'student',
            'username' => $this->session->userdata('username'),
            'enrolled_subjects' => $enrolled_subjects,
            'quizzes' => $quizzes
        );

        echo $this->load->view('simple_portal/student_quizzes', $data, TRUE);
    }

    /**
     * Student Assignments View
     */
    public function student_assignments()
    {
        if ($this->session->userdata('role') !== 'student') {
            redirect('simple_portal');
            return;
        }

        $student_id = $this->session->userdata('user_id');

        // Get student's enrolled subjects
        $enrolled_subjects = $this->get_student_enrolled_subjects($student_id);

        // Get published assignments for enrolled subjects
        $assignments = $this->get_published_assignments_for_student($student_id);

        $data = array(
            'logged_in' => true,
            'user_role' => 'student',
            'username' => $this->session->userdata('username'),
            'enrolled_subjects' => $enrolled_subjects,
            'assignments' => $assignments
        );

        echo $this->load->view('simple_portal/student_assignments', $data, TRUE);
    }

    /**
     * View Assignment Details
     */
    public function view_assignment($assignment_id)
    {
        if ($this->session->userdata('role') !== 'student') {
            redirect('simple_portal');
            return;
        }

        $student_id = $this->session->userdata('user_id');

        // Get assignment with subject info
        $assignment = $this->get_assignment_details($assignment_id);

        if (!$assignment) {
            $this->session->set_flashdata('message_type', 'error');
            $this->session->set_flashdata('message', 'Assignment not found.');
            redirect('simple_portal/student_assignments');
            return;
        }

        // Verify student is enrolled in the subject
        if (!$this->is_student_enrolled_in_subject($student_id, $assignment->subject_id)) {
            $this->session->set_flashdata('message_type', 'error');
            $this->session->set_flashdata('message', 'You are not enrolled in this subject.');
            redirect('simple_portal/student_assignments');
            return;
        }

        $data = array(
            'logged_in' => true,
            'user_role' => 'student',
            'username' => $this->session->userdata('username'),
            'assignment' => $assignment
        );

        echo $this->load->view('simple_portal/student_view_assignment', $data, TRUE);
    }

    /**
     * View Question Paper Details
     */
    public function view_question_paper($paper_id)
    {
        if ($this->session->userdata('role') !== 'student') {
            redirect('simple_portal');
            return;
        }

        $student_id = $this->session->userdata('user_id');

        // Get question paper with subject info
        $paper = $this->get_question_paper_details($paper_id);

        if (!$paper) {
            $this->session->set_flashdata('message_type', 'error');
            $this->session->set_flashdata('message', 'Question paper not found.');
            redirect('simple_portal/student_question_papers');
            return;
        }

        // Verify student is enrolled in the subject
        if (!$this->is_student_enrolled_in_subject($student_id, $paper->subject_id)) {
            $this->session->set_flashdata('message_type', 'error');
            $this->session->set_flashdata('message', 'You are not enrolled in this subject.');
            redirect('simple_portal/student_question_papers');
            return;
        }

        $data = array(
            'logged_in' => true,
            'user_role' => 'student',
            'username' => $this->session->userdata('username'),
            'paper' => $paper
        );

        echo $this->load->view('simple_portal/view_question_paper', $data, TRUE);
    }

    /**
     * Download Question Paper as PDF
     */
    public function download_question_paper($paper_id)
    {
        if ($this->session->userdata('role') !== 'student') {
            redirect('simple_portal');
            return;
        }

        $student_id = $this->session->userdata('user_id');

        // Get question paper
        $paper = $this->get_question_paper_details($paper_id);

        if (!$paper) {
            $this->session->set_flashdata('message_type', 'error');
            $this->session->set_flashdata('message', 'Question paper not found.');
            redirect('simple_portal/student_question_papers');
            return;
        }

        // Verify student is enrolled in the subject
        if (!$this->is_student_enrolled_in_subject($student_id, $paper->subject_id)) {
            $this->session->set_flashdata('message_type', 'error');
            $this->session->set_flashdata('message', 'You are not enrolled in this subject.');
            redirect('simple_portal/student_question_papers');
            return;
        }

        // Generate PDF (simplified version - you can enhance this)
        $this->load->helper('download');

        $content = "Question Paper: " . $paper->title . "\n\n";
        $content .= "Subject: " . $paper->subject_name . " (" . $paper->subject_code . ")\n";
        $content .= "Total Marks: " . $paper->total_marks . "\n";
        $content .= "Duration: " . $paper->duration_minutes . " minutes\n\n";
        $content .= "Content:\n" . $paper->formatted_content;

        $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $paper->title) . '.txt';
        force_download($filename, $content);
    }

    /**
     * Student Settings Page
     */
    public function student_settings()
    {
        if ($this->session->userdata('role') !== 'student') {
            redirect('simple_portal');
            return;
        }

        $user_id = $this->session->userdata('user_id');

        // Get user details
        $this->db->where('id', $user_id);
        $user = $this->db->get('users')->row_array();

        // Get student details
        $this->db->where('user_id', $user_id);
        $student_data = $this->db->get('students')->row_array();

        $data = array(
            'logged_in' => true,
            'user_role' => 'student',
            'username' => $this->session->userdata('username'),
            'user' => $user,
            'student_data' => $student_data
        );

        echo $this->load->view('simple_portal/student_settings', $data, TRUE);
    }

    /**
    /**
     * Student Schedule Page
     */
    public function student_schedule()
    {
        if ($this->session->userdata('role') !== 'student') {
            redirect('simple_portal');
            return;
        }

        $user_id = $this->session->userdata('user_id');

        // Fetch upcoming events (Quizzes and Assignments)
        // Since we don't have deadline columns, we'll fetch the most recent ones as "Upcoming/New"

        $events = [];

        // Fetch recent active quizzes
        $quizzes = $this->get_published_quizzes_for_student($user_id);
        if ($quizzes) {
            foreach (array_slice($quizzes, 0, 3) as $quiz) {
                // Simulate a due date for demo purposes (e.g., 7 days after published)
                $published_time = strtotime($quiz->published_at);
                $due_date = date('Y-m-d', strtotime('+7 days', $published_time));

                // Only show if "due date" is in future
                if (strtotime($due_date) >= time()) {
                    $events[] = [
                        'title' => $quiz->title,
                        'subject' => $quiz->subject_code . ' - ' . $quiz->subject_name,
                        'type' => 'Quiz',
                        'date' => $due_date,
                        'sort_date' => $due_date
                    ];
                }
            }
        }

        // Fetch recent assignments
        $assignments = $this->get_published_assignments_for_student($user_id);
        if ($assignments) {
            foreach (array_slice($assignments, 0, 3) as $assignment) {
                $published_time = strtotime($assignment->published_at);
                $due_date = date('Y-m-d', strtotime('+10 days', $published_time));

                if (strtotime($due_date) >= time()) {
                    $events[] = [
                        'title' => $assignment->title,
                        'subject' => $assignment->subject_code . ' - ' . $assignment->subject_name,
                        'type' => 'Assignment',
                        'date' => $due_date,
                        'sort_date' => $due_date
                    ];
                }
            }
        }

        // Sort events by date
        usort($events, function ($a, $b) {
            return strtotime($a['sort_date']) - strtotime($b['sort_date']);
        });

        $data = array(
            'logged_in' => true,
            'user_role' => 'student',
            'username' => $this->session->userdata('username'),
            'events' => $events
        );

        echo $this->load->view('simple_portal/student_schedule', $data, TRUE);
    }

    // ========================================
    // HELPER METHODS FOR STUDENT VIEWS
    // ========================================

    /**
     * Get student's enrolled subjects
     */
    private function get_student_enrolled_subjects($student_id)
    {
        if (!$this->db->table_exists('students') || !$this->db->table_exists('subjects')) {
            log_message('error', 'HawkAI: Required tables do not exist');
            return array();
        }

        // Get student's current semester and department
        $this->db->select('current_semester, department_id');
        $this->db->from('students');
        $this->db->where('user_id', $student_id);
        $student = $this->db->get()->row();

        if (!$student) {
            log_message('error', 'HawkAI: Student record not found for user_id=' . $student_id);
            return array();
        }

        $current_semester = $student->current_semester;
        $department_id = $student->department_id;

        // Get all active subjects up to and including the student's current semester
        // AND matching their department
        $this->db->select('id, subject_code, subject_name, semester, department_id');
        $this->db->from('subjects');
        $this->db->where('semester <=', $current_semester);
        $this->db->where('is_active', 1);
        
        // Filter by department if student has one
        if ($department_id) {
            $this->db->where('department_id', $department_id);
        }
        
        $this->db->order_by('semester', 'ASC');
        $this->db->order_by('subject_name', 'ASC');

        $result = $this->db->get()->result();
        
        log_message('info', 'HawkAI: get_student_enrolled_subjects called with student_id=' . $student_id . ', current_semester=' . $current_semester . ', department_id=' . $department_id . ', found ' . count($result) . ' subjects');
        log_message('info', 'HawkAI: SQL Query: ' . $this->db->last_query());
        
        return $result;
    }

    /**
     * Get faculty's teaching subjects
     */
    private function get_faculty_subjects($user_id)
    {
        if (!$this->db->table_exists('faculty_subjects') || !$this->db->table_exists('subjects') || !$this->db->table_exists('faculty')) {
            return array();
        }

        // First, get the faculty.id from user_id (since faculty_subjects uses faculty.id, not users.id)
        $faculty = $this->db->get_where('faculty', array('user_id' => $user_id))->row();
        if (!$faculty) {
            return array();
        }
        $faculty_id = $faculty->id;

        $this->db->select('s.id, s.subject_code, s.subject_name, s.semester');
        $this->db->from('faculty_subjects fs');
        $this->db->join('subjects s', 'fs.subject_id = s.id');
        $this->db->where('fs.faculty_id', $faculty_id);
        $this->db->where('s.is_active', 1);
        $this->db->order_by('s.semester', 'ASC');
        $this->db->order_by('s.subject_name', 'ASC');

        return $this->db->get()->result();
    }

    /**
     * Check if faculty teaches a specific subject
     */
    private function is_faculty_teaching_subject($user_id, $subject_id)
    {
        if (!$this->db->table_exists('faculty_subjects') || !$this->db->table_exists('faculty')) {
            return false;
        }

        // First, get the faculty.id from user_id (since faculty_subjects uses faculty.id, not users.id)
        $faculty = $this->db->get_where('faculty', array('user_id' => $user_id))->row();
        if (!$faculty) {
            return false;
        }
        $faculty_id = $faculty->id;

        $this->db->where('faculty_id', $faculty_id);
        $this->db->where('subject_id', $subject_id);
        $result = $this->db->get('faculty_subjects')->row();

        return $result !== null;
    }

    /**
     * Get published question papers for student's enrolled subjects
     */
    private function get_published_question_papers_for_student($student_id)
    {
        if (!$this->db->table_exists('ai_question_papers') || !$this->db->table_exists('student_enrollments')) {
            return array();
        }

        $this->db->select('qp.*, s.subject_code, s.subject_name');
        $this->db->from('ai_question_papers qp');
        $this->db->join('subjects s', 'qp.subject_id = s.id');
        $this->db->join('student_enrollments se', 'qp.subject_id = se.subject_id');
        $this->db->where('se.student_id', $student_id);
        $this->db->where('qp.is_published', 1);
        $this->db->order_by('qp.published_at', 'DESC');

        return $this->db->get()->result();
    }

    /**
     * Get published quizzes for student's enrolled subjects
     */
    private function get_published_quizzes_for_student($student_id)
    {
        if (!$this->db->table_exists('ai_quizzes') || !$this->db->table_exists('student_enrollments')) {
            return array();
        }

        $this->db->select('q.*, s.subject_code, s.subject_name');
        $this->db->from('ai_quizzes q');
        $this->db->join('subjects s', 'q.subject_id = s.id');
        $this->db->join('student_enrollments se', 'q.subject_id = se.subject_id');
        $this->db->where('se.student_id', $student_id);
        $this->db->where('q.is_published', 1);
        $this->db->order_by('q.published_at', 'DESC');

        return $this->db->get()->result();
    }

    /**
     * Get published assignments for student's enrolled subjects
     */
    private function get_published_assignments_for_student($student_id)
    {
        if (!$this->db->table_exists('ai_assignments') || !$this->db->table_exists('student_enrollments')) {
            return array();
        }

        $this->db->select('a.*, s.subject_code, s.subject_name');
        $this->db->from('ai_assignments a');
        $this->db->join('subjects s', 'a.subject_id = s.id');
        $this->db->join('student_enrollments se', 'a.subject_id = se.subject_id');
        $this->db->where('se.student_id', $student_id);
        $this->db->where('a.is_published', 1);
        $this->db->order_by('a.published_at', 'DESC');

        return $this->db->get()->result();
    }

    /**
     * Get assignment details with subject info
     */
    private function get_assignment_details($assignment_id)
    {
        if (!$this->db->table_exists('ai_assignments')) {
            return null;
        }

        $this->db->select('a.*, s.subject_code, s.subject_name');
        $this->db->from('ai_assignments a');
        $this->db->join('subjects s', 'a.subject_id = s.id', 'left');
        $this->db->where('a.id', $assignment_id);
        $this->db->where('a.is_published', 1);

        $query = $this->db->get();
        $assignment = $query->num_rows() > 0 ? $query->row() : null;

        // Parse assignment_data JSON
        if ($assignment && !empty($assignment->assignment_data)) {
            $assignment->parsed_data = json_decode($assignment->assignment_data, true);
        } else {
            $assignment->parsed_data = null;
        }

        return $assignment;
    }

    /**
     * Get question paper details with subject info
     */
    private function get_question_paper_details($paper_id)
    {
        if (!$this->db->table_exists('ai_question_papers')) {
            return null;
        }

        $this->db->select('qp.*, s.subject_code, s.subject_name');
        $this->db->from('ai_question_papers qp');
        $this->db->join('subjects s', 'qp.subject_id = s.id');
        $this->db->where('qp.id', $paper_id);
        $this->db->where('qp.is_published', 1);

        $query = $this->db->get();
        $paper = $query->num_rows() > 0 ? $query->row() : null;

        // Parse paper_data JSON and format as readable content
        if ($paper && !empty($paper->paper_data)) {
            $paper->formatted_content = $this->format_paper_data($paper->paper_data);
        } else {
            $paper->formatted_content = 'No content available.';
        }

        return $paper;
    }

    /**
     * Format paper_data JSON into readable text
     */
    private function format_paper_data($paper_data_json)
    {
        $paper_data = json_decode($paper_data_json, true);

        if (!$paper_data || !isset($paper_data['sections'])) {
            return 'No content available.';
        }

        $formatted = '';
        $question_num = 1;

        foreach ($paper_data['sections'] as $section_index => $section) {
            if (isset($section['section_title'])) {
                $formatted .= "Section " . ($section_index + 1) . ": " . $section['section_title'] . "\n";
                $formatted .= str_repeat("-", 80) . "\n\n";
            }

            if (isset($section['questions'])) {
                foreach ($section['questions'] as $question) {
                    $formatted .= "Q{$question_num}. ";

                    if (isset($question['question_text'])) {
                        $formatted .= $question['question_text'];
                    }

                    if (isset($question['marks'])) {
                        $formatted .= " [{$question['marks']} marks]";
                    }

                    $formatted .= "\n";

                    // Add options for MCQ
                    if (isset($question['type']) && $question['type'] === 'mcq' && isset($question['options'])) {
                        foreach ($question['options'] as $option) {
                            $formatted .= "   " . $option . "\n";
                        }
                    }

                    $formatted .= "\n";
                    $question_num++;
                }
            }

            $formatted .= "\n";
        }

        return $formatted;
    }

    /**
     * Check if student is enrolled in a subject
     */
    private function is_student_enrolled_in_subject($student_id, $subject_id)
    {
        // Check if student's current semester allows access to this subject
        if (!$this->db->table_exists('students') || !$this->db->table_exists('subjects')) {
            return false;
        }

        // Get student's current semester
        $this->db->select('current_semester');
        $this->db->from('students');
        $this->db->where('user_id', $student_id);
        $student = $this->db->get()->row();

        if (!$student) {
            return false;
        }

        // Get subject's semester
        $this->db->select('semester');
        $this->db->from('subjects');
        $this->db->where('id', $subject_id);
        $this->db->where('is_active', 1);
        $subject = $this->db->get()->row();

        if (!$subject) {
            return false;
        }

        // Student can access subjects up to and including their current semester
        return $subject->semester <= $student->current_semester;
    }

    // ========================================
    // QUIZ TAKING FUNCTIONALITY
    // ========================================

    /**
     * Take Quiz - Interactive quiz interface
     */
    public function take_quiz($quiz_id)
    {
        if ($this->session->userdata('role') !== 'student') {
            redirect('simple_portal');
            return;
        }

        $student_id = $this->session->userdata('user_id');

        // Get quiz details
        $quiz = $this->get_quiz_details($quiz_id);

        if (!$quiz) {
            $this->session->set_flashdata('message_type', 'error');
            $this->session->set_flashdata('message', 'Quiz not found.');
            redirect('simple_portal/student_quizzes');
            return;
        }

        // Verify student is enrolled in the subject
        if (!$this->is_student_enrolled_in_subject($student_id, $quiz->subject_id)) {
            $this->session->set_flashdata('message_type', 'error');
            $this->session->set_flashdata('message', 'You are not enrolled in this subject.');
            redirect('simple_portal/student_quizzes');
            return;
        }

        // Check if quiz has quiz_data
        if (empty($quiz->quiz_data)) {
            $this->session->set_flashdata('message_type', 'error');
            $this->session->set_flashdata('message', 'This quiz has no content. Please contact your instructor.');
            redirect('simple_portal/student_quizzes');
            return;
        }

        // Parse quiz data to extract questions
        $questions = $this->parse_quiz_content($quiz->quiz_data);

        // Check if questions were parsed successfully
        if (empty($questions)) {
            $this->session->set_flashdata('message_type', 'error');
            $this->session->set_flashdata('message', 'Unable to load quiz questions. Please contact your instructor.');
            redirect('simple_portal/student_quizzes');
            return;
        }

        $data = array(
            'logged_in' => true,
            'user_role' => 'student',
            'username' => $this->session->userdata('username'),
            'quiz' => $quiz,
            'questions' => $questions
        );

        echo $this->load->view('simple_portal/take_quiz', $data, TRUE);
    }

    /**
     * Submit Quiz - Process quiz answers and calculate score
     */
    public function submit_quiz()
    {
        header('Content-Type: application/json');

        if ($this->session->userdata('role') !== 'student') {
            echo json_encode(['success' => false, 'error' => 'Not authorized']);
            return;
        }

        if (!$this->input->post()) {
            echo json_encode(['success' => false, 'error' => 'Invalid request']);
            return;
        }

        $quiz_id = $this->input->post('quiz_id');
        $answers = $this->input->post('answers');
        $time_taken = $this->input->post('time_taken');

        $student_id = $this->session->userdata('user_id');

        // Get quiz details
        $quiz = $this->get_quiz_details($quiz_id);

        if (!$quiz) {
            echo json_encode(['success' => false, 'error' => 'Quiz not found']);
            return;
        }

        // Check if quiz has quiz_data
        if (empty($quiz->quiz_data)) {
            echo json_encode(['success' => false, 'error' => 'Quiz has no content']);
            return;
        }

        // Parse quiz data to get correct answers
        $questions = $this->parse_quiz_content($quiz->quiz_data);

        // Check if questions were parsed
        if (empty($questions)) {
            echo json_encode(['success' => false, 'error' => 'Unable to parse quiz questions']);
            return;
        }

        // Calculate score
        $total_questions = count($questions);
        $correct_answers = 0;
        $results = array();

        foreach ($questions as $index => $question) {
            $question_num = $index + 1;
            $user_answer = isset($answers[$question_num]) ? $answers[$question_num] : null;
            $correct_answer = $question['correct_answer'];
            $is_correct = ($user_answer === $correct_answer);

            if ($is_correct) {
                $correct_answers++;
            }

            $results[] = array(
                'question_num' => $question_num,
                'question' => $question['question'],
                'user_answer' => $user_answer,
                'correct_answer' => $correct_answer,
                'is_correct' => $is_correct
            );
        }

        $score = ($total_questions > 0) ? round(($correct_answers / $total_questions) * 100, 2) : 0;

        // Save quiz attempt to database (optional)
        $this->save_quiz_attempt($student_id, $quiz_id, $score, $correct_answers, $total_questions, $time_taken);

        echo json_encode([
            'success' => true,
            'score' => $score,
            'correct_answers' => $correct_answers,
            'total_questions' => $total_questions,
            'results' => $results,
            'time_taken' => $time_taken
        ]);
    }

    /**
     * Get quiz details with subject info
     */
    private function get_quiz_details($quiz_id)
    {
        if (!$this->db->table_exists('ai_quizzes')) {
            return null;
        }

        $this->db->select('q.*, s.subject_code, s.subject_name');
        $this->db->from('ai_quizzes q');
        $this->db->join('subjects s', 'q.subject_id = s.id');
        $this->db->where('q.id', $quiz_id);
        $this->db->where('q.is_published', 1);

        $query = $this->db->get();
        return $query->num_rows() > 0 ? $query->row() : null;
    }

    /**
     * Parse quiz content to extract questions and answers
     * Updated to work with JSON quiz_data structure
     */
    private function parse_quiz_content($quiz_data_json)
    {
        $questions = array();

        // Decode JSON data
        $quiz_data = json_decode($quiz_data_json, true);

        if (!$quiz_data || !isset($quiz_data['questions'])) {
            return $questions;
        }

        // Process each question from JSON
        foreach ($quiz_data['questions'] as $q) {
            if (!isset($q['question']) || !isset($q['options']) || !isset($q['correct_answer'])) {
                continue;
            }

            // Parse options from array format
            $options = array();
            foreach ($q['options'] as $option_text) {
                // Extract letter (A, B, C, D) from option text like "A) Answer text"
                if (preg_match('/^([A-D])\)\s*(.+)$/s', $option_text, $matches)) {
                    $options[$matches[1]] = trim($matches[2]);
                }
            }

            // Get correct answer letter
            $correct_answer = null;
            if (preg_match('/^([A-D])/', $q['correct_answer'], $matches)) {
                $correct_answer = $matches[1];
            }

            if (!empty($options) && $correct_answer) {
                $questions[] = array(
                    'question' => trim($q['question']),
                    'options' => $options,
                    'correct_answer' => $correct_answer
                );
            }
        }

        return $questions;
    }

    /**
     * Save quiz attempt to database
     */
    private function save_quiz_attempt($student_id, $quiz_id, $score, $correct_answers, $total_questions, $time_taken)
    {
        // Create table if it doesn't exist
        if (!$this->db->table_exists('quiz_attempts')) {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS quiz_attempts (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    student_id INT NOT NULL,
                    quiz_id INT NOT NULL,
                    score DECIMAL(5,2) NOT NULL,
                    correct_answers INT NOT NULL,
                    total_questions INT NOT NULL,
                    time_taken INT NOT NULL,
                    attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_student (student_id),
                    INDEX idx_quiz (quiz_id),
                    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
                    FOREIGN KEY (quiz_id) REFERENCES ai_quizzes(id) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
        }

        $attempt_data = array(
            'student_id' => $student_id,
            'quiz_id' => $quiz_id,
            'score' => $score,
            'correct_answers' => $correct_answers,
            'total_questions' => $total_questions,
            'time_taken' => $time_taken
        );

    }

    // ==========================================
    // FACULTY SPECIFIC VIEWS (Added for Sidebar Unification)
    // ==========================================

    /**
     * Profile View
     */
    public function profile()
    {
        if (!$this->session->userdata('logged_in')) {
            redirect('simple_portal');
        }

        $data['user_data'] = $this->session->userdata();
        $data['user_role'] = $this->session->userdata('role');
        $data['active_page'] = 'profile';

        $faculty_id = $this->session->userdata('user_id');
        if ($data['user_role'] === 'faculty') {
            $data['assigned_subjects'] = $this->Faculty_model->get_assigned_subjects($faculty_id);
        }

        $this->load->view('templates/header', $data);
        $this->load->view('simple_portal/profile', $data);
        $this->load->view('templates/footer', $data);
    }

    /**
     * Subject Resources View
     */
    public function subject_resources($subject_code = null)
    {
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'faculty') {
            redirect('simple_portal');
        }

        if (!$subject_code) {
            redirect('simple_portal/resources');
        }

        $subject_code = urldecode($subject_code);

        $data['user_data'] = $this->session->userdata();
        $data['user_role'] = $this->session->userdata('role');
        $data['active_page'] = 'resources';
        $data['active_subject'] = $subject_code;

        $faculty_id = $this->session->userdata('user_id');
        $data['assigned_subjects'] = $this->Faculty_model->get_assigned_subjects($faculty_id);

        $subject_id = null;
        $subject_name = '';
        foreach ($data['assigned_subjects'] as $sub) {
            if ($sub['subject_code'] === $subject_code) {
                $subject_id = $sub['subject_id'];
                $subject_name = $sub['subject_name'];
                break;
            }
        }

        if ($subject_id) {
            $all_resources = $this->Resource_model->get_resources_by_faculty($faculty_id);
            $data['resources'] = array_filter($all_resources, function ($r) use ($subject_id) {
                return $r->subject_id == $subject_id;
            });
            $data['filter_subject'] = $subject_name;
        } else {
            $data['resources'] = [];
            $data['error'] = "Subject not found or not assigned.";
        }

        $this->load->view('templates/header', $data);
        $this->load->view('simple_portal/resource_management', $data);
        $this->load->view('templates/footer', $data);
    }

    /**
     * Get resources for a subject (AJAX)
     */
    public function get_subject_resources()
    {
        header('Content-Type: application/json');

        if (!$this->session->userdata('logged_in')) {
            echo json_encode(['success' => false, 'error' => 'Not logged in']);
            return;
        }

        $subject_id = $this->input->get('subject_id');
        if (!$subject_id) {
            echo json_encode(['success' => false, 'error' => 'Subject ID required']);
            return;
        }

        $this->load->model('Resource_model');
        $resources = $this->Resource_model->get_resources_by_subject($subject_id);

        echo json_encode([
            'success' => true,
            'resources' => $resources
        ]);
    }

    /**
     * Clean JSON response from AI (remove markdown formatting)
     */
    private function clean_ai_json_response($content)
    {
        // Remove markdown code blocks
        $content = preg_replace('/```json\s*/', '', $content);
        $content = preg_replace('/```\s*$/', '', $content);
        $content = preg_replace('/```/', '', $content);

        // Remove any leading/trailing whitespace
        $content = trim($content);

        // Try to decode and validate
        $decoded = json_decode($content, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            // Re-encode to ensure clean formatting
            return json_encode($decoded, JSON_UNESCAPED_UNICODE);
        }

        return false;
    }

    /**
     * Manage Departments (Admin only)
     */
    public function manage_departments()
    {
        if ($this->session->userdata('role') !== 'admin') {
            redirect('simple_portal');
            return;
        }

        // Get all departments
        $this->db->order_by('name', 'ASC');
        $departments = $this->db->get('departments')->result();

        $data = array(
            'logged_in' => true,
            'user_role' => 'admin',
            'username' => $this->session->userdata('username'),
            'departments' => $departments,
            'active_page' => 'manage_departments'
        );

        echo $this->load->view('simple_portal/manage_departments', $data, TRUE);
    }

    /**
     * Save Department (Add/Edit)
     */
    public function save_department()
    {
        if ($this->session->userdata('role') !== 'admin') {
            redirect('simple_portal');
            return;
        }

        $department_id = $this->input->post('department_id');
        $name = trim($this->input->post('name'));
        $code = strtoupper(trim($this->input->post('code')));
        $description = trim($this->input->post('description'));

        // Validation
        if (empty($name) || empty($code)) {
            $this->session->set_flashdata('error', 'Department name and code are required.');
            redirect('simple_portal/manage_departments');
            return;
        }

        // Check for duplicate code
        $this->db->where('code', $code);
        if ($department_id) {
            $this->db->where('id !=', $department_id);
        }
        $existing = $this->db->get('departments')->row();

        if ($existing) {
            $this->session->set_flashdata('error', 'Department code already exists.');
            redirect('simple_portal/manage_departments');
            return;
        }

        $data = array(
            'name' => $name,
            'code' => $code,
            'description' => $description
        );

        if ($department_id) {
            // Update existing department
            $this->db->where('id', $department_id);
            $result = $this->db->update('departments', $data);
            $message = 'Department updated successfully!';
        } else {
            // Add new department
            $result = $this->db->insert('departments', $data);
            $message = 'Department added successfully!';
        }

        if ($result) {
            $this->session->set_flashdata('success', $message);
        } else {
            $this->session->set_flashdata('error', 'Failed to save department.');
        }

        redirect('simple_portal/manage_departments');
    }

    /**
     * Delete Department
     */
    public function delete_department($id)
    {
        if ($this->session->userdata('role') !== 'admin') {
            redirect('simple_portal');
            return;
        }

        // Check if department exists
        $this->db->where('id', $id);
        $department = $this->db->get('departments')->row();

        if (!$department) {
            $this->session->set_flashdata('error', 'Department not found.');
            redirect('simple_portal/manage_departments');
            return;
        }

        // Delete department (faculty will have department_id set to NULL due to foreign key constraint)
        $this->db->where('id', $id);
        $result = $this->db->delete('departments');

        if ($result) {
            $this->session->set_flashdata('success', 'Department deleted successfully.');
        } else {
            $this->session->set_flashdata('error', 'Failed to delete department.');
        }

        redirect('simple_portal/manage_departments');
    }

    /**
     * Get User Data (AJAX)
     */
    public function get_user_data($user_id)
    {
        header('Content-Type: application/json');
        
        if ($this->session->userdata('role') !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        // Get user data
        $this->db->select('u.id, u.username, u.email, u.role');
        $this->db->from('users u');
        $this->db->where('u.id', $user_id);
        $user = $this->db->get()->row_array();

        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'User not found']);
            return;
        }

        // Get role-specific data
        if ($user['role'] === 'faculty') {
            // Get faculty departments from junction table
            $this->db->select('fd.department_id');
            $this->db->from('faculty f');
            $this->db->join('faculty_departments fd', 'f.id = fd.faculty_id', 'left');
            $this->db->where('f.user_id', $user_id);
            $dept_result = $this->db->get()->result();
            
            $department_ids = array();
            foreach ($dept_result as $dept) {
                if ($dept->department_id) {
                    $department_ids[] = $dept->department_id;
                }
            }
            $user['department_ids'] = $department_ids;
        } elseif ($user['role'] === 'student') {
            $this->db->select('current_semester, enrollment_year, department_id');
            $this->db->from('students');
            $this->db->where('user_id', $user_id);
            $student = $this->db->get()->row();
            if ($student) {
                $user['current_semester'] = $student->current_semester;
                $user['enrollment_year'] = $student->enrollment_year;
                $user['department_id'] = $student->department_id;
            }
        }

        echo json_encode(['success' => true, 'user' => $user]);
    }

    /**
     * Update User
     */
    public function update_user()
    {
        if ($this->session->userdata('role') !== 'admin') {
            redirect('simple_portal');
            return;
        }

        $user_id = $this->input->post('user_id');
        $email = trim($this->input->post('email'));
        $password = trim($this->input->post('password'));
        $user_role = $this->input->post('user_role');

        // Validation
        if (empty($email)) {
            $this->session->set_flashdata('message_type', 'error');
            $this->session->set_flashdata('message', 'Email is required.');
            redirect('simple_portal');
            return;
        }

        // Check for duplicate email
        $this->db->where('email', $email);
        $this->db->where('id !=', $user_id);
        $existing = $this->db->get('users')->row();

        if ($existing) {
            $this->session->set_flashdata('message_type', 'error');
            $this->session->set_flashdata('message', 'Email already exists.');
            redirect('simple_portal');
            return;
        }

        // Update user data
        $user_data = ['email' => $email];
        
        // Update password if provided
        if (!empty($password)) {
            $user_data['password_hash'] = password_hash($password, PASSWORD_BCRYPT);
        }

        $this->db->where('id', $user_id);
        $this->db->update('users', $user_data);

        // Update role-specific data
        if ($user_role === 'faculty') {
            $department_ids = $this->input->post('department_ids');
            
            // Get faculty record
            $this->db->select('id');
            $this->db->from('faculty');
            $this->db->where('user_id', $user_id);
            $faculty = $this->db->get()->row();
            
            if ($faculty) {
                $faculty_id = $faculty->id;
                
                // Delete existing department assignments
                $this->db->where('faculty_id', $faculty_id);
                $this->db->delete('faculty_departments');
                
                // Insert new department assignments
                if (!empty($department_ids) && is_array($department_ids)) {
                    foreach ($department_ids as $dept_id) {
                        if (!empty($dept_id)) {
                            $this->db->insert('faculty_departments', [
                                'faculty_id' => $faculty_id,
                                'department_id' => $dept_id
                            ]);
                        }
                    }
                    
                    // Also update the main department_id in faculty table (use first selected)
                    $this->db->where('id', $faculty_id);
                    $this->db->update('faculty', ['department_id' => $department_ids[0]]);
                }
            }
        } elseif ($user_role === 'student') {
            $current_semester = $this->input->post('current_semester');
            $enrollment_year = $this->input->post('enrollment_year');
            $student_department_id = $this->input->post('student_department_id');
            
            $student_data = [];
            if ($current_semester) {
                $student_data['current_semester'] = $current_semester;
            }
            if ($enrollment_year) {
                $student_data['enrollment_year'] = $enrollment_year;
            }
            if ($student_department_id) {
                $student_data['department_id'] = $student_department_id;
            }
            
            if (!empty($student_data)) {
                $this->db->where('user_id', $user_id);
                $this->db->update('students', $student_data);
            }
        }

        $this->session->set_flashdata('message_type', 'success');
        $this->session->set_flashdata('message', 'User updated successfully!');
        redirect('simple_portal');
    }

    /**
     * Settings page (All roles)
     */
    public function settings()
    {
        if (!$this->session->userdata('logged_in')) {
            redirect('simple_portal');
            return;
        }

        $user_role = $this->session->userdata('role');
        $user_id = $this->session->userdata('user_id');

        // Get user data
        $this->db->where('id', $user_id);
        $user = $this->db->get('users')->row_array();

        $data = array(
            'logged_in' => true,
            'user_role' => $user_role,
            'username' => $this->session->userdata('username'),
            'user' => $user,
            'user_data' => $this->session->userdata(),
            'active_page' => 'settings'
        );

        // Get role-specific data
        if ($user_role === 'student') {
            $this->db->where('user_id', $user_id);
            $student_data = $this->db->get('students')->row_array();
            $data['student_data'] = $student_data;
        } elseif ($user_role === 'faculty') {
            // Get faculty data with department info
            $this->db->select('f.*, d.name as department_name, d.code as department_code');
            $this->db->from('faculty f');
            $this->db->join('departments d', 'f.department_id = d.id', 'left');
            $this->db->where('f.user_id', $user_id);
            $faculty_data = $this->db->get()->row_array();
            $data['faculty_data'] = $faculty_data;
        }

        echo $this->load->view('simple_portal/settings', $data, TRUE);
    }

    /**
     * Update password (All roles)
     */
    public function settings_update_password()
    {
        if (!$this->session->userdata('logged_in')) {
            redirect('simple_portal');
            return;
        }

        $user_id = $this->session->userdata('user_id');
        $current_password = $this->input->post('current_password');
        $new_password = $this->input->post('new_password');
        $confirm_password = $this->input->post('confirm_password');

        // Validate inputs
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $this->session->set_flashdata('error', 'All fields are required.');
            redirect('simple_portal/settings');
            return;
        }

        if ($new_password !== $confirm_password) {
            $this->session->set_flashdata('error', 'New passwords do not match.');
            redirect('simple_portal/settings');
            return;
        }

        if (strlen($new_password) < 6) {
            $this->session->set_flashdata('error', 'Password must be at least 6 characters long.');
            redirect('simple_portal/settings');
            return;
        }

        // Get current user
        $this->db->where('id', $user_id);
        $user = $this->db->get('users')->row();

        if (!$user) {
            $this->session->set_flashdata('error', 'User not found.');
            redirect('simple_portal/settings');
            return;
        }

        // Verify current password
        if (!password_verify($current_password, $user->password_hash)) {
            $this->session->set_flashdata('error', 'Current password is incorrect.');
            redirect('simple_portal/settings');
            return;
        }

        // Update password
        $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        $this->db->where('id', $user_id);
        $result = $this->db->update('users', ['password_hash' => $new_password_hash]);

        if ($result) {
            $this->session->set_flashdata('success', 'Password updated successfully!');
        } else {
            $this->session->set_flashdata('error', 'Failed to update password. Please try again.');
        }

        redirect('simple_portal/settings');
    }

    /**
     * Publish question paper to students
     */
    public function publish_question_paper()
    {
        header('Content-Type: application/json');

        // Restrict to faculty only
        if ($this->session->userdata('role') !== 'faculty') {
            echo json_encode(['success' => false, 'error' => 'Access denied. Only faculty can publish question papers.']);
            return;
        }

        try {
            $paper_id = $this->input->post('paper_id');
            $subject_id = $this->input->post('subject_id');

            if (!$paper_id || !$subject_id) {
                echo json_encode(['success' => false, 'error' => 'Paper ID and Subject ID are required']);
                return;
            }

            // Verify the paper belongs to the current user
            $this->db->where('id', $paper_id);
            $this->db->where('user_id', $this->session->userdata('user_id'));
            $paper = $this->db->get('ai_question_papers')->row();

            if (!$paper) {
                echo json_encode(['success' => false, 'error' => 'Question paper not found or access denied']);
                return;
            }

            // Update the paper to published status
            $update_data = [
                'is_published' => 1,
                'published_at' => date('Y-m-d H:i:s'),
                'subject_id' => $subject_id
            ];

            $this->db->where('id', $paper_id);
            $result = $this->db->update('ai_question_papers', $update_data);

            if ($result) {
                log_message('info', "Question paper $paper_id published to subject $subject_id by user " . $this->session->userdata('user_id'));
                echo json_encode(['success' => true, 'message' => 'Question paper published successfully']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to publish question paper']);
            }

        } catch (Exception $e) {
            log_message('error', 'Publish question paper error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
        }
    }

    /**
     * Publish quiz to students
     */
    public function publish_quiz()
    {
        header('Content-Type: application/json');

        // Restrict to faculty only
        if ($this->session->userdata('role') !== 'faculty') {
            echo json_encode(['success' => false, 'error' => 'Access denied. Only faculty can publish quizzes.']);
            return;
        }

        try {
            $quiz_id = $this->input->post('quiz_id');
            $subject_id = $this->input->post('subject_id');

            if (!$quiz_id || !$subject_id) {
                echo json_encode(['success' => false, 'error' => 'Quiz ID and Subject ID are required']);
                return;
            }

            // Verify the quiz belongs to the current user
            $this->db->where('id', $quiz_id);
            $this->db->where('user_id', $this->session->userdata('user_id'));
            $quiz = $this->db->get('ai_quizzes')->row();

            if (!$quiz) {
                echo json_encode(['success' => false, 'error' => 'Quiz not found or access denied']);
                return;
            }

            // Update the quiz to published status
            $update_data = [
                'is_published' => 1,
                'published_at' => date('Y-m-d H:i:s'),
                'subject_id' => $subject_id
            ];

            $this->db->where('id', $quiz_id);
            $result = $this->db->update('ai_quizzes', $update_data);

            if ($result) {
                log_message('info', "Quiz $quiz_id published to subject $subject_id by user " . $this->session->userdata('user_id'));
                echo json_encode(['success' => true, 'message' => 'Quiz published successfully']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to publish quiz']);
            }

        } catch (Exception $e) {
            log_message('error', 'Publish quiz error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
        }
    }

    /**
     * Publish assignment to students
     */
    public function publish_assignment()
    {
        header('Content-Type: application/json');

        // Restrict to faculty only
        if ($this->session->userdata('role') !== 'faculty') {
            echo json_encode(['success' => false, 'error' => 'Access denied. Only faculty can publish assignments.']);
            return;
        }

        try {
            $assignment_id = $this->input->post('assignment_id');
            $subject_id = $this->input->post('subject_id');

            if (!$assignment_id || !$subject_id) {
                echo json_encode(['success' => false, 'error' => 'Assignment ID and Subject ID are required']);
                return;
            }

            // Verify the assignment belongs to the current user
            $this->db->where('id', $assignment_id);
            $this->db->where('user_id', $this->session->userdata('user_id'));
            $assignment = $this->db->get('ai_assignments')->row();

            if (!$assignment) {
                echo json_encode(['success' => false, 'error' => 'Assignment not found or access denied']);
                return;
            }

            // Update the assignment to published status
            $update_data = [
                'is_published' => 1,
                'published_at' => date('Y-m-d H:i:s'),
                'subject_id' => $subject_id
            ];

            $this->db->where('id', $assignment_id);
            $result = $this->db->update('ai_assignments', $update_data);

            if ($result) {
                log_message('info', "Assignment $assignment_id published to subject $subject_id by user " . $this->session->userdata('user_id'));
                echo json_encode(['success' => true, 'message' => 'Assignment published successfully']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to publish assignment']);
            }

        } catch (Exception $e) {
            log_message('error', 'Publish assignment error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
        }
    }

    /**
     * Check if session is still valid (AJAX endpoint)
     */
    public function check_session()
    {
        header('Content-Type: application/json');

        $logged_in = $this->session->userdata('logged_in') === TRUE;
        $user_id = $this->session->userdata('user_id');

        echo json_encode([
            'valid' => $logged_in && !empty($user_id),
            'timestamp' => time()
        ]);
    }

    /**
     * Handle logout when tab is closed (Beacon API endpoint)
     */
    public function logout_on_close()
    {
        // This is called via sendBeacon when user closes tab
        // We'll mark the session for destruction

        if ($this->input->post('tab_close') === 'true') {
            // Destroy session
            $this->session->sess_destroy();

            // Clear all session data
            $_SESSION = array();

            // Clear session cookie
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(
                    session_name(),
                    '',
                    time() - 42000,
                    $params["path"],
                    $params["domain"],
                    $params["secure"],
                    $params["httponly"]
                );
            }
        }

        // Return success (even though beacon doesn't read response)
        http_response_code(204); // No content
    }
}
?>