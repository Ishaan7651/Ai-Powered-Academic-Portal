<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Simple User Model
 * Simplified version without complex error handling
 */
class User_model_simple extends CI_Model {
    
    private $table = 'users';
    
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    
    /**
     * Authenticate user credentials
     */
    public function authenticate($username, $password, $role) {
        // Input validation
        if (empty($username) || empty($password) || empty($role)) {
            return false;
        }
        
        // Sanitize username
        $username = trim($username);
        
        $this->db->where('username', $username);
        $this->db->where('role', $role);
        $this->db->where('is_active', TRUE);
        
        $query = $this->db->get($this->table);
        
        if ($query->num_rows() == 1) {
            $user = $query->row();
            
            // Verify password hash
            if (password_verify($password, $user->password_hash)) {
                return $user;
            }
        }
        
        return false;
    }
    
    /**
     * Get user by ID
     */
    public function get_user($id) {
        $this->db->where('id', $id);
        $this->db->where('is_active', TRUE);
        
        $query = $this->db->get($this->table);
        
        if ($query->num_rows() == 1) {
            return $query->row();
        }
        
        return false;
    }
    
    /**
     * Get all users
     */
    public function get_all_users() {
        $this->db->order_by('created_at', 'DESC');
        $query = $this->db->get($this->table);
        return $query->result();
    }
}
?>