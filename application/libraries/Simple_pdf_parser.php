<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Simple_pdf_parser {
    
    public function parseFromFile($file_path) {
        // Method 1: Try pdftotext command if available
        if (function_exists('shell_exec')) {
            $output = shell_exec("pdftotext -layout " . escapeshellarg($file_path) . " -");
            if (!empty($output) && strlen(trim($output)) > 10) {
                return $output;
            }
        }
        
        // Method 2: Basic PDF content extraction
        $content = file_get_contents($file_path);
        if (!$content) {
            throw new Exception('Cannot read PDF file');
        }
        
        // Simple text extraction - look for text in parentheses
        $text = '';
        if (preg_match_all('/\((.*?)\)/', $content, $matches)) {
            foreach ($matches[1] as $match) {
                $cleaned = str_replace(['\\(', '\\)', '\\\\'], ['(', ')', '\\'], $match);
                if (strlen($cleaned) > 1 && ctype_print($cleaned)) {
                    $text .= $cleaned . ' ';
                }
            }
        }
        
        if (empty($text)) {
            throw new Exception('Cannot extract readable text from PDF');
        }
        
        return $text;
    }
}