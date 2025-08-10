<?php

class Reminder extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("reminder_model");
        $this->load->helper('file');
        $this->load->library(['upload', 'session']);
    }

    public function index()
    {
        redirect(module_url("reminder/list"));
    }

    public function list()
    {
        $data = $this->reminder_model->get_all_reminders();
        
        $this->template->title("Senarai Pesanan - Reminder");
        $this->template->set("data", $data);
        $this->template->render();
    }

    public function upload()
    {
        $this->template->title("Upload PDF Pesanan");
        $this->template->render();
    }

    public function process_upload()
    {
        // Configure upload settings
        $config['upload_path'] = './uploads/pdf/';
        $config['allowed_types'] = 'pdf';
        $config['max_size'] = 5120; // 5MB
        $config['file_name'] = 'pesanan_' . time();
        
        // Create upload directory if it doesn't exist
        if (!is_dir($config['upload_path'])) {
            mkdir($config['upload_path'], 0755, true);
        }

        $this->upload->initialize($config);

        if (!$this->upload->do_upload('pdf_file')) {
            $error = $this->upload->display_errors('', '');
            $this->session->set_flashdata('error', 'Upload failed: ' . $error);
            redirect(module_url("reminder/upload"));
            return;
        }

        $upload_data = $this->upload->data();
        $file_path = $upload_data['full_path'];

        try {
            // Parse PDF and extract data
            $extracted_data = $this->parse_pdf($file_path);
            
            if (empty($extracted_data)) {
                $this->session->set_flashdata('error', 'No valid data found in PDF file');
                redirect(module_url("reminder/upload"));
                return;
            }

            // Save extracted data to database
            $saved_count = 0;
            foreach ($extracted_data as $data) {
                if ($this->reminder_model->save_reminder($data)) {
                    $saved_count++;
                }
            }

            // Clean up uploaded file
            unlink($file_path);

            $this->session->set_flashdata('success', "Successfully extracted and saved {$saved_count} records from PDF");
            redirect(module_url("reminder/list"));

        } catch (Exception $e) {
            // Clean up uploaded file
            if (file_exists($file_path)) {
                unlink($file_path);
            }
            
            log_message('error', 'PDF parsing error: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Error processing PDF: ' . $e->getMessage());
            redirect(module_url("reminder/upload"));
        }
    }

    private function parse_pdf($file_path)
    {
        $extracted_data = [];
        
        try {
            // Method 1: Using pdftotext (requires poppler-utils installed on server)
            if (function_exists('shell_exec')) {
                $text = shell_exec("pdftotext '$file_path' -");
                if ($text) {
                    $extracted_data = $this->extract_pesanan_data($text);
                }
            }
            
            // Method 2: Alternative using PHP PDF parser library
            // Uncomment and modify based on your chosen PDF library
            /*
            require_once APPPATH . 'third_party/pdfparser/vendor/autoload.php';
            
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($file_path);
            $text = $pdf->getText();
            
            $extracted_data = $this->extract_pesanan_data($text);
            */
            
        } catch (Exception $e) {
            log_message('error', 'PDF parsing failed: ' . $e->getMessage());
            throw $e;
        }
        
        return $extracted_data;
    }

    private function extract_pesanan_data($text)
    {
        $extracted_data = [];
        
        // Split text into lines for processing
        $lines = explode("\n", $text);
        $text_content = implode(" ", $lines); // Also keep as single string for some patterns
        
        $current_record = [];
        
        // Extract Purchase Order Number - pattern: 139138-00
        if (preg_match('/(\d{6}-\d{2})/', $text_content, $matches)) {
            $current_record['nombor_pesanan'] = $matches[1];
        }
        
        // Extract Supplier Code - pattern: S009070J
        if (preg_match('/([S][0-9]{6}[A-Z])/', $text_content, $matches)) {
            $current_record['kod_pembekal'] = $matches[1];
        }
        
        // Extract Supplier Name - look for company name after supplier code
        if (preg_match('/JAUHARYMAS\s+SDN\s+BHD/i', $text_content, $matches)) {
            $current_record['nama_pembekal'] = 'JAUHARYMAS SDN BHD';
        }
        
        // Extract Phone Number - pattern: 03-78463382
        if (preg_match('/(\d{2,3}-\d{8})/', $text_content, $matches)) {
            $current_record['nombor_telefon'] = $matches[1];
        }
        
        // Extract Order Date - pattern: 26/05/2025
        if (preg_match('/(\d{2}\/\d{2}\/\d{4})/', $text_content, $matches)) {
            $current_record['tarikh_pesanan'] = $matches[1];
        }
        
        // Extract Due Date - look for "Pada atau sebelum" followed by date
        if (preg_match('/(?:Pada atau sebelum|sebelum)\s*:?\s*(\d{2}\/\d{2}\/\d{4})/', $text_content, $matches)) {
            $current_record['tamat_pesanan'] = $matches[1];
        }
        
        // Extract Total Amount - pattern: 4200.00 or 4,200.00
        if (preg_match('/(\d{1,3}(?:,\d{3})*(?:\.\d{2})?)/', $text_content, $matches)) {
            $amount = str_replace(',', '', $matches[1]);
            if ((float)$amount > 0) {
                $current_record['jumlah_harga'] = $amount;
            }
        }
        
        // Extract Asset Number if available
        if (preg_match('/NO\s+ASET\s*:\s*([A-Z0-9]+)/i', $text_content, $matches)) {
            $current_record['no_aset'] = $matches[1];
        }
        
        // Extract Year - pattern: 2025
        if (preg_match('/(\d{4})/', $text_content, $matches)) {
            $current_record['tahun'] = $matches[1];
        }
        
        // Extract Description/Service
        if (preg_match('/TROUBLESHOOTING\s+FOR.*?SISTEM\s+CR/s', $text_content, $matches)) {
            $current_record['keterangan'] = trim($matches[0]);
        }
        
        // Create the final record if we have essential data
        if (!empty($current_record)) {
            $record = [
                'T06_ID_NOTIFIKASI' => $current_record['nombor_pesanan'] ?? 'AUTO_' . time(),
                'T06_NAMA_PEMBEKAL' => $current_record['nama_pembekal'] ?? 'Unknown Supplier',
                'T06_NOMBOR_TELEFON' => $current_record['nombor_telefon'] ?? '',
                'T06_NOMBOR_PESANAN' => $current_record['nombor_pesanan'] ?? '',
                'T06_TARIKH_PESANAN' => $this->format_date($current_record['tarikh_pesanan'] ?? ''),
                'T06_TAMAT_PESANAN' => $this->format_date($current_record['tamat_pesanan'] ?? ''),
                'T06_JUMLAH_HARGA' => $current_record['jumlah_harga'] ?? '0',
                'T06_TARIKH' => date('d-M-y'),
                'T06_KOD_PEMBEKAL' => $current_record['kod_pembekal'] ?? '',
                'T06_NO_ASET' => $current_record['no_aset'] ?? '',
                'T06_TAHUN' => $current_record['tahun'] ?? date('Y'),
                'T06_KETERANGAN' => $current_record['keterangan'] ?? ''
            ];
            
            $extracted_data[] = $record;
        }
        
        return $extracted_data;
    }

    private function format_date($date_string)
    {
        if (empty($date_string)) {
            return date('d-M-y');
        }
        
        try {
            // Handle different date formats
            $date_string = str_replace('/', '-', $date_string);
            
            // Try to parse the date - expecting DD/MM/YYYY or DD-MM-YYYY
            if (preg_match('/(\d{1,2})[-\/](\d{1,2})[-\/](\d{4})/', $date_string, $matches)) {
                $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
                $month = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
                $year = $matches[3];
                
                // Convert to Oracle format (DD-MON-YY)
                $date = DateTime::createFromFormat('d-m-Y', "$day-$month-$year");
                if ($date) {
                    return $date->format('d-M-y');
                }
            }
            
            return date('d-M-y');
            
        } catch (Exception $e) {
            return date('d-M-y');
        }
    }

    public function delete($id_notifikasi)
    {
        if ($this->reminder_model->delete_reminder($id_notifikasi)) {
            $this->session->set_flashdata('success', 'Record deleted successfully');
        } else {
            $this->session->set_flashdata('error', 'Failed to delete record');
        }
        
        redirect(module_url("reminder/list"));
    }

    public function edit($id_notifikasi)
    {
        $reminder = $this->reminder_model->get_reminder($id_notifikasi);
        
        if (!$reminder) {
            $this->session->set_flashdata('error', 'Record not found');
            redirect(module_url("reminder/list"));
            return;
        }
        
        $this->template->title("Edit Reminder");
        $this->template->set("reminder", $reminder);
        $this->template->render();
    }

    public function update($id_notifikasi)
    {
        $data = [
            'T06_NAMA_PEMBEKAL' => $this->input->post('nama_pembekal'),
            'T06_NOMBOR_TELEFON' => $this->input->post('nombor_telefon'),
            'T06_NOMBOR_PESANAN' => $this->input->post('nombor_pesanan'),
            'T06_TARIKH_PESANAN' => $this->format_date($this->input->post('tarikh_pesanan')),
            'T06_TAMAT_PESANAN' => $this->format_date($this->input->post('tamat_pesanan')),
            'T06_JUMLAH_HARGA' => $this->input->post('jumlah_harga'),
            'T06_KOD_PEMBEKAL' => $this->input->post('kod_pembekal'),
            'T06_NO_ASET' => $this->input->post('no_aset'),
            'T06_KETERANGAN' => $this->input->post('keterangan')
        ];
        
        if ($this->reminder_model->update_reminder($id_notifikasi, $data)) {
            $this->session->set_flashdata('success', 'Record updated successfully');
        } else {
            $this->session->set_flashdata('error', 'Failed to update record');
        }
        
        redirect(module_url("reminder/list"));
    }

    // AJAX method to get reminder statistics
    public function get_stats()
    {
        header('Content-Type: application/json');
        
        try {
            $stats = $this->reminder_model->get_reminder_stats();
            echo json_encode(['status' => 'success', 'data' => $stats]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        
        exit;
    }
}
?>