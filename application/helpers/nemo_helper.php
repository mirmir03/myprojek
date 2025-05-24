<?php

function echo_table_title($title, $button=false, $onclick="")
{
	if ($button)
	{
		$button = '<div class="input-group-btn">
                        <button class="btn btn-sm btn-default  pull-right" id="btn_add" onclick="'.$onclick.'">
                        	<i class="fa fa-plus-square"></i>
                        </button>
                      </div> ';
	}
	$s = '<div class="box-header">
                  <h3 class="box-title">'.$title.'</h3>
                  <div class="box-tools">
					<div class="input-group">
                      <!--
					  <input type="text" name="table_search" class="form-control input-sm pull-right" style="width: 150px;" placeholder="Search"/>
                      -->
					  '.$button.'
                    </div>
                  </div>
                </div>';
				
	echo $s;
}

function echo_table_title1($title, $button=false)
{
	
	$s = '<div class="box-header">
                  <h3 class="box-title">'.$title.'</h3>
                  <div class="box-tools">
					<div class="input-group">
                      
					  <div class="input-group-btn">
                        <button class="btn btn-sm btn-default  pull-right" id="btn_add"><i class="fa fa-plus-square"></i></button>
                      </div> 
                    </div>
                  </div>
                </div>';
				
	echo $s;
}

function get_staff_id()
{
	return $_SESSION['icno'];	
	//return $_SESSION["eprestasi"]["nokp"];
}

function getportal_icno()
{
	return $_SESSION['icno'];
}

function getsession()
{
	return $_SESSION["eprestasi"]["role"];
}

function set_eprestasi_session($nokp)
{
	$_SESSION["eprestasi"]["nokp"] = $nokp;
	
	$ci =& get_instance();
	$ci->load->model("sys_model", "sy");
	
	$arr = array();
	$roles = $ci->sy->get_roles($nokp); 
	
	foreach ($roles->result() as $r)
	{
		$arr[] = $r->T12_ROLE;
	}
	
	$wfppp = $ci->sy->get_workflow($nokp);
	if ($wfppp->num_rows() > 0) $arr[] = "PPP";
	
	$wfppk = $ci->sy->get_workflow($nokp, 'PPK');
	if ($wfppk->num_rows() > 0) $arr[] = "PPK";
	
	$wfppd = $ci->sy->get_workflow($nokp, 'PPD');
	if ($wfppd->num_rows() > 0) $arr[] = "PPD";
	
	$sms_profil = $ci->sy->infostaf($nokp)->row();
	$sess_gred = $sms_profil->GRED; 
	$_SESSION["eprestasi"]["nama"] = $sms_profil->NAMA;
	
	
	$check_gred = $ci->sy->get_gred_valid($sess_gred);
	if ($check_gred->num_rows() > 0) $arr[] = "PYD";
	
	$_SESSION["eprestasi"]["tahun_aktif"] = '2017';
	$_SESSION["eprestasi"]["role"] = $arr;
	$_SESSION["eprestasi"]["generate"] = true;

	
	// var_dump($_SESSION["eprestasi"]);
}

function set_pretend_tobe($nokp){
	// $_SESSION["eprestasi"]["nokp"] = $nokp;
	$_SESSION["eprestasi"]["role"] = null;
	$_SESSION["eprestasi"]["nokp_asal"] = getportal_icno();
	$_SESSION["eprestasi"]["nama_asal"] = $_SESSION["eprestasi"]["nama"];
	$_SESSION["eprestasi"]["fake"] = 1;
	set_eprestasi_session($nokp);
}

function reset_pretend()
{
	$nokp = $_SESSION["eprestasi"]["nokp_asal"];
	$_SESSION["eprestasi"]["nokp_asal"] = "";
	$_SESSION["eprestasi"]["fake"] = "";
	
	set_eprestasi_session($nokp);
}

function check_level_session($level)
{
	foreach($_SESSION["eprestasi"]["role"] as $r)
	{ 
		if ($r == $level)
			return true;
	}
	
	return false;
}
function get_matrik()
{
	return $_SESSION["MATRIK"];
}

function set_dropdown_selected($nama_field, $field)
{
	$ci =& get_instance();

	if (isset($_POST[$nama_field]))
	// if ($ci->input->post($nama_field) != "")
	{
		return $ci->input->post($nama_field);
	}
	else 
	{
		return $field;
	}
}
?>
