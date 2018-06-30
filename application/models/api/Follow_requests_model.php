<?php

class Follow_requests_model extends CI_Model {

    private $table = 'follow_requests';
    private $table_view = 'follow_requests_view';
    private $column_order = array(null, 'user_id', 'follower_id', 'created_date', 'modified_date', null);
    private $column_search = array('user_id', 'follower_id', 'created_date', 'modified_date');
    private $order = array('modified_date' => 'desc');

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    private function _getTablesQuery($array = array()) {
        $this->db->from($this->table_view);

        if ($this->input->post('user_id')):
            $this->db->where('user_id', $this->input->post('user_id'));
        endif;

        if (isset($array['user_id']) && !empty($array['user_id'])):
            $this->db->where('user_id', $array['user_id']);
        endif;

        if (isset($array['follow_id']) && !empty($array['follow_id'])):
            $this->db->where('follow_id', $array['follow_id']);
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

    public function getByUserId($id) {
        $this->db->from($this->table_view);
        $this->db->where('user_id', $id);
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

        if ($this->input->post('user_id')):
            $this->db->set('user_id', $this->input->post('user_id'));
        endif;

        if ($this->input->post('follow_id')):
            $this->db->set('follow_id', $this->input->post('follow_id'));
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

    public function checkRequest() {
        $this->db->from($this->table_view);
        $this->db->where('user_id', $this->input->post('user_id'));
        $this->db->where('follow_id', $this->input->post('follow_id'));
        $query = $this->db->get();
        return $query->row_array();
    }

    public function changeRequestStatus() {
        $this->db->trans_start();
        $id = $this->input->post('id');
        $this->db->set('request_status', $this->input->post('request_status'));
        $this->db->where('id', $id);
        $this->db->update($this->table);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return FALSE;
        } else {
            $this->db->trans_commit();
            return $this->getById($id);
        }
    }

}
