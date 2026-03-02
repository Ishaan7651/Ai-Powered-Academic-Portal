<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * AI Buddy Controller
 * Handles AI-powered features like chat, quiz generation, question papers
 */
class AI_buddy extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('AI_buddy_model');
        $this->load->model('Resource_model');
        $this->load->model('Faculty_model');
        $this->load->model('Subject_model');
        $this->load->library('AI_service');
        $this->load->library('session');
        $this->load->helper('url');

        // Check if user is logged in
        if (!$this->is_logged_in()) {
            redirect('simple_portal');
        }
    }

    /**
     * AI Buddy main page
     */
    public function index()
    {
        $data['user_data'] = $this->session->userdata();
        $data['user_role'] = $this->session->userdata('role');

        // Get user's resources based on role
        if ($data['user_role'] === 'faculty') {
            // Faculty should see all resources for quiz/question paper generation
            $data['resources'] = $this->Resource_model->get_all_resources();
            log_message('info', 'Faculty user - showing all resources: ' . count($data['resources']));
        } else {
            // For students, get all available resources from all subjects
            $this->load->model('Subject_model');
            $all_subjects = $this->Subject_model->get_all_subjects();
            $data['resources'] = [];

            foreach ($all_subjects as $subject) {
                $subject_resources = $this->Resource_model->get_resources_by_subject($subject->id);
                $data['resources'] = array_merge($data['resources'], $subject_resources);
            }
            log_message('info', 'Student user - resources from all subjects: ' . count($data['resources']));
        }

        // Get recent chat sessions
        $data['chat_sessions'] = $this->AI_buddy_model->get_user_chat_sessions($this->session->userdata('user_id'));

        // Get recent quizzes
        $data['quizzes'] = $this->AI_buddy_model->get_user_quizzes($this->session->userdata('user_id'));

        // Get recent question papers
        $data['question_papers'] = $this->AI_buddy_model->get_user_question_papers($this->session->userdata('user_id'));

        // Get recent assignments
        $data['assignments'] = $this->AI_buddy_model->get_user_assignments($this->session->userdata('user_id'));

        // Get usage stats
        $data['usage_stats'] = $this->AI_buddy_model->get_usage_stats($this->session->userdata('user_id'));

        $this->load->view('templates/header', $data);
        $this->load->view('simple_portal/dashboard', $data);
        $this->load->view('templates/footer', $data);
    }

    /**
     * Chat interface
     */
    public function chat($session_id = null)
    {
        $data['user_data'] = $this->session->userdata();
        $data['user_role'] = $this->session->userdata('role');

        // For students, redirect to subject selection if no session
        if ($data['user_role'] === 'student' && !$session_id) {
            redirect('ai_buddy/select_subject_for_chat');
            return;
        }

        if ($session_id) {
            // Load existing session
            $session = $this->AI_buddy_model->get_chat_session($session_id);

            if (!$session || $session->user_id != $this->session->userdata('user_id')) {
                $this->session->set_flashdata('error', 'Chat session not found.');
                redirect('ai_buddy');
                return;
            }

            $data['session'] = $session;
            $data['messages'] = $this->AI_buddy_model->get_chat_messages($session_id);
        } else {
            $data['session'] = null;
            $data['messages'] = [];
        }

        // Get user's resources for selection
        if ($data['user_role'] === 'faculty') {
            // Faculty should see all resources for chat functionality
            $data['resources'] = $this->Resource_model->get_all_resources();
            log_message('info', 'Chat: Faculty user - showing all resources: ' . count($data['resources']));
        } else {
            // For students, get all available resources
            $this->load->model('Subject_model');
            $all_subjects = $this->Subject_model->get_all_subjects();
            $data['resources'] = [];

            foreach ($all_subjects as $subject) {
                $subject_resources = $this->Resource_model->get_resources_by_subject($subject->id);
                $data['resources'] = array_merge($data['resources'], $subject_resources);
            }
            log_message('info', 'Chat: Student user - resources from all subjects: ' . count($data['resources']));
        }

        $this->load->view('templates/header', $data);
        $this->load->view('simple_portal/chat', $data);
        $this->load->view('templates/footer', $data);
    }

    /**
     * Select subject for chat (Student only)
     */
    public function select_subject_for_chat()
    {
        $data['user_data'] = $this->session->userdata();
        $data['user_role'] = $this->session->userdata('role');

        // Only for students
        if ($data['user_role'] !== 'student') {
            redirect('ai_buddy/chat');
            return;
        }

        $student_id = $this->session->userdata('user_id');

        // Get student's enrolled subjects grouped by semester
        $data['enrolled_subjects'] = $this->get_student_enrolled_subjects($student_id);

        $this->load->view('templates/header', $data);
        $this->load->view('ai_buddy/select_subject_chat', $data);
        $this->load->view('templates/footer', $data);
    }

    /**
     * Start chat with selected subject (Student only)
     */
    public function start_subject_chat()
    {
        $data['user_data'] = $this->session->userdata();
        $data['user_role'] = $this->session->userdata('role');

        // Only for students
        if ($data['user_role'] !== 'student') {
            redirect('ai_buddy/chat');
            return;
        }

        $subject_id = $this->input->get('subject_id');
        if (!$subject_id) {
            $this->session->set_flashdata('error', 'Please select a subject.');
            redirect('ai_buddy/select_subject_for_chat');
            return;
        }

        // Verify student is enrolled in this subject
        $student_id = $this->session->userdata('user_id');
        if (!$this->is_student_enrolled_in_subject($student_id, $subject_id)) {
            $this->session->set_flashdata('error', 'You are not enrolled in this subject.');
            redirect('ai_buddy/select_subject_for_chat');
            return;
        }

        // Get subject details
        $this->load->model('Subject_model');
        $subject = $this->Subject_model->get_subject($subject_id);

        // Get resources for this subject only
        $data['resources'] = $this->Resource_model->get_resources_by_subject($subject_id);
        $data['subject'] = $subject;
        $data['session'] = null;
        $data['messages'] = [];

        $this->load->view('templates/header', $data);
        $this->load->view('ai_buddy/chat_with_subject', $data);
        $this->load->view('templates/footer', $data);
    }

    /**
     * Create new chat session
     */
    public function create_chat_session()
    {
        if (!$this->input->post()) {
            redirect('ai_buddy');
            return;
        }

        $resource_id = $this->input->post('resource_id');
        $session_name = $this->input->post('session_name') ?: 'New Chat Session';

        $session_data = [
            'user_id' => $this->session->userdata('user_id'),
            'resource_id' => $resource_id ?: null,
            'session_name' => $session_name
        ];

        if ($this->AI_buddy_model->create_chat_session($session_data)) {
            $session_id = $this->db->insert_id();
            $this->session->set_flashdata('success', 'Chat session created successfully!');
            redirect('ai_buddy/chat/' . $session_id);
        } else {
            $this->session->set_flashdata('error', 'Failed to create chat session.');
            redirect('ai_buddy');
        }
    }

    /**
     * Send chat message (AJAX)
     */
    public function send_message()
    {
        header('Content-Type: application/json');

        if (!$this->input->post()) {
            echo json_encode(['success' => false, 'error' => 'Invalid request']);
            return;
        }

        $session_id = $this->input->post('session_id');
        $message = $this->input->post('message');
        $resource_id = $this->input->post('resource_id');

        // If no session_id, create a new session
        if (!$session_id || $session_id == 0) {
            $session_data = [
                'user_id' => $this->session->userdata('user_id'),
                'resource_id' => $resource_id ?: null,
                'session_name' => 'Chat Session - ' . date('Y-m-d H:i:s')
            ];

            if ($this->AI_buddy_model->create_chat_session($session_data)) {
                $session_id = $this->db->insert_id();
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to create chat session']);
                return;
            }
        }

        // Verify session ownership
        $session = $this->AI_buddy_model->get_chat_session($session_id);
        if (!$session || $session->user_id != $this->session->userdata('user_id')) {
            echo json_encode(['success' => false, 'error' => 'Invalid session']);
            return;
        }

        // Save user message
        $this->AI_buddy_model->add_chat_message([
            'session_id' => $session_id,
            'role' => 'user',
            'message' => $message,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        // Get document context if resource is attached
        $context = '';
        if ($session->resource_id) {
            $resource = $this->Resource_model->get_resource($session->resource_id);
            if ($resource && file_exists(FCPATH . $resource->file_path)) {
                log_message('info', 'Extracting text from resource: ' . $resource->file_path);

                $ext = strtolower(pathinfo($resource->file_path, PATHINFO_EXTENSION));
                if ($resource->file_type === 'pdf' || $ext === 'pdf') {
                    $context = $this->ai_service->extract_pdf_text(FCPATH . $resource->file_path);
                } elseif (in_array($ext, ['docx', 'doc'])) {
                    $context = $this->ai_service->extract_docx_text(FCPATH . $resource->file_path);
                } elseif ($ext === 'pptx') {
                    $context = $this->ai_service->extract_pptx_text(FCPATH . $resource->file_path);
                } elseif ($ext === 'ppt') {
                    $context = $this->ai_service->extract_ppt_text(FCPATH . $resource->file_path);
                } else {
                    $context = file_get_contents(FCPATH . $resource->file_path);
                }

                log_message('info', 'Extracted context length: ' . strlen($context));
                log_message('debug', 'Context preview: ' . substr($context, 0, 200));
            } else {
                log_message('error', 'Resource file not found: ' . ($resource ? $resource->file_path : 'resource not found'));
            }
        }

        // Get recent conversation history
        $recent_messages = $this->AI_buddy_model->get_recent_messages($session_id, 10);
        log_message('info', 'Chat: Retrieved ' . count($recent_messages) . ' recent messages for session ' . $session_id);

        // Ensure the current user message is included (it may not be if DB retrieval was faster than commit)
        $current_msg_included = false;
        foreach ($recent_messages as $msg) {
            if ($msg->role === 'user' && $msg->message === $message) {
                $current_msg_included = true;
                break;
            }
        }

        // If current message not found in recent history, add it manually
        if (!$current_msg_included) {
            $current_msg = new stdClass();
            $current_msg->role = 'user';
            $current_msg->message = $message;
            $recent_messages[] = $current_msg;
            log_message('info', 'Chat: Added current message manually to messages array');
        }

        // Build system prompt with context
        $system_prompt = "You are an AI teaching assistant helping with educational content.";
        if ($context && strlen(trim($context)) > 20) {
            $system_prompt .= "\n\nDocument Context:\n" . substr($context, 0, 3000); // Limit context size
            log_message('info', 'Using document context in AI prompt');
        } else {
            log_message('warning', 'No document context available or context too short');
            $system_prompt .= "\n\nNote: No specific document context is available. Please provide general educational assistance.";
        }

        // Get AI response
        $ai_response = $this->ai_service->chat($recent_messages, $system_prompt);

        if ($ai_response['success']) {
            // Save AI response
            $this->AI_buddy_model->add_chat_message([
                'session_id' => $session_id,
                'role' => 'assistant',
                'message' => $ai_response['content']
            ]);

            // Log usage
            $this->AI_buddy_model->log_usage([
                'user_id' => $this->session->userdata('user_id'),
                'feature_type' => 'chat',
                'resource_id' => $session->resource_id,
                'tokens_used' => $ai_response['tokens'] ?? 0
            ]);

            // Update session timestamp
            $this->AI_buddy_model->update_chat_session($session_id, ['updated_at' => date('Y-m-d H:i:s')]);

            log_message('info', 'AI response successful, length: ' . strlen($ai_response['content']));

            echo json_encode([
                'success' => true,
                'message' => $ai_response['content'],
                'session_id' => $session_id
            ]);
        } else {
            log_message('error', 'AI response failed: ' . ($ai_response['error'] ?? 'Unknown error'));
            echo json_encode([
                'success' => false,
                'error' => $ai_response['error'] ?? 'Failed to get AI response'
            ]);
        }
    }

    /**
     * Generate quiz page (Faculty only)
     */
    public function generate_quiz($resource_id = null)
    {
        $data['user_data'] = $this->session->userdata();
        $data['user_role'] = $this->session->userdata('role');

        // Restrict to faculty only
        if ($data['user_role'] !== 'faculty') {
            $this->session->set_flashdata('error', 'Access denied. Quiz generation is available for faculty only.');
            redirect('');
            return;
        }

        // Get faculty resources and subjects - show all resources for quiz generation
        $data['resources'] = $this->Resource_model->get_all_resources();
        $data['subjects'] = $this->Subject_model->get_all_subjects();
        log_message('info', 'Quiz: Faculty user - showing all resources: ' . count($data['resources']));

        $data['selected_resource_id'] = $resource_id;

        $this->load->view('simple_portal/generate_quiz', $data);
    }

    /**
     * Process quiz generation (Faculty only)
     */
    public function process_quiz_generation()
    {
        header('Content-Type: application/json');

        // Restrict to faculty only
        if ($this->session->userdata('role') !== 'faculty') {
            echo json_encode(['success' => false, 'error' => 'Access denied. Quiz generation is available for faculty only.']);
            return;
        }

        try {
            if (!$this->input->post()) {
                echo json_encode(['success' => false, 'error' => 'Invalid request']);
                return;
            }

            $resource_ids = $this->input->post('resource_ids');
            if (empty($resource_ids) || !is_array($resource_ids)) {
                echo json_encode(['success' => false, 'error' => 'Please select at least one resource']);
                return;
            }

            $num_questions = $this->input->post('num_questions') ?: 10;
            $difficulty = $this->input->post('difficulty') ?: 'medium';
            $title = $this->input->post('title') ?: 'Generated Quiz';

            // Get content from all selected resources
            $combined_content = '';
            $resource_titles = [];

            foreach ($resource_ids as $resource_id) {
                $resource = $this->Resource_model->get_resource($resource_id);
                if (!$resource) {
                    echo json_encode(['success' => false, 'error' => 'Resource not found: ID ' . $resource_id]);
                    return;
                }

                $file_path = FCPATH . $resource->file_path;
                if (!file_exists($file_path)) {
                    echo json_encode(['success' => false, 'error' => 'Resource file not found: ' . $resource->file_path]);
                    return;
                }

                // Extract content
                $ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
                if ($resource->file_type === 'pdf' || $ext === 'pdf') {
                    $content = $this->ai_service->extract_pdf_text($file_path);
                } elseif (in_array($ext, ['docx', 'doc'])) {
                    $content = $this->ai_service->extract_docx_text($file_path);
                } elseif ($ext === 'pptx') {
                    $content = $this->ai_service->extract_pptx_text($file_path);
                } elseif ($ext === 'ppt') {
                    $content = $this->ai_service->extract_ppt_text($file_path);
                } else {
                    $content = file_get_contents($file_path);
                }

                if (!empty(trim($content))) {
                    $combined_content .= "\n\n=== " . $resource->title . " ===\n" . $content;
                    $resource_titles[] = $resource->title;
                }
            }

            // Check if any content was extracted
            if (empty(trim($combined_content))) {
                echo json_encode(['success' => false, 'error' => 'Could not extract content from any selected files']);
                return;
            }

            log_message('info', 'Quiz generation: Combined content from ' . count($resource_titles) . ' resources, total length: ' . strlen($combined_content));

            // Limit content size for API
            if (strlen($combined_content) > 15000) {
                $combined_content = substr($combined_content, 0, 15000) . '...';
                log_message('info', 'Content truncated to 15000 characters for API limits');
            }

            // Generate quiz with retry mechanism
            $ai_response = $this->generate_quiz_with_retry($combined_content, $num_questions, $difficulty, $title);

        } catch (Exception $e) {
            log_message('error', 'Quiz generation error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
            return;
        }

        if ($ai_response['success']) {
            try {
                // Clean and validate JSON before saving
                $cleaned_json = $this->clean_ai_json_response($ai_response['content']);

                // Check if cleaning failed
                if ($cleaned_json === false) {
                    echo json_encode([
                        'success' => false,
                        'error' => 'Failed to parse AI response as valid JSON. The response may be truncated or malformed.',
                        'suggestion' => 'Try generating the quiz again with fewer questions or simpler content.',
                        'raw_response' => substr($ai_response['content'], 0, 300)
                    ]);
                    return;
                }

                // Validate that it's proper JSON
                $test_decode = json_decode($cleaned_json, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    echo json_encode([
                        'success' => false,
                        'error' => 'Invalid JSON from AI: ' . json_last_error_msg(),
                        'suggestion' => 'Try generating the quiz again. The AI response may have been corrupted.',
                        'raw_response' => substr($ai_response['content'], 0, 300)
                    ]);
                    return;
                }

                // Save quiz
                $quiz_data = [
                    'user_id' => $this->session->userdata('user_id'),
                    'resource_id' => $resource_ids[0], // Store first resource ID for compatibility
                    'title' => $title,
                    'difficulty' => $difficulty,
                    'num_questions' => $num_questions,
                    'quiz_data' => $cleaned_json
                ];

                // Check if tables exist
                if (!$this->db->table_exists('ai_quizzes')) {
                    echo json_encode([
                        'success' => false,
                        'error' => 'Database tables not created. Please run setup_ai_buddy_tables.php first.'
                    ]);
                    return;
                }

                if ($this->AI_buddy_model->create_quiz($quiz_data)) {
                    $quiz_id = $this->db->insert_id();

                    // Log usage (check if table exists first)
                    if ($this->db->table_exists('ai_usage_logs')) {
                        $this->AI_buddy_model->log_usage([
                            'user_id' => $this->session->userdata('user_id'),
                            'feature_type' => 'quiz',
                            'resource_id' => $resource_ids[0], // Log first resource ID
                            'tokens_used' => $ai_response['tokens'] ?? 0
                        ]);
                    }

                    echo json_encode([
                        'success' => true,
                        'quiz_id' => $quiz_id,
                        'quiz_data' => $test_decode
                    ]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Failed to save quiz to database']);
                }
            } catch (Exception $e) {
                log_message('error', 'Quiz save error: ' . $e->getMessage());
                echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
            }
        } else {
            echo json_encode(['success' => false, 'error' => $ai_response['error'] ?? 'AI generation failed']);
        }
    }

    /**
     * Generate question paper page (Faculty only)
     */
    public function generate_question_paper($resource_id = null)
    {
        $data['user_data'] = $this->session->userdata();
        $data['user_role'] = $this->session->userdata('role');

        // Restrict to faculty only
        if ($data['user_role'] !== 'faculty') {
            $this->session->set_flashdata('error', 'Access denied. Question paper generation is available for faculty only.');
            redirect('');
            return;
        }

        // Get faculty resources and subjects - show all resources for question paper generation
        $data['resources'] = $this->Resource_model->get_all_resources();
        $data['subjects'] = $this->Subject_model->get_all_subjects();
        log_message('info', 'Question Paper: Faculty user - showing all resources: ' . count($data['resources']));

        $data['selected_resource_id'] = $resource_id;

        $this->load->view('simple_portal/generate_question_paper', $data);
    }

    /**
     * Process question paper generation (Faculty only)
     */
    public function process_question_paper_generation()
    {
        header('Content-Type: application/json');

        // Increase execution time for question paper generation
        set_time_limit(60); // 60 seconds instead of default 30

        // Restrict to faculty only
        if ($this->session->userdata('role') !== 'faculty') {
            echo json_encode(['success' => false, 'error' => 'Access denied. Question paper generation is available for faculty only.']);
            return;
        }

        try {
            if (!$this->input->post()) {
                echo json_encode(['success' => false, 'error' => 'Invalid request']);
                return;
            }

            $resource_ids = $this->input->post('resource_ids');
            if (empty($resource_ids) || !is_array($resource_ids)) {
                echo json_encode(['success' => false, 'error' => 'Please select at least one resource']);
                return;
            }

            $title = $this->input->post('title') ?: 'Generated Question Paper';
            $subject_id = $this->input->post('subject_id');
            $total_marks = $this->input->post('total_marks') ?: 100;
            $duration_minutes = $this->input->post('duration_minutes') ?: 180;

            // Get sections configuration
            $sections = $this->input->post('sections') ?: [];

            // Get content from all selected resources
            $combined_content = '';
            $resource_titles = [];

            foreach ($resource_ids as $resource_id) {
                $resource = $this->Resource_model->get_resource($resource_id);
                if (!$resource) {
                    echo json_encode(['success' => false, 'error' => 'Resource not found: ID ' . $resource_id]);
                    return;
                }

                $file_path = FCPATH . $resource->file_path;
                if (!file_exists($file_path)) {
                    echo json_encode(['success' => false, 'error' => 'Resource file not found: ' . $resource->file_path]);
                    return;
                }

                // Extract content
                $ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
                if ($resource->file_type === 'pdf' || $ext === 'pdf') {
                    $content = $this->ai_service->extract_pdf_text($file_path);
                } elseif (in_array($ext, ['docx', 'doc'])) {
                    $content = $this->ai_service->extract_docx_text($file_path);
                } elseif ($ext === 'pptx') {
                    $content = $this->ai_service->extract_pptx_text($file_path);
                } elseif ($ext === 'ppt') {
                    $content = $this->ai_service->extract_ppt_text($file_path);
                } else {
                    $content = file_get_contents($file_path);
                }

                if (!empty(trim($content))) {
                    $combined_content .= "\n\n=== " . $resource->title . " ===\n" . $content;
                    $resource_titles[] = $resource->title;
                }
            }

            // Check if any content was extracted
            if (empty(trim($combined_content))) {
                echo json_encode(['success' => false, 'error' => 'Could not extract content from any selected files']);
                return;
            }

            // Limit content size for API - more aggressive for question papers
            if (strlen($combined_content) > 8000) {
                $combined_content = substr($combined_content, 0, 8000);
                log_message('info', 'Content truncated to 8000 characters for question paper generation to prevent timeouts');
            }

            // Prepare configuration
            $config = [
                'title' => $title,
                'total_marks' => $total_marks,
                'duration_minutes' => $duration_minutes,
                'sections' => $sections
            ];

            log_message('info', 'Question paper generation: Combined content from ' . count($resource_titles) . ' resources, total length: ' . strlen($combined_content));

            // Generate question paper with retry mechanism
            $ai_response = $this->generate_question_paper_with_retry($combined_content, $config);

        } catch (Exception $e) {
            log_message('error', 'Question paper generation error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            echo json_encode([
                'success' => false,
                'error' => 'Server error during question paper generation: ' . $e->getMessage(),
                'suggestion' => 'Please check the error logs and try again with simpler content.'
            ]);
            return;
        }

        if ($ai_response['success']) {
            try {
                // Clean and validate JSON before saving
                $cleaned_json = $this->clean_ai_json_response($ai_response['content']);

                // Check if cleaning failed
                if ($cleaned_json === false) {
                    echo json_encode([
                        'success' => false,
                        'error' => 'Failed to parse AI response as valid JSON. The response may be truncated or malformed.',
                        'suggestion' => 'Try generating the question paper again with fewer sections or simpler content.',
                        'raw_response' => substr($ai_response['content'], 0, 300)
                    ]);
                    return;
                }

                // Validate that it's proper JSON
                $test_decode = json_decode($cleaned_json, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    echo json_encode([
                        'success' => false,
                        'error' => 'Invalid JSON from AI: ' . json_last_error_msg(),
                        'suggestion' => 'Try generating the question paper again. The AI response may have been corrupted.',
                        'raw_response' => substr($ai_response['content'], 0, 300)
                    ]);
                    return;
                }

                // Save question paper
                $paper_data = [
                    'user_id' => $this->session->userdata('user_id'),
                    'resource_id' => $resource_ids[0], // Store first resource ID for compatibility
                    'subject_id' => $subject_id,
                    'title' => $title,
                    'total_marks' => $total_marks,
                    'duration_minutes' => $duration_minutes,
                    'paper_data' => $cleaned_json
                ];

                // Check if tables exist
                if (!$this->db->table_exists('ai_question_papers')) {
                    echo json_encode([
                        'success' => false,
                        'error' => 'Database tables not created. Please run setup_ai_buddy_tables.php first.'
                    ]);
                    return;
                }

                if ($this->AI_buddy_model->create_question_paper($paper_data)) {
                    $paper_id = $this->db->insert_id();

                    // Log usage (check if table exists first)
                    if ($this->db->table_exists('ai_usage_logs')) {
                        $this->AI_buddy_model->log_usage([
                            'user_id' => $this->session->userdata('user_id'),
                            'feature_type' => 'question_paper',
                            'resource_id' => $resource_ids[0], // Log first resource ID
                            'tokens_used' => $ai_response['tokens'] ?? 0
                        ]);
                    }

                    echo json_encode([
                        'success' => true,
                        'paper_id' => $paper_id,
                        'paper_data' => $test_decode
                    ]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Failed to save question paper to database']);
                }
            } catch (Exception $e) {
                log_message('error', 'Question paper save error: ' . $e->getMessage());
                echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
            }
        } else {
            echo json_encode(['success' => false, 'error' => $ai_response['error'] ?? 'AI generation failed']);
        }
    }

    /**
     * Generate assignment page (Faculty only)
     */
    public function generate_assignment($resource_id = null)
    {
        $data['user_data'] = $this->session->userdata();
        $data['user_role'] = $this->session->userdata('role');

        // Restrict to faculty only
        if ($data['user_role'] !== 'faculty') {
            $this->session->set_flashdata('message_type', 'error');
            $this->session->set_flashdata('message', 'Access denied. Please login as faculty to access AI features.');
            redirect('simple_portal');
            return;
        }

        // Get all resources for assignment generation
        $data['resources'] = $this->Resource_model->get_all_resources();
        $data['subjects'] = $this->Subject_model->get_all_subjects();
        log_message('info', 'Assignment: Faculty user - showing all resources: ' . count($data['resources']));

        $data['selected_resource_id'] = $resource_id;

        $this->load->view('simple_portal/generate_assignment', $data);
    }

    /**
     * Process assignment generation (Faculty only)
     */
    public function process_assignment_generation()
    {
        header('Content-Type: application/json');

        // Restrict to faculty only
        if ($this->session->userdata('role') !== 'faculty') {
            echo json_encode(['success' => false, 'error' => 'Access denied. Assignment generation is available for faculty only.']);
            return;
        }

        try {
            if (!$this->input->post()) {
                echo json_encode(['success' => false, 'error' => 'Invalid request']);
                return;
            }

            $resource_ids = $this->input->post('resource_ids');
            if (empty($resource_ids) || !is_array($resource_ids)) {
                echo json_encode(['success' => false, 'error' => 'Please select at least one resource']);
                return;
            }

            $title = $this->input->post('title') ?: 'Generated Assignment';
            $subject_id = $this->input->post('subject_id');
            $assignment_type = $this->input->post('assignment_type') ?: 'research';
            $difficulty = $this->input->post('difficulty') ?: 'medium';
            $word_count = $this->input->post('word_count') ?: 1000;
            $due_weeks = $this->input->post('due_weeks') ?: 2;

            // Get content from all selected resources
            $combined_content = '';
            $resource_titles = [];

            foreach ($resource_ids as $resource_id) {
                $resource = $this->Resource_model->get_resource($resource_id);
                if (!$resource) {
                    echo json_encode(['success' => false, 'error' => 'Resource not found: ID ' . $resource_id]);
                    return;
                }

                $file_path = FCPATH . $resource->file_path;
                if (!file_exists($file_path)) {
                    echo json_encode(['success' => false, 'error' => 'Resource file not found: ' . $resource->file_path]);
                    return;
                }

                // Extract content
                $ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
                if ($resource->file_type === 'pdf' || $ext === 'pdf') {
                    $content = $this->ai_service->extract_pdf_text($file_path);
                } elseif (in_array($ext, ['docx', 'doc'])) {
                    $content = $this->ai_service->extract_docx_text($file_path);
                } elseif ($ext === 'pptx') {
                    $content = $this->ai_service->extract_pptx_text($file_path);
                } elseif ($ext === 'ppt') {
                    $content = $this->ai_service->extract_ppt_text($file_path);
                } else {
                    $content = file_get_contents($file_path);
                }

                if (!empty(trim($content))) {
                    $combined_content .= "\n\n=== " . $resource->title . " ===\n" . $content;
                    $resource_titles[] = $resource->title;
                }
            }

            // Check if any content was extracted
            if (empty(trim($combined_content))) {
                echo json_encode(['success' => false, 'error' => 'Could not extract content from any selected files']);
                return;
            }

            // Limit content size for API
            if (strlen($combined_content) > 10000) {
                $combined_content = substr($combined_content, 0, 10000) . '...';
                log_message('info', 'Content truncated to 10000 characters for assignment generation');
            }

            log_message('info', 'Assignment generation: Combined content from ' . count($resource_titles) . ' resources, total length: ' . strlen($combined_content));

            // Generate assignment with retry mechanism
            $ai_response = $this->generate_assignment_with_retry($combined_content, $assignment_type, $difficulty, $word_count, $due_weeks, $title);

        } catch (Exception $e) {
            log_message('error', 'Assignment generation error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
            return;
        }

        if ($ai_response['success']) {
            try {
                // Clean and validate JSON before saving
                $cleaned_json = $this->clean_ai_json_response($ai_response['content']);

                // Check if cleaning failed
                if ($cleaned_json === false) {
                    echo json_encode([
                        'success' => false,
                        'error' => 'Failed to parse AI response as valid JSON. The response may be truncated or malformed.',
                        'suggestion' => 'Try generating the assignment again with simpler parameters.',
                        'raw_response' => substr($ai_response['content'], 0, 300)
                    ]);
                    return;
                }

                // Validate that it's proper JSON
                $test_decode = json_decode($cleaned_json, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    echo json_encode([
                        'success' => false,
                        'error' => 'Invalid JSON from AI: ' . json_last_error_msg(),
                        'suggestion' => 'Try generating the assignment again. The AI response may have been corrupted.',
                        'raw_response' => substr($ai_response['content'], 0, 300)
                    ]);
                    return;
                }

                // Save assignment
                $assignment_data = [
                    'user_id' => $this->session->userdata('user_id'),
                    'resource_id' => $resource_ids[0], // Store first resource ID for compatibility
                    'subject_id' => $subject_id,
                    'title' => $title,
                    'assignment_type' => $assignment_type,
                    'difficulty' => $difficulty,
                    'word_count' => $word_count,
                    'due_weeks' => $due_weeks,
                    'assignment_data' => $cleaned_json
                ];

                // Check if tables exist
                if (!$this->db->table_exists('ai_assignments')) {
                    echo json_encode([
                        'success' => false,
                        'error' => 'Database tables not created. Please run setup_ai_buddy_tables.php first.'
                    ]);
                    return;
                }

                if ($this->AI_buddy_model->create_assignment($assignment_data)) {
                    $assignment_id = $this->db->insert_id();

                    // Log usage (check if table exists first)
                    if ($this->db->table_exists('ai_usage_logs')) {
                        $this->AI_buddy_model->log_usage([
                            'user_id' => $this->session->userdata('user_id'),
                            'feature_type' => 'assignment',
                            'resource_id' => $resource_ids[0], // Log first resource ID
                            'tokens_used' => $ai_response['tokens'] ?? 0
                        ]);
                    }

                    echo json_encode([
                        'success' => true,
                        'assignment_id' => $assignment_id,
                        'assignment_data' => $test_decode
                    ]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Failed to save assignment to database']);
                }
            } catch (Exception $e) {
                log_message('error', 'Assignment save error: ' . $e->getMessage());
                echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
            }
        } else {
            echo json_encode(['success' => false, 'error' => $ai_response['error'] ?? 'AI generation failed']);
        }
    }

    /**
     * Generate question paper with retry mechanism for better reliability
     */
    private function generate_question_paper_with_retry($content, $config)
    {
        $max_attempts = 2; // Reduced from 3 to 2 to prevent timeout accumulation
        $attempt = 1;

        while ($attempt <= $max_attempts) {
            log_message('debug', "Question paper generation attempt $attempt of $max_attempts");

            // Adjust parameters for retry attempts
            if ($attempt > 1) {
                // Reduce complexity significantly for retry attempts
                $content = substr($content, 0, 4000); // Much shorter content
                log_message('debug', "Retry with reduced content: " . strlen($content) . " chars");

                // Simplify config for retry
                if (isset($config['sections']) && count($config['sections']) > 2) {
                    $config['sections'] = array_slice($config['sections'], 0, 2); // Only first 2 sections
                }
            }

            $ai_response = $this->ai_service->generate_question_paper($content, $config);

            if ($ai_response['success']) {
                // Test if the response is complete and valid
                $test_clean = $this->clean_ai_json_response($ai_response['content']);

                if ($test_clean !== false) {
                    $test_decode = json_decode($test_clean, true);
                    if (json_last_error() === JSON_ERROR_NONE && isset($test_decode['sections']) && !empty($test_decode['sections'])) {
                        log_message('debug', "Question paper generation successful on attempt $attempt");
                        return $ai_response;
                    }
                }

                log_message('warning', "Attempt $attempt produced invalid JSON, retrying...");
            } else {
                log_message('error', "Attempt $attempt failed with error: " . ($ai_response['error'] ?? 'Unknown error'));

                // If it's a timeout or API error, don't retry immediately
                if (
                    strpos($ai_response['error'] ?? '', 'timeout') !== false ||
                    strpos($ai_response['error'] ?? '', 'unavailable') !== false
                ) {
                    log_message('error', "API timeout detected, stopping retries to prevent further timeouts");
                    break;
                }
            }

            $attempt++;

            // Small delay between attempts
            if ($attempt <= $max_attempts) {
                sleep(2); // Increased delay to let API recover
            }
        }

        // All attempts failed, return error
        return [
            'success' => false,
            'error' => "Failed to generate valid question paper after $max_attempts attempts. The AI service may be experiencing high load. Please try again in a few minutes with simpler content."
        ];
    }

    /**
     * Generate assignment with retry mechanism for better reliability
     */
    private function generate_assignment_with_retry($content, $type, $difficulty, $word_count, $due_weeks, $title)
    {
        $max_attempts = 3;
        $attempt = 1;

        while ($attempt <= $max_attempts) {
            log_message('debug', "Assignment generation attempt $attempt of $max_attempts");

            // Adjust parameters for retry attempts
            if ($attempt > 1) {
                // Reduce complexity for retry attempts
                $word_count = max(500, intval($word_count / 2)); // Shorter assignment
                $content = substr($content, 0, 6000); // Shorter content
                log_message('debug', "Retry with reduced parameters: $word_count words, " . strlen($content) . " chars content");
            }

            $ai_response = $this->ai_service->generate_assignment($content, $type, $difficulty, $word_count, $due_weeks, $title);

            if ($ai_response['success']) {
                // Test if the response is complete and valid
                $test_clean = $this->clean_ai_json_response($ai_response['content']);

                if ($test_clean !== false) {
                    $test_decode = json_decode($test_clean, true);
                    if (json_last_error() === JSON_ERROR_NONE && isset($test_decode['assignment']) && !empty($test_decode['assignment'])) {
                        log_message('debug', "Assignment generation successful on attempt $attempt");
                        return $ai_response;
                    }
                }

                log_message('warning', "Attempt $attempt produced invalid JSON, retrying...");
            } else {
                log_message('error', "Attempt $attempt failed with error: " . ($ai_response['error'] ?? 'Unknown error'));
            }

            $attempt++;

            // Small delay between attempts
            if ($attempt <= $max_attempts) {
                sleep(1);
            }
        }

        // All attempts failed, return error
        return [
            'success' => false,
            'error' => "Failed to generate valid assignment after $max_attempts attempts. Please try with simpler content or parameters."
        ];
    }

    private function is_logged_in()
    {
        return $this->session->userdata('user_id') !== NULL &&
            $this->session->userdata('logged_in') === TRUE;
    }

    /**
     * Clean JSON response from AI (remove markdown formatting and control characters)
     */
    private function clean_ai_json_response($content)
    {
        // Log original content for debugging
        log_message('debug', 'Original AI response length: ' . strlen($content));
        log_message('debug', 'Original content (first 200 chars): ' . substr($content, 0, 200));

        // Remove markdown code blocks
        $content = preg_replace('/```json\s*/', '', $content);
        $content = preg_replace('/```\s*$/', '', $content);
        $content = preg_replace('/```/', '', $content);

        // Decode HTML entities that might be present
        $content = html_entity_decode($content, ENT_QUOTES, 'UTF-8');

        // Remove control characters (except newlines and tabs that are valid in JSON)
        $content = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $content);

        // Remove any leading/trailing whitespace
        $content = trim($content);

        // Check if response appears truncated
        $is_truncated = false;
        if (!empty($content)) {
            $last_char = substr($content, -1);
            $brace_count = substr_count($content, '{') - substr_count($content, '}');
            $bracket_count = substr_count($content, '[') - substr_count($content, ']');

            if ($last_char !== '}' || $brace_count > 0 || $bracket_count > 0) {
                $is_truncated = true;
                log_message('error', 'AI response appears truncated. Last char: ' . $last_char . ', Brace diff: ' . $brace_count . ', Bracket diff: ' . $bracket_count);
            }
        }

        // Try to find JSON content between braces
        if (preg_match('/\{.*\}/s', $content, $matches)) {
            $content = $matches[0];
        }

        // If truncated, try to fix common truncation issues
        if ($is_truncated) {
            // Try to close incomplete JSON structures
            $content = $this->attempt_json_repair($content);
        }

        // Additional cleaning for common issues
        $content = str_replace(['\u0000', '\u0001', '\u0002', '\u0003', '\u0004', '\u0005', '\u0006', '\u0007'], '', $content);

        // Try to decode and validate
        $decoded = json_decode($content, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            log_message('debug', 'JSON cleaning successful');
            // Re-encode to ensure clean formatting
            return json_encode($decoded, JSON_UNESCAPED_UNICODE);
        }

        // If still invalid, try more aggressive cleaning
        $content = mb_convert_encoding($content, 'UTF-8', 'UTF-8');
        $decoded = json_decode($content, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            log_message('debug', 'JSON fixed with encoding conversion');
            return json_encode($decoded, JSON_UNESCAPED_UNICODE);
        }

        // Log detailed error information
        log_message('error', 'Invalid JSON from AI - Error: ' . json_last_error_msg());
        log_message('error', 'Raw content (first 500 chars): ' . substr($content, 0, 500));
        log_message('error', 'Content bytes: ' . bin2hex(substr($content, 0, 100)));

        return false; // Return false instead of invalid content
    }

    /**
     * Generate quiz with retry mechanism for better reliability
     */
    private function generate_quiz_with_retry($content, $num_questions, $difficulty, $title)
    {
        $max_attempts = 3;
        $attempt = 1;

        while ($attempt <= $max_attempts) {
            log_message('debug', "Quiz generation attempt $attempt of $max_attempts");

            // Adjust parameters for retry attempts
            if ($attempt > 1) {
                // Reduce complexity for retry attempts
                $num_questions = max(2, intval($num_questions / 2)); // Fewer questions
                $content = substr($content, 0, 5000); // Shorter content
                log_message('debug', "Retry with reduced parameters: $num_questions questions, " . strlen($content) . " chars content");
            }

            $ai_response = $this->ai_service->generate_quiz($content, $num_questions, $difficulty);

            if ($ai_response['success']) {
                // Test if the response is complete and valid
                $test_clean = $this->clean_ai_json_response($ai_response['content']);

                if ($test_clean !== false) {
                    $test_decode = json_decode($test_clean, true);
                    if (json_last_error() === JSON_ERROR_NONE && isset($test_decode['questions']) && !empty($test_decode['questions'])) {
                        log_message('debug', "Quiz generation successful on attempt $attempt");
                        return $ai_response;
                    }
                }

                log_message('warning', "Attempt $attempt produced invalid JSON, retrying...");
            } else {
                log_message('error', "Attempt $attempt failed with error: " . ($ai_response['error'] ?? 'Unknown error'));
            }

            $attempt++;

            // Small delay between attempts
            if ($attempt <= $max_attempts) {
                sleep(1);
            }
        }

        // All attempts failed, return error
        return [
            'success' => false,
            'error' => "Failed to generate valid quiz after $max_attempts attempts. Please try with simpler content or fewer questions."
        ];
    }

    /**
     * Attempt to repair truncated JSON (simplified version)
     */
    private function attempt_json_repair($content)
    {
        log_message('debug', 'Attempting JSON repair for truncated content');

        // Simple repair: just try to close the most obvious issues
        $repaired = $content;

        // If it doesn't end with }, try to add closing structures
        if (!empty($repaired) && substr(trim($repaired), -1) !== '}') {
            // Count unmatched braces and brackets
            $open_braces = substr_count($repaired, '{');
            $close_braces = substr_count($repaired, '}');
            $open_brackets = substr_count($repaired, '[');
            $close_brackets = substr_count($repaired, ']');

            // Add minimal closing structures
            if ($open_brackets > $close_brackets) {
                $repaired .= str_repeat(']', $open_brackets - $close_brackets);
            }
            if ($open_braces > $close_braces) {
                $repaired .= str_repeat('}', $open_braces - $close_braces);
            }
        }

        return $repaired;
    }

    /**
     * Publish question paper to students
     */
    public function publish_question_paper()
    {
        header('Content-Type: application/json');

        // Restrict to faculty only
        if ($this->session->userdata('role') !== 'faculty') {
            echo json_encode(['success' => false, 'error' => 'Access denied. Only faculty can publish question papers.']);
            return;
        }

        try {
            $paper_id = $this->input->post('paper_id');
            $subject_id = $this->input->post('subject_id');

            if (!$paper_id || !$subject_id) {
                echo json_encode(['success' => false, 'error' => 'Paper ID and Subject ID are required']);
                return;
            }

            // Verify the paper belongs to the current user
            $this->db->where('id', $paper_id);
            $this->db->where('user_id', $this->session->userdata('user_id'));
            $paper = $this->db->get('ai_question_papers')->row();

            if (!$paper) {
                echo json_encode(['success' => false, 'error' => 'Question paper not found or access denied']);
                return;
            }

            // Update the paper to published status
            $update_data = [
                'is_published' => 1,
                'published_at' => date('Y-m-d H:i:s'),
                'subject_id' => $subject_id
            ];

            $this->db->where('id', $paper_id);
            $result = $this->db->update('ai_question_papers', $update_data);

            if ($result) {
                log_message('info', "Question paper $paper_id published to subject $subject_id by user " . $this->session->userdata('user_id'));
                echo json_encode(['success' => true, 'message' => 'Question paper published successfully']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to publish question paper']);
            }

        } catch (Exception $e) {
            log_message('error', 'Publish question paper error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
        }
    }

    /**
     * Publish quiz to students
     */
    public function publish_quiz()
    {
        header('Content-Type: application/json');

        // Restrict to faculty only
        if ($this->session->userdata('role') !== 'faculty') {
            echo json_encode(['success' => false, 'error' => 'Access denied. Only faculty can publish quizzes.']);
            return;
        }

        try {
            $quiz_id = $this->input->post('quiz_id');
            $subject_id = $this->input->post('subject_id');

            if (!$quiz_id || !$subject_id) {
                echo json_encode(['success' => false, 'error' => 'Quiz ID and Subject ID are required']);
                return;
            }

            // Verify the quiz belongs to the current user
            $this->db->where('id', $quiz_id);
            $this->db->where('user_id', $this->session->userdata('user_id'));
            $quiz = $this->db->get('ai_quizzes')->row();

            if (!$quiz) {
                echo json_encode(['success' => false, 'error' => 'Quiz not found or access denied']);
                return;
            }

            // Update the quiz to published status
            $update_data = [
                'is_published' => 1,
                'published_at' => date('Y-m-d H:i:s'),
                'subject_id' => $subject_id
            ];

            $this->db->where('id', $quiz_id);
            $result = $this->db->update('ai_quizzes', $update_data);

            if ($result) {
                log_message('info', "Quiz $quiz_id published to subject $subject_id by user " . $this->session->userdata('user_id'));
                echo json_encode(['success' => true, 'message' => 'Quiz published successfully']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to publish quiz']);
            }

        } catch (Exception $e) {
            log_message('error', 'Publish quiz error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
        }
    }

    /**
     * Publish assignment to students
     */
    public function publish_assignment()
    {
        header('Content-Type: application/json');

        // Restrict to faculty only
        if ($this->session->userdata('role') !== 'faculty') {
            echo json_encode(['success' => false, 'error' => 'Access denied. Only faculty can publish assignments.']);
            return;
        }

        try {
            $assignment_id = $this->input->post('assignment_id');
            $subject_id = $this->input->post('subject_id');

            if (!$assignment_id || !$subject_id) {
                echo json_encode(['success' => false, 'error' => 'Assignment ID and Subject ID are required']);
                return;
            }

            // Verify the assignment belongs to the current user
            $this->db->where('id', $assignment_id);
            $this->db->where('user_id', $this->session->userdata('user_id'));
            $assignment = $this->db->get('ai_assignments')->row();

            if (!$assignment) {
                echo json_encode(['success' => false, 'error' => 'Assignment not found or access denied']);
                return;
            }

            // Update the assignment to published status
            $update_data = [
                'is_published' => 1,
                'published_at' => date('Y-m-d H:i:s'),
                'subject_id' => $subject_id
            ];

            $this->db->where('id', $assignment_id);
            $result = $this->db->update('ai_assignments', $update_data);

            if ($result) {
                log_message('info', "Assignment $assignment_id published to subject $subject_id by user " . $this->session->userdata('user_id'));
                echo json_encode(['success' => true, 'message' => 'Assignment published successfully']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to publish assignment']);
            }

        } catch (Exception $e) {
            log_message('error', 'Publish assignment error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
        }
    }

    /**
     * Get student's enrolled subjects
     */
    private function get_student_enrolled_subjects($student_id)
    {
        if (!$this->db->table_exists('student_enrollments') || !$this->db->table_exists('subjects')) {
            return array();
        }

        $this->db->select('s.id, s.subject_code, s.subject_name, s.semester');
        $this->db->from('student_enrollments se');
        $this->db->join('subjects s', 'se.subject_id = s.id');
        $this->db->where('se.student_id', $student_id);
        $this->db->order_by('s.semester', 'ASC');
        $this->db->order_by('s.subject_name', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Check if student is enrolled in subject
     */
    private function is_student_enrolled_in_subject($student_id, $subject_id)
    {
        if (!$this->db->table_exists('student_enrollments')) {
            return false;
        }

        $this->db->where('student_id', $student_id);
        $this->db->where('subject_id', $subject_id);
        $query = $this->db->get('student_enrollments');

        return $query->num_rows() > 0;
    }
}