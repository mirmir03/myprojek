<?php
class DosimetriStaf extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("dosimetristaf_model");
    }

    // ==================== NON-GRAPH FUNCTIONS ====================

    public function update_month_value() {
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        // Check permission
        if (!$this->ion_auth->is_admin()) {
            echo json_encode([
                'success' => false,
                'message' => 'Unauthorized access'
            ]);
            return;
        }

        // Get POST data
        $id = $this->input->post('id');
        $year = $this->input->post('year');
        $month = $this->input->post('month');
        $value = $this->input->post('value');
        $staff = $this->input->post('staff');

        // Validate input
        if (!$year || !$month || !isset($value)) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid input parameters'
            ]);
            return;
        }

        // Convert value to proper format
        $value = floatval($value);
        if ($value < 0 || $value > 999.99) {
            echo json_encode([
                'success' => false,
                'message' => 'Value must be between 0 and 999.99'
            ]);
            return;
        }

        try {
            // If id exists, update the record
            if ($id) {
                $update_data = [
                    'T04_DOS_SETARA1_' . strtoupper($month) => $value,
                    'T04_UPDATED_AT' => date('Y-m-d H:i:s'),
                    'T04_UPDATED_BY' => $this->ion_auth->user()->row()->id
                ];

                $this->db->where('T04_ID_DOS_STAF', $id);
                $success = $this->db->update('EV_T04_DOSIMETRI_STAFF', $update_data);
            } 
            // If no id, create new record
            else {
                $insert_data = [
                    'T04_TAHUN' => $year,
                    'T04_DOS_SETARA1_' . $month => $value,
                    'T04_NAMA_PENGGUNA' => $staff,
                    'T04_CREATED_AT' => date('Y-m-d H:i:s'),
                    'T04_CREATED_BY' => $this->ion_auth->user()->row()->id
                ];

                $success = $this->db->insert('EV_T04_DOSIMETRI_STAFF', $insert_data);
                if ($success) {
                    $id = $this->db->insert_id();
                }
            }

            if ($success) {
                // Recalculate yearly total
                $this->dosimetristaf_model->calculate_yearly_totals($id);
            }

            echo json_encode([
                'success' => $success,
                'message' => $success ? 'Successfully updated' : 'Failed to update record'
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
    }
   public function listdos_staff() {
    $monthly_data = $this->dosimetristaf_model->get_monthly_dosimetri();
    
    $staff_data = [];
    foreach ($monthly_data as $row) {
        $staff = $row->T04_NAMA_PENGGUNA;
        $year = $row->TAHUN;
        $month_num = (int)$row->BULAN;
        $month_key = ['','JAN','FEB','MAR','APR','MAY','JUN',
                     'JUL','AUG','SEP','OCT','NOV','DEC'][$month_num];
        
        if (!isset($staff_data[$staff][$year])) {
            $staff_data[$staff][$year] = [
                'ave1' => [
                    'months' => array_fill_keys(
                        ['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC'], 
                        null
                    ),
                    'yearly_total' => 0
                ],
                'ave2' => [
                    'months' => array_fill_keys(
                        ['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC'], 
                        null
                    ),
                    'yearly_total' => 0
                ],
                'id' => $row->T04_ID_DOS_STAF,
                'tarikh' => $row->T04_TARIKH
            ];
        }

        // Set AVE1 data
        if ($row->T04_DOS_SETARA1 !== null) {
            $value = $row->T04_DOS_SETARA1;
            $staff_data[$staff][$year]['ave1']['months'][$month_key] = $value;
            $staff_data[$staff][$year]['ave1']['yearly_total'] += $value;
        }
        
        // Set AVE2 data
        if ($row->T04_DOS_SETARA2 !== null) {
            $value = $row->T04_DOS_SETARA2;
            $staff_data[$staff][$year]['ave2']['months'][$month_key] = $value;
            $staff_data[$staff][$year]['ave2']['yearly_total'] += $value;
        }
    }
    
    $this->template->title("Dos Berkesan Bulanan");
    $this->template->set("staff_data", $staff_data);
    $this->template->render();
}

    public function get_staff_by_category() {
        try {
            $category = $this->input->get('category');
            
            // Verify category exists
            if (empty($category)) {
                throw new Exception('Category parameter is required');
            }
            
            // Get data from model
            $staff = $this->dosimetristaf_model->get_staff_by_category($category);
            
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($staff));
                
        } catch (Exception $e) {
            log_message('error', 'Error in get_staff_by_category: ' . $e->getMessage());
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode(['error' => $e->getMessage()]));
        }
    }

    public function delete($id_dosimetri)
    {
        $this->dosimetristaf_model->delete_dosimetri($id_dosimetri);
        $this->session->set_flashdata('success', 'Rekod dosimetri berjaya dipadam');
        redirect(module_url("dosimetristaf/listdos_staff"));
    }

    public function add() {
    $data = [
        'T04_DOS_SETARA1' => $this->input->post("dos_setara1"),
        'T04_DOS_SETARA2' => $this->input->post("dos_setara2"),
        'T04_TARIKH' => $this->input->post("tarikh"),
        'T04_NAMA_PENGGUNA' => $this->input->post("nama_pengguna"),
        'T04_KATEGORI_PENGGUNA' => $this->input->post("kategori_pengguna")
    ];
    
    // Validate required fields
    if (empty($data['T04_TARIKH']) || empty($data['T04_NAMA_PENGGUNA']) || empty($data['T04_KATEGORI_PENGGUNA'])) {
        $this->session->set_flashdata('error', 'Sila isi semua ruangan yang diperlukan');
        redirect(module_url("dosimetristaf/listdos_staff"));
        return;
    }
    
    // Check if this staff already has a record for this month
    $date = new DateTime($data['T04_TARIKH']);
    $month = $date->format('m');
    $year = $date->format('Y');
    
    $existing = $this->dosimetristaf_model->check_existing_dosimetri(
        $data['T04_NAMA_PENGGUNA'], // Check for this specific staff only
        $month,
        $year
    );
    
    if ($existing) {
        $this->session->set_flashdata('error', 'Anda sudah merekodkan dosimetri untuk bulan ini. Sila kemaskini rekod sedia ada.');
        redirect(module_url("dosimetristaf/listdos_staff"));
        return;
    }
    
    // Calculate AVE values
    $data['T04_DOS_AVE1'] = $data['T04_DOS_SETARA1'];
    $data['T04_DOS_AVE2'] = $data['T04_DOS_SETARA2'];
    
    if ($this->dosimetristaf_model->insert_dosimetri($data)) {
        $this->session->set_flashdata('success', 'Rekod dosimetri berjaya ditambah');
    } else {
        $this->session->set_flashdata('error', 'Ralat berlaku semasa menyimpan rekod');
    }
    
    redirect(module_url("dosimetristaf/listdos_staff"));
}

    public function form_add()
    {
        $this->template->render();
    }

    public function form_edit($id_dosimetri)
{
    // Make sure we're using the same case as in __construct()
    $dosimetri = $this->dosimetristaf_model->get_dosimetri($id_dosimetri);

    if (!$dosimetri) {
        $this->session->set_flashdata('error', 'Rekod tidak dijumpai.');
        redirect(module_url('dosimetristaf/listdos_staff'));
    }
    // Format date properly
    if (!empty($dosimetri->T04_TARIKH)) {
        // Try multiple date formats
        $date_formats = ['d-M-Y', 'Y-m-d H:i:s', 'Y-m-d', 'd/m/Y'];
        $formatted_date = null;
        
        foreach ($date_formats as $format) {
            $date_obj = DateTime::createFromFormat($format, $dosimetri->T04_TARIKH);
            if ($date_obj !== false) {
                $dosimetri->T04_TARIKH = $date_obj->format('Y-m-d');
                break;
            }
        }
        
        // If still not formatted, try Oracle date format
        if (is_string($dosimetri->T04_TARIKH)) {
            $date_obj = date_create_from_format('d-M-Y', $dosimetri->T04_TARIKH);
            if ($date_obj) {
                $dosimetri->T04_TARIKH = $date_obj->format('Y-m-d');
            }
        }
    }

    // Use the template system consistently
    $this->template->title("Kemaskini Dosimetri Staf");
    $this->template->set("dosimetri", $dosimetri);
    $this->template->render("dosimetristaf/form_edit");
}



    public function save($id_dosimetri) {
        $data = [
            'T04_DOS_SETARA1' => $this->input->post("dos_setara1"),
            'T04_DOS_SETARA2' => $this->input->post("dos_setara2"),
            'T04_DOS_AVE1' => $this->input->post("dos_ave1"),
            'T04_DOS_AVE2' => $this->input->post("dos_ave2"),
            'T04_TARIKH' => $this->input->post("tarikh"),
            'T04_NAMA_PENGGUNA' => $this->input->post("nama_pengguna"),
            'T04_KATEGORI_PENGGUNA' => $this->input->post("kategori_pengguna")
        ];
        
        // Let the model handle decimal conversion
        if ($this->dosimetristaf_model->update_dosimetri($id_dosimetri, $data)) {
            $this->session->set_flashdata('success', 'Rekod dosimetri berjaya dikemaskini');
        } else {
            $this->session->set_flashdata('error', 'Ralat berlaku semasa mengemaskini rekod');
        }
        
        redirect(module_url("dosimetristaf/listdos_staff"));
    }

    // ==================== GRAPH-RELATED FUNCTIONS ====================
    public function graph()
{
    // Get selected year and staff from the form
    $selected_year = $this->input->get('year');
    $selected_staff = $this->input->get('staff');
    
    // If no year is selected, use current year
    if (!$selected_year) {
        $selected_year = date('Y');
    }

    // Fetch data for both T04_DOS_AVE1 and T04_DOS_AVE2 with staff filter
    $chart_data_ave1 = $this->dosimetristaf_model->get_dosimetri_chart_data($selected_year, 'T04_DOS_AVE1', $selected_staff);
    $chart_data_ave2 = $this->dosimetristaf_model->get_dosimetri_chart_data($selected_year, 'T04_DOS_AVE2', $selected_staff);
    
    // Process data for chart labels and values
    $labels_ave1 = [];
    $values_ave1 = [];
    
    foreach ($chart_data_ave1 as $row) {
        $labels_ave1[] = $row->COLUMN_NAME;
        $values_ave1[] = (float)$row->TOTAL_VALUE;
    }

    $labels_ave2 = [];
    $values_ave2 = [];
    
    foreach ($chart_data_ave2 as $row) {
        $labels_ave2[] = $row->COLUMN_NAME;
        $values_ave2[] = (float)$row->TOTAL_VALUE;
    }

    // Get list of all staff for the filter dropdown
    $staff_list = $this->dosimetristaf_model->get_all_staff();

    // Pass data to the view
    $this->template->title("Graf Analisis Dosimetri Staf");
    $this->template->set("chart_labels_ave1", json_encode($labels_ave1));
    $this->template->set("chart_values_ave1", json_encode($values_ave1));
    $this->template->set("chart_labels_ave2", json_encode($labels_ave2));
    $this->template->set("chart_values_ave2", json_encode($values_ave2));
    
    // Set the selected year and staff for the filter form
    $this->template->set("selected_year", $selected_year);
    $this->template->set("selected_staff", $selected_staff);

    // Get available years for the dropdown
    $years = range(2020, date('Y'));
    $this->template->set("years", $years);
    
    // Pass staff list to view
    $this->template->set("staff_list", $staff_list);

    $this->template->render();
}

    public function get_chart_data()
    {
        $selected_year = $this->input->post('year');
        
        $chart_data = $this->dosimetristaf_model->get_dosimetri_chart_data($selected_year);
        
        $labels = [];
        $values = [];
        
        foreach ($chart_data as $row) {
            $labels[] = $row->COLUMN_NAME;
            $values[] = (int)$row->TOTAL_VALUE;
        }
        
        echo json_encode([
            'labels' => $labels,
            'values' => $values,
            'total' => array_sum($values)
        ]);
    }
    public function check_existing() {
    try {
        $staff_name = $this->input->post('staff_name');
        $month = $this->input->post('month');
        $year = $this->input->post('year');
        
        if (empty($staff_name) || empty($month) || empty($year)) {
            throw new Exception('Missing parameters');
        }
        
        $exists = $this->dosimetristaf_model->check_existing_dosimetri($staff_name, $month, $year);
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['exists' => $exists]));
            
    } catch (Exception $e) {
        log_message('error', 'Error in check_existing: ' . $e->getMessage());
        $this->output
            ->set_status_header(500)
            ->set_content_type('application/json')
            ->set_output(json_encode(['error' => $e->getMessage()]));
    }
}
public function form_edit_month($id_dosimetri) {
    $month = $this->input->get('month');
    $year = $this->input->get('year', TRUE);
    
    if (!$year) {
        $year = date('Y'); // Default to current year if not specified
    }
    
    // Convert month name to number for database query
    $month_names = ['JAN' => '01', 'FEB' => '02', 'MAR' => '03', 'APR' => '04', 
                   'MAY' => '05', 'JUN' => '06', 'JUL' => '07', 'AUG' => '08', 
                   'SEP' => '09', 'OCT' => '10', 'NOV' => '11', 'DEC' => '12'];
    
    $month_number = isset($month_names[strtoupper($month)]) ? $month_names[strtoupper($month)] : '01';
    
    // Get the specific monthly record for this staff, month, and year
    $dosimetri = $this->dosimetristaf_model->get_record_by_month($id_dosimetri, $month_number, $year);
    
    // Debug log to check what we got
    log_message('debug', 'form_edit_month - Retrieved dosimetri data: ' . print_r($dosimetri, true));
    
    if (!$dosimetri) {
        // If no record exists for this month, get basic staff info and create empty values
        $staff_info = $this->dosimetristaf_model->get_dosimetri($id_dosimetri);
        if (!$staff_info) {
            $this->session->set_flashdata('error', 'Rekod staf tidak dijumpai.');
            redirect(module_url('dosimetristaf/listdos_staff'));
            return;
        }
        
        // Create a new record structure with empty values
        $dosimetri = (object)[
            'T04_ID_DOS_STAF' => $id_dosimetri,
            'T04_DOS_SETARA1' => '0.00',
            'T04_DOS_SETARA2' => '0.00',
            'T04_NAMA_PENGGUNA' => $staff_info->T04_NAMA_PENGGUNA,
            'T04_KATEGORI_PENGGUNA' => $staff_info->T04_KATEGORI_PENGGUNA,
            'T04_TARIKH' => null
        ];
        
        $this->session->set_flashdata('info', 'Tiada rekod untuk bulan ini. Anda boleh menambah data baru.');
    } else {
        // We found existing data - make sure it's properly formatted
        log_message('debug', 'Existing values - SETARA1: ' . $dosimetri->T04_DOS_SETARA1 . ', SETARA2: ' . $dosimetri->T04_DOS_SETARA2);
    }
    
    $this->template->title("Kemaskini Dosimetri Bulanan - " . strtoupper($month) . " " . $year);
    $this->template->set("dosimetri", $dosimetri);
    $this->template->set("month", strtoupper($month));
    $this->template->set("month_number", $month_number);
    $this->template->set("year", $year);
    $this->template->render("dosimetristaf/form_edit_month");
}

public function update_month() {
    $id = $this->input->post('id');
    $month = $this->input->post('month');
    $year = $this->input->post('year');
    $dos_setara1 = $this->input->post('dos_setara1');
    $dos_setara2 = $this->input->post('dos_setara2');
    
    // Debug submitted values
    log_message('debug', 'Update values - ID: ' . $id . ', Month: ' . $month . 
                        ', Year: ' . $year . ', SETARA1: ' . $dos_setara1 . 
                        ', SETARA2: ' . $dos_setara2);
    
    // Get the first day of the specified month
    $date = new DateTime();
    $date->setDate($year ? $year : date('Y'), date('m', strtotime("01-{$month}-2000")), 1);
    
    $data = [
        'T04_DOS_SETARA1' => $dos_setara1,
        'T04_DOS_SETARA2' => $dos_setara2,
        'T04_TARIKH' => $date->format('d-M-Y'), // Format date for Oracle
        'month' => $month, // Pass the month separately for reference
        'year' => $year ? $year : date('Y')
    ];
    
    if ($this->dosimetristaf_model->update_monthly_dosimetri($id, $data)) {
        $this->session->set_flashdata('success', 'Data berjaya dikemaskini');
    } else {
        $this->session->set_flashdata('error', 'Gagal mengemaskini data');
    }
    
    redirect(module_url('dosimetristaf/listdos_staff'));
}

}