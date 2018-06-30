<?php

require APPPATH . '/libraries/REST_Controller.php';

class Follow_requests extends Restserver\Libraries\REST_Controller {

    private $data = array();

    public function __construct($config = 'rest') {
        parent::__construct($config);
        $this->load->model('api/follow_requests_model');
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('', '');
    }

    public function index_post() {
        $this->data = array();
        $list = $this->follow_requests_model->getTables();

        $result = array();
        foreach ($list as $object) :
            $result[] = array(
                'id' => $object['id'],
                'user_id' => $object['user_id'],
                'user' => $object['user'],
                'user_image' => $object['user_image'],
                'follow_id' => $object['follow_id'],
                'follower' => $object['follower'],
                'follower_image' => $object['follower_image'],
                'request_status' => $object['request_status'],
                'status' => $object['status'] ? 'Enable' : 'Disable',
                'created_date' => date('Y-m-d s:i A', strtotime($object['created_date'])),
                'modified_date' => date('Y-m-d s:i A', strtotime($object['modified_date'])),
            );
        endforeach;

        $this->data['recordsTotal'] = $this->follow_requests_model->countAll();
        $this->data['recordsFiltered'] = $this->follow_requests_model->countFiltered();
        $this->data['data'] = $result;

        $this->response($this->data);
    }

    public function list_post() {
        $this->data = array();

        $list = $this->follow_requests_model->getTables();

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
                $object['user_id'],
                $object['follower_id'],
                $object['status'] ? 'Enable' : 'Disable',
                date('Y-m-d s:i A', strtotime($object['modified_date'])),
                $action
            );
        endforeach;

        $this->data['draw'] = $draw;
        $this->data['recordsTotal'] = $this->follow_requests_model->countAll();
        $this->data['recordsFiltered'] = $this->follow_requests_model->countFiltered();
        $this->data['data'] = $result;

        $this->response($this->data);
    }

    public function send_post() {
        $this->data = array();
        $this->addValidation();
        $result = $this->follow_requests_model->postData();
        if ($result):
            $this->data['status'] = TRUE;
            $this->data['message'] = $this->lang->line('success_update');
            $this->data['result'] = $result;
        else:
            $this->data['status'] = FALSE;
            $this->data['message'] = $this->lang->line('error_update');
            $this->data['result'] = array();
        endif;
        $this->response($this->data);
    }

    public function addValidation() {
        $this->data = array();
        $this->form_validation->set_rules('user_id', 'user', 'required|callback_validate_request');
        $this->form_validation->set_rules('follow_id', 'follower', 'required');

        if ($this->form_validation->run() == FALSE):
            $inputerror = array();
            $error_string = array();

            if (form_error('user_id')):
                $inputerror[] = 'user_id';
                $error_string[] = form_error('user_id');
            endif;

            if (form_error('follow_id')):
                $inputerror[] = 'follow_id';
                $error_string[] = form_error('follow_id');
            endif;

            $this->data['status'] = FALSE;
            $this->data['message'] = 'validation error!';
            $this->data['inputerror'] = $inputerror;
            $this->data['error_string'] = $error_string;
            echo json_encode($this->data);
            exit;
        endif;
    }

    public function validate_request() {
        if ($this->follow_requests_model->checkRequest()) {
            $this->form_validation->set_message('validate_request', 'request already send!');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function request_status_post() {
        $this->data = array();
        $this->requestValidation();
        $result = $this->follow_requests_model->changeRequestStatus();
        if ($result):
            $this->data['status'] = TRUE;
            $this->data['message'] = $this->lang->line('success_update');
            $this->data['result'] = $result;
        else:
            $this->data['status'] = FALSE;
            $this->data['message'] = $this->lang->line('error_update');
            $this->data['result'] = array();
        endif;
        $this->response($this->data);
    }

    public function requestValidation() {
        $this->data = array();
        $this->form_validation->set_rules('id', 'id', 'required');
        $this->form_validation->set_rules('request_status', 'request status', 'required');

        if ($this->form_validation->run() == FALSE):
            $inputerror = array();
            $error_string = array();

            if (form_error('id')):
                $inputerror[] = 'id';
                $error_string[] = form_error('id');
            endif;

            if (form_error('request_status')):
                $inputerror[] = 'request_status';
                $error_string[] = form_error('request_status');
            endif;

            $this->data['status'] = FALSE;
            $this->data['message'] = 'validation error!';
            $this->data['inputerror'] = $inputerror;
            $this->data['error_string'] = $error_string;
            echo json_encode($this->data);
            exit;
        endif;
    }

}
