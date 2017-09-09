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
        $this->db->select('users.user_id, person_name, users.status, roles.name rname, users.created_at, users.last_access');
        $this->db->join('roles', 'roles.role_id=users.role_id')
                ->join('persons', 'persons.person_id=users.person_id');
        return $this->db->get($this->table)->result();
    }

    public function get_login_info($u) {
        $this->db->join('persons', 'persons.person_id=users.person_id');
        $this->db->where('email', $u)->limit(1);
        $q = $this->db->get($this->table);
        return ($q->num_rows() > 0) ? $q->row() : false;
    }

    public function get_user($id) {
        $this->db->where($this->primary_key, $id)->limit(1);
        $this->db->join('persons', 'persons.person_id=users.person_id');
        $q = $this->db->get($this->table);
        return ($q->num_rows() > 0) ? $q->row() : false;
    }

    public function get_person($person_id) {
        $this->db->where('person_id', $person_id)->limit(1);
        $q = $this->db->get("persons");
        return ($q->num_rows() > 0) ? $q->row() : false;
    }

    public function get_user_by_person($person_id) {
        $this->db->where('person_id', $person_id)->limit(1);
        $q = $this->db->get($this->table);
        return ($q->num_rows() > 0) ? $q->row() : false;
    }

    public function update($id, $username, $password = '', $name = '', $status = null, $role = null) {
        if ((!empty($password)) || (isset($status)) || (isset($role))) {
            $this->db->where('user_id', $id);
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
        ////////////////////////////////////////////////////////
        $this->db->where('person_id', $this->get_user($id)->person_id);
        //update username
        $this->db->set('email', $username);
        if (!empty($name)) {
            $this->db->set('person_name', $name);
        }
        //do update
        $this->db->update('persons');
    }

    public function auth($username, $password) {
        $this->db->where('email', $username);
        $q = $this->db->get('persons');
        //find person first
        if ($q->num_rows() == 0) {
            return false;
        }
        $person = $q->row();
        $this->db->where('person_id', $person->person_id);
        $q2 = $this->db->get($this->table);
        //find user
        if ($q2->num_rows() == 0) {
            return false;
        }
        $user = $q2->row();
        return password_verify($password, $user->hash);
    }

    public function create_user($email, $plainpassword, $fullname, $institusi, $jabatan, $status, $role_id) {
        //create person first
        $person = $this->db->insert('persons', [
            'person_name' => $fullname,
            'instansi' => $institusi,
            'jabatan' => $jabatan,
            'email' => $email
        ]);
        if (!$person) {
            return false;
        }
        //then create user
        $person_id = $this->db->insert_id();
        $user = $this->db->insert($this->table, [
            'hash' => password_hash($plainpassword, PASSWORD_DEFAULT, ['cost' => 10]),
            'status' => $status,
            'role_id' => $role_id,
            'person_id' => $person_id
        ]);
        if (!$user) {
            return false;
        }
        return $person_id;
    }

    public function create_person($person_name, $institusi, $jabatan) {
        $person = $this->db->insert('persons', [
            'person_name' => $person_name,
            'instansi' => $institusi,
            'jabatan' => $jabatan
        ]);
        if (!$person) {
            return false;
        } else {
            return $this->db->insert_id();
        }
    }

    public function delete($uid) {
        $this->db->delete($this->table, ['user_id' => $uid]);
    }

    public function update_last_access($uid) {
        $this->db->where('user_id', $uid);
        $this->db->set('last_access', date("Y-m-d H:i:s"));
        $this->db->update($this->table);
    }

}
