<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * AI Service Library
 * Handles interactions with AI API (OpenAI, Gemini, etc.)
 */
class AI_service
{

    private $ci;
    private $api_key;
    private $api_endpoint;
    private $model;
    private $api_keys;
    private $current_key_index;

    public function __construct()
    {
        $this->ci =& get_instance();

        // Multiple API keys for rotation when rate limits are hit
        $this->api_keys = [
            'AIzaSyD7NdVJh43kZOPRQyL4ZycNawkjUnPO6Lw',
            'AIzaSyCI6e7CWUQaBRwd9FDtKdmezAWu02E5Dss',
            'AIzaSyB17CTojS_KE1YzQE33Tcf6aDvvjFpA4ls'
        ];

        // Initialize with first key
        $this->current_key_index = 0;
        $this->api_key = $this->api_keys[$this->current_key_index];

        $this->api_endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';
        $this->model = 'gemini-2.5-flash';

        log_message('info', 'AI Service initialized with API key index: ' . $this->current_key_index);

        // Load the simple PDF parser
        $this->ci->load->library('Simple_pdf_parser');
    }

    /**
     * Rotate to the next API key
     * Returns true if rotation was successful, false if all keys have been tried
     */
    private function rotate_api_key()
    {
        $this->current_key_index++;

        if ($this->current_key_index >= count($this->api_keys)) {
            // All keys exhausted
            log_message('warning', 'All API keys exhausted. Resetting to first key.');
            $this->current_key_index = 0;
            return false;
        }

        $this->api_key = $this->api_keys[$this->current_key_index];
        log_message('info', 'Rotated to API key index: ' . $this->current_key_index);
        return true;
    }

    /**
     * Reset API key rotation to start from the first key
     */
    private function reset_api_key_rotation()
    {
        $this->current_key_index = 0;
        $this->api_key = $this->api_keys[$this->current_key_index];
        log_message('info', 'Reset API key rotation to index: 0');
    }

    /**
     * Send chat message to AI
     */
    public function chat($messages, $system_prompt = null)
    {
        $formatted_messages = [];

        log_message('error', 'AI CHAT DEBUG: Starting chat with ' . count($messages) . ' messages, system_prompt: ' . (empty($system_prompt) ? 'NO' : 'YES'));

        // Add system prompt if provided
        if ($system_prompt) {
            $formatted_messages[] = [
                'role' => 'system',
                'content' => $system_prompt
            ];
            log_message('error', 'AI CHAT DEBUG: Added system prompt, length: ' . strlen($system_prompt));
        }

        // Add conversation messages
        foreach ($messages as $idx => $msg) {
            // Handle both object and array formats
            if (is_object($msg)) {
                $role = isset($msg->role) ? $msg->role : 'user';
                $content = isset($msg->message) ? $msg->message : '';
                log_message('error', 'AI CHAT DEBUG: Message ' . $idx . ' is object, role: ' . $role . ', has content: ' . (empty($content) ? 'NO' : 'YES'));
            } else {
                $role = isset($msg['role']) ? $msg['role'] : 'user';
                $content = isset($msg['content']) ? $msg['content'] : (isset($msg['message']) ? $msg['message'] : '');
                log_message('error', 'AI CHAT DEBUG: Message ' . $idx . ' is array, role: ' . $role . ', has content: ' . (empty($content) ? 'NO' : 'YES'));
            }

            // Only add if content is not empty
            if (!empty(trim($content))) {
                $formatted_messages[] = [
                    'role' => $role,
                    'content' => $content
                ];
                log_message('error', 'AI CHAT DEBUG: Added message ' . $idx . ' to formatted_messages');
            } else {
                log_message('error', 'AI CHAT DEBUG: Skipped empty message ' . $idx);
            }
        }

        log_message('error', 'AI CHAT DEBUG: Total formatted messages: ' . count($formatted_messages));

        // Ensure we have at least one message
        if (empty($formatted_messages) || (count($formatted_messages) == 1 && $formatted_messages[0]['role'] == 'system')) {
            log_message('error', 'AI chat: No valid messages to send to API');
            return [
                'success' => false,
                'error' => 'No messages to send. Please type a message first.'
            ];
        }

        return $this->call_api($formatted_messages);
    }

    /**
     * Generate quiz from document content
     */
    public function generate_quiz($document_content, $num_questions = 10, $difficulty = 'medium')
    {
        // Limit content size to prevent truncation
        if (strlen($document_content) > 8000) {
            $document_content = substr($document_content, 0, 8000) . '...';
        }

        $prompt = "Create {$num_questions} quiz questions. Content: {$document_content}";

        $messages = [
            ['role' => 'system', 'content' => 'You are a JSON generator. You ONLY output JSON. Never use markdown. Never use code blocks. Never use ```json. Start with { and end with }. Format: {"questions":[{"question":"text","options":["A) opt1","B) opt2","C) opt3","D) opt4"],"correct_answer":"A","explanation":"why"}]}'],
            ['role' => 'user', 'content' => $prompt]
        ];

        $response = $this->call_api($messages);

        // Clean the JSON response
        if ($response['success']) {
            $response['content'] = $this->clean_json_response($response['content']);
        }

        return $response;
    }

    /**
     * Generate question paper
     */
    public function generate_question_paper($document_content, $config)
    {
        // Limit content size more aggressively for question papers
        if (strlen($document_content) > 6000) {
            $document_content = substr($document_content, 0, 6000);
            log_message('info', 'Question paper content truncated to 6000 characters for API efficiency');
        }

        $format_instructions = $this->build_question_paper_format($config);

        $prompt = "Create a question paper. Be concise and generate COMPLETE JSON only.

Config: {$config['total_marks']} marks, {$config['duration_minutes']} minutes

{$format_instructions}

JSON format (complete this structure):
{
  \"sections\": [
    {
      \"section_name\": \"Section A\",
      \"questions\": [
        {
          \"question_number\": 1,
          \"question_text\": \"Question here?\",
          \"marks\": 2,
          \"type\": \"mcq\"
        }
      ]
    }
  ]
}

Content: {$document_content}";

        $messages = [
            ['role' => 'system', 'content' => 'Generate COMPLETE valid JSON only. No markdown. No truncation. Always close all braces and brackets.'],
            ['role' => 'user', 'content' => $prompt]
        ];

        return $this->call_api($messages, 0.3); // Lower temperature for more consistent JSON
    }

    /**
     * Generate assignment
     */
    public function generate_assignment($document_content, $type = 'research', $difficulty = 'medium', $word_count = 1000, $due_weeks = 2, $title = 'Assignment')
    {
        // Limit content size to prevent truncation
        if (strlen($document_content) > 8000) {
            $document_content = substr($document_content, 0, 8000) . '...';
        }

        $type_descriptions = [
            'research' => 'a research assignment requiring analysis and investigation',
            'essay' => 'an essay assignment requiring critical thinking and argumentation',
            'project' => 'a practical project assignment with deliverables',
            'case_study' => 'a case study analysis assignment',
            'presentation' => 'a presentation assignment with slides and speaking notes'
        ];

        $difficulty_levels = [
            'easy' => 'introductory level with basic concepts',
            'medium' => 'intermediate level with moderate complexity',
            'hard' => 'advanced level with complex analysis required'
        ];

        $prompt = "Create {$type_descriptions[$type]} based on the following document content. 

Assignment Requirements:
- Title: {$title}
- Type: {$type}
- Difficulty: {$difficulty_levels[$difficulty]}
- Target word count: {$word_count} words
- Due in: {$due_weeks} weeks

Format the response as JSON with this structure:
{
  \"assignment\": {
    \"title\": \"Assignment title\",
    \"description\": \"Detailed assignment description\",
    \"objectives\": [\"Learning objective 1\", \"Learning objective 2\"],
    \"tasks\": [
      {
        \"task_number\": 1,
        \"task_title\": \"Task title\",
        \"description\": \"What students need to do\",
        \"word_count\": 300
      }
    ],
    \"evaluation_criteria\": [
      {
        \"criterion\": \"Content Quality\",
        \"weight\": \"40%\",
        \"description\": \"How content will be evaluated\"
      }
    ],
    \"resources\": [\"Suggested resource 1\", \"Suggested resource 2\"],
    \"submission_guidelines\": [\"Guideline 1\", \"Guideline 2\"],
    \"due_date\": \"Due in {$due_weeks} weeks\",
    \"total_marks\": 100
  }
}

Document Content:
{$document_content}";

        $messages = [
            ['role' => 'system', 'content' => 'You are a JSON generator. You ONLY output JSON. Never use markdown. Never use code blocks. Never use ```json. Start with { and end with }. You are an expert educator creating assignments.'],
            ['role' => 'user', 'content' => $prompt]
        ];

        $response = $this->call_api($messages);

        // Clean the JSON response
        if ($response['success']) {
            $response['content'] = $this->clean_json_response($response['content']);
        }

        return $response;
    }

    /**
     * Generate summary from document content
     */
    public function generate_summary($document_content, $length = 'medium')
    {
        $length_guide = [
            'short' => '2-3 paragraphs',
            'medium' => '4-6 paragraphs',
            'long' => 'comprehensive summary with key points'
        ];

        $prompt = "Provide a {$length_guide[$length]} summary of the following document. Include key concepts, main ideas, and important details.

Document Content:
{$document_content}";

        $messages = [
            ['role' => 'system', 'content' => 'You are an expert at summarizing educational content.'],
            ['role' => 'user', 'content' => $prompt]
        ];

        return $this->call_api($messages);
    }

    /**
     * Generate mindmap from document content
     * Returns a hierarchical structure suitable for mindmap visualization
     */
    public function generate_mindmap($document_content, $subject_name = 'Subject')
    {
        // Limit content size to prevent truncation
        if (strlen($document_content) > 12000) {
            $document_content = substr($document_content, 0, 12000) . '...';
        }

        $prompt = "Analyze the following educational content and create a comprehensive mindmap structure.

Subject: {$subject_name}

Content:
{$document_content}

Create a hierarchical mindmap with:
1. Main central topic
2. 4-6 major branches (main concepts)
3. 2-4 sub-branches for each major branch (key points)
4. Brief descriptions for each node

IMPORTANT: Output ONLY valid JSON. No markdown, no code blocks, no ```json. Start with { and end with }.

Format:
{
  \"central_topic\": \"Main Subject Title\",
  \"branches\": [
    {
      \"id\": \"branch1\",
      \"title\": \"Main Concept 1\",
      \"description\": \"Brief description\",
      \"sub_branches\": [
        {
          \"id\": \"sub1_1\",
          \"title\": \"Key Point 1.1\",
          \"description\": \"Details\"
        }
      ]
    }
  ]
}";

        $messages = [
            ['role' => 'system', 'content' => 'You are a JSON generator for educational mindmaps. You ONLY output valid JSON. Never use markdown. Never use code blocks. Never use ```json. Start with { and end with }.'],
            ['role' => 'user', 'content' => $prompt]
        ];

        return $this->call_api($messages);
    }

    /**
     * Generate flashcards from document content
     * Returns flashcards with questions and answers for study
     */
    public function generate_flashcards($document_content, $subject_name = 'Subject', $num_cards = 15)
    {
        // Limit content size to prevent truncation
        if (strlen($document_content) > 12000) {
            $document_content = substr($document_content, 0, 12000) . '...';
        }

        $prompt = "Create {$num_cards} educational flashcards from the following content to help students study effectively.

Subject: {$subject_name}

Content:
{$document_content}

Create flashcards that:
1. Cover key concepts and important information
2. Have clear, concise questions on the front
3. Have detailed, helpful answers on the back
4. Include definitions, explanations, examples, and applications
5. Range from basic recall to deeper understanding

IMPORTANT: Output ONLY valid JSON. No markdown, no code blocks, no ```json. Start with { and end with }.

Format:
{
  \"subject\": \"{$subject_name}\",
  \"flashcards\": [
    {
      \"id\": 1,
      \"front\": \"Question or term to remember\",
      \"back\": \"Detailed answer or explanation\",
      \"category\": \"Category name (e.g., Definition, Concept, Application)\",
      \"difficulty\": \"easy|medium|hard\"
    }
  ]
}";

        $messages = [
            ['role' => 'system', 'content' => 'You are a JSON generator for educational flashcards. You ONLY output valid JSON. Never use markdown. Never use code blocks. Never use ```json. Start with { and end with }. Create helpful, clear flashcards for students.'],
            ['role' => 'user', 'content' => $prompt]
        ];

        return $this->call_api($messages);
    }

    /**
     * Extract text from PDF
     */
    public function extract_pdf_text($file_path)
    {
        try {
            // Check if file exists
            if (!file_exists($file_path)) {
                log_message('error', 'PDF file not found: ' . $file_path);
                return "PDF file not found.";
            }

            log_message('error', 'PDF EXTRACTION: Starting extraction for: ' . $file_path);

            // Method 1: Try using Smalot PDF Parser (if installed via Composer)
            $vendor_autoload = FCPATH . 'vendor/autoload.php';
            log_message('error', 'PDF EXTRACTION: Checking for vendor autoload at: ' . $vendor_autoload);
            log_message('error', 'PDF EXTRACTION: Vendor autoload exists: ' . (file_exists($vendor_autoload) ? 'YES' : 'NO'));

            if (file_exists($vendor_autoload)) {
                require_once $vendor_autoload;
                log_message('error', 'PDF EXTRACTION: Vendor autoload loaded successfully');

                try {
                    log_message('error', 'PDF EXTRACTION: Creating Smalot parser instance');
                    $parser = new \Smalot\PdfParser\Parser();

                    log_message('error', 'PDF EXTRACTION: Parsing file...');
                    $pdf = $parser->parseFile($file_path);

                    log_message('error', 'PDF EXTRACTION: Extracting text...');
                    $text = $pdf->getText();

                    log_message('error', 'PDF EXTRACTION: Raw text length: ' . strlen($text));
                    log_message('error', 'PDF EXTRACTION: First 200 chars: ' . substr($text, 0, 200));

                    if (!empty(trim($text)) && strlen(trim($text)) > 50) {
                        log_message('error', 'PDF EXTRACTION: Success! Cleaning text...');
                        // Clean the text to remove any problematic characters
                        $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
                        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $text);
                        $text = preg_replace('/\s+/', ' ', $text); // Normalize whitespace
                        $text = trim($text);

                        log_message('error', 'PDF EXTRACTION: Cleaned text length: ' . strlen($text));
                        return $text;
                    } else {
                        log_message('error', 'PDF EXTRACTION: Text too short or empty after extraction');
                    }
                } catch (Exception $e) {
                    log_message('error', 'PDF EXTRACTION: Smalot parser exception: ' . $e->getMessage());
                    log_message('error', 'PDF EXTRACTION: Stack trace: ' . $e->getTraceAsString());
                }
            } else {
                log_message('error', 'PDF EXTRACTION: Vendor autoload not found - Smalot parser not available');
            }

            // If Smalot failed, return helpful message
            log_message('error', 'PDF EXTRACTION: All methods failed');
            return "Unable to extract text from this PDF. The PDF may be:\n" .
                "1. Image-based (scanned document)\n" .
                "2. Encrypted or password-protected\n" .
                "3. Using complex encoding\n\n" .
                "Please try uploading the document as .txt or .docx instead.";

        } catch (Exception $e) {
            log_message('error', 'PDF EXTRACTION: Fatal error: ' . $e->getMessage());
            log_message('error', 'PDF EXTRACTION: Stack trace: ' . $e->getTraceAsString());
            return "Error extracting PDF text: " . $e->getMessage();
        }
    }

    /**
     * Enhanced PDF text extraction method
     */
    private function enhanced_pdf_text_extraction($file_path)
    {
        $content = file_get_contents($file_path);
        $text = '';

        // Method 1: Extract from BT...ET text blocks with better TJ parsing
        if (preg_match_all('/BT\s*(.*?)\s*ET/s', $content, $matches)) {
            foreach ($matches[1] as $textBlock) {
                // Parse TJ arrays more carefully
                if (preg_match_all('/\[(.*?)\]\s*TJ/s', $textBlock, $tjMatches)) {
                    foreach ($tjMatches[1] as $tjArray) {
                        $decoded = $this->decode_tj_array($tjArray);
                        if (strlen($decoded) > 3) {
                            $text .= $decoded . ' ';
                        }
                    }
                }

                // Parse simple Tj operations
                if (preg_match_all('/\((.*?)\)\s*Tj/s', $textBlock, $tjMatches)) {
                    foreach ($tjMatches[1] as $tjText) {
                        $decoded = $this->decode_pdf_string($tjText);
                        if (strlen($decoded) > 2) {
                            $text .= $decoded . ' ';
                        }
                    }
                }
            }
        }

        // Method 2: Extract from compressed streams with better decompression
        if (strlen($text) < 200) {
            if (preg_match_all('/stream\s*\n(.*?)\nendstream/s', $content, $matches)) {
                foreach ($matches[1] as $stream) {
                    $decompressed = $this->decompress_stream($stream);
                    if ($decompressed) {
                        // Look for text in the decompressed content
                        $readable = $this->extract_text_from_decompressed($decompressed);
                        if (strlen($readable) > 20) {
                            $text .= $readable . ' ';
                        }
                    }
                }
            }
        }

        // Method 3: Look for simple text patterns
        if (strlen($text) < 200) {
            if (preg_match_all('/\(([^)]{3,})\)/', $content, $matches)) {
                foreach ($matches[1] as $match) {
                    $decoded = $this->decode_pdf_string($match);
                    // Only include if it looks like real text (not just symbols)
                    if (strlen($decoded) > 3 && preg_match('/[a-zA-Z]/', $decoded)) {
                        $text .= $decoded . ' ';
                    }
                }
            }
        }

        return $this->clean_extracted_text($text);
    }

    /**
     * Decode TJ array (text with positioning)
     */
    private function decode_tj_array($array_content)
    {
        $text = '';

        // Remove angle brackets and decode hex strings
        $array_content = preg_replace_callback('/<([0-9A-Fa-f]+)>/', function ($matches) {
            return $this->hex_to_text($matches[1]);
        }, $array_content);

        // Extract strings from parentheses
        if (preg_match_all('/\(([^)]*)\)/', $array_content, $matches)) {
            foreach ($matches[1] as $match) {
                $decoded = $this->decode_pdf_string($match);
                $text .= $decoded;
            }
        }

        return $text;
    }

    /**
     * Convert hex string to text
     */
    private function hex_to_text($hex)
    {
        $text = '';
        for ($i = 0; $i < strlen($hex); $i += 2) {
            $byte = hexdec(substr($hex, $i, 2));
            if ($byte >= 32 && $byte <= 126) { // Printable ASCII
                $text .= chr($byte);
            } else if ($byte == 32) { // Space
                $text .= ' ';
            }
        }
        return $text;
    }

    /**
     * Decompress PDF stream
     */
    private function decompress_stream($stream)
    {
        // Try different decompression methods
        $methods = [
            function ($s) {
                return @gzuncompress($s);
            },
            function ($s) {
                return @gzinflate($s);
            },
            function ($s) {
                return @gzinflate(substr($s, 2));
            },
            function ($s) {
                return @gzinflate(substr($s, 2, -4));
            }
        ];

        foreach ($methods as $method) {
            $result = $method($stream);
            if ($result !== false && strlen($result) > strlen($stream)) {
                return $result;
            }
        }

        return $stream; // Return original if no decompression worked
    }

    /**
     * Extract text from decompressed stream content
     */
    private function extract_text_from_decompressed($content)
    {
        $text = '';

        // Look for BT...ET blocks in decompressed content
        if (preg_match_all('/BT\s*(.*?)\s*ET/s', $content, $matches)) {
            foreach ($matches[1] as $textBlock) {
                if (preg_match_all('/\((.*?)\)\s*Tj/s', $textBlock, $tjMatches)) {
                    foreach ($tjMatches[1] as $tjText) {
                        $decoded = $this->decode_pdf_string($tjText);
                        if (strlen($decoded) > 2 && preg_match('/[a-zA-Z]/', $decoded)) {
                            $text .= $decoded . ' ';
                        }
                    }
                }
            }
        }

        // Look for readable text patterns
        if (strlen($text) < 50) {
            $readable = preg_replace('/[^\x20-\x7E\s]/', '', $content);
            $readable = preg_replace('/\s+/', ' ', $readable);

            // Extract words that look like real text
            if (preg_match_all('/\b[a-zA-Z]{3,}\b/', $readable, $matches)) {
                $words = array_unique($matches[0]);
                if (count($words) > 5) {
                    $text = implode(' ', array_slice($words, 0, 100));
                }
            }
        }

        return $text;
    }

    /**
     * Simple PDF text extraction method
     */
    private function simple_pdf_text_extraction($file_path)
    {
        $content = file_get_contents($file_path);
        $text = '';

        // Method 1: Extract text from PDF streams
        if (preg_match_all('/stream\s*\n(.*?)\nendstream/s', $content, $matches)) {
            foreach ($matches[1] as $stream) {
                // Try different decompression methods
                $decompressed = @gzuncompress($stream);
                if ($decompressed === false) {
                    $decompressed = @gzinflate($stream);
                }
                if ($decompressed === false) {
                    $decompressed = @gzinflate(substr($stream, 2)); // Skip first 2 bytes
                }
                if ($decompressed === false) {
                    $decompressed = $stream;
                }

                // Extract readable text
                $readable = $this->extract_readable_text($decompressed);
                if (strlen($readable) > 20) {
                    $text .= $readable . ' ';
                }
            }
        }

        // Method 2: Extract text between parentheses (common in PDFs)
        if (strlen($text) < 100) {
            if (preg_match_all('/\((.*?)\)/s', $content, $matches)) {
                foreach ($matches[1] as $match) {
                    $readable = $this->extract_readable_text($match);
                    if (strlen($readable) > 5) {
                        $text .= $readable . ' ';
                    }
                }
            }
        }

        // Method 3: Extract text from Tj operators
        if (strlen($text) < 100) {
            if (preg_match_all('/\[(.*?)\]\s*TJ/s', $content, $matches)) {
                foreach ($matches[1] as $match) {
                    $readable = $this->extract_readable_text($match);
                    if (strlen($readable) > 5) {
                        $text .= $readable . ' ';
                    }
                }
            }
        }

        return $text;
    }

    /**
     * Extract readable text from binary content
     */
    private function extract_readable_text($content)
    {
        // Remove non-printable characters except spaces and newlines
        $text = preg_replace('/[^\x20-\x7E\s]/', '', $content);

        // Remove PDF escape sequences
        $text = str_replace(['\\(', '\\)', '\\\\', '\\n', '\\r', '\\t'], ['(', ')', '\\', ' ', ' ', ' '], $text);

        // Remove excessive whitespace
        $text = preg_replace('/\s+/', ' ', $text);

        return trim($text);
    }

    /**
     * Generate sample content based on filename for testing
     */
    private function generate_sample_content($filename)
    {
        $base_content = "This is sample content for the document: " . $filename . "\n\n";

        // Generate content based on filename patterns
        if (stripos($filename, 'computer') !== false || stripos($filename, 'cs') !== false) {
            $base_content .= "Computer Science Fundamentals\n\n";
            $base_content .= "This document covers key topics in computer science including:\n\n";
            $base_content .= "1. Programming Concepts\n";
            $base_content .= "   - Variables and data types\n";
            $base_content .= "   - Control structures (loops, conditionals)\n";
            $base_content .= "   - Functions and procedures\n";
            $base_content .= "   - Object-oriented programming principles\n\n";
            $base_content .= "2. Data Structures\n";
            $base_content .= "   - Arrays and linked lists\n";
            $base_content .= "   - Stacks and queues\n";
            $base_content .= "   - Trees and graphs\n";
            $base_content .= "   - Hash tables and dictionaries\n\n";
            $base_content .= "3. Algorithms\n";
            $base_content .= "   - Sorting algorithms (bubble sort, merge sort, quick sort)\n";
            $base_content .= "   - Search algorithms (linear search, binary search)\n";
            $base_content .= "   - Graph traversal (BFS, DFS)\n";
            $base_content .= "   - Dynamic programming concepts\n\n";
        } elseif (stripos($filename, 'math') !== false) {
            $base_content .= "Mathematics Concepts\n\n";
            $base_content .= "This document covers fundamental mathematical concepts including:\n\n";
            $base_content .= "1. Algebra\n   - Linear equations and inequalities\n   - Quadratic functions\n   - Polynomial operations\n\n";
            $base_content .= "2. Calculus\n   - Limits and continuity\n   - Derivatives and differentiation\n   - Integration techniques\n\n";
            $base_content .= "3. Statistics\n   - Probability distributions\n   - Hypothesis testing\n   - Regression analysis\n\n";
        } elseif (stripos($filename, 'physics') !== false) {
            $base_content .= "Physics Principles\n\n";
            $base_content .= "This document explores fundamental physics concepts:\n\n";
            $base_content .= "1. Mechanics\n   - Newton's laws of motion\n   - Energy and momentum\n   - Rotational dynamics\n\n";
            $base_content .= "2. Thermodynamics\n   - Heat and temperature\n   - Laws of thermodynamics\n   - Entropy and energy transfer\n\n";
            $base_content .= "3. Electromagnetism\n   - Electric fields and forces\n   - Magnetic fields\n   - Electromagnetic induction\n\n";
        } else {
            $base_content .= "Academic Content\n\n";
            $base_content .= "This document contains educational material covering various topics relevant to the course curriculum. ";
            $base_content .= "Students can ask questions about the concepts, definitions, examples, and applications discussed in this material. ";
            $base_content .= "The AI assistant can help explain complex topics, provide additional context, and answer specific questions about the content.\n\n";
            $base_content .= "Key Learning Objectives:\n";
            $base_content .= "- Understand fundamental concepts\n";
            $base_content .= "- Apply theoretical knowledge to practical problems\n";
            $base_content .= "- Develop critical thinking skills\n";
            $base_content .= "- Connect ideas across different topics\n\n";
        }

        $base_content .= "Note: This is sample content generated for testing purposes. In a real scenario, this would contain the actual extracted text from the PDF document.";

        return $base_content;
    }

    /**
     * Advanced PDF text extraction method
     */
    private function advanced_pdf_text_extraction($content)
    {
        $text = '';

        // Method 1: Extract text between BT and ET markers
        if (preg_match_all('/BT\s*(.*?)\s*ET/s', $content, $matches)) {
            foreach ($matches[1] as $textBlock) {
                // Extract text from Tj and TJ operators
                if (preg_match_all('/\[(.*?)\]\s*TJ/s', $textBlock, $tjMatches)) {
                    foreach ($tjMatches[1] as $tjText) {
                        $text .= $this->decode_pdf_string($tjText) . ' ';
                    }
                }
                if (preg_match_all('/\((.*?)\)\s*Tj/s', $textBlock, $tjMatches)) {
                    foreach ($tjMatches[1] as $tjText) {
                        $text .= $this->decode_pdf_string($tjText) . ' ';
                    }
                }
            }
        }

        return $text;
    }

    /**
     * Decode PDF text strings
     */
    private function decode_pdf_string($text)
    {
        // Remove PDF escape sequences
        $text = str_replace(['\\(', '\\)', '\\\\', '\\n', '\\r', '\\t'], ['(', ')', '\\', ' ', ' ', ' '], $text);
        // Remove non-printable characters except spaces and newlines
        $text = preg_replace('/[^\x20-\x7E\s]/', '', $text);
        return $text;
    }

    /**
     * Clean extracted text
     */
    private function clean_extracted_text($text)
    {
        // Remove PDF commands and artifacts
        $text = preg_replace('/\/[A-Z][a-zA-Z0-9]*\s+/', '', $text); // Remove PDF operators like /F1, /GS6
        $text = preg_replace('/\b[0-9]+(\.[0-9]+)?\s+[0-9]+(\.[0-9]+)?\s+[0-9]+(\.[0-9]+)?\s+[0-9]+(\.[0-9]+)?\s+(re|cm|Tm|l|m|S|f|Q|q)\b/', '', $text); // Remove coordinate commands
        $text = preg_replace('/\b(BT|ET|EMC|BMC|BDC|Do|gs|G|g|Tf|TJ|Tj|re|W\*|n|cm|Q|q|S|f|l|m|h)\b/', '', $text); // Remove PDF operators
        $text = preg_replace('/<<[^>]*>>/', '', $text); // Remove PDF dictionaries
        $text = preg_replace('/\[[^\]]*\]/', '', $text); // Remove arrays that aren't text
        $text = preg_replace('/\b[0-9]+(\.[0-9]+)?\b/', '', $text); // Remove standalone numbers

        // Remove excessive whitespace
        $text = preg_replace('/\s+/', ' ', $text);

        // Remove control characters
        $text = preg_replace('/[\x00-\x1F\x7F]/', '', $text);

        // Remove common PDF artifacts
        $text = preg_replace('/[^\x20-\x7E\s]/', '', $text);

        // Remove very short "words" that are likely artifacts
        $words = explode(' ', $text);
        $cleaned_words = [];
        foreach ($words as $word) {
            $word = trim($word);
            // Keep words that are at least 2 characters and contain letters
            if (strlen($word) >= 2 && preg_match('/[a-zA-Z]/', $word)) {
                $cleaned_words[] = $word;
            }
        }

        $text = implode(' ', $cleaned_words);

        // If we still don't have much readable text, try a different approach
        if (strlen($text) < 100) {
            // Look for patterns that might be encoded text
            $original = func_get_arg(0);
            if (preg_match_all('/\b[A-Za-z]{3,}\b/', $original, $matches)) {
                $words = array_unique($matches[0]);
                if (count($words) > 10) {
                    $text = implode(' ', array_slice($words, 0, 200));
                }
            }
        }

        // Trim and return
        return trim($text);
    }

    /**
     * Clean JSON response from AI (remove markdown formatting and decode HTML entities)
     */
    private function clean_json_response($content)
    {
        // Log original content for debugging
        log_message('debug', 'Original AI response length: ' . strlen($content));
        log_message('debug', 'Original content (first 200 chars): ' . substr($content, 0, 200));

        // Remove markdown code blocks
        $content = preg_replace('/```json\s*/', '', $content);
        $content = preg_replace('/```\s*$/', '', $content);
        $content = preg_replace('/```/', '', $content);

        // Decode HTML entities
        $content = html_entity_decode($content, ENT_QUOTES, 'UTF-8');

        // Remove control characters (except newlines and tabs that are valid in JSON)
        $content = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $content);

        // Remove any leading/trailing whitespace
        $content = trim($content);

        // Try to find JSON content between braces
        if (preg_match('/\{.*\}/s', $content, $matches)) {
            $content = $matches[0];
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
     * Extract text from DOCX
     */
    /**
     * Extract text from DOCX
     */
    public function extract_docx_text($file_path)
    {
        try {
            if (!file_exists($file_path)) {
                log_message('error', 'DOCX file not found: ' . $file_path);
                return "DOCX file not found.";
            }

            $content = '';
            $zip = new ZipArchive;
            if ($zip->open($file_path) === true) {
                if (($index = $zip->locateName('word/document.xml')) !== false) {
                    $xml = $zip->getFromIndex($index);

                    // Use DOMDocument for robust XML parsing
                    $dom = new DOMDocument();
                    // Suppress warnings for invalid XML structure
                    $prev_internal = libxml_use_internal_errors(true);
                    $dom->loadXML($xml, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);

                    // Extract text from paragraphs <w:p> to preserve structure
                    $paragraphs = $dom->getElementsByTagName('p'); // w:p

                    if ($paragraphs->length > 0) {
                        foreach ($paragraphs as $p) {
                            $texts = $p->getElementsByTagName('t'); // w:t
                            $paragraph_text = '';
                            foreach ($texts as $t) {
                                $paragraph_text .= $t->nodeValue;
                            }

                            if (!empty(trim($paragraph_text))) {
                                $content .= $paragraph_text . "\n";
                            }
                        }
                    } else {
                        // Fallback: simple strip tags if structure is unusual
                        $content = strip_tags($xml);
                    }

                    libxml_clear_errors();
                    libxml_use_internal_errors($prev_internal);

                } else {
                    log_message('error', 'word/document.xml not found in DOCX: ' . $file_path);
                    return "Unable to parse this DOCX file (invalid structure).";
                }
                $zip->close();
            } else {
                log_message('error', 'Failed to open DOCX as zip: ' . $file_path);
                return "Unable to open this DOCX file.";
            }

            if (empty(trim($content))) {
                return "No text content found in this document.";
            }

            return $content;

        } catch (Exception $e) {
            log_message('error', 'DOCX EXTRACTION: Error: ' . $e->getMessage());
            return "Error extracting DOCX text: " . $e->getMessage();
        }
    }

    /**
     * Extract text from PPTX
     */
    public function extract_pptx_text($file_path)
    {
        try {
            if (!file_exists($file_path)) {
                log_message('error', 'PPTX file not found: ' . $file_path);
                return "PPTX file not found.";
            }

            // Check if ZipArchive class is available
            if (!class_exists('ZipArchive')) {
                log_message('error', 'ZipArchive class not found. PHP ZIP extension is not enabled.');
                return "Error: PHP ZIP extension is not enabled on this server. Please enable the ZIP extension in php.ini to extract PowerPoint files. Contact your system administrator.";
            }

            $content = '';
            $zip = new ZipArchive;

            if ($zip->open($file_path) === true) {
                // Determine number of slides by counting slide files or check specific files
                // PPTX structure typically has slides in ppt/slides/slideX.xml

                $slide_count = 0;
                $max_slides = 100; // Cap to prevent infinite loops or timeout

                for ($i = 1; $i <= $max_slides; $i++) {
                    $slide_name = "ppt/slides/slide{$i}.xml";

                    if (($index = $zip->locateName($slide_name)) !== false) {
                        $xml = $zip->getFromIndex($index);

                        $dom = new DOMDocument();
                        $prev_internal = libxml_use_internal_errors(true);
                        $dom->loadXML($xml, LIBXML_NOENT | LIBXML_NOERROR | LIBXML_NOWARNING);

                        $slide_text = '';

                        // Method 1: Try to extract text using DrawingML namespace (preferred method)
                        // PPTX files use Office Open XML with DrawingML namespace for text
                        $drawingml_ns = 'http://schemas.openxmlformats.org/drawingml/2006/main';
                        $texts = $dom->getElementsByTagNameNS($drawingml_ns, 't');

                        if ($texts->length > 0) {
                            foreach ($texts as $t) {
                                $slide_text .= $t->nodeValue . " ";
                            }
                        }

                        // Method 2: Fallback - try getElementsByTagName for 't' without namespace
                        // This works when namespace prefixes are not used or processed differently
                        if (empty(trim($slide_text))) {
                            $texts = $dom->getElementsByTagName('t');
                            foreach ($texts as $t) {
                                $slide_text .= $t->nodeValue . " ";
                            }
                        }

                        // Method 3: Use XPath with namespace registration for more robust extraction
                        if (empty(trim($slide_text))) {
                            $xpath = new DOMXPath($dom);
                            // Register all namespaces commonly found in PPTX
                            $xpath->registerNamespace('a', 'http://schemas.openxmlformats.org/drawingml/2006/main');
                            $xpath->registerNamespace('p', 'http://schemas.openxmlformats.org/presentationml/2006/main');

                            // Query for text elements
                            $textNodes = $xpath->query('//a:t');
                            if ($textNodes !== false && $textNodes->length > 0) {
                                foreach ($textNodes as $node) {
                                    $slide_text .= $node->nodeValue . " ";
                                }
                            }
                        }

                        // Method 4: Last resort - use regex to extract text from a:t tags directly from XML
                        if (empty(trim($slide_text))) {
                            if (preg_match_all('/<a:t[^>]*>([^<]*)<\/a:t>/is', $xml, $matches)) {
                                foreach ($matches[1] as $match) {
                                    $slide_text .= $match . " ";
                                }
                            }
                        }

                        // Method 5: Final fallback - strip all tags to get any visible text content
                        if (empty(trim($slide_text))) {
                            $slide_text = strip_tags($xml);
                        }

                        if (!empty(trim($slide_text))) {
                            $content .= "Slide {$i}:\n" . trim($slide_text) . "\n\n";
                        }

                        $slide_count++;

                        libxml_clear_errors();
                        libxml_use_internal_errors($prev_internal);
                    } else {
                        // Assuming slides are numbered sequentially, break if one is missing
                        // However, sometimes they skip if slides were deleted? 
                        // Better to check a few more ahead just in case, but usually sequential.
                        if ($i > 1 && $zip->locateName("ppt/slides/slide" . ($i + 1) . ".xml") === false) {
                            break;
                        }
                    }
                }

                $zip->close();

                log_message('info', "PPTX Parsing: Extracted {$slide_count} slides from {$file_path}");

            } else {
                log_message('error', 'Failed to open PPTX as zip: ' . $file_path);
                return "Unable to open this PPTX file.";
            }

            if (empty(trim($content))) {
                return "No text content found in this presentation.";
            }

            return $content;

        } catch (Exception $e) {
            log_message('error', 'PPTX EXTRACTION: Error: ' . $e->getMessage());
            return "Error extracting PPTX text: " . $e->getMessage();
        }
    }

    /**
     * Extract text from legacy PPT files (binary OLE format)
     * PPT files are binary OLE compound documents, different from PPTX (ZIP/XML)
     */
    public function extract_ppt_text($file_path)
    {
        try {
            if (!file_exists($file_path)) {
                log_message('error', 'PPT file not found: ' . $file_path);
                return "PPT file not found.";
            }

            // Read the binary content
            $content = file_get_contents($file_path);

            if ($content === false) {
                log_message('error', 'Failed to read PPT file: ' . $file_path);
                return "Unable to read PPT file.";
            }

            // Check if it's a valid OLE file (starts with OLE signature)
            $ole_signature = "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1";
            if (substr($content, 0, 8) !== $ole_signature) {
                log_message('error', 'Invalid PPT file (not OLE format): ' . $file_path);
                return "Invalid PPT file format. This file may be corrupted or not a valid PowerPoint file.";
            }

            $extracted_text = '';

            // Method 1: Extract Unicode strings (most PPT text is stored as UTF-16LE)
            // Look for text records in the binary stream
            $unicode_text = '';
            $i = 0;
            $len = strlen($content);

            while ($i < $len - 1) {
                // Look for sequences of printable Unicode characters (UTF-16LE)
                // Each character is 2 bytes with the high byte often being 0x00 for ASCII
                $char_sequence = '';
                $start_pos = $i;

                while ($i < $len - 1) {
                    $low_byte = ord($content[$i]);
                    $high_byte = ord($content[$i + 1]);

                    // Check if it's a printable ASCII character in UTF-16LE
                    if ($high_byte === 0 && $low_byte >= 32 && $low_byte <= 126) {
                        $char_sequence .= chr($low_byte);
                        $i += 2;
                    } elseif ($high_byte === 0 && ($low_byte === 10 || $low_byte === 13 || $low_byte === 9)) {
                        // Newline, carriage return, or tab
                        $char_sequence .= ' ';
                        $i += 2;
                    } else {
                        break;
                    }
                }

                // If we found a sequence of at least 4 characters, it's likely text
                if (strlen($char_sequence) >= 4) {
                    // Filter out obviously non-text content
                    $trimmed = trim($char_sequence);
                    if (!empty($trimmed) && preg_match('/[a-zA-Z]/', $trimmed)) {
                        $unicode_text .= $trimmed . ' ';
                    }
                }

                $i++;
            }

            // Method 2: Also try to extract plain ASCII strings
            $ascii_text = '';
            if (preg_match_all('/[\x20-\x7E]{10,}/', $content, $matches)) {
                foreach ($matches[0] as $match) {
                    // Filter out binary-looking strings and common PPT internal strings
                    if (
                        !preg_match('/^[A-Z][a-z]+[A-Z]/', $match) && // CamelCase internal names
                        !preg_match('/^(Microsoft|PowerPoint|Office|Document|Summary|Root)/i', $match) &&
                        !preg_match('/[\x00-\x1F\x7F-\xFF]/', $match) &&
                        preg_match('/[a-zA-Z]{3,}/', $match)
                    ) { // Must have at least 3 consecutive letters
                        $ascii_text .= trim($match) . ' ';
                    }
                }
            }

            // Combine and clean up
            $extracted_text = $unicode_text . "\n" . $ascii_text;

            // Remove duplicates and clean up
            $words = array_unique(explode(' ', $extracted_text));
            $cleaned_text = '';

            foreach ($words as $word) {
                $word = trim($word);
                if (strlen($word) >= 2 && preg_match('/[a-zA-Z]/', $word)) {
                    $cleaned_text .= $word . ' ';
                }
            }

            // Remove excessive whitespace
            $cleaned_text = preg_replace('/\s+/', ' ', $cleaned_text);
            $cleaned_text = trim($cleaned_text);

            if (empty($cleaned_text)) {
                log_message('warning', 'No text extracted from PPT file: ' . $file_path);
                return "No text content could be extracted from this PowerPoint file. The file may contain only images or embedded objects.";
            }

            log_message('info', 'PPT Parsing: Extracted ' . strlen($cleaned_text) . ' characters from ' . $file_path);
            return $cleaned_text;

        } catch (Exception $e) {
            log_message('error', 'PPT EXTRACTION: Error: ' . $e->getMessage());
            return "Error extracting PPT text: " . $e->getMessage();
        }
    }

    /**
     * Build question paper format instructions
     */
    private function build_question_paper_format($config)
    {
        $instructions = "Total Marks: {$config['total_marks']}\n";
        $instructions .= "Duration: {$config['duration_minutes']} minutes\n\n";

        if (isset($config['sections']) && is_array($config['sections'])) {
            $instructions .= "Sections:\n";
            foreach ($config['sections'] as $index => $section) {
                $section_name = isset($section['name']) ? $section['name'] : 'Section ' . chr(65 + $index); // A, B, C...
                $num_questions = isset($section['num_questions']) ? $section['num_questions'] : 5;
                $marks_per_question = isset($section['marks_per_question']) ? $section['marks_per_question'] : 2;
                $type = isset($section['type']) ? $section['type'] : 'mcq';

                $instructions .= "- {$section_name}: {$num_questions} questions, {$marks_per_question} marks each\n";
                $instructions .= "  Type: {$type}\n";
            }
        }

        return $instructions;
    }

    /**
     * Call AI API (Google Gemini) with retry logic for rate limits and automatic key rotation
     */
    private function call_api($messages, $temperature = 0.7, $retry_count = 0, $keys_tried = 0)
    {
        $max_retries = 3;
        $base_delay = 2; // seconds
        $max_keys = count($this->api_keys);

        // Convert messages to Gemini format
        $contents = [];
        $system_instruction = '';

        log_message('error', 'AI API DEBUG: Received ' . count($messages) . ' messages to process (Retry: ' . $retry_count . ')');

        foreach ($messages as $msg) {
            if ($msg['role'] === 'system') {
                $system_instruction = $msg['content'];
                log_message('error', 'AI API DEBUG: Found system message, length: ' . strlen($msg['content']));
            } else {
                $gemini_role = $msg['role'] === 'assistant' ? 'model' : 'user';
                $contents[] = [
                    'role' => $gemini_role,
                    'parts' => [
                        ['text' => $msg['content']]
                    ]
                ];
                log_message('error', 'AI API DEBUG: Added ' . $gemini_role . ' message, length: ' . strlen($msg['content']));
            }
        }

        log_message('error', 'AI API DEBUG: After initial processing, contents count: ' . count($contents));

        // Filter contents to ensure first message is user (Gemini and other APIs require this)
        $filtered_contents = [];
        $found_user = false;

        foreach ($contents as $content) {
            if ($content['role'] === 'user') {
                $found_user = true;
            }

            if ($found_user) {
                // Ensure alternating roles if strictly required, but for now just ensure start with user
                $filtered_contents[] = $content;
            }
        }

        $contents = $filtered_contents;

        log_message('error', 'AI API DEBUG: After filtering, contents count: ' . count($contents));

        // If no user message found (or contents empty), create one from system instruction or dummy
        if (empty($contents)) {
            $text = $system_instruction ? $system_instruction : "Hello";
            // If system instruction was used as content, we don't need to prepend it later
            $system_instruction = '';

            $contents[] = [
                'role' => 'user',
                'parts' => [['text' => $text]]
            ];
            log_message('error', 'AI API DEBUG: No user message found, created dummy with text length: ' . strlen($text));
        }

        // Prepend system instruction to first user message if exists
        if ($system_instruction && !empty($contents)) {
            $original_length = strlen($contents[0]['parts'][0]['text']);
            $contents[0]['parts'][0]['text'] = $system_instruction . "\n\n" . $contents[0]['parts'][0]['text'];
            log_message('error', 'AI API DEBUG: Prepended system instruction. Original: ' . $original_length . ', New: ' . strlen($contents[0]['parts'][0]['text']));
        }

        log_message('error', 'AI API DEBUG: Final contents count before sending: ' . count($contents));
        log_message('error', 'AI API DEBUG: Contents structure: ' . json_encode(array_map(function ($c) {
            return ['role' => $c['role'], 'text_length' => strlen($c['parts'][0]['text'])];
        }, $contents)));

        // Clean UTF-8 encoding in contents to prevent JSON encoding errors
        foreach ($contents as &$content) {
            if (isset($content['parts'][0]['text'])) {
                // Remove invalid UTF-8 characters
                $content['parts'][0]['text'] = mb_convert_encoding($content['parts'][0]['text'], 'UTF-8', 'UTF-8');
                // Remove any null bytes or control characters that might cause issues
                $content['parts'][0]['text'] = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $content['parts'][0]['text']);
            }
        }
        unset($content); // Break reference

        $data = [
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => min($temperature, 0.3),  // Lower temperature for more reliable JSON
                'maxOutputTokens' => 8000,  // Increased token limit significantly
                'topP' => 0.8,  // Add top-p for better consistency
            ]
        ];

        log_message('error', 'AI API DEBUG: Data structure to send: ' . json_encode(['contents_count' => count($data['contents'])]));

        // Add API key to URL
        $url = $this->api_endpoint . '?key=' . $this->api_key;

        // Log the actual JSON being sent (first 500 chars)
        $json_payload = json_encode($data, JSON_UNESCAPED_UNICODE);
        $json_error = json_last_error();

        log_message('error', 'AI API DEBUG: JSON encode error code: ' . $json_error);
        log_message('error', 'AI API DEBUG: JSON payload length: ' . strlen($json_payload));
        log_message('error', 'AI API DEBUG: JSON payload (first 1000 chars): ' . substr($json_payload, 0, 1000));

        // Also log the raw data structure
        log_message('error', 'AI API DEBUG: Raw contents array count: ' . count($data['contents']));
        if (!empty($data['contents'])) {
            log_message('error', 'AI API DEBUG: First content item: ' . json_encode($data['contents'][0]));
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        // Fix SSL certificate issue on Windows
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        // Additional SSL and connection settings
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');

        // Add timeout settings to prevent hanging
        curl_setopt($ch, CURLOPT_TIMEOUT, 45); // 45 second timeout
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); // 10 second connection timeout

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        // Log detailed information for debugging
        log_message('debug', 'AI API call - HTTP Code: ' . $http_code);
        log_message('debug', 'AI API call - Response length: ' . strlen($response));

        if ($curl_error) {
            log_message('error', 'CURL error: ' . $curl_error);
            return [
                'success' => false,
                'error' => 'Connection error: ' . $curl_error . '. Please check your internet connection.'
            ];
        }

        // Handle 429 (Too Many Requests) with API key rotation and retry logic
        if ($http_code === 429) {
            log_message('warning', 'Rate limit hit (429) on API key index: ' . $this->current_key_index);

            // First, try rotating to the next API key
            if ($keys_tried < $max_keys - 1) {
                $rotation_success = $this->rotate_api_key();
                if ($rotation_success) {
                    log_message('info', 'Rotating to next API key (index: ' . $this->current_key_index . '). Keys tried: ' . ($keys_tried + 1) . '/' . $max_keys);
                    // Retry immediately with the new key
                    return $this->call_api($messages, $temperature, 0, $keys_tried + 1);
                }
            }

            // If all keys have been tried, use exponential backoff
            if ($retry_count < $max_retries) {
                // Exponential backoff: 2s, 4s, 8s
                $delay = $base_delay * pow(2, $retry_count);
                log_message('info', 'All API keys exhausted. Using exponential backoff. Retrying in ' . $delay . ' seconds... (Attempt ' . ($retry_count + 1) . '/' . $max_retries . ')');
                sleep($delay);

                // Reset to first key for the retry
                $this->reset_api_key_rotation();
                return $this->call_api($messages, $temperature, $retry_count + 1, 0);
            } else {
                log_message('error', 'Rate limit exceeded after trying all ' . $max_keys . ' API keys and ' . $max_retries . ' retries');
                // Reset to first key for next request
                $this->reset_api_key_rotation();
                return [
                    'success' => false,
                    'error' => 'API rate limit exceeded on all available keys. Please wait a moment and try again.'
                ];
            }
        }

        if ($http_code !== 200) {
            log_message('error', 'Gemini API error (HTTP ' . $http_code . '): ' . substr($response, 0, 500));
            return [
                'success' => false,
                'error' => 'AI service error (HTTP ' . $http_code . '). Please try again later.'
            ];
        }

        $result = json_decode($response, true);

        // Gemini response format
        if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            return [
                'success' => true,
                'content' => $result['candidates'][0]['content']['parts'][0]['text'],
                'tokens' => $result['usageMetadata']['totalTokenCount'] ?? 0
            ];
        }

        // Check for error in response
        if (isset($result['error'])) {
            log_message('error', 'Gemini API error: ' . json_encode($result['error']));
            return [
                'success' => false,
                'error' => 'AI error: ' . ($result['error']['message'] ?? 'Unknown error')
            ];
        }

        return [
            'success' => false,
            'error' => 'Invalid response from AI service'
        ];
    }
}