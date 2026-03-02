<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Working Faculty Controller
 * 
 * Simplified faculty functionality without complex dependencies
 */
class Faculty_working extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('Resource_model');
        $this->load->model('Assignment_model');
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->library('form_validation');
        $this->load->library('upload');
        
        // Check if user is logged in and has faculty role
        if (!$this->is_logged_in() || $this->session->userdata('role') !== 'faculty') {
            redirect('auth_working');
        }
    }
    
    /**
     * Faculty dashboard
     */
    public function dashboard() {
        $data['user_data'] = $this->session->userdata();
        $data['user_role'] = $this->session->userdata('role');
        
        // Get basic statistics
        $faculty_id = $this->session->userdata('user_id');
        $data['total_resources'] = $this->Resource_model->count_resources_by_faculty($faculty_id);
        $data['total_assignments'] = $this->Assignment_model->count_assignments_by_faculty($faculty_id);
        
        $this->load->view('templates/header', $data);
        $this->load->view('faculty/dashboard', $data);
        $this->load->view('templates/footer', $data);
    }
    
    /**
     * Resource management
     */
    public function resource_management() {
        $data['user_data'] = $this->session->userdata();
        $data['user_role'] = $this->session->userdata('role');
        
        $faculty_id = $this->session->userdata('user_id');
        $data['resources'] = $this->Resource_model->get_resources_by_faculty($faculty_id);
        
        $this->load->view('templates/header', $data);
        $this->load->view('faculty/resource_management', $data);
        $this->load->view('templates/footer', $data);
    }
    
    /**
     * Upload resource
     */
    public function upload_resource() {
        $data['user_data'] = $this->session->userdata();
        $data['user_role'] = $this->session->userdata('role');
        
        if ($this->input->post()) {
            $this->process_resource_upload();
        } else {
            $this->load->view('templates/header', $data);
            $this->load->view('faculty/upload_resource', $data);
            $this->load->view('templates/footer', $data);
        }
    }
    
    /**
     * Process resource upload
     */
    private function process_resource_upload() {
        $this->form_validation->set_rules('title', 'Title', 'required|trim|max_length[255]');
        $this->form_validation->set_rules('description', 'Description', 'trim');
        $this->form_validation->set_rules('subject_id', 'Subject', 'required|numeric');
        $this->form_validation->set_rules('semester', 'Semester', 'required|numeric');
        
        if ($this->form_validation->run() === FALSE) {
            $data['user_data'] = $this->session->userdata();
            $data['user_role'] = $this->session->userdata('role');
            $this->load->view('templates/header', $data);
            $this->load->view('faculty/upload_resource', $data);
            $this->load->view('templates/footer', $data);
            return;
        }
        
        // Handle file upload or web link
        $resource_data = array(
            'title' => $this->input->post('title'),
            'description' => $this->input->post('description'),
            'subject_id' => $this->input->post('subject_id'),
            'semester' => $this->input->post('semester'),
            'uploaded_by' => $this->session->userdata('user_id'),
            'upload_date' => date('Y-m-d H:i:s')
        );
        
        if ($this->input->post('resource_type') === 'weblink') {
            $resource_data['file_type'] = 'weblink';
            $resource_data['file_path'] = $this->input->post('web_url');
            $resource_data['file_size'] = 0;
        } else {
            // Handle file upload
            $config['upload_path'] = './uploads/resources/';
            $config['allowed_types'] = 'pdf|ppt|pptx|xls|xlsx|csv|epub';
            $config['max_size'] = 102400; // 100MB
            
            $this->upload->initialize($config);
            
            if ($this->upload->do_upload('resource_file')) {
                $upload_data = $this->upload->data();
                $resource_data['file_type'] = pathinfo($upload_data['file_name'], PATHINFO_EXTENSION);
                $resource_data['file_path'] = 'uploads/resources/' . $upload_data['file_name'];
                $resource_data['file_size'] = $upload_data['file_size'];
            } else {
                $this->session->set_flashdata('message_type', 'error');
                $this->session->set_flashdata('message', $this->upload->display_errors());
                redirect('faculty_working/upload_resource');
                return;
            }
        }
        
        if ($this->Resource_model->create_resource($resource_data)) {
            $this->session->set_flashdata('message_type', 'success');
            $this->session->set_flashdata('message', 'Resource uploaded successfully.');
        } else {
            $this->session->set_flashdata('message_type', 'error');
            $this->session->set_flashdata('message', 'Failed to upload resource.');
        }
        
        redirect('faculty_working/resource_management');
    }
    
    /**
     * Check if user is logged in
     */
    private function is_logged_in() {
        return $this->session->userdata('user_id') !== NULL && 
               $this->session->userdata('logged_in') === TRUE;
    }
}