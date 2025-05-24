<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * @author Cokeshome
 * @copyright Copyright (c) 2016, Cokeshome
 * 
 * This is controller for Supplier
 */
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Sys extends Adminx_Controller {
    
    /**
     * Load the models, library, etc
     *
     * 
     */
    //Permission
    protected $viewPermission   = "Supplier.View";
    protected $addPermission    = "Supplier.Add";
    protected $managePermission = "Supplier.Manage";
    protected $deletePermission = "Supplier.Delete";
	
	// protected $db = null; 
	

	protected $gen_module		= "gen"; 
	protected $gen_path 		= "tmp/gen/"; 
	protected $gen_controller 	= "my"; 
	protected $gen_model 		= "my"; 
	protected $exclude = array("datepicker","datetimepicker");
	

    public function __construct()
    {
        parent::__construct();
		$this->load->database();
		
		$this->lang->load('sys');
		$this->load->model('sys_model', 'im');
		$this->load->helper("file");
		
		$this->gen_path = APPPATH ."modules/".$this->gen_module;
    }

    
	
	function index($table="")
	{
		// $this->db = $this->load->database();
		// $this->db = $this->load->database('fasiliti', TRUE);
		
		//$table = "COV_TRACKING";
		
		if ( ! file_exists($f = APPPATH.'config/'.ENVIRONMENT.'/database.php')
        && ! file_exists($f = APPPATH.'config/database.php'))
   		{
        	show_error('The configuration file database.php does not exist.');
    	}

    	include($f);
    
    	$db_settings = $db;
    	
    	$arr_db = array();

    	foreach($db as $key => $value) {
    		$arr_db[$key] = $key;
    	}
		
		if (!$this->db->table_exists($table) ) die("No Table Found");
		
		$fields = $this->db->field_data($table);
		
		
		$pk = $this->im->get_primary_key($table);

		foreach ($fields as $field)
		{
				//echo $field->name." ";
				// echo $field->type." ";
				// echo $field->max_length." ";
				// echo ($field->name == $pk) ? "Y" : "";
				// echo "<br>";
		}
		
		$this->template->set("pk",$pk);
		$this->template->set("fields",$fields);
		$this->template->set("table",$table);
		$this->template->set("dbs",$arr_db);
		
		$this->template->title("Generator");
		$this->template->render("field");
	}
	
	function jana($nm_file="test.php" )
	{
		$fields = $this->input->post('checked')	;
		$pk = $this->input->post('pk');	
		$table = $this->input->post('table');
		$nm_controller = $this->input->post('nm_controller');
		$nm_sequence = $this->input->post('nm_sequence');
		$dbcon = $this->input->post('dbcon');
		
		$deleted = $this->input->post('deleted');
		$deleted_on = $this->input->post('deleted_on');
		$deleted_by = $this->input->post('deleted_by');
		$created_on = $this->input->post('created_on');
		$created_by = $this->input->post('created_by');
		$modified_on = $this->input->post('modified_on');
		$modified_by = $this->input->post('modified_by');
		
		$this->gen_controller = $nm_controller;
		$this->gen_model = $nm_controller;
	
		$string = read_file("tmp/crud/G_controller.php");		
		$model = read_file("tmp/crud/G_model.php");
		
		//lang
		$lang = read_file("tmp/crud/lang.php");
		
		//views
		$views_index = read_file("tmp/crud/view_index.php");
		$views_form = read_file("tmp/crud/view_form.php");
		$views_papar = read_file("tmp/crud/view_papar.php");
		$views_papar_field = read_file("tmp/crud/view_papar_field.php");
		$views_form_field = read_file("tmp/crud/view_form_field.php");
		$views_form_dropdown = read_file("tmp/crud/view_form_dropdown.php");
		$views_form_textarea = read_file("tmp/crud/view_form_textarea.php");
		$views_date_picker = read_file("tmp/crud/view_js_datepicker.php");
		$views_datetime_picker = read_file("tmp/crud/view_js_datetimepicker.php");
		
		$str_lang = "";
		$str_td_header = "";
		$str_td_data = "";
		$str_form_data = "";
		$str_form_array = "";
		$str_form_validation = "";
		$str_field_search = "";
		$str_field_select = "";
		$str_init_var = "";
		$str_csv_field = "";
		$str_join = "";
		$str_join_select = "";
		$js = "";
		$dbset = "";
		$dbselect = "*,";
		$autoincrement = ($nm_sequence != "") ? 'true' : 'false';
		$str_deleted = (isset($deleted)) ? $deleted." IS NULL AND " : "";
		$str_deleted_edit = (isset($deleted)) ? "'".$deleted."' => NULL " : "";
		
		$str_field_papar = "";
				
		$i = 0; 
		
		echo "Field generate : ".count($fields) ."<BR>";
		foreach($fields as $f)
		{ 
			$max = $this->input->post('max_'.$f);
			$type = $this->input->post('type_'.$f);
			$label = $this->input->post('label_'.$f);
			$jenis = $this->input->post('jenis_'.$f);
			$f_j = null;
			
			switch($type) 
			{
				case "NUMBER" : $validate = "|numeric";break;
				case "DATE" : $validate = ""; break;
				case "VARCHAR2" : $validate = "|max_length[".$max."]"; break;
				
				default : $validate = "";
			}
			
			switch($jenis) 
			{
				case "dropdown" : 
					$str_form_data .= str_replace(
						array("{dbfield}","{dbfieldname}", "{array_dropdown}"),
						array($f,strtolower($f), "array()"),
						$views_form_dropdown);
					break;
				
				case "dropdown_negeri" : 
					$str_form_data .= str_replace(
						array("{dbfield}","{dbfieldname}","{array_dropdown}"),
						array($f,strtolower($f),"\$negeri"),
						$views_form_dropdown);
						
					$str_init_var .= '$negeri = $this->lm->format_dropdown("KETERANGAN","KOD","JENIS = \'NEGERI\'","KETERANGAN");';
					
					
					$str_join_select .= " ->select(\"LN.KETERANGAN AS ".$f."_DESC\") \n";
					
					$str_join .= "->join(\"KMSKN.LOOKUP_NEGERI LN\", \"LN.KOD=".$table.".".$f."\")";
					
					$f_j = $f."_DESC";
					
					
					break;
				
				case "dropdown_bank" : 
					$str_form_data .= str_replace(
						array("{dbfield}","{dbfieldname}","{array_dropdown}"),
						array($f,strtolower($f),"\$array_bank"),
						$views_form_dropdown);
						
					$str_init_var .= '$array_bank = array("" => " - Nama Bank  -")+ $this->lm->format_dropdown("KETERANGAN","KOD","JENIS = \'BANK\'","KETERANGAN");';
					
					break;
				
				case "dropdown_yesno" : 
					$arrah_yesno = 'array("" => "- Sila pilih -","1"=>"Ya", "0"=>"Tidak")';
					$str_form_data .= str_replace(
						array("{dbfield}","{dbfieldname}","{array_dropdown}"),
						array($f,strtolower($f), "\$array_yesno"),
						$views_form_dropdown);
					
					$str_init_var .= '$array_yesno = array("" => " - Select  -")+ $this->lm->format_dropdown("KETERANGAN","KOD","JENIS = \'YESNO\'","KETERANGAN");';
					
					$str_join_select .= " ->select(\"LY.KETERANGAN AS ".$f."_DESC\") \n";
					
					$str_join .= "->join(\"KMSKN.LOOKUP_YESNO LY\", \"LY.KOD=".$table.".".$f."\")";
					
					$f_j = $f."_DESC";
					
					break;
				
				case "dropdown_staf" : 
					$str_form_data .= str_replace(
						array("{dbfield}","{dbfieldname}","{array_dropdown}"),
						array($f,strtolower($f), "\$array_yesno"),
						$views_form_dropdown);
					
					$str_init_var .= '$array_yesno = array("" => " - Select  -")+ $this->lm->format_dropdown("KETERANGAN","KOD","JENIS = \'YESNO\'","KETERANGAN");';
					
					$str_join_select .= " ->select(\"LY.KETERANGAN AS ".$f."_DESC\") \n";
					
					$str_join .= "->join(\"KMSKN.LOOKUP_YESNO LY\", \"LY.KOD=".$table.".".$f."\")";
					
					$f_j = $f."_DESC";
					
					break;	
					
				case "text" : 
					$str_form_data .= str_replace(
						array("{dbfield}","{dbfieldname}"),
						array($f,strtolower($f)),
						$views_form_field);
					break;
					
				case "textarea" : 
					$str_form_data .= str_replace(
						array("{dbfield}","{dbfieldname}"),
						array($f,strtolower($f)),
						$views_form_textarea);
					break;
				
				case "datepicker" : 
					$str_form_data .= str_replace(
						array("{dbfield}","{dbfieldname}"),
						array($f,strtolower($f)),
						$views_form_field);
						
					$js	.= str_replace(
						array("{dbfield}","{dbfieldname}"),
						array($f,strtolower($f)),
						$views_date_picker);
					 
					$dbset .= "list(\$hari, \$bulan, \$tahun) = explode(\"/\", \$this->input->post('".strtolower($f)."'));\n";
					$dbset .= "\$this->im->set('".$f."',\"to_date('\$hari\$bulan\$tahun','dd/mm/yyyy')\",false);";	
					
					$dbselect .= "$f as TKH_".$f.",";
					$str_field_select .= "->select(\"TO_CHAR($f,'DD/MM/YYYY') as $f\", false) ";				
					
					break;
				
				case "datetimepicker" : 
					$str_form_data .= str_replace(
						array("{dbfield}","{dbfieldname}"),
						array($f,strtolower($f)),
						$views_form_field);
						
					$js	.= str_replace(
						array("{dbfield}","{dbfieldname}"),
						array($f,strtolower($f)),
						$views_datetime_picker);
					 
					$dbset .= "list(\$tarikh, \$masa) = explode(\" \", \$this->input->post('".strtolower($f)."'));\n";
					$dbset .= "list(\$hari, \$bulan, \$tahun) = explode(\"/\", \$tarikh);\n";
					$dbset .= "list(\$jam, \$minit) = explode(\":\", \$masa);\n";
					$dbset .= "\$this->im->set('".$f."',\"to_date('\$hari\$bulan\$tahun \$jam:\$minit','DD/MM/YYYY HH24:MI')\",false);";	
					
					$dbselect .= "$f as TKH_".$f.",";
					$str_field_select .= "->select(\"TO_CHAR($f,'DD/MM/YYYY HH24:MI') as $f\", false) ";				
					
					break;	
					
				case "" : $validate = "|max_length[".$max."]"; break;
				
				default : null;
			}
			
			$this->input->post('".strtolower($f)."');
			
			$str_lang .= "\$lang['".strtolower($f)."'] = '".$label."';\n";
			$str_td_header .= "<th><?=lang('".strtolower($f)."')?></th>\n";
			$str_td_data .= "<td><?= \$record->".(($f_j == null) ? $f : $f_j)." ?></td>\n";
			$str_form_array .= $this->__jana_field_array($f, $jenis);
			$str_csv_field .= $f.", ";
			
			// $str_form_data .= str_replace(
				// array("{dbfield}","{dbfieldname}"),
				// array($f,strtolower($f)),
				// $views_form_field);
				
			$str_form_validation .= "\$this->form_validation->set_rules('".strtolower($f)."','lang:".strtolower($f)."','required".$validate."|trim');\n";
			$str_field_search .= $f." LIKE '%\$search2%' OR ";
			$str_field_select .= $this->__jana_select_display($f, $jenis);
			
			$str_field_papar .= str_replace(
						array("{dbfield}","{dbfieldname}"),
						array($f,strtolower($f)),
						$views_papar_field);
						
		}
	    $str_csv_field = substr($str_csv_field, 0, -2);

		$search = array(
				"{nm_module}",
				"{nm_controller}",
				"{nm_controller_class}",
				"{nm_model}",
				"{nm_model_class}",
				"{lang}",
				"{td_header}",
				"{td_data}",
				"{pk}",
				"{str_form_data}",
				"{str_form_array}",
				"{str_form_validation}",
				"{nama_table}",
				"{str_field_search}",
				"{deleted}",
				"{deleted_on}",
				"{deleted_by}",
				"{created_on}",
				"{created_by}",
				"{modified_on}",
				"{modified_by}",
				"{str_init_var}",
				"{str_csv_field}",
				"{nm_sequence}",
				"{dbcon}",
				"{js}",
				"{dbset}",
				"{dbselect}",
				"{str_field_select}",
				"{autoincrement}",
				"{str_deleted_edit}",
				"{str_join}",
				"{str_join_select}",
				"{str_field_papar}",
		);
		
		$replace = array(
				$this->gen_module,
				$this->gen_controller,
				ucfirst($this->gen_controller), 
				$this->gen_model,
				ucfirst($this->gen_model),
				$str_lang,
				$str_td_header,
				$str_td_data,
				$pk,
				$str_form_data,
				$str_form_array,
				$str_form_validation,
				$table,
				rtrim($str_field_search, 'OR '),
				$str_deleted,
				$deleted_on,
				$deleted_by,
				$created_on,
				$created_by,
				$modified_on,
				$modified_by,
				$str_init_var,
				$str_csv_field,
				$nm_sequence,
				$dbcon,
				$js,
				$dbset,
				substr($dbselect,0,-1),
				$str_field_select,
				$autoincrement,
				$str_deleted_edit,
				$str_join,
				$str_join_select,
				$str_field_papar
		);
		
		$string = str_replace($search, $replace, $string);
		$model = str_replace($search, $replace, $model);
		$views_index = str_replace($search, $replace, $views_index);
		$views_form = str_replace($search, $replace, $views_form);
		$views_papar = str_replace($search, $replace, $views_papar);
		$lang = str_replace($search, $replace, $lang);
		
		//create controllers
		
		$folder_controllers = $this->gen_path."/controllers";
		$folder_models = $this->gen_path."/models";
		$folder_views = $this->gen_path."/views";
		
		$folder_lang = $this->gen_path."/language";
		$folder_lang = $this->gen_path."/language";
		$folder_lang_eng = $this->gen_path."/language/english";
		
		if(!file_exists($folder_controllers)) mkdir($folder_controllers);
		if(!file_exists($folder_models)) mkdir($folder_models);
		if(!file_exists($folder_views)) mkdir($folder_views);
		
		if(!file_exists($folder_lang)) mkdir($folder_lang);
		if(!file_exists($folder_lang_eng)) mkdir($folder_lang_eng);
		
		$template_folder = $folder_views."/".$this->gen_controller;
		
		if(!file_exists($template_folder)) mkdir($template_folder);
		
		///controllers
		$f=write_file($folder_controllers."/".ucfirst($this->gen_controller).".php",$string, 'w');
		echo $folder_controllers."/".ucfirst($this->gen_controller).".php <br>";
		
		//models
		$f=write_file($folder_models."/".ucfirst($this->gen_model)."_model.php",$model, 'w');
		echo $folder_models."/".ucfirst($this->gen_model)."_model.php <br>";
		
		//language
		$f=write_file($folder_lang_eng."/".$this->gen_controller."_lang.php",$lang, 'w');
		echo $folder_lang_eng."/".$this->gen_controller."_lang.php <br>";
		
		
		//views
		$f=write_file($template_folder."/index.php",$views_index, 'w');
		echo $template_folder."/index.php<br>";
		
		$f=write_file($template_folder."/form.php",$views_form, 'w');
		echo $template_folder."/form.php<br>";
		
		$f=write_file($template_folder."/papar.php",$views_papar, 'w');
		echo $template_folder."/papar.php";
		
	}
	
	function __jana_field_array($f, $jenis )
	{
		if (!in_array($jenis, $this->exclude)) {
			$str = "'".$f."' => strtoupper(\$this->input->post('".strtolower($f)."')),\n";
			
			return $str;
		}
		
		return false;
	}
	
	function __jana_select_display($f, $jenis )
	{
		if (!in_array($jenis, $this->exclude)) { 
			$str = " ->select(\"".$f."\") \n";
			
			return $str;
		}
		
		return false;
	}
}
?>