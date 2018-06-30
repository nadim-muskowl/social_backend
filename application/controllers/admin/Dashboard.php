<?php

class Dashboard extends CI_Controller {

    private $dataOutput;

    public function __construct() {
        parent::__construct();
        if (!$this->admins_lib->isLogged()):
            redirect('admin/login');
        endif;
    }

    public function index() {
        $this->dataOutput['logout'] = base_url('admin/dashboard/logout');
        $this->dataOutput['name'] = $this->admins_lib->name;        
        
        

        $this->load->view('admin/common/header', $this->dataOutput);
        $this->load->view('admin/common/menu', $this->dataOutput);
        $this->load->view('admin/dashboard', $this->dataOutput);
        $this->load->view('admin/common/footer', $this->dataOutput);
    }

    public function logout() {
        $result = $this->admins_lib->logout();
        if ($result):
            redirect('admin/login');
        endif;
    }

}
