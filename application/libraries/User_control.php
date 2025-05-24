<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_control 
{
	///define url inside qr code
	protected $id_umt;
	protected $ci;
	
    function __construct($id_umt)
    { 
		$this->id_umt = $id_umt;
		$this->ci =& get_instance();
		
		// $this->ci->load->model("users/users_model","acl");
		$this->ci->load->model("users/auth_model","acl");
		
		$this->generate_menu();
    }
	
	function check_access()
	{ 
		$check = $this->ci->acl
		->select(" PG_T01_PENGGUNA.ID_UMT AS IC_T01,  PG_T01_PENGGUNA.ROLE, WARGA_UMT.NAMA")
		->where("PG_T01_PENGGUNA.ID_UMT", $this->id_umt)
		->join('WARGA_UMT', 'PG_T01_PENGGUNA.ID_UMT = WARGA_UMT.NOKP')
			->count_all();
		
		return ($check > 0) ? true : false;
		

	}
	function get_access()
	{ 
		return $check = $this->ci->acl
			->select(" PG_T01_PENGGUNA.ID_UMT AS IC_T01,  PG_T01_PENGGUNA.ROLE, WARGA_UMT.NAMA")
			->where("PG_T01_PENGGUNA.ID_UMT", $this->id_umt)
			->join('WARGA_UMT', 'PG_T01_PENGGUNA.ID_UMT = WARGA_UMT.NOKP')
			->find_all();


			// 	->select(" T01_PENGGUNA.ID_UMT AS IC_T01,  T01_PENGGUNA.ROLE, WARGA_UMT.NAMA")
		// 	->where("T01_PENGGUNA.ID_UMT", $this->id_umt)
		// 	->join('WARGA_UMT', 'T01_PENGGUNA.ID_UMT = WARGA_UMT.NOKP')
		// 	->find_all();
		
		// return ($check > 0) ? true : false;
		

	}
	
	function validate_access()
	{
		if (!$this->check_access()) 
		{
			 show_error("ANDA TIADA CAPAIAN KE SISTEM INI", 400, 'RALAT');
			// echo "<script>
				// alert('Anda tiada capaian ke sistem ini. Sila hubungi urusetia di Kolej Kediaman');
				// window.location.href='https://pelajar.mynemo.umt.edu.my/index.php?module=hepa';
			// </script>";
		}
	}
	
	function generate_menu()
	{	
		// if (!$this->check_access()) 
		// {
		// 	 show_error("ANDA TIADA CAPAIAN KE SISTEM INI", 400, 'RALAT');	
		// }
		
		// $pic = $this->ci->acl->where("T05_ID_STAF", $this->id_umt)->count_all();
		
		// if ($pic > 0) 
		// {
			// $this->ci->session->set_userdata('tangguh', "1");
			// $_SESSION["tangguh"]["pic"] = "1";
		// }

		$access_level = $this->check_access();
		
		if (!$access_level) 
		{
			 show_error("ANDA TIADA CAPAIAN KE SISTEM INI", 400, 'RALAT');	
		}

		// $access_level = $this->get_access(); 
		
		
		// $access_level = $this->ci->acl 
		// 	->select(" T01_PENGGUNA.ID_UMT AS IC_T01,  T01_PENGGUNA.ROLE, WARGA_UMT.NAMA")
		// 	->where("T01_PENGGUNA.ID_UMT", $this->id_umt)
		// 	->join('WARGA_UMT', 'T01_PENGGUNA.ID_UMT = WARGA_UMT.NOKP')
		// 	->find_all();
		
		// if (isset($_SESSION["icno"])) $_SESSION["icno"] = NULL;
		
		// $d_acl = [];
		// foreach($access_level as $ac) 
		// {
		// 	$_SESSION["icno"][] = $ac->ID_UMT;  
		// }
						
	}
}