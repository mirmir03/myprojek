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

class Parents extends Web_Controller {
    
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
		$this->lang->load('ora');
		$this->load->model('intake_model', 'im');        
		$this->load->model('users_model', 'um');        
    }

    
	
	function testoracle()
	{		
		//if (isset($_POST['delete']) && has_permission($this->deletePermission))
		if (isset($_POST['delete']) )
        {
            $checked = $this->input->post('checked');

            if (is_array($checked) && count($checked))
            {
                $result = FALSE;
                foreach ($checked as $pid)
                {
                    $menu_name = $this->im->find($pid)->NAMA_MENU;

                    $result = $this->im->delete($pid);

                    if($result)
                    {
                        $keterangan = "SUKSES, hapus data supplier ".$menu_name.", dengan ID : ".$pid;
                        $status     = 1;
                    }
                    else
                    {
                        $keterangan = "GAGAL, hapus data supplier ".$menu_name.", dengan ID : ".$pid;
                        $status     = 0;
                    }

                    $nm_hak_akses   = $this->deletePermission; 
                    $kode_universal = $pid;
                    $jumlah         = 1;
                    //$sql            = $this->db->last_query();

                    //simpan_aktifitas($nm_hak_akses, $kode_universal, $keterangan, $jumlah, $sql, $status);
                }

                if ($result)
                {
                    $this->template->set_message(count($checked) .' '. lang('menu_deleted') .'.', 'success');
                }
                else
                {
                    $this->template->set_message(lang('menu_del_failure') . $this->im->error, 'error');
                }
            }
            else
            {
                $this->template->set_message(lang('menu_del_error') . $this->im->error, 'error');
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
		
		$where = " DELETED IS NULL 
                AND (NAMA_MENU LIKE '%$search2%' OR URL LIKE '%$search2%')";
		
		$total = $this->im
                        ->where($where)
                        ->count_all();
		
	    $offset = $this->input->get('per_page');

        // $limit = $this->config->item('list_limit');
        $limit = 5;

        $this->pager['base_url']            = current_url().$filter;
        $this->pager['total_rows']          = $total;
        $this->pager['per_page']            = $limit;
        $this->pager['page_query_string']   = TRUE;
		
		$this->pagination->initialize($this->pager);
		
	
		$data = $this->im
				->where($where)
				->limit($limit, $offset)
				->find_all();	
		
		
		$this->template->set("results", $data);
		
		$this->template->set("numb", $offset+1);
		$this->template->set('search', $search);
		
		$this->template->title(lang('menu_title_manage'));
		$this->template->render("ora/senarai");
	}

	public function create()
   	{
		$this->template->set_message("Sila masukkan maklumat diperlukan dan Klik Submit Akujanji untuk menghantar akujanji", 'info');
               
        //$this->auth->restrict($this->addPermission);
                    
        if (isset($_POST['save']))
        {
            if ($id = $this->save_supplier())
            { 
              // $this->template->set_message(lang("menu_create_success"), 'success');
              $this->template->set_message(lang("asdasd"), 'success');
              redirect('reg/parents/confirm/'.$id);
            }
        }

        $this->template->title(lang('menu_title_new'));
		$this->template->render('parents/ora_form');
   	}
	
	public function edit($id)
   	{
   
  		//$this->auth->restrict($this->managePermission);
                
        $id = (int)$this->uri->segment(4);

        if (empty($id))
        {
            $this->template->set_message(lang("menu_invalid_id"), 'error');
            redirect('supplier');
        }

        if (isset($_POST['save']))
        {
            if ($this->save_supplier('update', $id))
            {
                $this->template->set_message(lang("menu_edit_success"), 'success');
				redirect('supplier/ora/testoracle');
            }

        }

        
        $data  = $this->im->find_by(array('ID' => $id, 'DELETED' => null));

        if(!$data)
        {
            $this->template->set_message(lang("menu_invalid_id"), 'error');
            redirect('supplier');
        }
        
        $this->template->set('data', $data);
        $this->template->title(lang("menu_title_edit"));
        $this->template->render('ora/ora_form');
   	}
	
	protected function save_supplier($type='insert', $id=0)
   	{

        $this->form_validation->set_rules('cov_nama_parent','lang:cov_ic','required|trim|max_length[45]');
        $this->form_validation->set_rules('cov_ic','lang:cov_ic','required|trim|max_length[255]');
        $this->form_validation->set_rules('cov_matrik','lang:cov_matrik','required|trim|max_length[15]');
        $this->form_validation->set_rules('cov_hp','lang:cov_hp','required|trim|max_length[15]');

        if ($this->form_validation->run() === FALSE)
        {
            $this->template->set_message(validation_errors(), 'error');
            return FALSE;
        }

        unset($_POST['submit'], $_POST['save']);

        if ($type == 'insert')
        {
            $id = $this->im->insert(array(
				"COV_NAMA_PARENT" => $this->input->post('cov_nama_parent'),
				"COV_IC" => $this->input->post('cov_ic'),
				"COV_MATRIK" => $this->input->post('cov_matrik'),
				"COV_NAMA_PELAJAR" => $this->input->post('cov_matrik'),
				"COV_PROGRAM" => $this->input->post('cov_program'),
				"COV_HP" => $this->input->post('cov_hp'),
			));

            if (is_numeric($id))
            {
                //Save Log
                $menu_name = $this->input->post('nm_supplier');
                if($id)
                {
                    $keterangan = "SUKSES, tambah data supplier ".$menu_name.", dengan ID : ".$id;
                    $status     = 1;
                }
                else
                {
                    $keterangan = "GAGAL, tambah data supplier ".$menu_name.", dengan ID : ".$id;
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
			
            $return = $this->im->update($id, array(
				"NAMA_MENU" => $this->input->post('nama_menu'),
				"URL" => $this->input->post('url'),
				"SORTING" => $this->input->post('sorting'),
			));

            //Save Log
            $menu_name = $this->input->post('nm_supplier');
            if($return)
            {
                $keterangan = "SUKSES, ubah data supplier ".$menu_name.", dengan ID : ".$id;
                $status     = 1;
            }
            else
            {
                $keterangan = "GAGAL, ubah data supplier ".$menu_name.", dengan ID : ".$id;
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
		$this->template->set_message(lang("menu_create_success"), 'success');
		$this->template->render('parents/confirm');
	}
	
	function find_pelajar()
	{
		$this->um->find_by();
	}
}
?>