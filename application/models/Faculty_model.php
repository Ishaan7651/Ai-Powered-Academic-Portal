<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Faculty_model extends CI_Model {
    
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    
    /**
     * Get all faculty with their assigned subjects
     */
    public function get_all_faculty_with_subjects() {
        try {
            // First, get all users with faculty role
            $this->db->select('id as user_id, username, email');
            $this->db->from('users');
            $this->db->where('role', 'faculty');
            $this->db->where('is_active', TRUE);
            $this->db->order_by('username');
            
            $faculty_users = $this->db->get()->result();
            
            $faculty_list = array();
            
            foreach ($faculty_users as $user) {
                // Get faculty record for this user
                $this->db->select('id as faculty_id, employee_id, department');
                $this->db->from('faculty');
                $this->db->where('user_id', $user->user_id);
                $faculty_record = $this->db->get()->row();
                
                if ($faculty_record) {
                    // Faculty record exists
                    $faculty = (object) array(
                        'user_id' => $user->user_id,
                        'username' => $user->username,
                        'email' => $user->email,
                        'faculty_id' => $faculty_record->faculty_id,
                        'employee_id' => $faculty_record->employee_id,
                        'department' => $faculty_record->department
                    );
                } else {
                    // Create faculty record
                    $faculty_data = [
                        'user_id' => $user->user_id,
                        'employee_id' => 'EMP' . str_pad($user->user_id, 4, '0', STR_PAD_LEFT),
                        'department' => 'General'
                    ];
                    $this->db->insert('faculty', $faculty_data);
                    $faculty_id = $this->db->insert_id();
                    
                    $faculty = (object) array(
                        'user_id' => $user->user_id,
                        'username' => $user->username,
                        'email' => $user->email,
                        'faculty_id' => $faculty_id,
                        'employee_id' => $faculty_data['employee_id'],
                        'department' => $faculty_data['department']
                    );
                }
                
                // Get assigned subjects
                $faculty->subjects = $this->get_faculty_subjects($faculty->faculty_id);
                
                $faculty_list[] = $faculty;
            }
            
            return $faculty_list;
            
        } catch (Exception $e) {
            // Log error and return empty array
            log_message('error', 'Faculty_model error: ' . $e->getMessage());
            return array();
        }
    }
    
    /**
     * Get subjects assigned to a faculty
     */
    public function get_faculty_subjects($faculty_id) {
        try {
            // Check if faculty_subjects table exists
            if (!$this->db->table_exists('faculty_subjects')) {
                return array(); // Return empty array if table doesn't exist
            }
            
            // First get subject IDs assigned to this faculty
            $this->db->select('subject_id');
            $this->db->from('faculty_subjects');
            $this->db->where('faculty_id', $faculty_id);
            $assignments = $this->db->get()->result();
            
            if (empty($assignments)) {
                return array(); // No subjects assigned
            }
            
            // Get subject IDs as array
            $subject_ids = array();
            foreach ($assignments as $assignment) {
                $subject_ids[] = $assignment->subject_id;
            }
            
            // Get subject details
            $this->db->select('id, subject_code, subject_name, semester');
            $this->db->from('subjects');
            $this->db->where_in('id', $subject_ids);
            // Check if is_active column exists
            if ($this->db->field_exists('is_active', 'subjects')) {
                $this->db->where('is_active', TRUE);
            }
            $this->db->order_by('semester, subject_name');
            
            return $this->db->get()->result();
            
        } catch (Exception $e) {
            log_message('error', 'get_faculty_subjects error: ' . $e->getMessage());
            return array();
        }
    }
    
    /**
     * Get faculty by user ID
     */
    public function get_faculty_by_user_id($user_id) {
        $this->db->where('user_id', $user_id);
        $query = $this->db->get('faculty');
        return $query->row();
    }
    
    /**
     * Create faculty record
     */
    public function create_faculty($data) {
        return $this->db->insert('faculty', $data);
    }
    
    /**
     * Update faculty
     */
    public function update_faculty($faculty_id, $data) {
        $this->db->where('id', $faculty_id);
        return $this->db->update('faculty', $data);
    }
    
    /**
     * Assign subject to faculty
     */
    public function assign_subject($faculty_id, $subject_id) {
        // Ensure faculty_subjects table exists
        if (!$this->db->table_exists('faculty_subjects')) {
            $this->create_faculty_subjects_table();
        }
        
        // Check if assignment already exists
        $this->db->where('faculty_id', $faculty_id);
        $this->db->where('subject_id', $subject_id);
        $existing = $this->db->get('faculty_subjects');
        
        if ($existing->num_rows() == 0) {
            $data = [
                'faculty_id' => $faculty_id,
                'subject_id' => $subject_id
            ];
            return $this->db->insert('faculty_subjects', $data);
        }
        
        return false; // Already assigned
    }
    
    /**
     * Remove subject assignment
     */
    public function remove_subject_assignment($faculty_id, $subject_id) {
        // Check if faculty_subjects table exists
        if (!$this->db->table_exists('faculty_subjects')) {
            return false; // Can't remove from non-existent table
        }
        
        $this->db->where('faculty_id', $faculty_id);
        $this->db->where('subject_id', $subject_id);
        return $this->db->delete('faculty_subjects');
    }
    
    /**
     * Get unassigned subjects for a faculty
     */
    public function get_unassigned_subjects($faculty_id) {
        try {
            // Get all subjects
            $this->db->select('*');
            $this->db->from('subjects');
            // Check if is_active column exists
            if ($this->db->field_exists('is_active', 'subjects')) {
                $this->db->where('is_active', TRUE);
            }
            $this->db->order_by('semester, subject_name');
            $all_subjects = $this->db->get()->result();
            
            // Get assigned subject IDs
            $assigned_subject_ids = array();
            if ($this->db->table_exists('faculty_subjects')) {
                $this->db->select('subject_id');
                $this->db->from('faculty_subjects');
                $this->db->where('faculty_id', $faculty_id);
                $assignments = $this->db->get()->result();
                
                foreach ($assignments as $assignment) {
                    $assigned_subject_ids[] = $assignment->subject_id;
                }
            }
            
            // Filter out assigned subjects
            $unassigned_subjects = array();
            foreach ($all_subjects as $subject) {
                if (!in_array($subject->id, $assigned_subject_ids)) {
                    $unassigned_subjects[] = $subject;
                }
            }
            
            return $unassigned_subjects;
            
        } catch (Exception $e) {
            log_message('error', 'get_unassigned_subjects error: ' . $e->getMessage());
            return array();
        }
    }
    
    /**
     * Create faculty table if it doesn't exist
     */
    private function create_faculty_table() {
        $sql = "CREATE TABLE IF NOT EXISTS faculty (
            id INT PRIMARY KEY AUTO_INCREMENT,
            user_id INT NOT NULL,
            employee_id VARCHAR(20) UNIQUE,
            department VARCHAR(100),
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_employee_id (employee_id)
        )";
        
        $this->db->query($sql);
    }
    
    /**
     * Count faculty assigned to a subject
     */
    public function count_faculty_by_subject($subject_id) {
        // Check if faculty_subjects table exists
        if (!$this->db->table_exists('faculty_subjects')) {
            return 0; // No assignments if table doesn't exist
        }
        
        $this->db->where('subject_id', $subject_id);
        return $this->db->count_all_results('faculty_subjects');
    }
    
    /**
     * Create faculty_subjects table if it doesn't exist
     */
    private function create_faculty_subjects_table() {
        $sql = "CREATE TABLE IF NOT EXISTS faculty_subjects (
            id INT PRIMARY KEY AUTO_INCREMENT,
            faculty_id INT NOT NULL,
            subject_id INT NOT NULL,
            assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (faculty_id) REFERENCES faculty(id) ON DELETE CASCADE,
            FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
            UNIQUE KEY unique_assignment (faculty_id, subject_id),
            INDEX idx_faculty (faculty_id),
            INDEX idx_subject (subject_id)
        )";
        
        $this->db->query($sql);
    }
}