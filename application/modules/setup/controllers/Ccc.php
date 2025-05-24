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

class Ccc extends Admin_Controller {
    
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
	
	function setup_jenis_tuntutan()
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

	function setup_admin()
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
	
	function setup_dekan()
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


}
?>

