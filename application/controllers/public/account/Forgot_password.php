<?php

class Forgot_password extends CI_Controller {

    public function index() {
        $this->load->view('public/common/header');        
        $this->load->view('public/account/forgot_password');
        $this->load->view('public/common/footer');
    }

}
