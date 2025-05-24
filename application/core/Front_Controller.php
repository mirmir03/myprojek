<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Front_Controller extends Base_Controller
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

        // $this->load->model('identitas_model');

        // $this->form_validation->set_error_delimiters('', '');

        // $idt = $this->identitas_model->find(1);

        // $this->template->set('idt', $idt);
        $this->template->set_theme('default');
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