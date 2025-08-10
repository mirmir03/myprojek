<?php
class Dosimetristaf_model extends CI_Model {
    
    // ==================== UTILITY FUNCTIONS ====================
    // Convert decimal to integer for Oracle storage
private function _toOracleInt($value) {
    if ($value === null || $value === '') return null;
    return (int)round((float)$value * 100);
}

// Convert Oracle integer back to decimal with proper formatting
public function _toDecimal($value) {
    if ($value === null) return null;
    $decimal = (float)$value / 100;
    return number_format($decimal, 2, '.', ''); // Formats to 2 decimal places
}
    
    // ==================== NON-GRAPH FUNCTIONS ====================
    public function get_all_dosimetri() {
        $query = $this->db->get("EV_T04_DOSIMETRI_STAFF");
        
        // Convert integer values back to decimals
        foreach ($query->result() as $row) {
            $row->T04_DOS_SETARA1 = $this->_toDecimal($row->T04_DOS_SETARA1);
            $row->T04_DOS_SETARA2 = $this->_toDecimal($row->T04_DOS_SETARA2);
            $row->T04_DOS_AVE1 = $this->_toDecimal($row->T04_DOS_AVE1);
            $row->T04_DOS_AVE2 = $this->_toDecimal($row->T04_DOS_AVE2);
        }
        
        return $query;
    }

    public function get_staff_by_category($category) {
        $this->db->select('ID_STAF_PKU, NAMA_STAF_PKU as name');
        $this->db->from('EV_STAF_PKU');
        $this->db->where('JAWATAN_STAF_PKU', $category);
        
        $query = $this->db->get();
        
        if (!$query) {
            log_message('error', 'Database error: ' . $this->db->error()['message']);
            return array();
        }
        
        return $query->result_array();
    }
    
    public function delete_dosimetri($id_dosimetri) {
        $this->db->where('T04_ID_DOS_STAF', $id_dosimetri);
        return $this->db->delete('EV_T04_DOSIMETRI_STAFF');
    }
    
    public function get_dosimetri($id) {
    $this->db->select('*');
    $this->db->where('T04_ID_DOS_STAF', $id);
    $query = $this->db->get('EV_T04_DOSIMETRI_STAFF');
    
    if ($query->num_rows() > 0) {
        $row = $query->row();
        
        // Convert all numeric values to proper decimals for display
        $row->T04_DOS_SETARA1 = $this->_toDecimal($row->T04_DOS_SETARA1);
        $row->T04_DOS_SETARA2 = $this->_toDecimal($row->T04_DOS_SETARA2);
        $row->T04_DOS_AVE1 = $this->_toDecimal($row->T04_DOS_AVE1);
        $row->T04_DOS_AVE2 = $this->_toDecimal($row->T04_DOS_AVE2);
        
        // Debug log for verification
        log_message('debug', 'Retrieved dosimetri record for display:');
        log_message('debug', 'SETARA1: ' . $row->T04_DOS_SETARA1);
        log_message('debug', 'SETARA2: ' . $row->T04_DOS_SETARA2);
        log_message('debug', 'AVE1: ' . $row->T04_DOS_AVE1);
        log_message('debug', 'AVE2: ' . $row->T04_DOS_AVE2);
        
        // Handle date formatting
        if (!empty($row->T04_TARIKH)) {
            if (is_object($row->T04_TARIKH)) {
                // If it's a DateTime object (from OCI8)
                $row->T04_TARIKH = $row->T04_TARIKH->format('Y-m-d');
            } else {
                // If it's a string, try to parse it
                $oracle_date = date_create_from_format('d-M-Y', $row->T04_TARIKH);
                if ($oracle_date) {
                    $row->T04_TARIKH = $oracle_date->format('Y-m-d');
                } else {
                    // Fallback - try to parse as is
                    try {
                        $date = new DateTime($row->T04_TARIKH);
                        $row->T04_TARIKH = $date->format('Y-m-d');
                    } catch (Exception $e) {
                        $row->T04_TARIKH = date('Y-m-d'); // Default to today
                    }
                }
            }
        }
        
        return $row;
    }
    return false;
}

public function get_monthly_records_by_staff($id_dosimetri)
{
    // First get staff info from the ID
    $this->db->select('T04_NAMA_PENGGUNA');
    $this->db->where('T04_ID_DOS_STAF', $id_dosimetri);
    $staff = $this->db->get('EV_T04_DOSIMETRI_STAFF')->row();
    
    if (!$staff) return false;

    // Then get all monthly records for this staff
    $this->db->select([
        'T04_ID_DOS_STAF',
        'TO_CHAR(T04_TARIKH, "MM") as month',
        'TO_CHAR(T04_TARIKH, "YYYY") as year',
        'T04_DOS_SETARA1',
        'T04_DOS_SETARA2',
        'T04_KATEGORI_PENGGUNA'
    ]);
    $this->db->where('T04_NAMA_PENGGUNA', $staff->T04_NAMA_PENGGUNA);
    $this->db->order_by('TO_CHAR(T04_TARIKH, "YYYY-MM")', 'DESC');
    return $this->db->get('EV_T04_DOSIMETRI_STAFF')->result();
}

public function get_record_by_month($id_dosimetri, $month, $year)
{
    // Get complete record with all fields and specific month/year
    $this->db->select('*');
    $this->db->where('T04_ID_DOS_STAF', $id_dosimetri);
    $this->db->where("TO_CHAR(T04_TARIKH, 'MM') =", $month);
    $this->db->where("TO_CHAR(T04_TARIKH, 'YYYY') =", $year);
    $record = $this->db->get('EV_T04_DOSIMETRI_STAFF')->row();
    
    if (!$record) {
        log_message('debug', 'No record found for ID: ' . $id_dosimetri . ', Month: ' . $month . ', Year: ' . $year);
        return false;
    }
    
    log_message('debug', 'Found record: ' . print_r($record, true));

    // Convert all numeric values to decimals for display
    $record->T04_DOS_SETARA1 = $this->_toDecimal($record->T04_DOS_SETARA1);
    $record->T04_DOS_SETARA2 = $this->_toDecimal($record->T04_DOS_SETARA2);
    $record->T04_DOS_AVE1 = $this->_toDecimal($record->T04_DOS_AVE1);
    $record->T04_DOS_AVE2 = $this->_toDecimal($record->T04_DOS_AVE2);
    
    // Format date for display
    if (!empty($record->T04_TARIKH)) {
        if (is_object($record->T04_TARIKH)) {
            // If it's a DateTime object (from OCI8)
            $record->T04_TARIKH = $record->T04_TARIKH->format('Y-m-d');
        } else {
            // If it's a string, try to parse it
            $oracle_date = date_create_from_format('d-M-Y', $record->T04_TARIKH);
            if ($oracle_date) {
                $record->T04_TARIKH = $oracle_date->format('Y-m-d');
            }
        }
    }
    
    // Debug log to verify data
    log_message('debug', 'Retrieved record for edit form:');
    log_message('debug', print_r($record, true));
    
    return $record;
}
    
    public function insert_dosimetri($data) {
        // Convert decimals to integers
        $oracleData = [
            'T04_DOS_SETARA1' => $this->_toOracleInt($data['T04_DOS_SETARA1']),
            'T04_DOS_SETARA2' => $this->_toOracleInt($data['T04_DOS_SETARA2']),
            'T04_DOS_AVE1' => $this->_toOracleInt($data['T04_DOS_AVE1']),
            'T04_DOS_AVE2' => $this->_toOracleInt($data['T04_DOS_AVE2']),
            'T04_NAMA_PENGGUNA' => $data['T04_NAMA_PENGGUNA'],
            'T04_KATEGORI_PENGGUNA' => $data['T04_KATEGORI_PENGGUNA']
        ];
        
        // Convert date to Oracle format
        $date = DateTime::createFromFormat('Y-m-d', $data['T04_TARIKH']);
        if (!$date) {
            log_message('error', 'Invalid date format: ' . $data['T04_TARIKH']);
            return false;
        }
        $oracleData['T04_TARIKH'] = $date->format('d-M-Y');
        
        return $this->db->insert('EV_T04_DOSIMETRI_STAFF', $oracleData);
    }

    public function update_dosimetri($id, $data) {
        // Convert decimals to integers
        $oracleData = [
            'T04_DOS_SETARA1' => $this->_toOracleInt($data['T04_DOS_SETARA1']),
            'T04_DOS_SETARA2' => $this->_toOracleInt($data['T04_DOS_SETARA2']),
            'T04_DOS_AVE1' => $this->_toOracleInt($data['T04_DOS_AVE1']),
            'T04_DOS_AVE2' => $this->_toOracleInt($data['T04_DOS_AVE2']),
            'T04_NAMA_PENGGUNA' => $data['T04_NAMA_PENGGUNA'],
            'T04_KATEGORI_PENGGUNA' => $data['T04_KATEGORI_PENGGUNA']
        ];
        
        // Convert date to Oracle format
        $date = DateTime::createFromFormat('Y-m-d', $data['T04_TARIKH']);
        if (!$date) {
            log_message('error', 'Invalid date format: ' . $data['T04_TARIKH']);
            return false;
        }
        $oracleData['T04_TARIKH'] = $date->format('d-M-Y');
        
        $this->db->where('T04_ID_DOS_STAF', $id);
        return $this->db->update('EV_T04_DOSIMETRI_STAFF', $oracleData);
    }

    // ==================== GRAPH-RELATED FUNCTIONS ====================
    public function get_dosimetri_chart_data($selected_year = null, $column = 'T04_DOS_AVE1') {
        $this->db->select('T04_NAMA_PENGGUNA as COLUMN_NAME, SUM(' . $column . ') as TOTAL_VALUE');
        $this->db->from('EV_T04_DOSIMETRI_STAFF');
        
        if ($selected_year) {
            $this->db->where("EXTRACT(YEAR FROM T04_TARIKH) = ", $selected_year);
        }
        
        $this->db->group_by('T04_NAMA_PENGGUNA');
        $this->db->order_by('TOTAL_VALUE', 'DESC');
        
        $query = $this->db->get();
        $results = $query->result();
        
        // Convert Oracle integers back to decimals for chart display
        foreach ($results as $row) {
            $row->TOTAL_VALUE = $this->_toDecimal($row->TOTAL_VALUE);
        }
        
        return $results;
    }

    public function get_dos_chart_data($dos_column, $selected_year = null) {
        $this->db->select("T04_NAMA_PENGGUNA, SUM($dos_column) as TOTAL_VALUE");
        $this->db->from("EV_T04_DOSIMETRI_STAFF");

        if ($selected_year) {
            $this->db->where("EXTRACT(YEAR FROM T04_TARIKH) =", $selected_year);
        }

        $this->db->group_by("T04_NAMA_PENGGUNA");
        $this->db->order_by("T04_NAMA_PENGGUNA", "ASC");

        $query = $this->db->get();
        return $query->result();
    }

   public function get_monthly_dosimetri() {
    $this->db->select([
        'T04_ID_DOS_STAF',
        'T04_NAMA_PENGGUNA',
        // Normalize year format in the query
        'CASE WHEN TO_CHAR(T04_TARIKH, \'YYYY\') LIKE \'00%\' 
              THEN \'20\' || SUBSTR(TO_CHAR(T04_TARIKH, \'YYYY\'), 3)
              ELSE TO_CHAR(T04_TARIKH, \'YYYY\') 
         END as TAHUN',
        'TO_CHAR(T04_TARIKH, \'MM\') as BULAN',
        'T04_DOS_SETARA1',
        'T04_DOS_SETARA2',
        'T04_TARIKH'
    ]);
    $this->db->from('EV_T04_DOSIMETRI_STAFF');
    $query = $this->db->get();
    return $query->result();

    // Convert all numeric values back to proper decimals
    foreach ($results as $row) {
        $row->T04_DOS_SETARA1 = $this->_toDecimal($row->T04_DOS_SETARA1);
        $row->T04_DOS_SETARA2 = $this->_toDecimal($row->T04_DOS_SETARA2);
    }
    
    return $results;
    
    
    if (!$query) {
        log_message('error', 'Database error: ' . $this->db->error()['message']);
        return [];
    }
    
    $results = $query->result();
    
    foreach ($results as $row) {
        // Debug output to check raw values
        log_message('debug', 'Raw data - SETARA1: '.$row->T04_DOS_SETARA1.', SETARA2: '.$row->T04_DOS_SETARA2);
        
        // Convert values
        $row->T04_DOS_SETARA1 = $this->_toDecimal($row->T04_DOS_SETARA1);
        $row->T04_DOS_SETARA2 = $this->_toDecimal($row->T04_DOS_SETARA2);
        $row->T04_DOS_AVE1 = $this->_toDecimal($row->T04_DOS_AVE1);
        $row->T04_DOS_AVE2 = $this->_toDecimal($row->T04_DOS_AVE2);
        
        // Debug converted values
        log_message('debug', 'Converted - SETARA1: '.$row->T04_DOS_SETARA1.', SETARA2: '.$row->T04_DOS_SETARA2);
    }
    
    return $results;
}

public function check_existing_dosimetri($staff_name, $month, $year) {
    $this->db->where('T04_NAMA_PENGGUNA', $staff_name);
    $this->db->where("TO_CHAR(T04_TARIKH, 'MM') =", $month);
    $this->db->where("TO_CHAR(T04_TARIKH, 'YYYY') =", $year);
    $query = $this->db->get('EV_T04_DOSIMETRI_STAFF');
    
    return $query->num_rows() > 0;
}
public function update_dosimetristaf($id, $data) {
    $this->db->where('T04_ID_DOS_STAF', $id);
    return $this->db->update('EV_T04_DOSIMETRI_STAFF', $data);
}

// Add this method to your Dosimetristaf_model.php
public function update_monthly_dosimetri($id, $data) {
    // Get the existing record with all fields
    $this->db->where('T04_ID_DOS_STAF', $id);
    $existing_record = $this->db->get('EV_T04_DOSIMETRI_STAFF')->row();
    
    if (!$existing_record) {
        log_message('error', 'Record not found for ID: ' . $id);
        return false;
    }

    // Convert existing values to decimal for display/comparison
    $existing_record->T04_DOS_SETARA1 = $this->_toDecimal($existing_record->T04_DOS_SETARA1);
    $existing_record->T04_DOS_SETARA2 = $this->_toDecimal($existing_record->T04_DOS_SETARA2);
    $existing_record->T04_DOS_AVE1 = $this->_toDecimal($existing_record->T04_DOS_AVE1);
    $existing_record->T04_DOS_AVE2 = $this->_toDecimal($existing_record->T04_DOS_AVE2);

    log_message('debug', 'Existing values before update:');
    log_message('debug', 'SETARA1: ' . $existing_record->T04_DOS_SETARA1);
    log_message('debug', 'SETARA2: ' . $existing_record->T04_DOS_SETARA2);
    log_message('debug', 'AVE1: ' . $existing_record->T04_DOS_AVE1);
    log_message('debug', 'AVE2: ' . $existing_record->T04_DOS_AVE2);

    // Only update values that are provided in the data array
    $setara1 = isset($data['T04_DOS_SETARA1']) ? $this->_toOracleInt($data['T04_DOS_SETARA1']) : $this->_toOracleInt($existing_record->T04_DOS_SETARA1);
    $setara2 = isset($data['T04_DOS_SETARA2']) ? $this->_toOracleInt($data['T04_DOS_SETARA2']) : $this->_toOracleInt($existing_record->T04_DOS_SETARA2);

    // Calculate new AVE values based on SETARA values
    $ave1 = $setara1; // AVE1 is based on SETARA1
    $ave2 = $setara2; // AVE2 is based on SETARA2

    // Prepare the update data with all fields
    $update_data = [
        'T04_DOS_SETARA1' => $setara1,
        'T04_DOS_SETARA2' => $setara2,
        'T04_DOS_AVE1' => $ave1,
        'T04_DOS_AVE2' => $ave2,
        'T04_TARIKH' => $data['T04_TARIKH'],
        'T04_NAMA_PENGGUNA' => $existing_record->T04_NAMA_PENGGUNA,
        'T04_KATEGORI_PENGGUNA' => $existing_record->T04_KATEGORI_PENGGUNA
    ];

    // Debug logging
    log_message('debug', 'Existing record: ' . print_r($existing_record, true));
    log_message('debug', 'Update data: ' . print_r($update_data, true));

    // Update the record
    $this->db->where('T04_ID_DOS_STAF', $id);
    $result = $this->db->update('EV_T04_DOSIMETRI_STAFF', $update_data);
    
    if (!$result) {
        log_message('error', 'Update failed: ' . print_r($this->db->error(), true));
        log_message('error', 'Last query: ' . $this->db->last_query());
    } else {
        // Verify the update
        $updated_record = $this->get_dosimetri($id);
        log_message('debug', 'Updated record: ' . print_r($updated_record, true));
    }
    
    return $result;
}

}