<?php
class User_model extends CI_Model {
    public function get_user_by_staffid($staffid) {
        return $this->db->get_where('EV_T02_STAF_XRAY', [
            'T02_ID_STAF' => $staffid,
            'T02_IS_ACTIVE' => 'Y'
        ])->row();
    }
}
