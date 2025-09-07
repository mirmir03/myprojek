<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Remark_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Save or update remark (without patient FK dependency)
     */
    public function save_remark($data) {
        try {
            // Ensure bulan & tahun are integers (to match Oracle NUMBER)
            $data['T08_BULAN'] = (int) $data['T08_BULAN'];
            $data['T08_TAHUN'] = (int) $data['T08_TAHUN'];

            // Check if remark already exists
            $existing = $this->db->where('T08_BAHAGIAN_UTAMA', $data['T08_BAHAGIAN_UTAMA'])
                                ->where('T08_BULAN', $data['T08_BULAN'])
                                ->where('T08_TAHUN', $data['T08_TAHUN'])
                                ->get('EV_T08_REMARK')
                                ->row();

            if ($existing) {
                // Update existing remark
                $this->db->where('T08_ID', $existing->T08_ID);
                $this->db->set('T08_UPDATED_AT', "TO_DATE('" . date('Y-m-d H:i:s') . "', 'YYYY-MM-DD HH24:MI:SS')", false);
                $result = $this->db->update('EV_T08_REMARK', ['T08_REMARK' => $data['T08_REMARK']]);
                log_message('debug', 'Remark updated for: ' . $data['T08_BAHAGIAN_UTAMA'] . '-' . $data['T08_BULAN'] . '-' . $data['T08_TAHUN']);
                return $result;
            } else {
                // Insert new remark
                $insert_data = [
                    'T08_BAHAGIAN_UTAMA' => $data['T08_BAHAGIAN_UTAMA'],
                    'T08_BULAN' => $data['T08_BULAN'],
                    'T08_TAHUN' => $data['T08_TAHUN'],
                    'T08_REMARK' => $data['T08_REMARK']
                ];

                // For CREATED_AT
                $this->db->set('T08_CREATED_AT', "TO_DATE('" . date('Y-m-d H:i:s') . "', 'YYYY-MM-DD HH24:MI:SS')", false);
                
                try {
                    $sequence_check = $this->db->query("SELECT 1 FROM USER_SEQUENCES WHERE SEQUENCE_NAME = 'EV_T08_REMARK_SEQ'");
                    if ($sequence_check && $sequence_check->num_rows() > 0) {
                        $this->db->set('T08_ID', 'EV_T08_REMARK_SEQ.NEXTVAL', false);
                    }
                } catch (Exception $e) {
                    log_message('info', 'Sequence EV_T08_REMARK_SEQ not found, proceeding without it');
                }

                $result = $this->db->insert('EV_T08_REMARK', $insert_data);
                log_message('debug', 'New remark inserted for: ' . $data['T08_BAHAGIAN_UTAMA'] . '-' . $data['T08_BULAN'] . '-' . $data['T08_TAHUN']);
                return $result;
            }
        } catch (Exception $e) {
            log_message('error', 'Remark model save error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get remark by filters - Fixed to return string directly
     */
    public function get_remark($bahagian_utama, $bulan, $tahun) {
        try {
            log_message('debug', 'Getting remark for: ' . $bahagian_utama . '-' . $bulan . '-' . $tahun);
            
            $remark = $this->db->select('T08_REMARK')
                              ->where('T08_BAHAGIAN_UTAMA', $bahagian_utama)
                              ->where('T08_BULAN', (int)$bulan)
                              ->where('T08_TAHUN', (int)$tahun)
                              ->get('EV_T08_REMARK')
                              ->row();
            
            $result = $remark ? $remark->T08_REMARK : '';
            log_message('debug', 'Remark found: ' . ($result ? 'YES' : 'NO') . ' - Length: ' . strlen($result));
            
            return $result;
        } catch (Exception $e) {
            log_message('error', 'Remark model get error: ' . $e->getMessage());
            return '';
        }
    }

    /**
     * Validate that patient data exists for the given combination
     */
    public function validate_patient_data_exists($bahagian_utama, $bulan, $tahun) {
        try {
            $count = $this->db->where('T01_BAHAGIAN_UTAMA', $bahagian_utama)
                             ->where("EXTRACT(MONTH FROM T01_TARIKH) = {$bulan}", null, FALSE)
                             ->where("EXTRACT(YEAR FROM T01_TARIKH) = {$tahun}", null, FALSE)
                             ->where('T01_STATUS', 1)
                             ->count_all_results('EV_T01_PESAKIT');
            
            log_message('debug', 'Patient data validation - Count: ' . $count . ' for ' . $bahagian_utama . '-' . $bulan . '-' . $tahun);
            return $count > 0;
        } catch (Exception $e) {
            log_message('error', 'Patient validation error: ' . $e->getMessage());
            return false;
        }
    }

    public function get_patient_statistics($bahagian_utama, $bulan, $tahun) {
        try {
            $this->db->select('COUNT(*) as total_patients, T01_JANTINA')
                     ->where('T01_BAHAGIAN_UTAMA', $bahagian_utama)
                     ->where("EXTRACT(MONTH FROM T01_TARIKH) = {$bulan}", null, FALSE)
                     ->where("EXTRACT(YEAR FROM T01_TARIKH) = {$tahun}", null, FALSE)
                     ->where('T01_STATUS', 1)
                     ->group_by('T01_JANTINA');
                      
            return $this->db->get('EV_T01_PESAKIT')->result();
        } catch (Exception $e) {
            log_message('error', 'Patient statistics error: ' . $e->getMessage());
            return [];
        }
    }
}
?>