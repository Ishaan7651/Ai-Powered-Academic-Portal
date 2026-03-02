<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Working Authentication Controller
 * 
 * Simplified version that works without complex dependencies
 */
class Auth_working extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->library('form_validation');
    }
    
    /**
     * Default authentication page
     */
    public function index() {
        if ($this->is_logged_in()) {
            $this->redirect_to_role_dashboard();
            return;
        }
        
        $this->load->view('templates/header');
        $this->load->view('auth/role_selection');
        $this->load->view('templates/footer');
    }
    
    /**
     * Role-specific login page
     */
    public function login($role = null) {
        if ($this->is_logged_in()) {
            $this->redirect_to_role_dashboard();
            return;
        }
        
        // Validate role parameter
        $valid_roles = ['admin', 'faculty', 'student'];
        if (!in_array($role, $valid_roles)) {
            redirect('auth_working');
            return;
        }
        
        if ($this->input->post()) {
            $this->process_login($role);
        } else {
            $data['role'] = $role;
            $this->load->view('templates/header', $data);
            $this->load->view('auth/login', $data);
            $this->load->view('templates/footer', $data);
        }
    }
    
    /**
     * Process login form submission
     */
    private function process_login($role) {
        $this->form_validation->set_rules('username', 'Username', 'required|trim|max_length[50]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
        
        if ($this->form_validation->run() === FALSE) {
            $data['role'] = $role;
            $this->load->view('templates/header', $data);
            $this->load->view('auth/login', $data);
            $this->load->view('templates/footer', $data);
            return;
        }
        
        $username = $this->security->xss_clean($this->input->post('username'));
        $password = $this->input->post('password');
        
        $user = $this->User_model->authenticate($username, $password, $role);
        
        if ($user) {
            // Check if account is active
            if (!$user->is_active) {
                $this->session->set_flashdata('message_type', 'error');
                $this->session->set_flashdata('message', 'Your account has been deactivated. Please contact the administrator.');
                $data['role'] = $role;
                $this->load->view('templates/header', $data);
                $this->load->view('auth/login', $data);
                $this->load->view('templates/footer', $data);
                return;
            }
            
            // Regenerate session ID for security
            $this->session->sess_regenerate(TRUE);
            
            // Set session data
            $session_data = array(
                'user_id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role,
                'logged_in' => TRUE,
                'login_time' => time(),
                'last_activity' => time()
            );
            
            $this->session->set_userdata($session_data);
            
            // Redirect to role-specific dashboard
            $this->redirect_to_role_dashboard();
        } else {
            $this->session->set_flashdata('message_type', 'error');
            $this->session->set_flashdata('message', 'Invalid username or password.');
            $data['role'] = $role;
            $this->load->view('templates/header', $data);
            $this->load->view('auth/login', $data);
            $this->load->view('templates/footer', $data);
        }
    }
    
    /**
     * Logout user and destroy session
     */
    public function logout() {
        // Clear all session data
        $this->session->sess_destroy();
        
        // Set logout message
        $this->session->set_flashdata('message_type', 'success');
        $this->session->set_flashdata('message', 'You have been successfully logged out.');
        
        redirect('auth_working');
    }
    
    /**
     * Silent logout for browser close events (AJAX endpoint)
     */
    public function silent_logout() {
        // Only accept POST requests
        if ($this->input->method() !== 'post') {
            show_404();
            return;
        }
        
        // Destroy session
        $this->session->sess_destroy();
        
        // Return JSON response
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['success' => true]));
    }
    
    /**
     * Check if user is logged in
     */
    private function is_logged_in() {
        return $this->session->userdata('user_id') !== NULL && 
               $this->session->userdata('logged_in') === TRUE;
    }
    
    /**
     * Redirect user to their role-specific dashboard
     */
    private function redirect_to_role_dashboard() {
        $role = $this->session->userdata('role');
        switch ($role) {
            case 'admin':
                redirect('admin/dashboard');
                break;
            case 'faculty':
                redirect('faculty/dashboard');
                break;
            case 'student':
                redirect('student/dashboard');
                break;
            default:
                redirect('auth_working');
        }
    }
}