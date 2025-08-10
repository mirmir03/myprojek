<?php

class Reject_model extends CI_Model
{
    public function get_all_rejects()
    {
        $query = $this->db->get("EV_T06_REJECT_ANALYSIS");
        return $query;
    }

    public function delete_reject($id_reject)
    {
        $this->db->where('T06_ID_REJECT', $id_reject);
        return $this->db->delete('EV_T06_REJECT_ANALYSIS');
    }

    public function get_reject($id)
    {
        $this->db->where('T06_ID_REJECT', $id);
        $query = $this->db->get('EV_T06_REJECT_ANALYSIS');
        if ($query->num_rows() > 0) {
            return $query->row();
        }
        return false;
    }

    /**
     * Get reject data for chart generation
     * @param string $month - Filter by month (1-12)
     * @param string $year - Filter by year (YYYY)
     * @return array - Chart data grouped by reject type
     */
    public function get_reject_chart_data($month = null, $year = null)
    {
        // Build the WHERE clause based on filters
        $where_conditions = [];
        $params = [];

        if (!empty($month) && !empty($year)) {
            // Filter by specific month and year
            $where_conditions[] = "EXTRACT(MONTH FROM T06_TARIKH) = :month";
            $where_conditions[] = "EXTRACT(YEAR FROM T06_TARIKH) = :year";
            $params[':month'] = $month;
            $params[':year'] = $year;
        } elseif (!empty($year)) {
            // Filter by year only
            $where_conditions[] = "EXTRACT(YEAR FROM T06_TARIKH) = :year";
            $params[':year'] = $year;
        } elseif (!empty($month)) {
            // Filter by month only (across ALL years) - FIX: Remove year restriction
            $where_conditions[] = "EXTRACT(MONTH FROM T06_TARIKH) = :month";
            $params[':month'] = $month;
        }

        // Build the complete SQL query
        $sql = "SELECT 
                    T06_JENIS_REJECT,
                    COUNT(*) as TOTAL_REJECTS
                FROM EV_T06_REJECT_ANALYSIS";

        if (!empty($where_conditions)) {
            $sql .= " WHERE " . implode(" AND ", $where_conditions);
        }

        $sql .= " GROUP BY T06_JENIS_REJECT
                 ORDER BY TOTAL_REJECTS DESC";

        // Execute the query
        $stmt = oci_parse($this->db->conn_id, $sql);
        
        // Bind parameters - FIX: Properly bind all parameters
        foreach ($params as $param => $value) {
            oci_bind_by_name($stmt, $param, $params[$param]); // Use reference to the array value
        }
        
        oci_execute($stmt);

        // Fetch results
        $results = [];
        while (($row = oci_fetch_assoc($stmt)) != false) {
            $obj = new stdClass();
            $obj->T06_JENIS_REJECT = $row['T06_JENIS_REJECT'];
            $obj->TOTAL_REJECTS = $row['TOTAL_REJECTS'];
            $results[] = $obj;
        }

        oci_free_statement($stmt);
        
        return $results;
    }

    /**
     * Get available years from the reject data for filter dropdown
     * @return array - List of years
     */
    public function get_available_years()
    {
        $sql = "SELECT DISTINCT EXTRACT(YEAR FROM T06_TARIKH) as YEAR 
                FROM EV_T06_REJECT_ANALYSIS 
                WHERE T06_TARIKH IS NOT NULL 
                ORDER BY YEAR DESC";
        
        $stmt = oci_parse($this->db->conn_id, $sql);
        oci_execute($stmt);
        
        $years = [];
        while (($row = oci_fetch_assoc($stmt)) != false) {
            $years[] = $row['YEAR'];
        }
        
        oci_free_statement($stmt);
        
        return $years;
    }

    /**
     * Get available months for a specific year
     * @param string $year
     * @return array - List of months with data
     */
    public function get_available_months($year = null)
    {
        $sql = "SELECT DISTINCT EXTRACT(MONTH FROM T06_TARIKH) as MONTH 
                FROM EV_T06_REJECT_ANALYSIS 
                WHERE T06_TARIKH IS NOT NULL";
        
        $params = [];
        if (!empty($year)) {
            $sql .= " AND EXTRACT(YEAR FROM T06_TARIKH) = :year";
            $params[':year'] = $year;
        }
        
        $sql .= " ORDER BY MONTH";
        
        $stmt = oci_parse($this->db->conn_id, $sql);
        
        foreach ($params as $param => $value) {
            oci_bind_by_name($stmt, $param, $params[$param]);
        }
        
        oci_execute($stmt);
        
        $months = [];
        while (($row = oci_fetch_assoc($stmt)) != false) {
            $months[] = $row['MONTH'];
        }
        
        oci_free_statement($stmt);
        
        return $months;
    }
}