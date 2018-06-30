<?php

class Admins_lib {

    private $ci;
    private $sessionData = array();
    public $id = NULL;
    public $name;
    public $contact;
    public $email;
    public $image;
    public $created_date;
    public $modified_date;

    public function __construct() {
        $this->ci = & get_instance();
        $this->ci->load->model('api/admins_model');
        $this->ci->load->library('email');
        $this->ci->load->library('session');

        $this->sessionData = $this->ci->session->userdata('admins_log');

        if ($this->sessionData) {
            $this->id = $this->sessionData['id'];
            $data = $this->ci->admins_model->getById($this->id);
            if ($data) {
                $this->id = $data['id'];
                $this->name = $data['name'];
                $this->contact = $data['contact'];
                $this->email = $data['email'];
                $this->image = $data['image'];
                $this->created_date = $data['created_date'];
                $this->modified_date = $data['modified_date'];
            }
        }
    }

    public function login() {
        $result = $this->ci->admins_model->login();
        if ($result):
            $this->ci->session->set_userdata('admins_log', $result);
            return $result;
        else:
            return FALSE;
        endif;
    }

    public function isLogged() {
        if (!$this->id) :
            return FALSE;
        else:
            return TRUE;
        endif;
    }

    public function logout() {
        if ($this->ci->session->has_userdata('admins_log')):
            $this->ci->session->unset_userdata('admins_log');
            return TRUE;
        else:
            return FALSE;
        endif;
    }

}
