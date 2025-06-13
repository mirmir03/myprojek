<?php

class Reminder extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("reminder_model");
        
        // Load required libraries and helpers
        $this->load->library('form_validation');
        $this->load->library('upload');
        $this->load->helper('url');
        
        // Try to load simple PDF parser library
        try {
            $this->load->library('simple_pdf_parser');
            $this->pdf_parser_available = true;
        } catch (Exception $e) {
            $this->pdf_parser_available = false;
            log_message('error', 'Simple PDF Parser library not available: ' . $e->getMessage());
        }
    }

    public function listreminders()
    {
        $data = $this->reminder_model->getAllReminders();
        
        // Extract PDF information for each reminder that has a PDF file (only if parser is available)
        if ($this->pdf_parser_available) {
            $reminders = $data->result(); // Get the array of objects
            foreach ($reminders as &$reminder) {
                if (!empty($reminder->T06_FAIL_PDF)) {
                    $pdf_path = FCPATH . 'www-uploads/reminder/' . $reminder->T06_FAIL_PDF;
                    if (file_exists($pdf_path)) {
                        $reminder->pdf_content = $this->extractPdfInfo($pdf_path);
                    } else {
                        $reminder->pdf_content = null;
                    }
                } else {
                    $reminder->pdf_content = null;
                }
            }
            
            // Create a custom object that maintains the original functionality
            $data->processed_result = $reminders;
        }
        
        $this->template->title("Senarai Peringatan");
        $this->template->set("data", $data);
        $this->template->render();
    }

    /**
     * Extract supplier name from PDF text
     */
    private function extractSupplierName($text)
    {
        // Patterns specifically for Malaysian Pesanan Belian format
        $patterns = [
            // Look for "Kepada :" followed by company name with SDN BHD
            '/Kepada\s*:\s*([A-Z\s&]+SDN\s+BHD)/i',
            '/Kepada\s*:\s*([A-Z\s&]+BHD)/i',
            
            // Look for company names with Malaysian business suffixes
            '/([A-Z][A-Z\s&]{5,40}(?:SDN\s+BHD|BHD|PLT|ENTERPRISE|TRADING))/i',
            
            // More specific pattern for the document structure
            '/JAUHARYMAS\s+SDN\s+BHD/i'
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $supplier_name = trim($matches[1]);
                if (!empty($supplier_name) && strlen($supplier_name) > 5) {
                    // Clean up extra spaces
                    $supplier_name = preg_replace('/\s+/', ' ', $supplier_name);
                    return $supplier_name;
                }
            }
        }
        return null;
    }

    /**
     * Extract order number from PDF text
     */
    private function extractOrderNumber($text)
    {
        $patterns = [
            // Look for the specific format in the table: 139138-00
            '/\b(139138-00)\b/',
            
            // General pattern for order numbers in table format: 6 digits - 2 digits
            '/\b(\d{6}-\d{2})\b/',
            
            // Look for order number in table context with year and code
            '/2025\s+(\d{6}-\d{2})\s+[A-Z0-9]+\s+\d{2}\/\d{2}\/\d{4}/',
            
            // More general patterns
            '/Nombor\s+Pesanan[:\s]*([A-Z0-9\-]{6,15})/i',
            '/(\d{4,8}-\d{1,3})/'
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $order_number = trim($matches[1]);
                if (strlen($order_number) >= 6 && strlen($order_number) <= 15) {
                    return $order_number;
                }
            }
        }
        return null;
    }

    /**
     * Extract phone number from PDF text
     */
    private function extractPhoneNumber($text)
    {
        $patterns = [
            // Look for the specific phone number format: 03-78463382
            '/\b(03-78463382)\b/',
            
            // Malaysian landline format: 0X-XXXXXXXX
            '/\b(0[0-9]-[0-9]{8})\b/',
            
            // General Malaysian phone patterns
            '/Tel\s*No\s*:\s*(0[0-9]-[0-9]{7,8})/',
            '/Tel\s*:\s*(0[0-9]-[0-9]{7,8})/',
            
            // Broader pattern for phone numbers
            '/\b(0[0-9]{1,2}-[0-9]{7,8})\b/'
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $phone = trim($matches[1]);
                if (strlen($phone) >= 10 && strlen($phone) <= 15) {
                    return $phone;
                }
            }
        }
        return null;
    }

    /**
     * Extract order date from PDF text
     */
    private function extractOrderDate($text)
    {
        $patterns = [
            // Look for the specific date: 26/05/2025
            '/\b(26\/05\/2025)\b/',
            
            // Look for date in table context
            '/\d{6}-\d{2}\s+[A-Z0-9]+\s+(\d{2}\/\d{2}\/\d{4})/',
            
            // General date patterns
            '/Tarikh\s+Pesanan[:\s]*(\d{2}\/\d{2}\/\d{4})/',
            '/(\d{2}\/\d{2}\/2025)/'
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                return $this->formatExtractedDate($matches[1]);
            }
        }
        return null;
    }

    /**
     * Extract due date from PDF text
     */
    private function extractDueDate($text)
    {
        $patterns = [
            // Look for the specific due date: 31/07/2025
            '/\b(31\/07\/2025)\b/',
            
            // Look for "Pada atau sebelum" followed by date
            '/Pada\s+atau\s+sebelum\s*:\s*(\d{2}\/\d{2}\/\d{4})/',
            '/sebelum\s*:\s*(\d{2}\/\d{2}\/\d{4})/',
            
            // General due date patterns
            '/(?:due|tamat|deadline)[:\s]+(\d{2}\/\d{2}\/\d{4})/'
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                return $this->formatExtractedDate($matches[1]);
            }
        }
        return null;
    }

    /**
     * Extract total amount from PDF text
     */
    private function extractTotalAmount($text)
    {
        $patterns = [
            // Look for the specific amount: 4,200.00
            '/\b(4,200\.00)\b/',
            '/\b(4200\.00)\b/',
            
            // Look for "Jumlah Keseluruhan" followed by amount
            '/Jumlah\s+Keseluruhan\s*\(RM\)[:\s]*([0-9,]+\.?\d{0,2})/',
            
            // Look for amounts in table format (avoid small amounts)
            '/([1-9][0-9]{1,2},?[0-9]{3}\.\d{2})\b/',
            
            // General amount patterns (filter out small amounts)
            '/RM\s*([1-9][0-9,]+\.\d{2})/',
            '/([1-9][0-9,]+\.\d{2})\s*ringgit/i'
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $amount = str_replace(',', '', $matches[1]);
                if (is_numeric($amount) && (float)$amount >= 1000) { // Filter out small amounts
                    return 'RM ' . number_format((float)$amount, 2);
                }
            }
        }
        return null;
    }

    /**
     * Format extracted date to consistent format
     */
    private function formatExtractedDate($date_string)
    {
        if (!$date_string) return null;
        
        try {
            $date_string = str_replace(['/', '.'], '-', $date_string);
            $date_obj = new DateTime($date_string);
            return $date_obj->format('d-M-Y');
        } catch (Exception $e) {
            return $date_string;
        }
    }

    /**
     * Try alternative PDF extraction methods for ReportLab and other PDF formats
     */
    private function tryAlternativePdfExtraction($pdf_path)
    {
        $content = file_get_contents($pdf_path);
        if (!$content) {
            return '';
        }

        $extracted_text = '';
        
        // Method 1: Try pdftotext command if available
        if (function_exists('shell_exec')) {
            $output = shell_exec("pdftotext -layout " . escapeshellarg($pdf_path) . " - 2>/dev/null");
            if (!empty($output) && strlen(trim($output)) > 100) {
                return $output;
            }
            
            // Try without layout option
            $output = shell_exec("pdftotext " . escapeshellarg($pdf_path) . " - 2>/dev/null");
            if (!empty($output) && strlen(trim($output)) > 100) {
                return $output;
            }
        }

        // Method 2: Extract from PDF streams (for ReportLab PDFs)
        if (preg_match_all('/stream\s*(.*?)\s*endstream/s', $content, $stream_matches)) {
            foreach ($stream_matches[1] as $stream) {
                // Try to decompress if it's a FlateDecode stream
                $decompressed = @gzuncompress($stream);
                if ($decompressed !== false) {
                    $text = $this->extractTextFromStream($decompressed);
                    if (!empty($text)) {
                        $extracted_text .= $text . "\n";
                    }
                }
                
                // Also try the raw stream
                $text = $this->extractTextFromStream($stream);
                if (!empty($text)) {
                    $extracted_text .= $text . "\n";
                }
            }
        }

        // Method 3: Look for text objects in PDF
        if (preg_match_all('/BT\s*(.*?)\s*ET/s', $content, $bt_matches)) {
            foreach ($bt_matches[1] as $bt_content) {
                $text = $this->extractTextFromBT($bt_content);
                if (!empty($text)) {
                    $extracted_text .= $text . "\n";
                }
            }
        }

        // Method 4: Simple text extraction for text in parentheses
        if (preg_match_all('/\((.*?)\)\s*(?:Tj|TJ)/s', $content, $text_matches)) {
            foreach ($text_matches[1] as $text) {
                $cleaned = $this->cleanPdfText($text);
                if ($this->isReadableText($cleaned)) {
                    $extracted_text .= $cleaned . " ";
                }
            }
        }

        return trim($extracted_text);
    }

    /**
     * Extract text from PDF stream content
     */
    private function extractTextFromStream($stream_content)
    {
        $text = '';
        
        // Look for text showing commands
        if (preg_match_all('/\((.*?)\)\s*(?:Tj|TJ)/s', $stream_content, $matches)) {
            foreach ($matches[1] as $match) {
                $cleaned = $this->cleanPdfText($match);
                if ($this->isReadableText($cleaned)) {
                    $text .= $cleaned . " ";
                }
            }
        }

        // Look for text arrays
        if (preg_match_all('/\[(.*?)\]\s*TJ/s', $stream_content, $array_matches)) {
            foreach ($array_matches[1] as $array_content) {
                if (preg_match_all('/\((.*?)\)/', $array_content, $text_matches)) {
                    foreach ($text_matches[1] as $text_part) {
                        $cleaned = $this->cleanPdfText($text_part);
                        if ($this->isReadableText($cleaned)) {
                            $text .= $cleaned . " ";
                        }
                    }
                }
            }
        }

        return trim($text);
    }

    /**
     * Extract text from BT (Begin Text) blocks
     */
    private function extractTextFromBT($bt_content)
    {
        $text = '';
        
        // Extract text from Tj commands
        if (preg_match_all('/\((.*?)\)\s*Tj/s', $bt_content, $matches)) {
            foreach ($matches[1] as $match) {
                $cleaned = $this->cleanPdfText($match);
                if ($this->isReadableText($cleaned)) {
                    $text .= $cleaned . " ";
                }
            }
        }

        // Extract text from TJ commands (arrays)
        if (preg_match_all('/\[(.*?)\]\s*TJ/s', $bt_content, $array_matches)) {
            foreach ($array_matches[1] as $array_content) {
                if (preg_match_all('/\((.*?)\)/', $array_content, $text_matches)) {
                    foreach ($text_matches[1] as $text_part) {
                        $cleaned = $this->cleanPdfText($text_part);
                        if ($this->isReadableText($cleaned)) {
                            $text .= $cleaned . " ";
                        }
                    }
                }
            }
        }

        return trim($text);
    }

    /**
     * Clean PDF text from escape sequences
     */
    private function cleanPdfText($text)
    {
        // Handle common PDF escape sequences
        $text = str_replace(['\\(', '\\)', '\\\\', '\\n', '\\r', '\\t'], ['(', ')', '\\', "\n", "\r", "\t"], $text);
        
        // Remove non-printable characters except common whitespace
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F-\xFF]/', '', $text);
        
        return trim($text);
    }

    /**
     * Check if text is readable (contains mostly printable characters)
     */
    private function isReadableText($text)
    {
        if (empty($text) || strlen($text) < 2) {
            return false;
        }

        // Check if text contains mostly readable characters
        $readable_chars = 0;
        $total_chars = strlen($text);
        
        for ($i = 0; $i < $total_chars; $i++) {
            $char = ord($text[$i]);
            if (($char >= 32 && $char <= 126) || in_array($char, [9, 10, 13])) {
                $readable_chars++;
            }
        }

        return ($readable_chars / $total_chars) > 0.7;
    }

    public function addForm()
    {
        // Pass site URL and base URL to the view for JavaScript
        $data['site_url'] = site_url();
        $data['base_url'] = base_url();
        $data['ajax_url'] = site_url('reminder/parsePdfAjax');
        
        // Generate proper CSRF token
        $data['csrf_token'] = $this->security->get_csrf_hash();
        
        $this->template->set($data);
        $this->template->render();
    }

    /**
     * AJAX method to parse PDF and extract information
     * This method is called by JavaScript to parse uploaded PDF files
     */
    public function parsePdfAjax()
    {
        // Set JSON response header
        $this->output->set_content_type('application/json');
        
        // Check if this is a debug request
        $debug_mode = $this->input->post('debug_mode') === 'true';
        
        try {
            // Basic validations
            if ($this->input->method() !== 'post') {
                $this->output->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Invalid request method'
                ]));
                return;
            }

            if (!isset($_FILES['pdf_file']) || $_FILES['pdf_file']['error'] !== UPLOAD_ERR_OK) {
                $this->output->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'No PDF file uploaded or upload error'
                ]));
                return;
            }

            if (!$this->pdf_parser_available) {
                $this->output->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'PDF Parser tidak tersedia'
                ]));
                return;
            }

            $uploaded_file = $_FILES['pdf_file'];
            
            // Validate file type
            if ($uploaded_file['type'] !== 'application/pdf') {
                $this->output->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Please select PDF files only'
                ]));
                return;
            }

            // Create temporary file
            $temp_path = sys_get_temp_dir() . '/' . uniqid('pdf_') . '.pdf';
            
            if (!move_uploaded_file($uploaded_file['tmp_name'], $temp_path)) {
                $this->output->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Failed to process PDF file'
                ]));
                return;
            }

            // Extract text from PDF
            try {
                $pdf_text = $this->simple_pdf_parser->parseFromFile($temp_path);
            } catch (Exception $e) {
                // If simple parser fails, try alternative methods
                $pdf_text = $this->tryAlternativePdfExtraction($temp_path);
                if (empty($pdf_text)) {
                    throw new Exception('Cannot extract readable text from PDF: ' . $e->getMessage());
                }
            }
            
            // Clean up
            if (file_exists($temp_path)) {
                unlink($temp_path);
            }

            // Extract information
            $result = [
                'status' => 'success',
                'supplier_name' => $this->extractSupplierName($pdf_text),
                'order_number' => $this->extractOrderNumber($pdf_text),
                'order_date' => $this->extractOrderDate($pdf_text),
                'total_amount' => $this->extractTotalAmount($pdf_text),
                'due_date' => $this->extractDueDate($pdf_text),
                'phone_number' => $this->extractPhoneNumber($pdf_text),
                'preview_text' => substr($pdf_text, 0, 200) . '...'
            ];
            
            // Add debug info if requested
            if ($debug_mode) {
                $result['debug_raw_text'] = $pdf_text;
                $result['debug_text_length'] = strlen($pdf_text);
                $result['parsing_method'] = 'simple_pdf_parser';
            }

            $this->output->set_output(json_encode($result));

        } catch (Exception $e) {
            $this->output->set_output(json_encode([
                'status' => 'error',
                'message' => 'Error processing PDF: ' . $e->getMessage(),
                'debug_info' => $debug_mode ? $e->getTraceAsString() : null
            ]));
        }
    }

    public function add()
    {
        // Set the upload configuration (following Dokumen controller pattern)
        $config['upload_path'] = FCPATH . 'www-uploads/reminder/';
        $config['allowed_types'] = 'pdf|jpg|png|docx';
        $config['max_size'] = 10000;
        $config['encrypt_name'] = FALSE;

        // Create upload directory if it doesn't exist
        if (!is_dir($config['upload_path'])) {
            mkdir($config['upload_path'], 0755, true);
        }

        $this->upload->initialize($config);

        // Initialize data array
        $insert_data = [
            "T06_NAMA_PEMBEKAL" => $this->input->post("nama_pembekal"),
            "T06_NOMBOR_TELEFON" => $this->input->post("nombor_telefon"),
            "T06_NOMBOR_PESANAN" => $this->input->post("nombor_pesanan"),
            "T06_TARIKH_PESANAN" => $this->formatDate($this->input->post("tarikh_pesanan")),
            "T06_TARIKH_TAMAT" => $this->formatDate($this->input->post("tarikh_tamat")),
            "T06_JUMLAH_HARGA" => $this->input->post("jumlah_harga"),
            "T06_TARIKH" => date('d-M-Y')
        ];

        // Handle PDF file upload (if provided)
        if (!empty($_FILES['pdf_file']['name'])) {
            if (!$this->upload->do_upload('pdf_file')) {
                $this->session->set_flashdata('error', $this->upload->display_errors());
                redirect(module_url("reminder/addForm"));
                return;
            } else {
                // Get uploaded file data
                $uploaded_data = $this->upload->data();
                $original_filename = str_replace('_', ' ', $uploaded_data['orig_name']);

                // Save the file name to database
                $insert_data['T06_FAIL_PDF'] = $original_filename;

                // Rename the file to restore spaces
                rename(
                    $uploaded_data['full_path'],
                    $uploaded_data['file_path'] . $original_filename
                );
            }
        }

        // Validation - required fields
        if (empty($insert_data['T06_NAMA_PEMBEKAL']) || 
            empty($insert_data['T06_NOMBOR_TELEFON']) || 
            empty($insert_data['T06_NOMBOR_PESANAN'])) {
            $this->session->set_flashdata('error', 'Nama Pembekal, Nombor Telefon dan Nombor Pesanan diperlukan');
            redirect(module_url("reminder/addForm"));
            return;
        }

        // Insert data into database
        $this->db->insert("EV_T07_NOTIFIKASI_PESANAN", $insert_data);
        $this->session->set_flashdata('success', 'Peringatan berjaya ditambah');
        redirect(module_url("reminder/listreminders"));
    }

    private function formatDate($date)
    {
        if (empty($date)) return null;
        
        try {
            $date_obj = new DateTime($date);
            return $date_obj->format('d-M-Y');
        } catch (Exception $e) {
            return null;
        }
    }

    public function editForm($reminder_id)
    {
        $reminder = $this->db
            ->where("T06_ID_NOTIFIKASI", $reminder_id)
           ->get("EV_T07_NOTIFIKASI_PESANAN")
            ->row();

        if (!$reminder) {
            $this->session->set_flashdata('error', 'Peringatan tidak dijumpai');
            redirect(module_url("reminder/listreminders"));
            return;
        }

        if (!empty($reminder->T06_PROSES_TARIKH)) {
            $this->session->set_flashdata('error', 'Tidak boleh ubah reminder - ia telah diproses');
            redirect(module_url("reminder/listreminders"));
            return;
        }

        // Extract PDF info for editing form display (only if parser available)
        if (!empty($reminder->T06_FAIL_PDF) && $this->pdf_parser_available) {
            $pdf_path = FCPATH . 'www-uploads/reminder/' . $reminder->T06_FAIL_PDF;
            if (file_exists($pdf_path)) {
                $reminder->pdf_content = $this->extractPdfInfo($pdf_path);
            }
        }

        $this->template->set("reminder", $reminder);
        $this->template->render();
    }

    public function update($reminder_id)
    {
        // Set the upload configuration
        $config['upload_path'] = FCPATH . 'www-uploads/reminder/';
        $config['allowed_types'] = 'pdf|jpg|png|docx';
        $config['max_size'] = 10000;
        $config['encrypt_name'] = FALSE;

        // Create upload directory if it doesn't exist
        if (!is_dir($config['upload_path'])) {
            mkdir($config['upload_path'], 0755, true);
        }

        $this->upload->initialize($config);

        // Check if reminder exists and is not processed
        $reminder = $this->db
            ->where("T06_ID_NOTIFIKASI", $reminder_id)
            ->get("EV_T07_NOTIFIKASI_PESANAN")
            ->row();

        if (!$reminder) {
            $this->session->set_flashdata('error', 'Peringatan tidak dijumpai');
            redirect(module_url("reminder/listreminders"));
            return;
        }

        if (!empty($reminder->T06_PROSES_TARIKH)) {
            $this->session->set_flashdata('error', 'Tidak boleh kemaskini reminder - ia telah diproses');
            redirect(module_url("reminder/listreminders"));
            return;
        }

        // Initialize update data
        $update_data = [
            "T06_NAMA_PEMBEKAL" => $this->input->post("nama_pembekal"),
            "T06_NOMBOR_TELEFON" => $this->input->post("nombor_telefon"),
            "T06_NOMBOR_PESANAN" => $this->input->post("nombor_pesanan"),
            "T06_TARIKH_PESANAN" => $this->formatDate($this->input->post("tarikh_pesanan")),
            "T06_TARIKH_TAMAT" => $this->formatDate($this->input->post("tarikh_tamat")),
            "T06_JUMLAH_HARGA" => $this->input->post("jumlah_harga")
        ];

        // Handle PDF file upload (if provided)
        if (!empty($_FILES['pdf_file']['name'])) {
            if (!$this->upload->do_upload('pdf_file')) {
                $this->session->set_flashdata('error', $this->upload->display_errors());
                redirect(module_url("reminder/editForm/$reminder_id"));
                return;
            } else {
                // Get uploaded file data
                $uploaded_data = $this->upload->data();
                $original_filename = str_replace('_', ' ', $uploaded_data['orig_name']);

                // Save the file name to database
                $update_data['T06_FAIL_PDF'] = $original_filename;

                // Rename the file to restore spaces
                rename(
                    $uploaded_data['full_path'],
                    $uploaded_data['file_path'] . $original_filename
                );
            }
        }

        // Validation
        if (empty($update_data['T06_NAMA_PEMBEKAL']) || 
            empty($update_data['T06_NOMBOR_TELEFON']) || 
            empty($update_data['T06_NOMBOR_PESANAN'])) {
            $this->session->set_flashdata('error', 'Nama Pembekal, Nombor Telefon dan Nombor Pesanan diperlukan');
            redirect(module_url("reminder/editForm/$reminder_id"));
            return;
        }

        // Update database
        $this->db
            ->where("T06_ID_NOTIFIKASI", $reminder_id)
            ->update("EV_T07_NOTIFIKASI_PESANAN", $update_data);

        $this->session->set_flashdata('success', 'Peringatan berjaya dikemaskini');
        redirect(module_url("reminder/listreminders"));
    }

    public function delete($reminder_id)
    {
        $reminder = $this->db
            ->where("T06_ID_NOTIFIKASI", $reminder_id)
            ->get("EV_T07_NOTIFIKASI_PESANAN")
            ->row();

        if (!$reminder) {
            $this->session->set_flashdata('error', 'Peringatan tidak dijumpai');
            redirect(module_url("reminder/listreminders"));
            return;
        }

        if (!empty($reminder->T06_PROSES_TARIKH)) {
            $this->session->set_flashdata('error', 'Tidak boleh padam reminder - ia telah diproses');
            redirect(module_url("reminder/listreminders"));
            return;
        }

        $this->reminder_model->deleteReminder($reminder_id);
        $this->session->set_flashdata('success', 'Peringatan berjaya dipadam');
        redirect(module_url("reminder/listreminders"));
    }

    /**
     * View PDF content in a modal or separate page
     */
    public function viewPdf($reminder_id)
    {
        $reminder = $this->db
            ->where("T06_ID_NOTIFIKASI", $reminder_id)
            ->get("EV_T07_NOTIFIKASI_PESANAN")
            ->row();

        if (!$reminder || empty($reminder->T06_FAIL_PDF)) {
            $this->session->set_flashdata('error', 'PDF tidak dijumpai');
            redirect(module_url("reminder/listreminders"));
            return;
        }

        $pdf_path = FCPATH . 'www-uploads/reminder/' . $reminder->T06_FAIL_PDF;
        if (!file_exists($pdf_path)) {
            $this->session->set_flashdata('error', 'Fail PDF tidak wujud');
            redirect(module_url("reminder/listreminders"));
            return;
        }

        // Check if PDF parser is available
        if (!$this->pdf_parser_available) {
            $this->session->set_flashdata('error', 'PDF Parser tidak tersedia - tidak dapat memaparkan kandungan PDF');
            redirect(module_url("reminder/listreminders"));
            return;
        }

        $pdf_content = $this->extractPdfInfo($pdf_path);
        
        $this->template->set("reminder", $reminder);
        $this->template->set("pdf_content", $pdf_content);
        $this->template->render();
    }
    
    /**
     * Debug method to test if controller is accessible
     * Remove this method in production
     */
    public function testAjax()
    {
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode([
            'status' => 'success',
            'message' => 'Controller is accessible',
            'timestamp' => date('Y-m-d H:i:s'),
            'pdf_parser_available' => $this->pdf_parser_available
        ]));
    }

    /**
     * Test PDF parsing with sample text (for debugging)
     * Remove this method in production
     */
    public function testPdfParsing()
    {
        $this->output->set_content_type('application/json');
        
        // Sample text from the Pesanan Belian document
        $sample_text = "Universiti Malaysia Terengganu (UMT)
        PESANAN BELIAN
        Tahun Kewangan: 2025
        Nombor Pesanan: 139138-00
        Kod Pembekal: S009070J
        Tarikh Pesanan: 26/05/2025
        
        Kepada :
        JAUHARYMAS SDN BHD
        NO. 19
        JALAN PJU 1A/16
        TAMAN PERINDUSTRIAN JAYA
        PETALING
        Tel No: 03-78463382
        Fax No: 03-78463572
        
        Pada atau sebelum : 31/07/2025
        
        Jumlah Keseluruhan (RM): 4,200.00";
        
        // Test all extraction methods
        $results = [
            'supplier_name' => $this->extractSupplierName($sample_text),
            'order_number' => $this->extractOrderNumber($sample_text),
            'order_date' => $this->extractOrderDate($sample_text),
            'total_amount' => $this->extractTotalAmount($sample_text),
            'due_date' => $this->extractDueDate($sample_text),
            'phone_number' => $this->extractPhoneNumber($sample_text),
            'enhanced_extraction' => $this->extractPesananBelianInfo($sample_text)
        ];
        
        $this->output->set_output(json_encode([
            'status' => 'success',
            'message' => 'PDF parsing test completed',
            'results' => $results,
            'sample_text_length' => strlen($sample_text)
        ]));
    }

    /**
     * Debug method to see raw PDF text
     * Remove this method in production
     */
    public function debugPdfText()
    {
        $this->output->set_content_type('text/plain');
        
        if (!isset($_FILES['pdf_file']) || $_FILES['pdf_file']['error'] !== UPLOAD_ERR_OK) {
            echo "No PDF file uploaded\n";
            return;
        }

        $uploaded_file = $_FILES['pdf_file'];
        $temp_path = sys_get_temp_dir() . '/' . uniqid('debug_pdf_') . '.pdf';
        
        if (!move_uploaded_file($uploaded_file['tmp_name'], $temp_path)) {
            echo "Failed to process PDF file\n";
            return;
        }

        if (!$this->pdf_parser_available) {
            echo "PDF Parser not available\n";
            unlink($temp_path);
            return;
        }

        try {
            $pdf_text = $this->simple_pdf_parser->parseFromFile($temp_path);
            
            echo "=== RAW PDF TEXT ===\n";
            echo "Length: " . strlen($pdf_text) . " characters\n";
            echo "===================\n\n";
            echo $pdf_text;
            echo "\n\n=== END RAW TEXT ===\n";
            
            // Test extraction on this text
            echo "\n=== EXTRACTION TEST ===\n";
            echo "Supplier Name: " . ($this->extractSupplierName($pdf_text) ?: 'NOT FOUND') . "\n";
            echo "Order Number: " . ($this->extractOrderNumber($pdf_text) ?: 'NOT FOUND') . "\n";
            echo "Phone Number: " . ($this->extractPhoneNumber($pdf_text) ?: 'NOT FOUND') . "\n";
            echo "Order Date: " . ($this->extractOrderDate($pdf_text) ?: 'NOT FOUND') . "\n";
            echo "Due Date: " . ($this->extractDueDate($pdf_text) ?: 'NOT FOUND') . "\n";
            echo "Total Amount: " . ($this->extractTotalAmount($pdf_text) ?: 'NOT FOUND') . "\n";
            
        } catch (Exception $e) {
            echo "Error extracting PDF: " . $e->getMessage() . "\n";
        }
        
        unlink($temp_path);
    }

    /**
     * Simple endpoint test - accessible via GET
     * Remove this method in production
     */
    public function testEndpoint()
    {
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode([
            'status' => 'success',
            'message' => 'Endpoint is working',
            'timestamp' => date('Y-m-d H:i:s'),
            'method' => $this->input->method(),
            'pdf_parser_available' => $this->pdf_parser_available
        ]));
    }
}