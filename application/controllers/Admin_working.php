<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Working Admin Controller
 * 
 * Simplified admin functionality without complex dependencies
 */
class Admin_working extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->model('Faculty_model');
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->library('form_validation');
        
        // Check if user is logged in and has admin role
        if (!$this->is_logged_in() || $this->session->userdata('role') !== 'admin') {
            redirect('auth_working');
        }
    }
    
    /**
     * Admin dashboard
     */
    public function dashboard() {
        $data['user_data'] = $this->session->userdata();
        $data['user_role'] = $this->session->userdata('role');
        
        // Get basic statistics
        $data['total_users'] = $this->User_model->count_all_users();
        $data['total_faculty'] = $this->User_model->count_users_by_role('faculty');
        $data['total_students'] = $this->User_model->count_users_by_role('student');
        
        $this->load->view('templates/header', $data);
        $this->load->view('admin/dashboard', $data);
        $this->load->view('templates/footer', $data);
    }
    
    /**
     * Manage users
     */
    public function users() {
        $data['user_data'] = $this->session->userdata();
        $data['user_role'] = $this->session->userdata('role');
        $data['users'] = $this->User_model->get_all_users();
        
        $this->load->view('templates/header', $data);
        $this->load->view('admin/users', $data);
        $this->load->view('templates/footer', $data);
    }
    
    /**
     * Manage faculty
     */
    public function faculty() {
        $data['user_data'] = $this->session->userdata();
        $data['user_role'] = $this->session->userdata('role');
        $data['faculty_list'] = $this->Faculty_model->get_all_faculty_with_subjects();
        
        $this->load->view('templates/header', $data);
        $this->load->view('admin/faculty', $data);
        $this->load->view('templates/footer', $data);
    }
    
    /**
     * Check if user is logged in
     */
    private function is_logged_in() {
        return $this->session->userdata('user_id') !== NULL && 
               $this->session->userdata('logged_in') === TRUE;
    }
}