<?php defined('BASEPATH') || exit('No direct script access allowed');
/**
 * @Author  : Suwito
 * @Email   : suwito.lt@gmail.com
 * @Date    : 2016-09-30 13:36:42
 * @Last Modified by    : Suwito
 * @Last Modified time  : 2016-11-07 22:15:59
 */

/**
 * A simple helper method for checking menu items against the current class/controller.
 * This function copied from cibonfire
 * <code>
 *   <a href="<?php echo site_url(SITE_AREA . '/content'); ?>" <?php echo check_class(SITE_AREA . '/content'); ?> >
 *    Admin Home
 *  </a>
 *
 * </code>
 *
 * @param string $item       The name of the class to check against.
 * @param bool   $class_only If true, will only return 'active'. If false, will
 * return 'class="active"'.
 *
 * @return string Either 'active'/'class="active"' or an empty string.
 */
function check_class($item = '', $class_only = false)
{
    if (strtolower(get_instance()->router->class) == strtolower($item)) {
        return $class_only ? 'active' : 'class="active"';
    }

    return '';
}

/**
 * A simple helper method for checking menu items against the current method
 * (controller action) (as far as the Router knows).
 *
 * @param string    $item       The name of the method to check against. Can be an array of names.
 * @param bool      $class_only If true, will only return 'active'. If false, will return 'class="active"'.
 *
 * @return string Either 'active'/'class="active"' or an empty string.
 */
function check_method($item, $class_only = false)
{
    $items = is_array($item) ? $item : array($item);
    if (in_array(get_instance()->router->method, $items)) {
        return $class_only ? 'active' : 'class="active"';
    }

    return '';
}

/**
 * Check if the logged user has permission or not
 * @param string $permission_name
 * @return bool True if has permission and false if not
 */
function has_permission($permission_name = "")
{
    $ci =& get_instance();
    
	$return = $ci->auth->has_permission($permission_name);

	return $return;
}

/**
 * @param  string $kode_tambahan
 * @return string generated code
 */
function gen_primary($kode_tambahan = "")
{

    $CI     =& get_instance();

    $tahun          = intval(date('Y'));
    $bulan          = intval(date('m'));
    $hari           = intval(date('d'));
    $jam            = intval(date('H'));
    $menit          = intval(date('i'));
    $detik          = intval(date('s'));
    $temp_ip        = ($CI->input->ip_address()) == "::1" ? "127.0.0.1" : $CI->input->ip_address();
    $temp_ip        = explode(".", $temp_ip);
    $ipval          = $temp_ip[0] + $temp_ip[1] + $temp_ip[2] + $temp_ip[3];

    $kode_rand      = mt_rand(1,1000)+$ipval;
    $letter1        = chr(mt_rand(65,90));
    $letter2        = chr(mt_rand(65,90));

    $kode_primary   = $tahun.$bulan.$hari.$jam.$menit.$detik.$letter1.$kode_rand.$letter2;
                
    return $kode_tambahan . $kode_primary;
}

if(! function_exists('simpan_aktifitas'))
{
    function simpan_aktifitas($nm_hak_akses = "", $kode_universal = "", $keterangan ="", $jumlah = 0, $sql = "", $status = NULL)
    {
        $CI     =& get_instance();

        $CI->load->model('aktifitas/aktifitas_model');

        $result = $CI->aktifitas_model->simpan_aktifitas($nm_hak_akses, $kode_universal, $keterangan, $jumlah, $sql, $status);

        return $result;
    }
}

/*
* $date_from is the date with format dd/mm/yyyy H:i:s / dd/mm/yyyy
*/
if (! function_exists('date_ymd')) {
    function date_ymd($date_from)
    {
        $error = false;
        if(strlen($date_from) <= 10){
            list($dd,$mm,$yyyy) = explode('/',$date_from);

            if (!checkdate(intval($mm),intval($dd),intval($yyyy)))
            {
                    $error = true;
            }
        }
        else
        {
            list($dd,$mm,$yyyy) = explode('/',$date_from);
            list($yyyy,$hhii) = explode(" ", $yyyy);

            if (!checkdate($mm,$dd,$yyyy))
            {
                    $error = true;
            }
        }

        if($error)
        {
            return false;
        }

        if(strlen($date_from) <= 10)
        {
            $date_from = DateTime::createFromFormat('d/m/Y', $date_from);   
            $date_from = $date_from->format('Y-m-d');
        }
        else
        {
            $date_from = DateTime::createFromFormat('d/m/Y H:i', $date_from);   
            $date_from = $date_from->format('Y-m-d H:i');   
        }
        
        return $date_from;
    }
}

if(! function_exists('simpan_alurkas')){

    function simpan_alurkas($kode_accountKas = null, $ket = "", $total = null , $status = null, $nm_hak_akses = ""){

        $CI     =& get_instance();

        $CI->load->model('kas/kas_model');

        $result = $CI->kas_model->simpan_alurKas($kode_accountKas, $ket, $total, $status, $nm_hak_akses);

        return $result;

    }

}

if(! function_exists('convert_bulan_to_malay')){

    function convert_bulan_ke_malay($tarikh){

        list($hari, $bulan, $tahun) = explode("/", $tarikh);
		switch($bulan)
		{
			case "01" : $mbulan = 'Januari'; break;
			case "02" : $mbulan = 'Februari';break;
			case "03" : $mbulan = 'Mac';break;
			case "04" : $mbulan = 'April';break;
			case "05" : $mbulan = 'Mei';break;
			case "06" : $mbulan = 'Jun';break;
			case "07" : $mbulan = 'Julai';break;
			case "08" : $mbulan = 'Ogos';break;
			case "09" : $mbulan = 'September';break;
			case "10" : $mbulan = 'Oktober';break;
			case "11" : $mbulan = 'November';break;
			case "12" : $mbulan = 'Disember';break;
			
			default: $mbulan = '';
		}
		
		return $hari.' '.$mbulan.' '.$tahun;
		
    }

}

if(! function_exists('module_url')){
	
	function module_url($method)
	{
		$CI     =& get_instance();	
		return  get_instance()->config->site_url($CI->router->fetch_module() .'/'.$method, NULL);
		return $CI->router->fetch_module() ;
	}
}

// if(! function_exists('hide_ic')){
	
// 	function hide_ic($ic="")
// 	{	

// 		return substr_replace($ic, str_repeat('*', strlen($ic)-6), 0, -6);		
		
// 	}
// }