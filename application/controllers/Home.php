<?php

class Home extends CI_Controller {

    public function index() {
        $this->load->view('public/common/header');
        $this->load->view('public/common/menu');
        $this->load->view('public/home');
        $this->load->view('public/common/footer');
    }

    public function forgot_password() {
        $data['page_title'] = 'forgot password';
        $data['url'] = base_url('forgot_password');
        $this->load->view('email_template/user/forgot_password', $data);
    }

}
