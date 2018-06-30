<?php

require APPPATH . '/libraries/REST_Controller.php';

class Admins extends Restserver\Libraries\REST_Controller {

    private $data = array();

    public function __construct($config = 'rest') {
        parent::__construct($config);
        $this->load->model('api/admins_model');
    }

    public function index_post() {
        $this->data = array();

        $list = $this->admins_model->getTables();

        $result = array();
        foreach ($list as $object) :
            $result[] = array(
                'id' => $object['id'],
                'name' => $object['name'],
                'email' => $object['email'],
                'contact' => $object['contact'],
                'status' => $object['status'] ? 'Enable' : 'Disable',
                'created_date' => date('Y-m-d s:i A', strtotime($object['created_date'])),
                'modified_date' => date('Y-m-d s:i A', strtotime($object['modified_date'])),
            );
        endforeach;

        $this->data['recordsTotal'] = $this->admins_model->countAll();
        $this->data['recordsFiltered'] = $this->admins_model->countFiltered();
        $this->data['data'] = $result;

        $this->response($this->data);
    }

    public function list_post() {
        $this->data = array();

        $list = $this->admins_model->getTables();

        if ($this->input->post('draw')):
            $draw = $this->input->post('draw');
        else:
            $draw = 10;
        endif;

        $result = array();
        foreach ($list as $object) :
            $action = '';
            $action .= '<a class="btn btn-sm btn-primary" href="javascript:void(0)" data-toggle="tooltip" title="Edit" onclick="edit_record(' . "'" . $object['id'] . "'" . ')"><i class="glyphicon glyphicon-pencil"></i></a>';
            $action .= ' <a class="btn btn-sm btn-danger" href="javascript:void(0)" data-toggle="tooltip" title="Delete" onclick="delete_record(' . "'" . $object['id'] . "'" . ')"><i class="glyphicon glyphicon-trash"></i></a>';

            $checkbox = '<input type="checkbox" class="data-check" value="' . $object['id'] . '">';

            $result[] = array(
                $checkbox,
                $object['name'],
                $object['email'],
                $object['contact'],
                $object['status'] ? 'Enable' : 'Disable',
                date('Y-m-d s:i A', strtotime($object['modified_date'])),
                $action
            );
        endforeach;

        $this->data['draw'] = $draw;
        $this->data['recordsTotal'] = $this->admins_model->countAll();
        $this->data['recordsFiltered'] = $this->admins_model->countFiltered();
        $this->data['data'] = $result;

        $this->response($this->data);
    }

    public function detail_get($id) {
        $this->data = array();
        $result = $this->admins_model->getById($id);
        if ($result):
            $this->data['status'] = TRUE;
            $this->data['message'] = 'loading..';
            $this->data['result'] = $result;
        else:
            $this->data['status'] = FALSE;
            $this->data['message'] = 'no result found!';
            $this->data['result'] = array();
        endif;

        $this->response($this->data);
    }

    public function login_post() {
        $this->data = array();
        $this->loginValidation();
        $result = $this->admins_lib->login();
        $this->data['result'] = array();
        $this->data['redirect'] = base_url('admin');
        if ($result):
            $this->data['status'] = TRUE;
            $this->data['message'] = 'login success!';
            $this->data['result'] = $result;
        else:
            $this->data['status'] = FALSE;
            $this->data['message'] = 'login failed!';
        endif;
        $this->response($this->data);
    }

    public function loginValidation() {
        $this->data = array();
        $this->load->library('form_validation');

        $this->form_validation->set_rules('username', 'Username', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() == FALSE):
            $error['username'] = form_error('username', '', '');
            $error['password'] = form_error('password', '', '');

            $this->data['status'] = FALSE;
            $this->data['message'] = 'validation error!';
            $this->data['result'] = $error;
            echo json_encode($this->data);
            exit;
        endif;
    }

    public function add_post() {
        $this->data = array();
        $this->addValidation();
        $result = $this->admins_model->postData();
        if ($result):
            $this->data['status'] = TRUE;
            $this->data['message'] = 'admin add success!';
            $this->data['result'] = $result;
        else:
            $this->data['status'] = FALSE;
            $this->data['message'] = 'admin adding failed!';
            $this->data['result'] = array();
        endif;
        $this->response($this->data);
    }

    public function addValidation() {
        $this->data = array();
        $this->load->library('form_validation');

        $this->form_validation->set_rules('name', 'Name', 'required|min_length[3]|max_length[20]');
        $this->form_validation->set_rules('contact', 'Contact', 'required|min_length[5]|max_length[10]');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[admins.email]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[5]|max_length[10]');
        $this->form_validation->set_rules('passconf', 'Password Confirmation', 'required|matches[password]');

        if ($this->form_validation->run() == FALSE):
            $error['name'] = form_error('name', '', '');
            $error['contact'] = form_error('contact', '', '');
            $error['email'] = form_error('email', '', '');
            $error['password'] = form_error('password', '', '');
            $error['passconf'] = form_error('passconf', '', '');

            $this->data['status'] = FALSE;
            $this->data['message'] = 'validation error!';
            $this->data['result'] = $error;
            echo json_encode($this->data);
            exit;
        endif;
    }

    public function updatePassword_post() {
        $this->data = array();
        $this->updatePasswordValidation();
        $result = $this->admins_model->updatePassword();
        if ($result):
            $this->data['status'] = TRUE;
            $this->data['message'] = 'password update success!';
            $this->data['result'] = $result;
        else:
            $this->data['status'] = FALSE;
            $this->data['message'] = 'password update failed!';
            $this->data['result'] = array();
        endif;
        $this->response($this->data);
    }

    public function updatePasswordValidation() {
        $this->data = array();
        $this->load->library('form_validation');

        $this->form_validation->set_rules('id', 'User Id', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[5]|max_length[10]');
        $this->form_validation->set_rules('passconf', 'Password Confirmation', 'required|matches[password]');

        if ($this->form_validation->run() == FALSE):
            $error['id'] = form_error('id', '', '');
            $error['password'] = form_error('password', '', '');
            $error['passconf'] = form_error('passconf', '', '');

            $this->data['status'] = FALSE;
            $this->data['message'] = 'validation error!';
            $this->data['result'] = $error;
            echo json_encode($this->data);
            exit;
        endif;
    }

    public function forgot_post() {
        $this->data = array();
        $this->forgotValidation();
        $result = $this->admins_model->forgotPassword();
        if ($result):
            $this->data['status'] = TRUE;
            $this->data['message'] = 'email send success!';
            $this->data['result'] = $result;
        else:
            $this->data['status'] = FALSE;
            $this->data['message'] = 'email send failed!';
            $this->data['result'] = array();
        endif;
        $this->response($this->data);
    }

    public function forgotValidation() {
        $this->data = array();
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|callback_validate_email');
        if ($this->form_validation->run() == FALSE):
            $inputerror = array();
            $error_string = array();

            if (form_error('email')):
                $inputerror[] = 'email';
                $error_string[] = form_error('email');
            endif;

            $this->data['status'] = FALSE;
            $this->data['message'] = 'validation error!';
            $this->data['inputerror'] = $inputerror;
            $this->data['error_string'] = $error_string;
            echo json_encode($this->data);
            exit;
        endif;
    }

    public function validate_email($field_value) {
        if ($this->admins_model->getByEmail($field_value)) {
            return TRUE;
        } else {
            $this->form_validation->set_message('validate_email', 'please enter valid {field}!');
            return FALSE;
        }
    }

    public function logout_get() {
        $this->data = array();
        $result = $this->admins_lib->logout();
        $this->data['result'] = array();
        $this->data['redirect'] = base_url('admin/login');
        if ($result):
            $this->data['status'] = TRUE;
            $this->data['message'] = 'logout success!';
        else:
            $this->data['status'] = FALSE;
            $this->data['message'] = 'logout failed!';
        endif;
        $this->response($this->data);
    }

}
