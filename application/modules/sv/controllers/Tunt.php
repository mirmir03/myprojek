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

class Tunt extends Admin_Controller {
    
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

    public function __construct()
    {
        parent::__construct();
		$this->lang->load('ccc');
		$this->load->model('ccc_model', 'im'); 			
	
    }

    
	
	function index()
	{		
		if (isset($_POST['csv']) )
		{
			$this->__csv();
		}
		
		//if (isset($_POST['delete']) && has_permission($this->deletePermission))
		if (isset($_POST['delete']) )
        {
            $checked = $this->input->post('checked');

            if (is_array($checked) && count($checked))
            {
                $result = FALSE;
                foreach ($checked as $pid)
                {
                    //$ccc_name = $this->im->find($pid)->NAMA_MENU;

                    $result = $this->im->delete($pid);

                    // if($result)
                    // {
                        // $keterangan = "SUKSES, hapus data supplier ".$ccc_name.", dengan ID : ".$pid;
                        // $status     = 1;
                    // }
                    // else
                    // {
                        // $keterangan = "GAGAL, hapus data supplier ".$ccc_name.", dengan ID : ".$pid;
                        // $status     = 0;
                    // }

                    $nm_hak_akses   = $this->deletePermission; 
                    $kode_universal = $pid;
                    $jumlah         = 1;
                    //$sql            = $this->db->last_query();

                    //simpan_aktifitas($nm_hak_akses, $kode_universal, $keterangan, $jumlah, $sql, $status);
                }

                if ($result)
                {
                    $this->template->set_message(count($checked) .' '. lang('ccc_deleted') .'.', 'success');
                }
                else
                {
                    $this->template->set_message(lang('ccc_del_failure') . $this->im->error, 'error');
                }
            }
            else
            {
                $this->template->set_message(lang('ccc_del_error') . $this->im->error, 'error');
            }
			
		
        }
		
		// Pagination
        $this->load->library('pagination');
		
		if(isset($_POST['table_search']))
        { 
            $search = isset($_POST['table_search'])?$this->input->post('table_search'):'';
        }
        else
        {
            $search = isset($_GET['search'])?$this->input->get('search'):'';
        }

        $filter = "";
        if($search!="")
        {
            $filter = "?search=".$search;
        }

        // $search2 = $this->db->escape_str($search);
        $search2 = $search;
		
		$where = "  
                 (COV_MATRIK LIKE '%$search2%' OR COV_HP LIKE '%$search2%' OR COV_BANK_KOD LIKE '%$search2%')";
		
		$total = $this->im
                        ->where($where)
                        ->count_all();
		
	    $offset = $this->input->get('per_page');

        // $limit = $this->config->item('list_limit');
        $limit = 4;

        $this->pager['base_url']            = current_url().$filter;
        $this->pager['total_rows']          = $total;
        $this->pager['per_page']            = $limit;
        $this->pager['page_query_string']   = TRUE;
		
		$this->pagination->initialize($this->pager);
		
	
		$data = $this->im
				->select("COV_ID")
				 ->select("COV_MATRIK") 
			 ->select("COV_HP") 
			 ->select("COV_BANK_KOD") 

				
				 ->where($where)
				 ->limit($limit, $offset)
				->order_by("COV_ID")
				->find_all();
		
		
		$this->template->set("results", $data);
		
		$this->template->set("numb", $offset+1);
		$this->template->set('search', $search);
		
		$this->template->title(lang('ccc_title_manage'));
		
		//Load default view on folder {folder}/index.php 
		$this->template->render();
	}

	function baki()
	{		
		if (isset($_POST['csv']) )
		{
			$this->__csv();
		}
		
		//if (isset($_POST['delete']) && has_permission($this->deletePermission))
		if (isset($_POST['delete']) )
        {
            $checked = $this->input->post('checked');

            if (is_array($checked) && count($checked))
            {
                $result = FALSE;
                foreach ($checked as $pid)
                {
                    //$ccc_name = $this->im->find($pid)->NAMA_MENU;

                    $result = $this->im->delete($pid);

                    // if($result)
                    // {
                        // $keterangan = "SUKSES, hapus data supplier ".$ccc_name.", dengan ID : ".$pid;
                        // $status     = 1;
                    // }
                    // else
                    // {
                        // $keterangan = "GAGAL, hapus data supplier ".$ccc_name.", dengan ID : ".$pid;
                        // $status     = 0;
                    // }

                    $nm_hak_akses   = $this->deletePermission; 
                    $kode_universal = $pid;
                    $jumlah         = 1;
                    //$sql            = $this->db->last_query();

                    //simpan_aktifitas($nm_hak_akses, $kode_universal, $keterangan, $jumlah, $sql, $status);
                }

                if ($result)
                {
                    $this->template->set_message(count($checked) .' '. lang('ccc_deleted') .'.', 'success');
                }
                else
                {
                    $this->template->set_message(lang('ccc_del_failure') . $this->im->error, 'error');
                }
            }
            else
            {
                $this->template->set_message(lang('ccc_del_error') . $this->im->error, 'error');
            }
			
		
        }
		
		// Pagination
        $this->load->library('pagination');
		
		if(isset($_POST['table_search']))
        { 
            $search = isset($_POST['table_search'])?$this->input->post('table_search'):'';
        }
        else
        {
            $search = isset($_GET['search'])?$this->input->get('search'):'';
        }

        $filter = "";
        if($search!="")
        {
            $filter = "?search=".$search;
        }

        // $search2 = $this->db->escape_str($search);
        $search2 = $search;
		
		$where = "  
                 (COV_MATRIK LIKE '%$search2%' OR COV_HP LIKE '%$search2%' OR COV_BANK_KOD LIKE '%$search2%')";
		
		$total = $this->im
                        ->where($where)
                        ->count_all();
		
	    $offset = $this->input->get('per_page');

        // $limit = $this->config->item('list_limit');
        $limit = 4;

        $this->pager['base_url']            = current_url().$filter;
        $this->pager['total_rows']          = $total;
        $this->pager['per_page']            = $limit;
        $this->pager['page_query_string']   = TRUE;
		
		$this->pagination->initialize($this->pager);
		
	
		$data = $this->im
				->select("COV_ID")
				 ->select("COV_MATRIK") 
			 ->select("COV_HP") 
			 ->select("COV_BANK_KOD") 

				
				 ->where($where)
				 ->limit($limit, $offset)
				->order_by("COV_ID")
				->find_all();
		
		
		$this->template->set("results", $data);
		
		$this->template->set("numb", $offset+1);
		$this->template->set('search', $search);
		
		$this->template->title(lang('ccc_title_manage'));
		
		//Load default view on folder {folder}/index.php 
		$this->template->render();
	}
	
	public function papar($id)
   	{
   
  		//$this->auth->restrict($this->managePermission);
                
        //$id = (int)$this->uri->segment(4);

        if (empty($id))
        {
            $this->template->set_message(lang("ccc_invalid_id"), 'error');
            redirect('supplier');
        }
        
        $data  = $this->im
			 ->select("COV_MATRIK") 
 ->select("COV_HP") 
 ->select("COV_BANK_KOD") 

			->find($id);
			// ->find_by(array('COV_ID' => $id, ));

        if(!$data)
        {
            $this->template->set_message(lang("ccc_invalid_id"), 'error');
            redirect('supplier');
        }
        
        $this->template->set('data', $data);
        $this->template->set('id', $id);
        $this->template->title(lang("ccc_title_edit"));
        $this->template->render($this->folder .'papar');
   	}
	
	public function create()
   	{
		$this->template->set_message("Arahan Sini", 'info');
               
        //$this->auth->restrict($this->addPermission);
                    
        if (isset($_POST['save']))
        {
            if ($id = $this->save_ccc())
            { 
              // $this->template->set_message(lang("ccc_create_success"), 'success');
              $this->template->set_message(lang("asdasd"), 'success');
              redirect(module_url('ccc/index'));
            }
        }

        $this->template->title(lang('ccc_title_new'));
		$this->template->render($this->folder .'baki');
   	}
	
	public function edit($id)
   	{
   
  		//$this->auth->restrict($this->managePermission);
                
        //$id = (int)$this->uri->segment(4);

        if (empty($id))
        {
            $this->template->set_message(lang("ccc_invalid_id"), 'error');
            redirect('supplier');
        }

        if (isset($_POST['save']))
        {
            if ($this->save_ccc('update', $id))
            {
                $this->template->set_message(lang("ccc_edit_success"), 'success');
				redirect(module_url ('/ccc/index'));
            }

        }

        
        $data  = $this->im
			 ->select("COV_MATRIK") 
 ->select("COV_HP") 
 ->select("COV_BANK_KOD") 

			->find($id);
			// ->find_by(array('COV_ID' => $id, ));

        if(!$data)
        {
            $this->template->set_message(lang("ccc_invalid_id"), 'error');
            redirect('supplier');
        }
        
        $this->template->set('data', $data);
        $this->template->title(lang("ccc_title_edit"));
        $this->template->render($this->folder .'form');
   	}
	
	public function hapus()
	{
		$id = $this->input->post('id');
		$this->im->delete($id);
		$this->template->set_message(lang('ccc_deleted') .'.', 'success');
		redirect('gen/ccc');
	}
	
	public function username_check($str)
        {
                if ($str == 'test')
                {
                        $this->form_validation->set_message('username_check', 'The {field} field can not be the word "test"');
                        return FALSE;
                }
                else
                {
                        return TRUE;
                }
        }
		
	protected function save_ccc($type='insert', $id=0)
   	{
		$this->form_validation->set_rules('cov_matrik','lang:cov_matrik','callback_username_check|numeric|required|max_length[20]|trim');
		$this->form_validation->set_rules('cov_hp','lang:cov_hp','max_length[20]|required||trim');
		$this->form_validation->set_rules('cov_bank_kod','lang:cov_bank_kod','required|max_length[20]|trim');
		
        if ($this->form_validation->run() === FALSE)
        { 
            $this->template->set_message(validation_errors(), 'error');
            return FALSE;
        }

        unset($_POST['submit'], $_POST['save']);
		
		$dt = array(
			'COV_MATRIK' => strtoupper($this->input->post('cov_matrik')),
'COV_HP' => strtoupper($this->input->post('cov_hp')),
'COV_BANK_KOD' => strtoupper($this->input->post('cov_bank_kod')),

		);

		
		
        if ($type == 'insert')
        {
            $id = $this->im->insert($dt);

            if (is_numeric($id))
            {
                //Save Log
                $ccc_name = $this->input->post('nm_supplier');
                if($id)
                {
                    $keterangan = "SUKSES, tambah data supplier ".$ccc_name.", dengan ID : ".$id;
                    $status     = 1;
                }
                else
                {
                    $keterangan = "GAGAL, tambah data supplier ".$ccc_name.", dengan ID : ".$id;
                    $status     = 0;
                }

                $nm_hak_akses   = $this->addPermission; 
                $kode_universal = $id;
                $jumlah         = 1;
                //$sql            = $this->db->last_query();

                //simpan_aktifitas($nm_hak_akses, $kode_universal, $keterangan, $jumlah, $sql, $status);
                // $return = TRUE;
                $return = $id;
            }
            else
            {
                $return = FALSE;
            }
			
			return $return;
        }
        else if ($type == 'update')
        {
			
            $return = $this->im->update($id, $dt);

            //Save Log
            $ccc_name = $this->input->post('nm_supplier');
            if($return)
            {
                $keterangan = "SUKSES, ubah data supplier ".$ccc_name.", dengan ID : ".$id;
                $status     = 1;
            }
            else
            {
                $keterangan = "GAGAL, ubah data supplier ".$ccc_name.", dengan ID : ".$id;
                $status     = 0;
            }

            $nm_hak_akses   = $this->addPermission; 
            $kode_universal = $id;
            $jumlah         = 1;
           // $sql            = $this->db->last_query();

            //simpan_aktifitas($nm_hak_akses, $kode_universal, $keterangan, $jumlah, $sql, $status);
        
		}
	
		return $return;
	} 
	
	function confirm($id="1")
	{
		include(APPPATH.'libraries/phpqrcode/qrlib.php');
		
		$tempDir = 'tmp/';
		$fileName = '1.png'; 		
		$pngAbsoluteFilePath = $tempDir.$fileName;
		$codeContents = 'mynemo.umt.edu.my/cov/aj/'.$id; 
		
		QRcode::png($codeContents, $pngAbsoluteFilePath,QR_ECLEVEL_L, 10, 1); 
		$image = imagecreatefrompng($pngAbsoluteFilePath);
		imagedestroy($image);
		//unlink($pngAbsoluteFilePath);

		$this->template->set('qr', $pngAbsoluteFilePath);
		$this->template->title("Pengesahan");
		$this->template->set_message(lang("ccc_create_success"), 'success');
		$this->template->render('parents/confirm');
	}
	
	function find_pelajar($matrik = "")
	{
		$this->load->library("jquery");
		$matrik = strtoupper(str_replace(' ', '',$this->input->post('matrik')));
		//$data  = $this->im->find_by(array('ID' => $id, 'DELETED' => null));
		$data = $this->um->find($matrik); 
		
		if ($data) {
			j("#cov_nama_pelajar")->val($data->NAMA);
			j("#cov_program")->val($data->PROGRAM_PEKERJAAN);
			j("#cov_matrik")->val($matrik);
		}
		else
		{
			ajaxAlert("Tiada rekod nombor matrik ($matrik) ini. Sila semak nombor matrik yang dimasukkan.");
			j("#cov_matrik")->val($matrik);
		}
		
		
		ajaxResponse();
	}
	
	function csv()
	{
		// output headers so that the file is downloaded rather than displayed
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=data_'.time().'.csv');
		
		// create a file pointer connected to the output stream
		$fp = fopen('php://output', 'w');
		
		// output the column headings
		//start ejas
		fputcsv($fp, array(
			'HELP TOPIC ID', 
			'HELP KEYWORD ID',
		));
		
		
		$list = $this->im
			  ->select('COV_MATRIK, COV_HP, COV_BANK_KOD')
			  ->find_all();
		//end ejas
		
		/*$list = $this->im
				->select("LOKASI, NAMA, PROGRAM, JANTINA")
				->join("WARGA_UMT","WARGA_UMT.ID_UMT = TDG_TB_MENU.COV_MATRIK")
				->join("NEGERI","WARGA_UMT.NEGERI_TETAP = NEGERI.KOD_UPU",'LEFT')
				->where($where)
				->find_all();
		*/		
		
		foreach ($list as $fields) {
			// fputcsv($fp, $fields);
			fputcsv($fp, get_object_vars($fields));
		}
	}

	public function mohon()
   	{
		// $this->template->set_message("Arahan Sini", 'info');
               
        //$this->auth->restrict($this->addPermission);
                    
        if (isset($_POST['save']))
        {
            if ($id = $this->save_ccc())
            { 
              // $this->template->set_message(lang("ccc_create_success"), 'success');
              $this->template->set_message(lang("asdasd"), 'success');
              redirect(module_url('ccc/index'));
            }
        }

        $this->template->title(lang('ccc_title_new'));
		$this->template->render();
   	}
	
	public function detail()
   	{
		// $this->template->set_message("Arahan Sini", 'info');
               
        //$this->auth->restrict($this->addPermission);
                    
        if (isset($_POST['save']))
        {
            if ($id = $this->save_ccc())
            { 
              // $this->template->set_message(lang("ccc_create_success"), 'success');
              $this->template->set_message(lang("asdasd"), 'success');
              redirect(module_url('ccc/index'));
            }
        }

        $this->template->title(lang('ccc_title_new'));
		$this->template->render();
   	}
	
	public function tunt()
   	{
		// $this->template->set_message("Arahan Sini", 'info');
               
        //$this->auth->restrict($this->addPermission);
                    
        if (isset($_POST['save']))
        {
            if ($id = $this->save_ccc())
            { 
              // $this->template->set_message(lang("ccc_create_success"), 'success');
              $this->template->set_message(lang("asdasd"), 'success');
              redirect(module_url('ccc/index'));
            }
        }

        $this->template->title(lang('ccc_title_new'));
		$this->template->render();
   	}
	
	public function edittunt()
   	{
		// $this->template->set_message("Arahan Sini", 'info');
               
        //$this->auth->restrict($this->addPermission);
                    
        if (isset($_POST['save']))
        {
            if ($id = $this->save_ccc())
            { 
              // $this->template->set_message(lang("ccc_create_success"), 'success');
              $this->template->set_message(lang("asdasd"), 'success');
              redirect(module_url('ccc/index'));
            }
        }

        $this->template->title(lang('ccc_title_new'));
		$this->template->render();
   	}
	
	public function kenyataan()
   	{
		// $this->template->set_message("Arahan Sini", 'info');
               
        //$this->auth->restrict($this->addPermission);
                    
        if (isset($_POST['save']))
        {
            if ($id = $this->save_ccc())
            { 
              // $this->template->set_message(lang("ccc_create_success"), 'success');
              $this->template->set_message(lang("asdasd"), 'success');
              redirect(module_url('ccc/index'));
            }
        }

        $this->template->title(lang('ccc_title_new'));
		$this->template->render();
   	}
	
	public function listkenyataan()
   	{
		// $this->template->set_message("Arahan Sini", 'info');
               
        //$this->auth->restrict($this->addPermission);
                    
        if (isset($_POST['save']))
        {
            if ($id = $this->save_ccc())
            { 
              // $this->template->set_message(lang("ccc_create_success"), 'success');
              $this->template->set_message(lang("asdasd"), 'success');
              redirect(module_url('ccc/index'));
            }
        }

        $this->template->title(lang('ccc_title_new'));
		$this->template->render();
   	}
	
	public function addkeny()
   	{
		// $this->template->set_message("Arahan Sini", 'info');
               
        //$this->auth->restrict($this->addPermission);
                    
        if (isset($_POST['save']))
        {
            if ($id = $this->save_ccc())
            { 
              // $this->template->set_message(lang("ccc_create_success"), 'success');
              $this->template->set_message(lang("asdasd"), 'success');
              redirect(module_url('ccc/index'));
            }
        }

        $this->template->title(lang('ccc_title_new'));
		$this->template->render();
   	}
	
	public function listmohon()
   	{                    
        if (isset($_POST['save']))
        {
            if ($id = $this->save_ccc())
            { 
              // $this->template->set_message(lang("ccc_create_success"), 'success');
              $this->template->set_message(lang("asdasd"), 'success');
              redirect(module_url('ccc/index'));
            }
        }

        $this->template->title(lang('ccc_title_new'));
		$this->template->render();
   	}
	
	public function summary()
   	{                    
        if (isset($_POST['save']))
        {
            if ($id = $this->save_ccc())
            { 
              // $this->template->set_message(lang("ccc_create_success"), 'success');
              $this->template->set_message(lang("asdasd"), 'success');
              redirect(module_url('ccc/index'));
            }
        }

        $this->template->title(lang('ccc_title_new'));
		$this->template->render();
   	}
	
	public function detail_claim()
   	{                    
        if (isset($_POST['save']))
        {
            if ($id = $this->save_ccc())
            { 
              // $this->template->set_message(lang("ccc_create_success"), 'success');
              $this->template->set_message(lang("asdasd"), 'success');
              redirect(module_url('ccc/index'));
            }
        }

        $this->template->title(lang('ccc_title_new'));
		$this->template->render();
   	}
	
	public function listmohon_all()
   	{                    
        if (isset($_POST['save']))
        {
            if ($id = $this->save_ccc())
            { 
              // $this->template->set_message(lang("ccc_create_success"), 'success');
              $this->template->set_message(lang("asdasd"), 'success');
              redirect(module_url('ccc/index'));
            }
        }

        $this->template->title("Senarai Permohonan");
		$this->template->render();
   	}
}
?>

