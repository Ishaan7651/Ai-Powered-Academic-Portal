<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Simple Base Controller
 * Clean implementation without complex error handling
 */
class Simple_Controller extends CI_Controller {
    
    protected $user_data;
    protected $user_role;
    
    public function __construct() {
        parent::__construct();
        
        // Load basic libraries
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->database();
        
        // Initialize user session data
        $this->user_data = $this->session->userdata();
        $this->user_role = $this->session->userdata('role');
    }
    
    /**
     * Check if user is logged in
     */
    protected function is_logged_in() {
        return $this->session->userdata('user_id') !== NULL && 
               $this->session->userdata('logged_in') === TRUE;
    }
    
    /**
     * Check if user has specific role
     */
    protected function has_role($required_role) {
        return $this->user_role === $required_role;
    }
    
    /**
     * Redirect to login if not authenticated
     */
    protected function require_login() {
        if (!$this->is_logged_in()) {
            redirect('portal');
        }
    }
    
    /**
     * Require specific role or redirect to dashboard
     */
    protected function require_role($required_role) {
        $this->require_login();
        
        if (!$this->has_role($required_role)) {
            $this->set_message('error', 'Access denied. Insufficient permissions.');
            $this->redirect_to_role_dashboard();
        }
    }
    
    /**
     * Redirect user to their role-specific dashboard
     */
    protected function redirect_to_role_dashboard() {
        switch ($this->user_role) {
            case 'admin':
                redirect('portal/admin_dashboard');
                break;
            case 'faculty':
                redirect('portal/faculty_dashboard');
                break;
            case 'student':
                redirect('portal/student_dashboard');
                break;
            default:
                redirect('portal');
        }
    }
    
    /**
     * Load view with template
     */
    protected function load_view($view, $data = array()) {
        $data['user_data'] = $this->user_data;
        $data['user_role'] = $this->user_role;
        $data['logged_in'] = $this->is_logged_in();
        
        $this->load->view('templates/simple_header', $data);
        $this->load->view($view, $data);
        $this->load->view('templates/simple_footer', $data);
    }
    
    /**
     * Set flash message
     */
    protected function set_message($type, $message) {
        $this->session->set_flashdata('message_type', $type);
        $this->session->set_flashdata('message', $message);
    }
}
?>