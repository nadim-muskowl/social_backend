<?php

class Login extends CI_Controller {

    public function index() {
        $this->load->view('admin/common/header');
        $this->load->view('admin/login');
        $this->load->view('admin/common/footer');
    }

}
