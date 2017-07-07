<?php

defined('BASEPATH') OR
        exit('No direct script access allowed');
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Users_model
 *
 * @author Administrator
 */
class Users_model extends CI_Model {

    public $table = 'users';
    public $primary_key = 'user_id';

    public function __construct() {
        parent::__construct();
    }

    public function get_all_joined() {
        $this->db->select('users.user_id, users.name, users.status, roles.name rname, users.created_at, users.last_access');
        $this->db->join('roles', 'roles.role_id=users.role_id');
        return $this->db->get($this->table)->result();
    }

    public function get_login_info($u) {
        $this->db->where('email', $u)->limit(1);
        $q = $this->db->get($this->table);
        return ($q->num_rows() > 0) ? $q->row() : false;
    }

    public function get_user($id) {
        $this->db->where($this->primary_key, $id)->limit(1);
        $q = $this->db->get($this->table);
        return ($q->num_rows() > 0) ? $q->row() : false;
    }

    public function update_user($id, $username, $password = '', $status = null, $role = null) {
        $this->db->where('user_id', $id);
        //update username
        $this->db->set('email', $username);
        //and update password if $password is not empty
        if (!empty($password)) {
            $this->db->set('hash', password_hash($password, PASSWORD_DEFAULT, ['cost' => 10]));
        }
        if (isset($status)) {
            $this->db->set('status', $status);
        }
        if (isset($role)) {
            $this->db->set('role_id', $role);
        }
        //do update
        $this->db->update($this->table);
    }

    public function auth($username, $password) {

        $this->db->where('email', $username);
        $q = $this->db->get($this->table);
        if ($q->num_rows() == 0) {
            return false;
        }
        $user = $q->row();
        return password_verify($password, $user->hash);
    }

}
