<?php

class Pesanan extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("pesanan_model");
        $this->load->helper(array('form', 'url', 'file'));
        $this->load->library('upload');
    }

    public function listpesanan()
    {
        $data = $this->pesanan_model->get_all_pesanan();

        $this->template->title("Senarai Pesanan");
        $this->template->set("data", $data);
        $this->template->render();
    }

    public function delete($id_pesanan, $id2 = "")
    {
        $this->pesanan_model->delete_pesanan($id_pesanan);
        $this->session->set_flashdata('success', 'Pesanan deleted successfully');
        redirect(module_url("pesanan/listpesanan"));
    }

    public function form_add()
    {
        $this->template->render();
    }

    public function upload_pdf()
    {
        // Configure upload preferences
        $config['upload_path'] = './uploads/pesanan/';
        $config['allowed_types'] = 'pdf';
        $config['max_size'] = 10240; // 10MB
        $config['encrypt_name'] = TRUE;

        // Create directory if it doesn't exist
        if (!is_dir($config['upload_path'])) {
            mkdir($config['upload_path'], 0755, true);
        }

        $this->upload->initialize($config);

        if (!$this->upload->do_upload('pdf_file')) {
            $error = $this->upload->display_errors();
            $this->session->set_flashdata('error', 'Upload failed: ' . $error);
            redirect(module_url("pesanan/form_add"));
            return;
        }

        $upload_data = $this->upload->data();
        $pdf_path = $upload_data['full_path'];
        
        // Parse PDF and extract data
        $extracted_data = $this->parse_pdf($pdf_path);
        
        if ($extracted_data === false) {
            $this->session->set_flashdata('error', 'Failed to parse PDF file');
            redirect(module_url("pesanan/form_add"));
            return;
        }

        // Add the uploaded file path to extracted data
        $extracted_data['T06_PDF_FILE'] = $upload_data['file_name'];
        
        // Remove T06_ID_NOTIFIKASI if it exists (let database handle auto-increment)
        if (isset($extracted_data['T06_ID_NOTIFIKASI'])) {
            unset($extracted_data['T06_ID_NOTIFIKASI']);
        }
        
        // Insert data into database
        $result = $this->pesanan_model->insert_pesanan($extracted_data);
        
        if ($result) {
            $this->session->set_flashdata('success', 'Pesanan added successfully from PDF');
        } else {
            $this->session->set_flashdata('error', 'Failed to save pesanan data');
        }
        
        redirect(module_url("pesanan/listpesanan"));
    }

    private function parse_pdf($pdf_path)
    {
        try {
            // Use shell command to convert PDF to text using pdftotext
            $text_content = shell_exec("pdftotext -layout '$pdf_path' -");
            
            if (empty($text_content)) {
                // Alternative method using simple file reading (if PDF is text-based)
                $text_content = $this->extract_text_from_pdf($pdf_path);
            }

            if (empty($text_content)) {
                return false;
            }

            return $this->extract_data_from_text($text_content);
            
        } catch (Exception $e) {
            log_message('error', 'PDF parsing error: ' . $e->getMessage());
            return false;
        }
    }

    private function extract_text_from_pdf($pdf_path)
    {
        // Simple PDF text extraction for basic PDFs
        $content = file_get_contents($pdf_path);
        
        // Basic PDF text extraction (works for simple PDFs)
        if (preg_match_all('/\((.*?)\)/', $content, $matches)) {
            return implode(' ', $matches[1]);
        }
        
        return '';
    }

    private function extract_data_from_text($text)
    {
        $data = array();
        
        // Extract Nama Pembekal (Supplier Name) - T06_NAMA_PEMBEKAL
        if (preg_match('/Kepada\s*:\s*(.+?)(?:\n|\r)/i', $text, $matches)) {
            $data['T06_NAMA_PEMBEKAL'] = trim($matches[1]);
        } else {
            // Alternative pattern
            if (preg_match('/KEPADA\s*:\s*(.+?)(?:\n|\r)/i', $text, $matches)) {
                $data['T06_NAMA_PEMBEKAL'] = trim($matches[1]);
            }
        }

        // Extract Nombor Telefon (Phone Number) - T06_NOMBOR_TELEFON
        if (preg_match('/Tel\s*No\s*:\s*([0-9\-\s]+)/i', $text, $matches)) {
            $data['T06_NOMBOR_TELEFON'] = trim($matches[1]);
        }

        // Extract Nombor Pesanan (Order Number) - T06_NOMBOR_PESANAN
        if (preg_match('/Nombor\s*Pesanan\s*:?\s*([A-Z0-9\-]+)/i', $text, $matches)) {
            $data['T06_NOMBOR_PESANAN'] = trim($matches[1]);
        } else {
            // Alternative pattern for order number
            if (preg_match('/(\d{6}-\d{2})/i', $text, $matches)) {
                $data['T06_NOMBOR_PESANAN'] = trim($matches[1]);
            }
        }

        // Extract Tarikh Pesanan (Order Date) - T06_TARIKH_PESANAN
        if (preg_match('/Tarikh\s*Pesanan\s*:?\s*(\d{2}\/\d{2}\/\d{4})/i', $text, $matches)) {
            $data['T06_TARIKH_PESANAN'] = $this->convert_date_format($matches[1]);
        }

        // Extract Tamat Pesanan (Due Date) - T06_TAMAT_PESANAN
        if (preg_match('/Pada\s*atau\s*sebelum\s*:?\s*(\d{2}\/\d{2}\/\d{4})/i', $text, $matches)) {
            $data['T06_TAMAT_PESANAN'] = $this->convert_date_format($matches[1]);
        }

        // Extract Jumlah Harga (Total Amount) - T06_JUMLAH_HARGA
        if (preg_match('/Jumlah\s*\(RM\)\s*:?\s*([\d,]+\.?\d*)/i', $text, $matches)) {
            $data['T06_JUMLAH_HARGA'] = (float) str_replace(',', '', $matches[1]);
        } else {
            // Alternative pattern for amount
            if (preg_match('/([\d,]+\.\d{2})/i', $text, $matches)) {
                $amount = str_replace(',', '', $matches[1]);
                if ((float)$amount > 0) {
                    $data['T06_JUMLAH_HARGA'] = (float) $amount;
                }
            }
        }

        // Set T06_TARIKH (current timestamp for when record is created)
        $data['T06_TARIKH'] = date('Y-m-d H:i:s');

        // Set default values if not found
        $data['T06_NAMA_PEMBEKAL'] = $data['T06_NAMA_PEMBEKAL'] ?? 'Not found';
        $data['T06_NOMBOR_TELEFON'] = $data['T06_NOMBOR_TELEFON'] ?? 'Not found';
        $data['T06_NOMBOR_PESANAN'] = $data['T06_NOMBOR_PESANAN'] ?? 'Not found';
        $data['T06_TARIKH_PESANAN'] = $data['T06_TARIKH_PESANAN'] ?? date('Y-m-d');
        $data['T06_TAMAT_PESANAN'] = $data['T06_TAMAT_PESANAN'] ?? date('Y-m-d');
        $data['T06_JUMLAH_HARGA'] = $data['T06_JUMLAH_HARGA'] ?? 0.00;

        return $data;
    }

    private function convert_date_format($date_string)
    {
        // Convert from DD/MM/YYYY to YYYY-MM-DD
        $date_parts = explode('/', $date_string);
        if (count($date_parts) === 3) {
            return $date_parts[2] . '-' . $date_parts[1] . '-' . $date_parts[0];
        }
        return date('Y-m-d');
    }

    public function form_edit($id_pesanan)
    {
        $pesanan = $this->pesanan_model->get_pesanan($id_pesanan);
        
        if (!$pesanan) {
            $this->session->set_flashdata('error', 'Pesanan not found');
            redirect(module_url("pesanan/listpesanan"));
        }

        $this->template->set("pesanan", $pesanan);
        $this->template->render();
    }

    public function save($id_pesanan)
    {
        $data_to_update = [
            "T06_NAMA_PEMBEKAL" => $this->input->post("T06_NAMA_PEMBEKAL"),
            "T06_NOMBOR_TELEFON" => $this->input->post("T06_NOMBOR_TELEFON"),
            "T06_NOMBOR_PESANAN" => $this->input->post("T06_NOMBOR_PESANAN"),
            "T06_TARIKH_PESANAN" => $this->input->post("T06_TARIKH_PESANAN"),
            "T06_TAMAT_PESANAN" => $this->input->post("T06_TAMAT_PESANAN"),
            "T06_JUMLAH_HARGA" => (float) $this->input->post("T06_JUMLAH_HARGA"),
            "T06_TARIKH" => date('Y-m-d H:i:s') // Update timestamp
        ];

        // Remove T06_ID_NOTIFIKASI if it exists (should not be updated)
        if (isset($data_to_update['T06_ID_NOTIFIKASI'])) {
            unset($data_to_update['T06_ID_NOTIFIKASI']);
        }

        $result = $this->pesanan_model->update_pesanan($id_pesanan, $data_to_update);
        
        if ($result) {
            $this->session->set_flashdata('success', 'Pesanan updated successfully');
        } else {
            $this->session->set_flashdata('error', 'Failed to update pesanan');
        }
        
        redirect(module_url("pesanan/listpesanan"));
    }

    public function add_manual()
    {
        $data_to_insert = [
            "T06_NAMA_PEMBEKAL" => $this->input->post("T06_NAMA_PEMBEKAL"),
            "T06_NOMBOR_TELEFON" => $this->input->post("T06_NOMBOR_TELEFON"),
            "T06_NOMBOR_PESANAN" => $this->input->post("T06_NOMBOR_PESANAN"),
            "T06_TARIKH_PESANAN" => $this->input->post("T06_TARIKH_PESANAN"),
            "T06_TAMAT_PESANAN" => $this->input->post("T06_TAMAT_PESANAN"),
            "T06_JUMLAH_HARGA" => (float) $this->input->post("T06_JUMLAH_HARGA"),
            "T06_PDF_FILE" => null, // Manual entry, no PDF file
            "T06_TARIKH" => date('Y-m-d H:i:s') // Current timestamp
        ];

        // Remove T06_ID_NOTIFIKASI if it exists (let database handle auto-increment)
        if (isset($data_to_insert['T06_ID_NOTIFIKASI'])) {
            unset($data_to_insert['T06_ID_NOTIFIKASI']);
        }

        $result = $this->pesanan_model->insert_pesanan($data_to_insert);
        
        if ($result) {
            $this->session->set_flashdata('success', 'Pesanan added successfully');
        } else {
            $this->session->set_flashdata('error', 'Failed to add pesanan');
        }
        
        redirect(module_url("pesanan/listpesanan"));
    }
}
