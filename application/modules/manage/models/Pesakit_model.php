<?php

class Pesakit_model extends CI_Model
{
    public function get_all_pesakit()
{
    return $this->db->where('T01_STATUS', 1)
                    ->get("EV_T01_PESAKIT");
}


public function soft_delete($id_pesakit)
{
    // Only update status, do not delete data
    return $this->db->where("T01_ID_PESAKIT", $id_pesakit)
                    ->update("EV_T01_PESAKIT", ["T01_STATUS" => 0]);
}


    public function delete($id_pesakit, $id2 = "")
{
    $patient = $this->db
        ->where("T01_ID_PESAKIT", $id_pesakit)
        ->get("EV_T01_PESAKIT")
        ->row();

    if (!empty($patient->T01_DOCTOR_COMMENT)) {
        $this->session->set_flashdata('error', 'Cannot delete patient record - doctor comment exists');
        redirect(module_url("pesakit/listpesakit"));
        return;
    }

    // 🔄 Soft delete instead of real deletion
    $this->db->where("T01_ID_PESAKIT", $id_pesakit);
    $this->db->update("EV_T01_PESAKIT", ["T01_STATUS" => 0]);

    $this->session->set_flashdata('success', 'Patient record deleted (soft delete)');
    redirect(module_url("pesakit/listpesakit"));
}


    public function get_all_patients() {
        // You can reuse your existing method
        return $this->get_all_pesakit();
    }

    // Add this method to your Pesakit_model class
// Updated get_warga_data method in Pesakit_model
public function get_warga_data($no_pengenalan)
{
    // Select both name and gender from EV_T01_WARGA
    $this->db->select("T01_NAMA_WARGA as nama, T01_JANTINA as jantina");
    $this->db->where("T01_NO_PENGENALAN", $no_pengenalan);
    $query = $this->db->get("EV_T01_WARGA");
    
    if ($query->num_rows() > 0) {
        return $query->row();
    }
    
    return false;
}

// Enhanced method for controller use
public function get_patient_data_enhanced($no_rujukan, $kategori)
{
    if ($kategori == 'pelajar' || $kategori == 'staf') {
        $warga_data = $this->get_warga_data($no_rujukan);
        
        if ($warga_data) {
            return [
                'status' => 'success',
                'data' => [
                    'nama' => $warga_data->nama,
                    'jantina' => $warga_data->jantina  // Return actual database gender
                ]
            ];
        } else {
            return [
                'status' => 'not_found',
                'message' => 'No rujukan tidak ditemui dalam sistem EV_T01_WARGA.'
            ];
        }
    } else {
        // Handle other categories - derive gender from IC
        if (preg_match('/^\d{6}-\d{2}-\d{4}$/', $no_rujukan)) {
            $lastDigit = substr($no_rujukan, -1);
            $jantina = ((int)$lastDigit % 2 === 0) ? 'Perempuan' : 'Lelaki';
            
            return [
                'status' => 'success',
                'data' => [
                    'nama' => '',
                    'jantina' => $jantina
                ]
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Format No Rujukan tidak sah.'
            ];
        }
    }
}

    public function get_patient($id) {
        $this->db->where('T01_ID_PESAKIT', $id);
        $query = $this->db->get('EV_T01_PESAKIT');
        
        if ($query->num_rows() > 0) {
            return $query->row();
        }
        
        return false;
    }

    public function delete_doctor_comment($patient_id) {
    // Validate patient_id
    if (empty($patient_id)) {
        log_message('error', 'Empty patient_id in delete_doctor_comment');
        return false;
    }
    
    // Only update the comment field - no date field needed
    $data = array(
        'T01_DOCTOR_COMMENT' => null
    );
    
    $this->db->where('T01_ID_PESAKIT', $patient_id);
    $result = $this->db->update('EV_T01_PESAKIT', $data);
    
    // Log the query for debugging
    log_message('debug', 'Delete comment query: ' . $this->db->last_query());
    log_message('debug', 'Delete comment result: ' . ($result ? 'true' : 'false'));
    
    if (!$result) {
        $error = $this->db->error();
        log_message('error', 'Database error in delete_doctor_comment: ' . json_encode($error));
    }
    
    return $result;
}
    // FIXED GRAPH DATA METHOD - Oracle Compatible with Better Data Type Handling
public function get_graph_data($bahagian_utama, $kategori, $month = null, $year = null)
{
    // Build base query with proper GROUP BY
    $this->db->select('T01_SUB_BAHAGIAN, T01_JANTINA, COUNT(*) as count');
    $this->db->from('EV_T01_PESAKIT');
    $this->db->where('T01_BAHAGIAN_UTAMA', $bahagian_utama);
    $this->db->where('T01_KATEGORI', $kategori);
    
    // FIXED: Handle DD-MON-YY date format (like 23-JAN-25, 08-FEB-25)
    if ($month && $year) {
    $month_str = str_pad($month, 2, '0', STR_PAD_LEFT);
    $this->db->where("TO_CHAR(T01_TARIKH, 'MM') = '$month_str'", null, false);
    $this->db->where("TO_CHAR(T01_TARIKH, 'YYYY') = '$year'", null, false);
} elseif ($year) {
    $this->db->where("TO_CHAR(T01_TARIKH, 'YYYY') = '$year'", null, false);


        
    } elseif ($month) {
        // If only month is provided, use current year
        $current_year = date('Y');
        $year_2digit = substr($current_year, -2);
        $month_names = ['', 'JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 
                       'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
        $month_abbr = isset($month_names[(int)$month]) ? $month_names[(int)$month] : '';
        
        if ($month_abbr) {
            $this->db->where("UPPER(SUBSTR(T01_TARIKH, 4, 3)) = '$month_abbr'", null, false);
            $this->db->where("SUBSTR(T01_TARIKH, 8, 2) = '$year_2digit'", null, false);
        }
    }
    
    $this->db->group_by(['T01_SUB_BAHAGIAN', 'T01_JANTINA']);
    $this->db->order_by('T01_SUB_BAHAGIAN');
    
    $query = $this->db->get();
    
    // Debug: Log the generated query
    log_message('debug', 'Graph Query: ' . $this->db->last_query());
    
    $results = $query->result();
    
    // Debug: Log raw results
    log_message('debug', 'Raw results: ' . json_encode($results));
    
    // Process data for chart format
    $processed_data = [];
    
    foreach ($results as $row) {
        $sub_bahagian = trim($row->T01_SUB_BAHAGIAN);
        $jantina = trim($row->T01_JANTINA);
        
        // Handle Oracle's string COUNT values and convert to integer
        $count = 0;
        if (isset($row->COUNT)) {
            $count = (int)$row->COUNT;
        } elseif (isset($row->count)) {
            $count = (int)$row->count;
        }
        
        log_message('debug', "Processing row - Sub: $sub_bahagian, Gender: $jantina, Count: $count");
        
        if (!isset($processed_data[$sub_bahagian])) {
            $processed_data[$sub_bahagian] = [
                'Lelaki' => 0,
                'Perempuan' => 0
            ];
        }
        
        if ($jantina === 'Lelaki' || $jantina === 'Perempuan') {
            $processed_data[$sub_bahagian][$jantina] = $count;
        } else {
            log_message('debug', "Unexpected gender value: '$jantina'");
        }
    }
    
    // Debug: Log processed data
    log_message('debug', 'Processed data: ' . json_encode($processed_data));
    
    // Format data for Chart.js
    $chart_data = [
        'labels' => [],
        'datasets' => [
            [
                'label' => 'Lelaki',
                'data' => [],
                'backgroundColor' => 'rgba(54, 162, 235, 0.6)',
                'borderColor' => 'rgba(54, 162, 235, 1)',
                'borderWidth' => 1
            ],
            [
                'label' => 'Perempuan',
                'data' => [],
                'backgroundColor' => 'rgba(255, 99, 132, 0.6)',
                'borderColor' => 'rgba(255, 99, 132, 1)',
                'borderWidth' => 1
            ]
        ]
    ];
    
    if (!empty($processed_data)) {
        foreach ($processed_data as $sub_bahagian => $gender_data) {
            $chart_data['labels'][] = $sub_bahagian;
            $chart_data['datasets'][0]['data'][] = $gender_data['Lelaki'];
            $chart_data['datasets'][1]['data'][] = $gender_data['Perempuan'];
        }
    } else {
        // No data found
        $chart_data['labels'] = ['No Data'];
        $chart_data['datasets'][0]['data'] = [0];
        $chart_data['datasets'][1]['data'] = [0];
        log_message('debug', 'No data found for the given criteria');
    }
    
    log_message('debug', 'Final chart data: ' . json_encode($chart_data));
    
    return $chart_data;
}

// FIXED: Debug method with enhanced data type handling
public function debug_graph_data($bahagian_utama, $kategori)
{
    // Simple query without date filters for debugging
    $this->db->select('T01_SUB_BAHAGIAN, T01_JANTINA, COUNT(*) as count');
    $this->db->from('EV_T01_PESAKIT');
    $this->db->where('T01_BAHAGIAN_UTAMA', $bahagian_utama);
    $this->db->where('T01_KATEGORI', $kategori);
    $this->db->group_by(['T01_SUB_BAHAGIAN', 'T01_JANTINA']);
    $this->db->order_by('T01_SUB_BAHAGIAN');
    
    $query = $this->db->get();
    
    // Log the query for debugging
    log_message('debug', 'Debug Query: ' . $this->db->last_query());
    
    $results = $query->result();
    
    // Enhanced debug info
    $debug_info = [
        'query' => $this->db->last_query(),
        'raw_results' => $results,
        'result_count' => count($results),
        'parameters' => [
            'bahagian_utama' => $bahagian_utama,
            'kategori' => $kategori
        ]
    ];
    
    // Additional check: get all records for this combination
    $this->db->select('T01_ID_PESAKIT, T01_SUB_BAHAGIAN, T01_JANTINA, T01_TARIKH');
    $this->db->from('EV_T01_PESAKIT');
    $this->db->where('T01_BAHAGIAN_UTAMA', $bahagian_utama);
    $this->db->where('T01_KATEGORI', $kategori);
    $all_records_query = $this->db->get();
    
    $debug_info['all_matching_records'] = $all_records_query->result();
    $debug_info['all_records_query'] = $this->db->last_query();
    
    // FIXED: Process the data to show what the chart would look like with proper type conversion
    $processed_data = [];
    foreach ($results as $row) {
        $sub_bahagian = trim($row->T01_SUB_BAHAGIAN);
        $jantina = trim($row->T01_JANTINA);
        
        // Handle both uppercase COUNT and lowercase count with proper type conversion
        $count = 0;
        if (isset($row->COUNT)) {
            $count = (int)$row->COUNT; // Convert Oracle string to integer
        } elseif (isset($row->count)) {
            $count = (int)$row->count; // Convert string to integer
        }
        
        if (!isset($processed_data[$sub_bahagian])) {
            $processed_data[$sub_bahagian] = [
                'Lelaki' => 0,
                'Perempuan' => 0
            ];
        }
        
        if ($jantina === 'Lelaki' || $jantina === 'Perempuan') {
            $processed_data[$sub_bahagian][$jantina] = $count;
        }
    }
    
    $debug_info['processed_for_chart'] = $processed_data;
    
    // ADDED: Show what the final chart data would look like
    $chart_data = [
        'labels' => [],
        'datasets' => [
            [
                'label' => 'Lelaki',
                'data' => [],
                'backgroundColor' => 'rgba(54, 162, 235, 0.6)',
                'borderColor' => 'rgba(54, 162, 235, 1)',
                'borderWidth' => 1
            ],
            [
                'label' => 'Perempuan',
                'data' => [],
                'backgroundColor' => 'rgba(255, 99, 132, 0.6)',
                'borderColor' => 'rgba(255, 99, 132, 1)',
                'borderWidth' => 1
            ]
        ]
    ];
    
    if (!empty($processed_data)) {
        foreach ($processed_data as $sub_bahagian => $gender_data) {
            $chart_data['labels'][] = $sub_bahagian;
            $chart_data['datasets'][0]['data'][] = $gender_data['Lelaki'];
            $chart_data['datasets'][1]['data'][] = $gender_data['Perempuan'];
        }
    }
    
    $debug_info['final_chart_data'] = $chart_data;
    
    log_message('debug', 'Debug info: ' . json_encode($debug_info));
    
    return $debug_info;
}

// Method to get sample dates for debugging
public function get_sample_dates($limit = 10)
{
    $this->db->select('T01_ID_PESAKIT, T01_TARIKH, T01_BAHAGIAN_UTAMA, T01_KATEGORI, T01_SUB_BAHAGIAN, T01_JANTINA');
    $this->db->from('EV_T01_PESAKIT');
    $this->db->limit($limit);
    $query = $this->db->get();
    
    log_message('debug', 'Sample dates query: ' . $this->db->last_query());
    
    return $query->result();
}  
public function update_comment($patient_id, $comment)
{
    if (empty($patient_id)) {
        log_message('error', 'update_comment: Empty patient ID');
        return false;
    }

    $data = [
        'T01_DOCTOR_COMMENT' => $comment
    ];

    $this->db->where('T01_ID_PESAKIT', $patient_id);
    $result = $this->db->update('EV_T01_PESAKIT', $data);

    log_message('debug', 'update_comment query: ' . $this->db->last_query());

    if (!$result) {
        $error = $this->db->error();
        log_message('error', 'update_comment DB error: ' . json_encode($error));
    }

    return $result;
}


}
?>