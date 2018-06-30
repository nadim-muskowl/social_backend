<?php

class Admins_model extends CI_Model {

    private $table = 'admins';
    private $table_view = 'admins';
    private $column_order = array(null, 'name', 'email', 'contact', 'modified_date', null);
    private $column_search = array('name', 'email', 'contact', 'modified_date');
    private $order = array('modified_date' => 'desc');

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    private function _getTablesQuery($array = array()) {
        $this->db->from($this->table_view);

        if (isset($array['name']) && !empty($array['name'])):
            $this->db->where('name', $array['name']);
        endif;

        if (isset($array['email']) && !empty($array['email'])):
            $this->db->where('email', $array['email']);
        endif;

        if (isset($array['contact']) && !empty($array['contact'])):
            $this->db->where('contact', $array['contact']);
        endif;

        $status = 1;
        if ($this->input->post('status') && $this->input->post('status') == 'false'):
            $status = 0;
        endif;

        $this->db->where('status', $status);

        $i = 0;
        foreach ($this->column_search as $item) :
            if (isset($_POST['length'])) :
                if (isset($_POST['search']['value'])) :
                    if ($i === 0) :
                        $this->db->group_start();
                        $this->db->like($item, $_POST['search']['value']);
                    else :
                        $this->db->or_like($item, $_POST['search']['value']);
                    endif;
                    if (count($this->column_search) - 1 == $i):
                        $this->db->group_end();
                    endif;
                endif;
            endif;
            $i++;
        endforeach;

        if (isset($_POST['order'])) :
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        elseif (isset($this->order)) :
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        endif;
    }

    public function getTables($array = array()) {
        $this->_getTablesQuery($array);
        if (isset($_POST['length'])) :
            if ($_POST['length'] != -1):
                $this->db->limit($_POST['length'], $_POST['start']);
            endif;
        endif;
        $query = $this->db->get();
        return $query->result_array();
    }

    public function countFiltered($array = array()) {
        $this->_getTablesQuery($array);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function countAll() {
        $this->db->from($this->table_view);
        return $this->db->count_all_results();
    }

    public function getById($id) {
        $this->db->from($this->table_view);
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }
    
    public function getByEmail($email) {
        $this->db->from($this->table_view);
        $this->db->where('email', $email);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function deleteById($id) {
        $this->db->trans_start();
        $this->db->where('id', $id);
        $this->db->delete($this->table);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return FALSE;
        } else {
            $this->db->trans_commit();
            return TRUE;
        }
    }

    public function postData() {
        $this->db->trans_start();

        if ($this->input->post('name')):
            $this->db->set('name', $this->input->post('name'));
        endif;

        if ($this->input->post('email')):
            $this->db->set('email', $this->input->post('email'));
        endif;

        if ($this->input->post('contact')):
            $this->db->set('contact', $this->input->post('contact'));
        endif;

        if ($this->input->post('password')):
            $this->db->set('password', $this->input->post('password'));
        endif;

        if ($this->input->post('id')):
            $id = $this->input->post('id');
            $this->db->where('id', $id);
            $this->db->update($this->table);
        else:
            $this->db->insert($this->table);
            $id = $this->db->insert_id();
        endif;

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return FALSE;
        } else {
            $this->db->trans_commit();
            return $this->getById($id);
        }
    }

    public function login() {
        $this->db->from($this->table_view);
        $this->db->group_start();
        $this->db->where('email', $this->input->post('username'));
        $this->db->or_where('contact', $this->input->post('username'));
        $this->db->group_end();
        $this->db->where('password', $this->input->post('password'));
        $query = $this->db->get();
//        print_r($this->db->last_query());
//        exit;
        return $query->row_array();
    }
    
    
     public function updatePassword() {
        $this->db->trans_start();

        $this->db->set('password', $this->input->post('password'));

        $id = $this->input->post('id');
        $this->db->where('id', $id);
        $this->db->update($this->table);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return FALSE;
        } else {
            $this->db->trans_commit();
            return TRUE;
        }
    }

    public function forgotPassword() {
        $this->db->trans_start();
        
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return FALSE;
        } else {
            $this->db->trans_commit();
            return TRUE;
        }
    }

}
