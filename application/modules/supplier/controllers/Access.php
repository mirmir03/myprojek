<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * @author CokesHome
 * @copyright Copyright (c) 2015, CokesHome
 * 
 * This is controller for Authentication
 */

class Access extends Front_Controller {
    
    /**
     * Load the models, library, etc
     *
     * 
     */
    public function __construct()
    {
    	parent::__construct();
		// $this->load->model("qr_model",'qrm');
		$this->autoload['libraries'][] = 'auth';
    }
	
	function index()
	{
		$this->load->library("users/auth");
		
		 // $Ldata = $this->auth->vlogin_all('m57016', 'C3654851')  ;
		$Ldata = $this->auth->vlogin_all('p3920', '820519115343')  ;
		// $Ldata = $this->auth->vlogin_all('abdul.rahim', 'kambing82')  ;
		// var_dump($Ldata);
	
		
		$principal = $Ldata[0]['dn']; 
				
		$ou = explode(",", $principal)[1];
		
		if (strrpos($ou, '=' ) == false)  die ("error");

		$ou =  substr($ou, strrpos($ou, '=' )+1)."\n";
		

		
		
	}
	
	function login()
	{ 
		$this->load->library("auth");
		
		if ($this->input->post("username")) {
			
			$uid = $this->input->post("username");
			$pwd = $this->input->post("password");
			
			if ($this->auth->vlogin_all($uid, $pwd)) {
				
				
			}
			else
			{			
				$this->template->set_message('ID dan KATALALUAN anda tidak sah. Sila masukkan ID dan KATALALUAN yang betul.', 'warning');
				$this->template->render("login");
			}
		}
		else {
			$requested_page = $this->session->userdata('requested_page');
            if($requested_page != '')
            {
                //print($requested_page);
            }
			$this->template->render("login");
		}
	}
	
	public function vhadir($sess_id = "")
	{ 	
		$eventid = $this->session->userdata('eventid');
		if (!$eventid) {
			if (!$this->qrm->check_valid_qr($sess_id)) 
			{
				$this->session->sess_destroy();
				redirect("expired");				
			}
		}		
		
		$user = $this->session->userdata('app_session'); //var_dump($user);exit;
		
		if (!$user) {
			redirect(site_url('logmasuk'));
		}
	
		$id = $this->session->userdata('eventid');
		
		$data = array(
			"T01_ID"	=> 	$id,	 
			"T02_USER"	=> 	$user["idwarga"],
			//"T02_USER"	=> 	$user["icno"],
			//"T02_IPADDR"	=>	$this->input->ip_address(),
			"T02_IPADDR"	=>	$this->ip_umt(),
			"T02_DEVICE"	=>	$this->agent->platform(),
			"T02_BROWSER"	=>	$this->agent->browser(),
			"T02_T03_HASH"	=>	$sess_id,
			"T02_HOSTNAME"	=>	gethostname()
		);
		
		$hadir = $this->qrm->get_kehadiran($user["icno"], $id);
		
		if($hadir->num_rows() > 0)
		{
			$this->template->set_message('Pengesahan kehadiran program ini telah dIbuat sebelum ini', 'info');
		}
		else
		{
			$this->qrm->addpensyarah($data);
			$this->template->set_message('Pengesahan Kehadiran Berjaya', 'success');
		}		
		
		$this->template->set("ipaddr",  $this->input->ip_address());
		$this->template->set("nama",  $user["nama"]);
		$this->template->set("nokp",  $user["icno"]);
		$this->template->render("success_tpl");
		$this->session->sess_destroy();
	}
	
	public function nolanding($sess_id = "")
	{
		$this->template->set_message('Sila imbas kod QR', 'warning');
		$this->template->render("nolanding");
	}
	
	public function expired($sess_id = "")
	{		
		$this->template->set_message("Kod QR tidak valid atau telah expired. Sila scan semula","warning");
		$this->template->render("nolanding");
	}
	
	public function logout()
    {
        $this->session->sess_destroy();
        redirect('/');
    }
	
	function test()
	{
		//var_dump($_SESSION);
		//echo $this->session->userdata('requested_page');
		echo $this->agent->platform();
	}
	
	function ip_umt()
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']) and $_SERVER['HTTP_CLIENT_IP'])
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) and $_SERVER['HTTP_X_FORWARDED_FOR'])
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']) and $_SERVER['HTTP_X_FORWARDED'])
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']) and $_SERVER['HTTP_FORWARDED_FOR'])
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']) and $_SERVER['HTTP_FORWARDED'])
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if($_SERVER['REMOTE_ADDR'])
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
		
        return $ipaddress ;
     }
	 
	 function testldap()
	 {
		$this->auth->vlogin_all("", "1111") ;
	 }
}