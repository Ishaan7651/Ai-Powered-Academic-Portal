<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Working Student Controller
 * 
 * Simplified student functionality without complex dependencies
 */
class Student_working extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('Resource_model');
        $this->load->model('Student_model');
        $this->load->library('session');
        $this->load->helper('url');
        
        // Check if user is logged in and has student role
        if (!$this->is_logged_in() || $this->session->userdata('role') !== 'student') {
            redirect('auth_working');
        }
    }
    
    /**
     * Student dashboard
     */
    public function dashboard() {
        $data['user_data'] = $this->session->userdata();
        $data['user_role'] = $this->session->userdata('role');
        
        // Get student info
        $student_id = $this->session->userdata('user_id');
        $student_info = $this->Student_model->get_student_info($student_id);
        $data['current_semester'] = $student_info ? $student_info->current_semester : 1;
        
        // Get available semesters (1 to current semester)
        $data['available_semesters'] = range(1, $data['current_semester']);
        
        $this->load->view('templates/header', $data);
        $this->load->view('student/dashboard', $data);
        $this->load->view('templates/footer', $data);
    }
    
    /**
     * Access resources by semester
     */
    public function resource_access($semester = null) {
        $data['user_data'] = $this->session->userdata();
        $data['user_role'] = $this->session->userdata('role');
        
        $student_id = $this->session->userdata('user_id');
        $student_info = $this->Student_model->get_student_info($student_id);
        $current_semester = $student_info ? $student_info->current_semester : 1;
        
        // Default to current semester if not specified
        if (!$semester) {
            $semester = $current_semester;
        }
        
        // Check if student can access this semester
        if ($semester > $current_semester) {
            $this->session->set_flashdata('message_type', 'error');
            $this->session->set_flashdata('message', 'You cannot access future semester materials.');
            redirect('student_working/dashboard');
            return;
        }
        
        $data['semester'] = $semester;
        $data['current_semester'] = $current_semester;
        $data['resources'] = $this->Resource_model->get_resources_by_semester($semester);
        
        $this->load->view('templates/header', $data);
        $this->load->view('student/resource_access', $data);
        $this->load->view('templates/footer', $data);
    }
    
    /**
     * Download resource
     */
    public function download_resource($resource_id) {
        $resource = $this->Resource_model->get_resource($resource_id);
        
        if (!$resource) {
            show_404();
            return;
        }
        
        // Check if student can access this resource
        $student_id = $this->session->userdata('user_id');
        $student_info = $this->Student_model->get_student_info($student_id);
        $current_semester = $student_info ? $student_info->current_semester : 1;
        
        if ($resource->semester > $current_semester) {
            $this->session->set_flashdata('message_type', 'error');
            $this->session->set_flashdata('message', 'You cannot access this resource.');
            redirect('student_working/dashboard');
            return;
        }
        
        if ($resource->file_type === 'weblink') {
            redirect($resource->file_path);
        } else {
            $file_path = FCPATH . $resource->file_path;
            if (file_exists($file_path)) {
                force_download($resource->title . '.' . $resource->file_type, file_get_contents($file_path));
            } else {
                show_404();
            }
        }
    }
    
    /**
     * Check if user is logged in
     */
    private function is_logged_in() {
        return $this->session->userdata('user_id') !== NULL && 
               $this->session->userdata('logged_in') === TRUE;
    }
}