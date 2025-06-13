<?php

class Pesakit_model extends CI_Model
{
    public function get_all_pesakit()
    {
        $query = $this->db->get("EV_T01_PESAKIT");
        return $query;
    }

    public function delete_pesakit($id_pesakit) {
        // Check if record has doctor comment
        $patient = $this->db
            ->where("T01_ID_PESAKIT", $id_pesakit)
            ->get("EV_T01_PESAKIT")
            ->row();
    
        if (!empty($patient->T01_DOCTOR_COMMENT)) {
            return false; // Return false if delete not allowed
        }
    
        $this->db->where('T01_ID_PESAKIT', $id_pesakit);
        return $this->db->delete('EV_T01_PESAKIT');
    }

    public function get_all_patients() {
        // You can reuse your existing method
        return $this->get_all_pesakit();
    }

    public function get_patient($id) {
        $this->db->where('T01_ID_PESAKIT', $id);
        $query = $this->db->get('EV_T01_PESAKIT');
        
        if ($query->num_rows() > 0) {
            return $query->row();
        }
        
        return false;
    }

    public function get_doctor_comment($patient_id) {
        $this->db->select('T01_DOCTOR_COMMENT');
        $this->db->where('T01_ID_PESAKIT', $patient_id);
        $query = $this->db->get('EV_T01_PESAKIT');
        return $query->row()->T01_DOCTOR_COMMENT ?? null;
    }
    public function update_comment($id, $comment) {
        if (empty($id)) {
            log_message('error', 'Empty ID in update_comment');
            return false;
        }
        
        $this->db->set('T01_DOCTOR_COMMENT', $comment);
        $this->db->where('T01_ID_PESAKIT', $id);
        return $this->db->update('EV_T01_PESAKIT');
    }

    // FIXED GRAPH DATA METHOD - Oracle Compatible with Better Date Handling
// FIXED GRAPH DATA METHOD - Handle Oracle's uppercase COUNT
public function get_graph_data($bahagian_utama, $kategori, $month = null, $year = null)
{
    // Build base query with proper GROUP BY
    $this->db->select('T01_SUB_BAHAGIAN, T01_JANTINA, COUNT(*) as count');
    $this->db->from('EV_T01_PESAKIT');
    $this->db->where('T01_BAHAGIAN_UTAMA', $bahagian_utama);
    $this->db->where('T01_KATEGORI', $kategori);
    
    // FIXED: Handle Oracle date format DD-MON-YY (e.g., 12-JUN-25)
    if ($month && $year) {
        // Convert month number to month name (Jan, Feb, etc.)
        $month_names = ['', 'JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 
                       'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
        $month_name = isset($month_names[(int)$month]) ? $month_names[(int)$month] : '';
        
        if ($month_name) {
            // Handle both 2-digit and 4-digit years
            $year_2digit = substr($year, -2); // Get last 2 digits
            
            // For Oracle DD-MON-YY format
            $this->db->where("UPPER(SUBSTR(T01_TARIKH, 4, 3)) = '$month_name'", null, false);
            $this->db->where("SUBSTR(T01_TARIKH, 8, 2) = '$year_2digit'", null, false);
        }
    } elseif ($year) {
        // Filter by year only - get last 2 characters for DD-MON-YY
        $year_2digit = substr($year, -2);
        $this->db->where("SUBSTR(T01_TARIKH, 8, 2) = '$year_2digit'", null, false);
    } elseif ($month) {
        // If only month is provided, use current year
        $current_year = date('Y');
        $year_2digit = substr($current_year, -2);
        $month_names = ['', 'JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 
                       'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
        $month_name = isset($month_names[(int)$month]) ? $month_names[(int)$month] : '';
        
        if ($month_name) {
            $this->db->where("UPPER(SUBSTR(T01_TARIKH, 4, 3)) = '$month_name'", null, false);
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
        
        // FIXED: Handle both uppercase COUNT and lowercase count
        $count = isset($row->COUNT) ? (int)$row->COUNT : (isset($row->count) ? (int)$row->count : 0);
        
        if (!isset($processed_data[$sub_bahagian])) {
            $processed_data[$sub_bahagian] = [
                'Lelaki' => 0,
                'Perempuan' => 0
            ];
        }
        
        // Make sure jantina matches exactly
        if ($jantina === 'Lelaki' || $jantina === 'Perempuan') {
            $processed_data[$sub_bahagian][$jantina] = $count;
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
    
    // Use actual data from database
    if (!empty($processed_data)) {
        foreach ($processed_data as $sub_bahagian => $gender_data) {
            $chart_data['labels'][] = $sub_bahagian;
            $chart_data['datasets'][0]['data'][] = $gender_data['Lelaki'];
            $chart_data['datasets'][1]['data'][] = $gender_data['Perempuan'];
        }
    } else {
        // No data found, show message
        $chart_data['labels'] = ['No Data'];
        $chart_data['datasets'][0]['data'] = [0];
        $chart_data['datasets'][1]['data'] = [0];
    }
    
    // Debug: Log final chart data
    log_message('debug', 'Final chart data: ' . json_encode($chart_data));
    
    return $chart_data;
}

// FIXED: Debug method with proper COUNT handling
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
    
    // ADDED: Process the data to show what the chart would look like
    $processed_data = [];
    foreach ($results as $row) {
        $sub_bahagian = trim($row->T01_SUB_BAHAGIAN);
        $jantina = trim($row->T01_JANTINA);
        
        // Handle both uppercase COUNT and lowercase count
        $count = isset($row->COUNT) ? (int)$row->COUNT : (isset($row->count) ? (int)$row->count : 0);
        
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
    
    log_message('debug', 'Debug info: ' . json_encode($debug_info));
    
    return $debug_info;
}

// ADDITIONAL: Method to get sample dates for debugging
public function get_sample_dates($limit = 10)
{
    $this->db->select('T01_ID_PESAKIT, T01_TARIKH, T01_BAHAGIAN_UTAMA, T01_KATEGORI, T01_SUB_BAHAGIAN, T01_JANTINA');
    $this->db->from('EV_T01_PESAKIT');
    $this->db->limit($limit);
    $query = $this->db->get();
    
    log_message('debug', 'Sample dates query: ' . $this->db->last_query());
    
    return $query->result();
}
   
}
?>

 


