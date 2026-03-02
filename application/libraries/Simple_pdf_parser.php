<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Simple PDF Parser Library
 * A basic PDF text extraction library for CodeIgniter
 */
class Simple_pdf_parser {
    
    private $ci;
    
    public function __construct() {
        $this->ci =& get_instance();
    }
    
    /**
     * Extract text from PDF file
     */
    public function extract_text($file_path) {
        if (!file_exists($file_path)) {
            return false;
        }
        
        $content = file_get_contents($file_path);
        if (!$content) {
            return false;
        }
        
        return $this->parse_pdf_content($content);
    }
    
    /**
     * Parse PDF content and extract text
     */
    private function parse_pdf_content($content) {
        $text = '';
        
        // Method 1: Extract from compressed streams
        $text .= $this->extract_from_streams($content);
        
        // Method 2: Extract from text objects
        if (strlen($text) < 100) {
            $text .= $this->extract_from_text_objects($content);
        }
        
        // Method 3: Extract from simple text patterns
        if (strlen($text) < 100) {
            $text .= $this->extract_simple_text($content);
        }
        
        return $this->clean_text($text);
    }
    
    /**
     * Extract text from PDF streams
     */
    private function extract_from_streams($content) {
        $text = '';
        
        // Find all stream objects
        if (preg_match_all('/stream\s*\n(.*?)\nendstream/s', $content, $matches)) {
            foreach ($matches[1] as $stream) {
                $decoded = $this->decode_stream($stream);
                if ($decoded) {
                    $readable = $this->extract_readable_chars($decoded);
                    if (strlen($readable) > 20) {
                        $text .= $readable . ' ';
                    }
                }
            }
        }
        
        return $text;
    }
    
    /**
     * Extract text from PDF text objects (BT...ET blocks)
     */
    private function extract_from_text_objects($content) {
        $text = '';
        
        // Find text objects between BT and ET
        if (preg_match_all('/BT\s*(.*?)\s*ET/s', $content, $matches)) {
            foreach ($matches[1] as $textBlock) {
                // Extract Tj operations (show text)
                if (preg_match_all('/\((.*?)\)\s*Tj/s', $textBlock, $tjMatches)) {
                    foreach ($tjMatches[1] as $tjText) {
                        $text .= $this->decode_pdf_string($tjText) . ' ';
                    }
                }
                
                // Extract TJ operations (show text with positioning)
                if (preg_match_all('/\[(.*?)\]\s*TJ/s', $textBlock, $tjMatches)) {
                    foreach ($tjMatches[1] as $tjText) {
                        // Parse array of strings and numbers
                        $text .= $this->parse_tj_array($tjText) . ' ';
                    }
                }
            }
        }
        
        return $text;
    }
    
    /**
     * Extract simple text patterns
     */
    private function extract_simple_text($content) {
        $text = '';
        
        // Look for text in parentheses (common in PDFs)
        if (preg_match_all('/\(([^)]+)\)/', $content, $matches)) {
            foreach ($matches[1] as $match) {
                $decoded = $this->decode_pdf_string($match);
                if (strlen($decoded) > 3 && ctype_print($decoded)) {
                    $text .= $decoded . ' ';
                }
            }
        }
        
        return $text;
    }
    
    /**
     * Decode PDF stream (try different compression methods)
     */
    private function decode_stream($stream) {
        // Try gzuncompress (FlateDecode)
        $decoded = @gzuncompress($stream);
        if ($decoded !== false) {
            return $decoded;
        }
        
        // Try gzinflate
        $decoded = @gzinflate($stream);
        if ($decoded !== false) {
            return $decoded;
        }
        
        // Try gzinflate with different parameters
        $decoded = @gzinflate(substr($stream, 2));
        if ($decoded !== false) {
            return $decoded;
        }
        
        // Return original if no decompression worked
        return $stream;
    }
    
    /**
     * Decode PDF string (handle escape sequences)
     */
    private function decode_pdf_string($string) {
        // Handle common PDF escape sequences
        $string = str_replace(['\\(', '\\)', '\\\\', '\\n', '\\r', '\\t'], 
                             ['(', ')', '\\', "\n", "\r", "\t"], $string);
        
        // Remove other escape sequences
        $string = preg_replace('/\\\\[0-7]{1,3}/', '', $string);
        $string = preg_replace('/\\\\[^nrt\\\\()]/', '', $string);
        
        return $string;
    }
    
    /**
     * Parse TJ array (text with positioning)
     */
    private function parse_tj_array($array_content) {
        $text = '';
        
        // Simple parsing - extract strings from the array
        if (preg_match_all('/\(([^)]*)\)/', $array_content, $matches)) {
            foreach ($matches[1] as $match) {
                $text .= $this->decode_pdf_string($match);
            }
        }
        
        return $text;
    }
    
    /**
     * Extract readable characters from binary content
     */
    private function extract_readable_chars($content) {
        // Remove non-printable characters except spaces and newlines
        $text = preg_replace('/[^\x20-\x7E\s]/', '', $content);
        
        // Remove excessive whitespace
        $text = preg_replace('/\s+/', ' ', $text);
        
        return trim($text);
    }
    
    /**
     * Clean extracted text
     */
    private function clean_text($text) {
        // Remove excessive whitespace
        $text = preg_replace('/\s+/', ' ', $text);
        
        // Remove control characters
        $text = preg_replace('/[\x00-\x1F\x7F]/', '', $text);
        
        // Remove common PDF artifacts
        $text = str_replace(['obj', 'endobj', 'xref', 'trailer', 'startxref'], '', $text);
        
        // Trim and return
        return trim($text);
    }
}