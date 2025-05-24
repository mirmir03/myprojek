<?php defined('BASEPATH') || exit('No direct script access allowed');

class Menuloader
{
	protected $ci;
	protected $x; //Db Prefix
	protected $uri; //Current uri string
	protected $user_id;
	protected $is_admin;
	protected $module;

	public function __construct(){
		$this->ci =& get_instance();
		$module = $this->ci->router->fetch_module();
	}
	
	function render()
	{
		$this->ci->load->view($this->module .'/menu');
	}
}