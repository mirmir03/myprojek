<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Admin_Controller extends Base_Controller
{

    //--------------------------------------------------------------------
    protected $user_data;
    protected $pager;
    protected $limit;
    /**
     * Class constructor
     *
     */
    public function __construct()
    { 
		$_SESSION["icno"] = "820519115343";
       
        $this->autoload['helpers'][]   = 'form';
        $this->autoload['libraries'][] = 'Template';
        $this->autoload['libraries'][] = 'supplier/auth';  
        // $this->autoload['libraries'][] = 'todo';
 
        $this->autoload['helpers'][]   = 'form'; 
        $this->db = $this->load->database("default", true); 
		$this->load->helper("app");
		// $this->load->model('dashboard/kawalan_model', 'kawal');
		
		// $this->db->query("ALTER SESSION SET NLS_DATE_FORMAT = 'DD/MM/YYYY HH24:MI:SS'"); 
        parent::__construct();

		if (!isset($_SESSION["icno"]) ) {
			$domain =  $_SERVER["SERVER_NAME"];
			$http =  (isset($_SERVER["REQUEST_SCHEME"] ) && $_SERVER["REQUEST_SCHEME"]=='https') ? 'https':'http';	
			$link = $http.'://'.$domain.'/mynemov3.1/login';
			redirect($link);
		}
		
		/*if (!$this->auth->is_login())  //mcmaa tutup
        {  
            // redirect('login');
			
			if (!isset($_SESSION["icno"]) and !isset($_SESSION["MATRIK"])) {
				echo "<script>
					alert('Tiada session');
					window.location.href='https://mynemo.umt.edu.my'
				</script>";
			}
			
			$array = array(
				"id_user"	=>	(isset($_SESSION["icno"]) ? $_SESSION["icno"] : $_SESSION["MATRIK"]) ,
				"idwarga"	=>	(isset($_SESSION["icno"]) ? $_SESSION["icno"] : $_SESSION["MATRIK"]),
				"icno"	=>	(isset($_SESSION["icno"]) ? $_SESSION["icno"] : $_SESSION["MATRIK"]),
				"nama"	=>	(isset($_SESSION["icno"]) ? $_SESSION["icno"] : $_SESSION["MATRIK"]),
				"username"	=>	(isset($_SESSION["icno"]) ? $_SESSION["icno"] : $_SESSION["MATRIK"])
			);
			$this->session->set_userdata('app_session', $array);
		}
		 
		
		$array = array(
			"id_user"	=>	(isset($_SESSION["icno"]) ? $_SESSION["icno"] : $_SESSION["MATRIK"]) ,
			"idwarga"	=>	(isset($_SESSION["icno"]) ? $_SESSION["icno"] : $_SESSION["MATRIK"]),
			"icno"	=>	(isset($_SESSION["icno"]) ? $_SESSION["icno"] : $_SESSION["MATRIK"]),
			"nama"	=>	(isset($_SESSION["icno"]) ? $_SESSION["icno"] : $_SESSION["MATRIK"]),
			"username"	=>	(isset($_SESSION["icno"]) ? $_SESSION["icno"] : $_SESSION["MATRIK"])
		);
        */
		$array = array(
			"id_user"	=>	(isset($_SESSION["icno"]) ? $_SESSION["icno"] : "") ,
			"idwarga"	=>	(isset($_SESSION["icno"]) ? $_SESSION["icno"] : ""),
			"icno"	=>	(isset($_SESSION["icno"]) ? $_SESSION["icno"] : ""),
			"nama"	=>	(isset($_SESSION["icno"]) ? $_SESSION["icno"] : ""),
			"username"	=>	(isset($_SESSION["icno"]) ? $_SESSION["icno"] : "")
			);
        // $this->load->library('user_control', $array['idwarga']);
		$this->session->set_userdata('app_session', $array);
        //$this->user_data = $this->auth->userdata();  //mcmaa tutup
            

        $this->form_validation->set_error_delimiters('', '');
        
        // todo
        // $total = ($this->user_data->id_role == "5") ? $this->todo->jana() : "";
        // $total_msg = $this->todo->jana_msg();
 

        $this->pager = array(
            'full_tag_open'     => '<ul class="pagination pagination-sm m-0 float-right">',
            'full_tag_close'    => '</ul>',

            'next_link'         => '&raquo;',
            'prev_link'         => '&laquo;',

            'next_tag_open'     => '<li class="page-item page-link">',
            'next_tag_close'    => '</li>',

            'prev_tag_open'     => '<li class="page-item page-link">',
            'prev_tag_close'    => '</li>',

            'first_tag_open'    => '<li class="page-item page-link">',
            'first_tag_close'   => '</li>',

            'last_tag_open'     => '<li  class="page-item page-link">',
            'last_tag_close'    => '</li>',

            'cur_tag_open'      => '<li class="page-item active"><a class="page-link" href="#">',
            'cur_tag_close'     => '</a></li>',
            
            'num_tag_open'      => '<li class="page-item page-link">',
            'num_tag_close'     => '</li>',
        );
 
        
        // $this->template->set('idt', $idt);
        $this->template->set('userData', $this->user_data);
        $this->template->set('todo', 0);
        $this->template->set('inbox', 0);
        $this->template->set_theme('admin');
        $this->template->set_layout('index');
        //Overwrite if the request is ajax
        if($this->input->is_ajax_request())
        {
            $this->template->set_layout('ajax');
        } 
        
        $this->form_validation->set_error_delimiters('<p>','</p>');
    }//end __construct()

    // public function set_session()
    // {
    //     if (isset($_SESSION["icno"]))
    //     {
    //         $ic = $_SESSION["icno"];			
            
    //         $profile = $this->db
    //             ->where("NOKP_BARU", $ic)
    //             ->get("SMS.PERSONEL")->row(); 

    //         return true;
    //     }
        
    //     header("Location: https://mynemo.umt.edu.my");
    // } 

    // public function disable_layout()
	// {
	// 	$this->template->set_layout('ajax');
	// }

}
/*End of Front Controller*/