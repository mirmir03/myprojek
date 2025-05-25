<?php
class DosimetriPesakit_model extends CI_Model 
{
    public function __construct() 
    {
        parent::__construct();
    }

    public function create($data)
{
    return $this->db->insert('EV_T03_DOSIMETRI_PESAKIT', $data);
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
    return $this->db->delete('EV_T03_DOSIMETRI_PESAKIT');
}
}