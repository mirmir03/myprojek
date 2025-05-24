<?php
class User_model extends CI_Model
{
    

    function ValidateEmail($email)
    {
        $query = $this->db->query("SELECT * FROM PG_T02_USER WHERE PG_T02_EMAIL='$email'");
		if($query->num_rows() == 1)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
        
    }

    function updatePasswordhash($data,$email)
    {
        $this->db->where('email','$email');
        $this->db->update('users',$data);

    }
}