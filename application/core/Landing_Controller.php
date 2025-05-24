<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Landing_Controller extends Base_Controller
{

    //--------------------------------------------------------------------

    /**
     * Class constructor
     *
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->library(array('form_validation','template'));
 
        $this->autoload['helpers'][]   = 'form';
        $this->autoload['helpers'][]   = 'apps';
        $this->autoload['libraries'][] = 'Template';
        $this->autoload['libraries'][] = 'supplier/auth';
        // $this->db = $this->load->database("default", true);

        $this->load->helper("app");
		
        $this->template->set_theme('psh');
        $this->template->set_layout('index');
        //Overwrite if the request is ajax
        if($this->input->is_ajax_request())
        {
            $this->template->set_layout('ajax');
        }
    }//end __construct()

    //--------------------------------------------------------------------

}
/*End of Front Controller*/