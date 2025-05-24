<?php 

class Booking extends Admin_Controller 
{
    public function mohon()
    {        
        $this->template->render();
    }

    public function listmohon()
    {        
        $this->template->render();
    }

    public function edit()
    {
        echo "call dari function edit";
        // http://localhost/sample/manage/booking/edit
    }
}