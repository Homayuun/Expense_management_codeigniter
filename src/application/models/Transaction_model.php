<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transaction_model extends CI_Model
{
    private $table = 'transactions';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_all($profile_id = null)
    {
        $this->db->select('t.*, c.name as category_name, c.type as category_type');
        $this->db->from($this->table.' t');
        $this->db->join('categories c', 't.category_id = c.id', 'left');

        if ($profile_id) {
            $this->db->where('t.profile_id', $profile_id);
        }

        return $this->db->get()->result_array();
    }

    public function get_by_id($id)
    {
        $this->db->select('t.*, c.name as category_name, c.type as category_type');
        $this->db->from($this->table.' t');
        $this->db->join('categories c', 't.category_id = c.id', 'left');
        $this->db->where('t.id', $id);
        return $this->db->get()->row_array();
    }

    public function insert($data)
    {
        $profile = $this->db->get_where('profiles', ['id' => $data['profile_id']])->row_array();
        if (!$profile) return false;

        $category = $this->db->get_where('categories', [
            'id' => $data['category_id'],
            'profile_id' => $data['profile_id']
        ])->row_array();
        if (!$category) return false;

        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        if (isset($data['profile_id']) && isset($data['category_id'])) {
            $category = $this->db->get_where('categories', [
                'id' => $data['category_id'],
                'profile_id' => $data['profile_id']
            ])->row_array();
            if (!$category) return false;
        }

        $this->db->where('id', $id)->update($this->table, $data);
        return $this->db->affected_rows();
    }

    public function delete($id)
    {
        $this->db->where('id', $id)->delete($this->table);
        return $this->db->affected_rows();
    }

    public function filter($profile_id, $start_date = null, $end_date = null, $title = null, $type = null)
    {
        $this->db->select('t.*, c.name as category_name, c.type as category_type');
        $this->db->from($this->table.' t');
        $this->db->join('categories c', 't.category_id = c.id', 'left');
        $this->db->where('t.profile_id', $profile_id);

        if ($start_date && $end_date) {
            $this->db->where('t.transaction_date >=', $start_date);
            $this->db->where('t.transaction_date <=', $end_date);
        }

        if ($title) {
            $this->db->like('t.title', $title);
        }

        if ($type) {
            $this->db->where('t.type', $type);
        }

        return $this->db->get()->result_array();
    }

    public function sum_by_type($profile_id, $type)
    {
        $this->db->select_sum('amount');
        $this->db->where('profile_id', $profile_id);
        $this->db->where('type', $type);
        $result = $this->db->get($this->table)->row_array();
        return $result['amount'] ?? 0;
    }
}