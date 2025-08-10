<?php
class Dosimetripesakit_model extends CI_Model 
{
    public function __construct() 
    {
        parent::__construct();
    }
    
    public function create($data) 
    {
        // Verify required fields exist
        if (!isset($data['T01_ID_PESAKIT'])) {
            log_message('error', 'Missing patient ID in dosimetry data');
            return false;
        }
        
        // Verify patient exists
        $this->db->where('T01_ID_PESAKIT', $data['T01_ID_PESAKIT']);
        $query = $this->db->get('EV_T01_PESAKIT');
        
        if ($query->num_rows() == 0) {
            log_message('error', 'Invalid patient ID: ' . $data['T01_ID_PESAKIT']);
            return false;
        }
        
        // Insert the record - Oracle compatible way
        $result = $this->db->insert('EV_T03_DOSIMETRI_PESAKIT', $data);
        
        if ($result && $this->db->affected_rows() > 0) {
            log_message('info', 'Dosimetry record inserted successfully for patient: ' . $data['T01_ID_PESAKIT']);
            return true; // Return true for success - no need for insert_id in Oracle
        }
        
        // Log database error details
        $error = $this->db->error();
        log_message('error', 'Database insert failed: ' . print_r($error, true));
        return false;
    }
    
    public function get_all_dosimetri()
    {
        $this->db->select('d.*, p.T01_NAMA_PESAKIT, p.T01_NO_RUJUKAN');
        $this->db->from('EV_T03_DOSIMETRI_PESAKIT d');
        $this->db->join('EV_T01_PESAKIT p', 'd.T01_ID_PESAKIT = p.T01_ID_PESAKIT', 'left');
        $this->db->order_by('d.T03_ID_DOS_PESAKIT', 'DESC');
        return $this->db->get();
    }
    
    public function delete($id)
    {
        $this->db->where('T03_ID_DOS_PESAKIT', $id);
        $result = $this->db->delete('EV_T03_DOSIMETRI_PESAKIT');
        
        if (!$result) {
            $error = $this->db->error();
            log_message('error', 'Delete Error: ' . print_r($error, true));
        }
        
        return $result;
    }

    public function get_by_id($id)
{
    return $this->db->where('T03_ID_DOS_PESAKIT', $id)->get('EV_T03_DOSIMETRI_PESAKIT')->row();
}
public function update($id_dosimetri)
{
    // Load model
    $this->load->model('Dosimetripesakit_model');

    // Validate form input
    $tarikh = $this->input->post('tarikh');
    $nilai_dos = $this->input->post('nilai_dos');
    // Add more fields as needed

    if (empty($tarikh) || empty($nilai_dos)) {
        $this->session->set_flashdata('error', 'Required fields are missing.');
        redirect(module_url("dosimetripesakit/form_edit/" . $id_dosimetri));
        return;
    }

    // Prepare data
    $data = [
        'T03_TARIKH' => $tarikh,
        'T03_NILAI_DOS' => $nilai_dos,
        // Include other fields from your form
    ];

    // Update record
    $result = $this->Dosimetripesakit_model->update($id_dosimetri, $data);

    // Handle result
    if ($result) {
        $this->session->set_flashdata('success', 'Dosimetry record updated successfully.');
    } else {
        $this->session->set_flashdata('error', 'Failed to update dosimetry record.');
    }

    redirect(module_url("dosimetripesakit/list")); // adjust if your list function is named differently
}

public function check_existing_dosimetri($staff_name, $month, $year) {
    $this->db->where('T04_NAMA_PENGGUNA', $staff_name);
    $this->db->where("TO_CHAR(T04_TARIKH, 'MM') =", $month);
    $this->db->where("TO_CHAR(T04_TARIKH, 'YYYY') =", $year);
    $query = $this->db->get('EV_T04_DOSIMETRI_STAFF');
    
    return $query->num_rows() > 0;
}

}