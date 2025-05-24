<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * @author Cokeshome
 * @copyright Copyright (c) 2016, Cokeshome
 * 
 * This is controller for Supplier
 */

class Supplier extends Admin_Controller {
    
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

        $this->lang->load('supplier/supplier');
        $this->load->model('supplier/supplier_model');

        $this->template->title(lang('supplier_title_manage'));
		$this->template->page_icon('fa fa-table');
    }

    public function index()
    {
        $this->auth->restrict($this->viewPermission);

        if (isset($_POST['delete']) && has_permission($this->deletePermission))
        {
            $checked = $this->input->post('checked');

            if (is_array($checked) && count($checked))
            {
                $result = FALSE;
                foreach ($checked as $pid)
                {
                    $supplier_name = $this->supplier_model->find($pid)->nm_supplier;

                    $result = $this->supplier_model->delete($pid);

                    if($result)
                    {
                        $keterangan = "SUKSES, hapus data supplier ".$supplier_name.", dengan ID : ".$pid;
                        $status     = 1;
                    }
                    else
                    {
                        $keterangan = "GAGAL, hapus data supplier ".$supplier_name.", dengan ID : ".$pid;
                        $status     = 0;
                    }

                    $nm_hak_akses   = $this->deletePermission; 
                    $kode_universal = $pid;
                    $jumlah         = 1;
                    $sql            = $this->db->last_query();

                    //simpan_aktifitas($nm_hak_akses, $kode_universal, $keterangan, $jumlah, $sql, $status);
                }

                if ($result)
                {
                    $this->template->set_message(count($checked) .' '. lang('supplier_deleted') .'.', 'success');
                }
                else
                {
                    $this->template->set_message(lang('supplier_del_failure') . $this->supplier_model->error, 'error');
                }
            }
            else
            {
                $this->template->set_message(lang('supplier_del_error') . $this->supplier_model->error, 'error');
            }
        }//end if

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

        $search2 = $this->db->escape_str($search);
        
        $where = "`deleted` = 0
                AND (`nm_supplier` LIKE '%$search2%' ESCAPE '!'
                OR `alamat` LIKE '%$search2%' ESCAPE '!'
                OR `telp1` LIKE '%$search2%' ESCAPE '!'
                OR `telp2` LIKE '%$search2%' ESCAPE '!'
                OR `email` LIKE '%$search2%' ESCAPE '!'
                OR `cp` LIKE '%$search2%' ESCAPE '!'
                OR `ket` LIKE '%$search2%' ESCAPE '!'
                OR IF(st_aktif = 1, 'Aktif', 'Tidak Aktif') LIKE '%$search2%' ESCAPE '!')";
        
        $total = $this->supplier_model
                        ->where($where)
                        ->count_all();

        $offset = $this->input->get('per_page');

        $limit = $this->config->item('list_limit');

        $this->pager['base_url']            = current_url().$filter;
        $this->pager['total_rows']          = $total;
        $this->pager['per_page']            = $limit;
        $this->pager['page_query_string']   = TRUE;

        $this->pagination->initialize($this->pager);

        $data = $this->supplier_model
                    ->where($where)
                    ->order_by('nm_supplier','ASC')
                    ->limit($limit, $offset)->find_all();

        $this->template->set('results', $data);
        $this->template->set('search', $search);
        $this->template->title(lang('supplier_title_manage'));
        $this->template->set("numb", $offset+1);
        $this->template->render('index'); 
    }

   	//Create New Supplier
   	public function create()
   	{

        $this->auth->restrict($this->addPermission);
                    
        if (isset($_POST['save']))
        {
            if ($this->save_supplier())
            {
              $this->template->set_message(lang("supplier_create_success"), 'success');
              redirect('supplier');
            }
        }

        $this->template->title(lang('supplier_title_new'));
		$this->template->render('supplier_form');
   	}

   	//Edit Supplier
   	public function edit()
   	{
   		
  		$this->auth->restrict($this->managePermission);
                
        $id = (int)$this->uri->segment(3);

        if (empty($id))
        {
            $this->template->set_message(lang("supplier_invalid_id"), 'error');
            redirect('supplier');
        }

        if (isset($_POST['save']))
        {
            if ($this->save_supplier('update', $id))
            {
                $this->template->set_message(lang("supplier_edit_success"), 'success');
            }

        }

        
        $data  = $this->supplier_model->find_by(array('id_supplier' => $id, 'deleted' => 0));

        if(!$data)
        {
            $this->template->set_message(lang("supplier_invalid_id"), 'error');
            redirect('supplier');
        }
        
        $this->template->set('data', $data);
        $this->template->title(lang("supplier_title_edit"));
        $this->template->render('supplier_form');
   	}

   	protected function save_supplier($type='insert', $id=0)
   	{

        $this->form_validation->set_rules('nm_supplier','lang:supplier_name','required|trim|max_length[45]');
        $this->form_validation->set_rules('alamat','lang:supplier_addr','required|trim|max_length[255]');
        $this->form_validation->set_rules('telp1','lang:supplier_telp1','required|trim|max_length[15]');
        $this->form_validation->set_rules('telp2','lang:supplier_telp2','trim|max_length[15]');
        $this->form_validation->set_rules('email','lang:supplier_email','trim|valid_email|max_length[45]');
        $this->form_validation->set_rules('cp','lang:supplier_cp','required|trim|max_length[45]');
        $this->form_validation->set_rules('ket','lang:supplier_desc','trim|max_length[255]');

        if ($this->form_validation->run() === FALSE)
        {
            $this->template->set_message(validation_errors(), 'error');
            return FALSE;
        }

        unset($_POST['submit'], $_POST['save']);

        if ($type == 'insert')
        {
            $id = $this->supplier_model->insert($_POST);

            if (is_numeric($id))
            {
                //Save Log
                $supplier_name = $this->input->post('nm_supplier');
                if($id)
                {
                    $keterangan = "SUKSES, tambah data supplier ".$supplier_name.", dengan ID : ".$id;
                    $status     = 1;
                }
                else
                {
                    $keterangan = "GAGAL, tambah data supplier ".$supplier_name.", dengan ID : ".$id;
                    $status     = 0;
                }

                $nm_hak_akses   = $this->addPermission; 
                $kode_universal = $id;
                $jumlah         = 1;
                $sql            = $this->db->last_query();

                //simpan_aktifitas($nm_hak_akses, $kode_universal, $keterangan, $jumlah, $sql, $status);

                $return = TRUE;
            }
            else
            {
                $return = FALSE;
            }
        }
        else if ($type == 'update')
        {
            $return = $this->supplier_model->update($id, $_POST);

            //Save Log
            $supplier_name = $this->input->post('nm_supplier');
            if($return)
            {
                $keterangan = "SUKSES, ubah data supplier ".$supplier_name.", dengan ID : ".$id;
                $status     = 1;
            }
            else
            {
                $keterangan = "GAGAL, ubah data supplier ".$supplier_name.", dengan ID : ".$id;
                $status     = 0;
            }

            $nm_hak_akses   = $this->addPermission; 
            $kode_universal = $id;
            $jumlah         = 1;
            $sql            = $this->db->last_query();

            //simpan_aktifitas($nm_hak_akses, $kode_universal, $keterangan, $jumlah, $sql, $status);
        }

        return $return;
   	}

}
?>