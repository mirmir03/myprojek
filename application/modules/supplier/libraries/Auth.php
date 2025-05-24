<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * This library for authentication user 
 */
class Auth
{
	protected $ci;
	protected $user;

	public function __construct()
	{
		$this->ci =& get_instance();
        $this->ci->load->library('session');
        $this->ci->lang->load('users/users');
		$this->ci->load->model(array('supplier/users_model'));

		$this->user = $this->ci->session->userdata('app_session'); 
	}

	public function is_login()
	{
		return ($this->user) ? TRUE : FALSE; 
	}

    public function user_id()
    {
        return $this->user['idwarga'];
        // return $this->user['id_user'];
    }
	
	public function icno()
    {
        return $this->user['icno'];
    }
	
	public function user_group()
    {
        return $this->user['type'];
    }

    public function userdata()
    {
        $userdata =  $this->ci->users_model->select(array("WARGA_UMT.*"))
                                ->find($this->user_id());
        
        return $userdata;
    }
	
	public function vlogin_allx($uid = "", $pwd = "")
	{
		$array = array(
			"id_user"	=>	1,
			"icno"	=>	'820519115343',
			"nama"	=>	'rahim',
			"username"	=>	'abdul.rahim'
		);
		$this->ci->session->set_userdata('app_session', $array);
	
		$requested_page = $this->ci->session->userdata('requested_page'); 	
		if($requested_page != '')
		{ 
			redirect($requested_page);   
		}
		else
		{
			redirect(site_url("nopage"));
		}
		
		return true;
	}	
	
	public function vlogin_all($uid = "", $pwd = "")
	{
		$log = $this->ldaplog_all($uid, $pwd); 	
		
		if ($log){ 	
			
			$principal = $log[0]['dn']; 
				
			$dn = explode(",", $principal)[1];
			
			if (strrpos($dn, '=' ) == false)  die ("error");

			$ou =  trim(substr($dn, strrpos($dn, '=' )+1));
		
			
			switch($ou)
			{
				case "Staff" : 
				
					$nama = $log[0]['displayname'][0];
					$id = $log[0]['umticnumber'][0];
					$ic = $log[0]['umticnumber'][0];
					$type="S";
					
					break;
					
				case "Undergraduates" : 
				
					$nama = $log[0]['displayname'][0];
					$id = $log[0]['umtmatricnumber'][0];
					$ic = $log[0]['umticnumber'][0];
					$type="U";
					break;
					
				case "Graduates" : 
				
					$nama = $log[0]['displayname'][0];
					$id = $log[0]['umtmatricnumber'][0];
					$ic = $log[0]['umticnumber'][0];
					$type="G";
					
					break;
				
				default :  NULL; 
			}
			echo $nama;
			
	
			$array = array(
				"idwarga"	=>	$id,
				"icno"	=>	$ic,
				"nama"	=>	$nama,
				"type"	=>	$type
			);
			$this->ci->session->set_userdata('app_session', $array);
			//$this->ci->log_model->insert(array("COV_ID_UMT"	=>	$id));
		
			$requested_page = $this->ci->session->userdata('requested_page');
            if($requested_page != '')
            { 
                redirect($requested_page);   
            }
			else
			{
				redirect(site_url("/"));
			}
			
			return true;
		}
		else 
		{
			$this->ci->template->set_message("Login Gagal", 'error');
			return false;
		}
		
	}
	
	
	
	function ldaplog($pid, $credential)
	{		
		$host = '192.168.20.202';
		$ldapUsername  = "cn=SysAdmin,o=DSA,dc=umt,dc=edu,dc=my";
		$ldapPassword = "M3ng484ngT3l1p0t";

		$baseDn = 'dc=umt,dc=edu,dc=my';

				
	

		$ldap = ldap_connect($host);
		if(!ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3)){
		print "Could not set LDAPv3\r\n";
		}
		else 
		{
			$r = ldap_bind($ldap, $ldapUsername, $ldapPassword) or die("\r\nCould not connect to LDAP server\r\n");
		}
		


		//ldap_start_tls($ldap);

		if (isset($ldap) && $ldap != '') {

		/* search for pid dn */

		
			$result=ldap_search($ldap, "dc=umt,dc=edu,dc=my",'uid='.$pid); 

			if ($result != 0) {
				$entries = ldap_get_entries($ldap, $result);  
				if ($entries['count'] > 0) {
					$principal = $entries[0]['dn'];
				
					//$nokp = trim($entries[0]["employeenumber"][0]);
					$nokp =$entries[0]["umticnumber"][0];
				
					//echo "test=".$principal;
					
					if (isset($principal)) 
					{

					/* bind as this user */

						if (@ldap_bind($ldap, $principal, $credential)) {
							
							echo "ok";  
							return $nokp;

						} else {
							$msg="ID dan KATALALUAN anda tidak sah. Sila masukkan ID dan KATALALUAN yang betul.";
							$this->ci->template->set_message($msg, 'warning');						
						} 
					} else {
						print('User not found in LDAP');
						
					}
					
					ldap_free_result($result);
				}
				

			}	
			else 
			{
				print('Error occured searching the LDAP');
			}
			ldap_close($ldap);
		} else {
			print('Could not connect to LDAP at '.$host);
			return false;
		}


		
		
	}
	
	function ldaplog_allx($pid, $credential)
	{
		return array();
	}
	function ldaplog_all($pid, $credential)
	{		
		$host = '192.168.20.202';
		$ldapUsername  = "cn=SysAdmin,o=DSA,dc=umt,dc=edu,dc=my";
		$ldapPassword = "M3ng484ngT3l1p0t";

		$baseDn = 'dc=umt,dc=edu,dc=my';

				
	

		$ldap = ldap_connect($host);
		if(!ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3)){ 
		print "Could not set LDAPv3\r\n";
		}
		else 
		{
			$r = ldap_bind($ldap, $ldapUsername, $ldapPassword) or die("\r\nCould not connect to LDAP server\r\n");
		}
		


		//ldap_start_tls($ldap);

		if (isset($ldap) && $ldap != '') {

		/* search for pid dn */

		
			$result=ldap_search($ldap, "dc=umt,dc=edu,dc=my",'uid='.$pid); 

			if ($result != 0) {
				$entries = ldap_get_entries($ldap, $result);  
				if ($entries['count'] > 0) {
					$principal = $entries[0]['dn'];
				
					//$nokp = trim($entries[0]["employeenumber"][0]);
					$nokp =$entries[0]["umticnumber"][0];
				
					//echo "test=".$principal;
					
					if (isset($principal)) 
					{

					/* bind as this user */

						if (@ldap_bind($ldap, $principal, $credential)) {
							
							// echo "ok";  
							return $entries;

						} else {
							$msg="ID dan KATALALUAN anda tidak sah. Sila masukkan ID dan KATALALUAN yang betul.w";
							$this->ci->template->set_message($msg, 'warning');						
						} 
					} else {
						print('User not found in LDAP');
						
					}
					
					ldap_free_result($result);
				}
				

			}	
			else 
			{
				print('Error occured searching the LDAP');
			}
			ldap_close($ldap);
		} else {
			print('Could not connect to LDAP at '.$host);
			return false;
		}


		
		
	}
	
	public function login($username = "", $password = "")
	{
		if($this->is_login())
        {
            redirect('/');
        }

		$user 	= $this->ci->users_model->find_by(array('username' => $username));

    	if(!$user)
    	{
    		$this->ci->template->set_message(lang('users_login_fail'), 'error');
    		return FALSE;
    	}

        if($user->deleted == 1)
        {
            $this->ci->template->set_message(lang('users_already_deleted'), 'error');
            return FALSE;
        }

    	if($user->st_aktif == 0)
    	{
    		$this->ci->template->set_message(lang('users_not_active'), 'error');
    		return FALSE;
    	}

    	if(password_verify($password, $user->password))
    	{
    		//Buat Session
    		$array = array();
    		foreach ($user as $key => $usr) {
    			$array[$key] = $usr;
    		}

    		$this->ci->session->set_userdata('app_session', $array);
            //Set User Data
            $this->user = $this->ci->session->userdata('app_session');
            //Update Login Terakhir
            $ip_address = ($this->ci->input->ip_address()) == "::1" ? "127.0.0.1" : $this->ci->input->ip_address();
            $this->ci->users_model->update($this->user_id(), array('login_terakhir' => date('Y-m-d H:i:s'), 'ip' => $ip_address));

            $requested_page = $this->ci->session->userdata('requested_page');
            if($requested_page != '')
            {
                redirect($requested_page);
            }

    		redirect("/");
    	}

        $this->ci->template->set_message(lang('users_wrong_password'), 'error');
        $this->ci->template->message();
        return FALSE;
	}

    
	
	public function logout()
    {
        $this->ci->session->sess_destroy();
        redirect('login');
    }

    public function is_admin()
    {
        $id = $this->user_id();

        $data = $this->ci->users_model->join('user_groups','users.id_user = user_groups.id_user')
                                    ->find_by(array('users.id_user' => $id, 'id_group' => 1));
                                    
        if($data)
        {
            return TRUE;
        }

        return FALSE;
    }

    public function get_user_groups()
    {
        $id = $this->user_id();

        $groups = $this->ci->user_groups_model->select("user_groups.id_group, groups.nm_group")
                                    ->join('groups', 'user_groups.id_group = groups.id_group')
                                    ->order_by('nm_group', 'ASC')
                                    ->find_all_by(array('id_user' => $id));

        $return = "";
        $arr    = array();
        if($groups)
        {
            foreach ($groups as $key => $gr) {
                $arr[] = ucwords($gr->nm_group);
            }

            $return = implode(", ", $arr);
        }

        return $return;
    }

    public function has_permission($nm_permission = "")
    {
        if($nm_permission == "")
        {
            return FALSE;
        }

        if($this->is_admin())
        {
            return TRUE;
        }

        $id = $this->user_id();

        $group_permissions = $this->ci->users_model->join('user_groups','users.id_user = user_groups.id_user')
                                        ->join('group_permissions','user_groups.id_group = group_permissions.id_group')
                                        ->join('permissions','group_permissions.id_permission = permissions.id_permission')
                                        ->find_by(array('nm_permission' => $nm_permission,'users.id_user' => $id));
        if($group_permissions)
        {
            return TRUE;
        }

        $user_permissions = $this->ci->users_model->join('user_permissions','users.id_user = user_permissions.id_user')
                                        ->join('permissions','user_permissions.id_permission = permissions.id_permission')
                                        ->find_by(array('nm_permission' => $nm_permission,'users.id_user' => $id));
        if($user_permissions)
        {
            return TRUE;
        }

        return FALSE;
    }

    public function restrict($permission = null, $uri = null) // This function copied from bonfire with modification
    {
        // If user isn't logged in, redirect to the login page.
        if ($this->is_login() === false) {
            $this->ci->template->set_message(lang('users_must_login'), 'error');
            redirect('login');
        }

        // Check whether the user has the proper permissions.
        if (empty($permission) || $this->has_permission($permission)) {
            return true;
        }

        // If the user is logged in, but does not have permission...
        // If $uri is not set, get the previous page from the session.
        if (! $uri) {
            $uri      = $this->ci->session->userdata('previous_page');
            $req_page = $this->ci->session->userdata('requested_page');

            // If previous page and current page are the same, but the user no longer
            // has permission, redirect to site URL to prevent an infinite loop.
            if ($uri == $req_page) {
                $uri = site_url();
            }
        }

        // Inform the user of the lack of permission and redirect.
        $this->ci->template->set_message(lang('users_no_permission'), 'error');
        redirect($uri);
    }
}