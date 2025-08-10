<?php

class Pesanan_model extends CI_Model
{
    private $table = 'EV_T07_NOTIFIKASI_PESANAN';
    private $primary_key = 'T06_ID_NOTIFIKASI';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_all_pesanan()
    {
        $this->db->order_by('T06_TARIKH', 'DESC');
        return $this->db->get($this->table)->result();
    }

    public function get_pesanan($id)
    {
        $this->db->where($this->primary_key, $id);
        return $this->db->get($this->table)->row();
    }

    public function insert_pesanan($data)
    {
        return $this->db->insert($this->table, $data);
    }

    public function update_pesanan($id, $data)
    {
        $this->db->where($this->primary_key, $id);
        return $this->db->update($this->table, $data);
    }

    public function delete_pesanan($id)
    {
        $this->db->where($this->primary_key, $id);
        return $this->db->delete($this->table);
    }

    public function get_pesanan_by_order_number($order_number)
    {
        $this->db->where('T06_NOMBOR_PESANAN', $order_number);
        return $this->db->get($this->table)->row();
    }

    public function get_pesanan_by_supplier($supplier_name)
    {
        $this->db->like('T06_NAMA_PEMBEKAL', $supplier_name);
        $this->db->order_by('T06_TARIKH', 'DESC');
        return $this->db->get($this->table)->result();
    }

    public function get_pesanan_by_date_range($start_date, $end_date)
    {
        $this->db->where('T06_TARIKH_PESANAN >=', $start_date);
        $this->db->where('T06_TARIKH_PESANAN <=', $end_date);
        $this->db->order_by('T06_TARIKH_PESANAN', 'DESC');
        return $this->db->get($this->table)->result();
    }
}
?>