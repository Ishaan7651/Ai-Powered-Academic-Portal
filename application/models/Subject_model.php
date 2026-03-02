<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Subject_model extends CI_Model {
    
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    
    /**
     * Get all subjects
     */
    public function get_all_subjects() {
        // Check if is_active column exists
        if ($this->db->field_exists('is_active', 'subjects')) {
            $this->db->where('is_active', TRUE);
        }
        $this->db->order_by('semester, subject_name');
        return $this->db->get('subjects')->result();
    }
    
    /**
     * Count all subjects
     */
    public function count_all_subjects() {
        // Check if is_active column exists
        if ($this->db->field_exists('is_active', 'subjects')) {
            $this->db->where('is_active', TRUE);
            return $this->db->count_all_results('subjects');
        } else {
            return $this->db->count_all('subjects');
        }
    }
    
    /**
     * Get subject by ID
     */
    public function get_subject($id) {
        $this->db->where('id', $id);
        $query = $this->db->get('subjects');
        return $query->row();
    }
    
    /**
     * Create new subject
     */
    public function create_subject($data) {
        return $this->db->insert('subjects', $data);
    }
    
    /**
     * Update subject
     */
    public function update_subject($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('subjects', $data);
    }
    
    /**
     * Delete subject (soft delete)
     */
    public function delete_subject($id) {
        // Check if is_active column exists
        if ($this->db->field_exists('is_active', 'subjects')) {
            $this->db->where('id', $id);
            return $this->db->update('subjects', ['is_active' => FALSE]);
        } else {
            // Hard delete if no is_active column
            $this->db->where('id', $id);
            return $this->db->delete('subjects');
        }
    }
    
    /**
     * Get subjects by semester
     */
    public function get_subjects_by_semester($semester) {
        $this->db->where('semester', $semester);
        // Check if is_active column exists
        if ($this->db->field_exists('is_active', 'subjects')) {
            $this->db->where('is_active', TRUE);
        }
        $this->db->order_by('subject_name');
        return $this->db->get('subjects')->result();
    }
    
    /**
     * Get subject by code
     */
    public function get_subject_by_code($subject_code) {
        $this->db->where('subject_code', $subject_code);
        // Check if is_active column exists
        if ($this->db->field_exists('is_active', 'subjects')) {
            $this->db->where('is_active', TRUE);
        }
        return $this->db->get('subjects')->row();
    }
}