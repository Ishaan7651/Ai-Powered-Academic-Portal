<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AI_buddy_model extends CI_Model {
    
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    
    // ==================== CHAT SESSIONS ====================
    
    /**
     * Create new chat session
     */
    public function create_chat_session($data) {
        return $this->db->insert('ai_chat_sessions', $data);
    }
    
    /**
     * Get chat sessions by user
     */
    public function get_user_chat_sessions($user_id, $active_only = true) {
        $this->db->select('cs.*, r.title as resource_title, r.file_type');
        $this->db->from('ai_chat_sessions cs');
        $this->db->join('resources r', 'cs.resource_id = r.id', 'left');
        $this->db->where('cs.user_id', $user_id);
        
        if ($active_only) {
            $this->db->where('cs.is_active', TRUE);
        }
        
        $this->db->order_by('cs.updated_at', 'DESC');
        return $this->db->get()->result();
    }
    
    /**
     * Get chat session by ID
     */
    public function get_chat_session($session_id) {
        $this->db->select('cs.*, r.title as resource_title, r.file_path, r.file_type');
        $this->db->from('ai_chat_sessions cs');
        $this->db->join('resources r', 'cs.resource_id = r.id', 'left');
        $this->db->where('cs.id', $session_id);
        return $this->db->get()->row();
    }
    
    /**
     * Update chat session
     */
    public function update_chat_session($session_id, $data) {
        $this->db->where('id', $session_id);
        return $this->db->update('ai_chat_sessions', $data);
    }
    
    /**
     * Delete chat session
     */
    public function delete_chat_session($session_id) {
        $this->db->where('id', $session_id);
        return $this->db->update('ai_chat_sessions', ['is_active' => FALSE]);
    }
    
    // ==================== CHAT MESSAGES ====================
    
    /**
     * Add message to chat session
     */
    public function add_chat_message($data) {
        return $this->db->insert('ai_chat_messages', $data);
    }
    
    /**
     * Get chat messages for session
     */
    public function get_chat_messages($session_id, $limit = 50) {
        $this->db->where('session_id', $session_id);
        $this->db->order_by('created_at', 'ASC');
        $this->db->limit($limit);
        return $this->db->get('ai_chat_messages')->result();
    }
    
    /**
     * Get recent messages for context
     */
    public function get_recent_messages($session_id, $limit = 10) {
        $this->db->where('session_id', $session_id);
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit($limit);
        $messages = $this->db->get('ai_chat_messages')->result();
        return array_reverse($messages); // Return in chronological order
    }
    
    // ==================== QUIZZES ====================
    
    /**
     * Create quiz
     */
    public function create_quiz($data) {
        return $this->db->insert('ai_quizzes', $data);
    }
    
    /**
     * Get user quizzes
     */
    public function get_user_quizzes($user_id) {
        $this->db->select('q.*, r.title as resource_title');
        $this->db->from('ai_quizzes q');
        $this->db->join('resources r', 'q.resource_id = r.id', 'left');
        $this->db->where('q.user_id', $user_id);
        $this->db->order_by('q.created_at', 'DESC');
        return $this->db->get()->result();
    }
    
    /**
     * Get quiz by ID
     */
    public function get_quiz($quiz_id) {
        $this->db->select('q.*, r.title as resource_title, r.file_path');
        $this->db->from('ai_quizzes q');
        $this->db->join('resources r', 'q.resource_id = r.id', 'left');
        $this->db->where('q.id', $quiz_id);
        return $this->db->get()->row();
    }
    
    /**
     * Delete quiz
     */
    public function delete_quiz($quiz_id) {
        $this->db->where('id', $quiz_id);
        return $this->db->delete('ai_quizzes');
    }
    
    // ==================== QUESTION PAPERS ====================
    
    /**
     * Create question paper
     */
    public function create_question_paper($data) {
        return $this->db->insert('ai_question_papers', $data);
    }
    
    /**
     * Get user question papers
     */
    public function get_user_question_papers($user_id) {
        $this->db->select('qp.*, r.title as resource_title, s.subject_name, s.subject_code');
        $this->db->from('ai_question_papers qp');
        $this->db->join('resources r', 'qp.resource_id = r.id', 'left');
        $this->db->join('subjects s', 'qp.subject_id = s.id', 'left');
        $this->db->where('qp.user_id', $user_id);
        $this->db->order_by('qp.created_at', 'DESC');
        return $this->db->get()->result();
    }
    
    /**
     * Get question paper by ID
     */
    public function get_question_paper($paper_id) {
        $this->db->select('qp.*, r.title as resource_title, r.file_path, s.subject_name, s.subject_code');
        $this->db->from('ai_question_papers qp');
        $this->db->join('resources r', 'qp.resource_id = r.id', 'left');
        $this->db->join('subjects s', 'qp.subject_id = s.id', 'left');
        $this->db->where('qp.id', $paper_id);
        return $this->db->get()->row();
    }
    
    /**
     * Delete question paper
     */
    public function delete_question_paper($paper_id) {
        $this->db->where('id', $paper_id);
        return $this->db->delete('ai_question_papers');
    }
    
    // ==================== ASSIGNMENTS ====================
    
    /**
     * Create assignment
     */
    public function create_assignment($data) {
        return $this->db->insert('ai_assignments', $data);
    }
    
    /**
     * Get user assignments
     */
    public function get_user_assignments($user_id) {
        $this->db->select('a.*, r.title as resource_title, s.subject_name, s.subject_code');
        $this->db->from('ai_assignments a');
        $this->db->join('resources r', 'a.resource_id = r.id', 'left');
        $this->db->join('subjects s', 'a.subject_id = s.id', 'left');
        $this->db->where('a.user_id', $user_id);
        $this->db->order_by('a.created_at', 'DESC');
        return $this->db->get()->result();
    }
    
    /**
     * Get assignment by ID
     */
    public function get_assignment($assignment_id) {
        $this->db->select('a.*, r.title as resource_title, r.file_path, s.subject_name, s.subject_code');
        $this->db->from('ai_assignments a');
        $this->db->join('resources r', 'a.resource_id = r.id', 'left');
        $this->db->join('subjects s', 'a.subject_id = s.id', 'left');
        $this->db->where('a.id', $assignment_id);
        return $this->db->get()->row();
    }
    
    /**
     * Delete assignment
     */
    public function delete_assignment($assignment_id) {
        $this->db->where('id', $assignment_id);
        return $this->db->delete('ai_assignments');
    }
    
    // ==================== USAGE LOGS ====================
    
    /**
     * Log AI usage
     */
    public function log_usage($data) {
        return $this->db->insert('ai_usage_logs', $data);
    }
    
    /**
     * Get usage statistics
     */
    public function get_usage_stats($user_id, $days = 30) {
        $this->db->select('feature_type, COUNT(*) as count, SUM(tokens_used) as total_tokens');
        $this->db->from('ai_usage_logs');
        $this->db->where('user_id', $user_id);
        $this->db->where('created_at >=', date('Y-m-d H:i:s', strtotime("-{$days} days")));
        $this->db->group_by('feature_type');
        return $this->db->get()->result();
    }
}