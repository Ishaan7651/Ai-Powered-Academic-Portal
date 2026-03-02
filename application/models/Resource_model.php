<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Resource_model extends CI_Model {
    
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    
    /**
     * Create new resource
     */
    public function create_resource($data) {
        try {
            // Handle date column name differences
            if (isset($data['upload_date']) && $this->db->field_exists('created_at', 'resources') && !$this->db->field_exists('upload_date', 'resources')) {
                $data['created_at'] = $data['upload_date'];
                unset($data['upload_date']);
            }
            
            // Log the data being inserted for debugging
            log_message('info', 'Inserting resource data: ' . print_r($data, true));
            
            return $this->db->insert('resources', $data);
            
        } catch (Exception $e) {
            log_message('error', 'Resource creation error: ' . $e->getMessage());
            log_message('error', 'Resource data: ' . print_r($data, true));
            return false;
        }
    }
    
    /**
     * Get resources by faculty
     */
    public function get_resources_by_faculty($faculty_id) {
        try {
            // Determine which date column to use and create alias
            $date_column = '';
            if ($this->db->field_exists('upload_date', 'resources')) {
                $date_column = 'r.upload_date as upload_date';
            } elseif ($this->db->field_exists('created_at', 'resources')) {
                $date_column = 'r.created_at as upload_date';
            }
            
            // Check if faculty_id column exists
            if ($this->db->field_exists('faculty_id', 'resources')) {
                $select_fields = 'r.id, r.title, r.description, r.file_type, r.file_path, r.file_size, r.subject_id, r.faculty_id, s.subject_name, s.subject_code, s.semester';
                if ($date_column) {
                    $select_fields .= ', ' . $date_column;
                }
                if ($this->db->field_exists('is_active', 'resources')) {
                    $select_fields .= ', r.is_active';
                }
                
                $this->db->select($select_fields);
                $this->db->from('resources r');
                $this->db->join('subjects s', 'r.subject_id = s.id');
                $this->db->where('r.faculty_id', $faculty_id);
            } else {
                // Fallback: use uploaded_by if faculty_id doesn't exist
                if ($this->db->field_exists('uploaded_by', 'resources')) {
                    // Get user_id from faculty table
                    $this->db->select('user_id');
                    $this->db->from('faculty');
                    $this->db->where('id', $faculty_id);
                    $faculty = $this->db->get()->row();
                    
                    if (!$faculty) {
                        return array(); // No faculty found
                    }
                    
                    $select_fields = 'r.id, r.title, r.description, r.file_type, r.file_path, r.file_size, r.subject_id, r.uploaded_by, s.subject_name, s.subject_code, s.semester';
                    if ($date_column) {
                        $select_fields .= ', ' . $date_column;
                    }
                    if ($this->db->field_exists('is_active', 'resources')) {
                        $select_fields .= ', r.is_active';
                    }
                    
                    $this->db->select($select_fields);
                    $this->db->from('resources r');
                    $this->db->join('subjects s', 'r.subject_id = s.id');
                    $this->db->where('r.uploaded_by', $faculty->user_id);
                } else {
                    return array(); // No way to filter by faculty
                }
            }
            
            // Check if is_active column exists
            if ($this->db->field_exists('is_active', 'resources')) {
                $this->db->where('r.is_active', TRUE);
            }
            
            // Check which date column exists and use it for ordering
            if ($this->db->field_exists('upload_date', 'resources')) {
                $this->db->order_by('r.upload_date', 'DESC');
            } elseif ($this->db->field_exists('created_at', 'resources')) {
                $this->db->order_by('r.created_at', 'DESC');
            } else {
                $this->db->order_by('r.id', 'DESC'); // Fallback to ID
            }
            
            return $this->db->get()->result();
            
        } catch (Exception $e) {
            log_message('error', 'get_resources_by_faculty error: ' . $e->getMessage());
            return array();
        }
    }
    
    /**
     * Count resources by faculty
     */
    public function count_resources_by_faculty($faculty_id) {
        // Check if faculty_id column exists
        if ($this->db->field_exists('faculty_id', 'resources')) {
            $this->db->where('faculty_id', $faculty_id);
        } else {
            // Fallback: use uploaded_by if faculty_id doesn't exist
            if ($this->db->field_exists('uploaded_by', 'resources')) {
                // Get user_id from faculty table
                $this->db->select('user_id');
                $this->db->from('faculty');
                $this->db->where('id', $faculty_id);
                $faculty = $this->db->get()->row();
                
                if ($faculty) {
                    $this->db->where('uploaded_by', $faculty->user_id);
                } else {
                    return 0; // No faculty found
                }
            } else {
                return 0; // No way to filter by faculty
            }
        }
        
        // Check if is_active column exists
        if ($this->db->field_exists('is_active', 'resources')) {
            $this->db->where('is_active', TRUE);
        }
        return $this->db->count_all_results('resources');
    }
    
    /**
     * Get resource by ID
     */
    public function get_resource($id) {
        $this->db->where('id', $id);
        $query = $this->db->get('resources');
        return $query->row();
    }
    
    /**
     * Update resource
     */
    public function update_resource($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('resources', $data);
    }
    
    /**
     * Delete resource (soft delete)
     */
    public function delete_resource($id) {
        // Check if is_active column exists
        if ($this->db->field_exists('is_active', 'resources')) {
            $this->db->where('id', $id);
            return $this->db->update('resources', ['is_active' => FALSE]);
        } else {
            // Hard delete if no is_active column
            $this->db->where('id', $id);
            return $this->db->delete('resources');
        }
    }
    
    /**
     * Get resources by subject
     */
    public function get_resources_by_subject($subject_id) {
        try {
            // Determine which date column to use and create alias
            $date_column = '';
            if ($this->db->field_exists('upload_date', 'resources')) {
                $date_column = 'r.upload_date as upload_date';
            } elseif ($this->db->field_exists('created_at', 'resources')) {
                $date_column = 'r.created_at as upload_date';
            }
            
            $select_fields = 'r.id, r.title, r.description, r.file_type, r.file_path, r.file_size, r.subject_id, s.subject_name, s.subject_code, u.username as faculty_name';
            if ($date_column) {
                $select_fields .= ', ' . $date_column;
            }
            if ($this->db->field_exists('is_active', 'resources')) {
                $select_fields .= ', r.is_active';
            }
            if ($this->db->field_exists('faculty_id', 'resources')) {
                $select_fields .= ', r.faculty_id';
            }
            
            $this->db->select($select_fields);
            $this->db->from('resources r');
            $this->db->join('subjects s', 'r.subject_id = s.id');
            
            // Handle faculty join based on available columns - use LEFT JOIN to include resources without faculty
            if ($this->db->field_exists('faculty_id', 'resources')) {
                $this->db->join('faculty f', 'r.faculty_id = f.id', 'left');
                $this->db->join('users u', 'f.user_id = u.id', 'left');
            } else if ($this->db->field_exists('uploaded_by', 'resources')) {
                $this->db->join('users u', 'r.uploaded_by = u.id', 'left');
            }
            
            $this->db->where('r.subject_id', $subject_id);
            
            // Check if is_active column exists
            if ($this->db->field_exists('is_active', 'resources')) {
                $this->db->where('r.is_active', TRUE);
            }
            
            // Check which date column exists and use it for ordering
            if ($this->db->field_exists('upload_date', 'resources')) {
                $this->db->order_by('r.upload_date', 'DESC');
            } elseif ($this->db->field_exists('created_at', 'resources')) {
                $this->db->order_by('r.created_at', 'DESC');
            } else {
                $this->db->order_by('r.id', 'DESC'); // Fallback to ID
            }
            
            return $this->db->get()->result();
            
        } catch (Exception $e) {
            log_message('error', 'get_resources_by_subject error: ' . $e->getMessage());
            return array();
        }
    }
    
    /**
     * Get all active resources
     */
    public function get_all_resources() {
        try {
            // Determine which date column to use and create alias
            $date_column = '';
            if ($this->db->field_exists('upload_date', 'resources')) {
                $date_column = 'r.upload_date as upload_date';
            } elseif ($this->db->field_exists('created_at', 'resources')) {
                $date_column = 'r.created_at as upload_date';
            }
            
            $select_fields = 'r.id, r.title, r.description, r.file_type, r.file_path, r.file_size, r.subject_id, s.subject_name, s.subject_code, s.semester';
            if ($date_column) {
                $select_fields .= ', ' . $date_column;
            }
            if ($this->db->field_exists('is_active', 'resources')) {
                $select_fields .= ', r.is_active';
            }
            if ($this->db->field_exists('faculty_id', 'resources')) {
                $select_fields .= ', r.faculty_id';
            }
            
            $this->db->select($select_fields);
            $this->db->from('resources r');
            $this->db->join('subjects s', 'r.subject_id = s.id', 'left');
            
            // Check if is_active column exists
            if ($this->db->field_exists('is_active', 'resources')) {
                $this->db->where('r.is_active', TRUE);
            }
            
            // Check which date column exists and use it for ordering
            if ($this->db->field_exists('upload_date', 'resources')) {
                $this->db->order_by('r.upload_date', 'DESC');
            } elseif ($this->db->field_exists('created_at', 'resources')) {
                $this->db->order_by('r.created_at', 'DESC');
            } else {
                $this->db->order_by('r.id', 'DESC'); // Fallback to ID
            }
            
            return $this->db->get()->result();
            
        } catch (Exception $e) {
            log_message('error', 'get_all_resources error: ' . $e->getMessage());
            return array();
        }
    }
    
    /**
     * Count resources by subject
     */
    public function count_resources_by_subject($subject_id) {
        $this->db->where('subject_id', $subject_id);
        
        // Check if is_active column exists
        if ($this->db->field_exists('is_active', 'resources')) {
            $this->db->where('is_active', TRUE);
        }
        
        return $this->db->count_all_results('resources');
    }
}