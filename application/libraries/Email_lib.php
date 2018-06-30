<?php

class Email_lib {

    private $ci;
    private $emails;
    private $email;
    private $email_data = array();
    private $subject;
    private $message;
    private $status;
    private $track_email = FALSE;
    private $email_status;

    public function __construct() {
        $this->ci = & get_instance();
        $this->ci->load->database();
//        $this->ci->load->library('setting_lib');
//        $config['protocol'] = $this->ci->setting_lib->config('config', 'mail_protocol');
//        $config['smtp_host'] = $this->ci->setting_lib->config('config', 'smtp_hostname');
//        $config['smtp_port'] = $this->ci->setting_lib->config('config', 'smtp_port');
//        $config['smtp_user'] = $this->ci->setting_lib->config('config', 'smtp_username');
//        $config['smtp_pass'] = $this->ci->setting_lib->config('config', 'smtp_password');
//        $config['smtp_timeout'] = $this->ci->setting_lib->config('config', 'smtp_timeout');
        $config['mailtype'] = 'html';
        $config['charset'] = 'utf-8';
        $config['wordwrap'] = TRUE;

        $this->ci->load->library('email', $config);
        $this->ci->email->clear();
        $this->ci->email->initialize($config);
        $this->ci->email->set_newline("\r\n");
        $this->ci->email->clear();
    }

    public function send($email, $email_data = array()) {
        $this->status = TRUE;
        $this->email_data = $email_data;
        $this->subject = $this->email_data['title'];
        $this->message = $this->email_data['message'];

//        print_r($this->message);
//        exit;

        $this->ci->email->from('nadim@muskowl.com', 'social app');
        $this->ci->email->reply_to('nadim@muskowl.com', 'social app');
        $this->ci->email->to($this->email);
        $this->ci->email->subject($this->subject);
        $this->ci->email->message($this->message);

        if ($this->ci->email->send()):
            $this->email_status = 'Sent';
        else:
            $this->email_status = 'Failed';
            $this->status = FALSE;
        endif;

        return $this->status;
    }

}
