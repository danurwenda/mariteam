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

    public function get_table() {
        $this->db->select('persons.person_id,person_name,user_id,name,last_access');
        $this->db
                ->join('users', 'persons.person_id=users.person_id', 'left')
                ->join('roles', 'roles.role_id=users.role_id', 'left');
        return $this->db->get('persons')->result();
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
        $this->db->select('persons.*');
        $this->db->select('user_id,role_id,status');
        $this->db->where('persons.person_id', $person_id)->limit(1);
        $this->db->join('users', 'persons.person_id=users.person_id', 'left');
        $q = $this->db->get("persons");
        if ($q->num_rows() > 0) {
            $p = $q->row();
            //add groups
            $p->groups = [];
            $groups = $this->db->get_where('person_group', ['person_id' => $p->person_id]);
            foreach ($groups->result() as $pt) {
                $p->groups[] = $pt->group_id;
            }
            return $p;
        } else {
            return false;
        }
    }

    public function get_user_by_person($person_id) {
        $this->db->where('person_id', $person_id)->limit(1);
        $q = $this->db->get($this->table);
        return ($q->num_rows() > 0) ? $q->row() : false;
    }

    public function update(
    $person_id, $name, $instansi, $jabatan, $phone, $is_user, $email, $password, $status, $role, $groups
    ) {
        $this->db->where('person_id', $person_id);
        $ret = $this->db->update('persons', [
            'person_name' => $name,
            'instansi' => $instansi,
            'jabatan' => $jabatan,
            'email' => $email,
            'phone' => $phone
        ]);
        $this->set_groups($person_id, $groups);
        if (isset($is_user) ){
            // password may or may not be set
            if (!empty($password)) {
                $this->db->set('hash', password_hash($password, PASSWORD_DEFAULT, ['cost' => 10]));
            }
            $this->db->set('status', $status);
            $this->db->set('role_id', $role);
            if ($is_user == 'on') {
                $this->db->set('person_id', $person_id);
                //create new user
                $ret = $ret && $this->db->insert('users');
                $is_user = $this->db->insert_id();
            } else {
                $this->db->where('user_id', $is_user);
                $ret = $ret && $this->db->update('users');
            }
        }
        return $ret;
    }

    public function get_groups_dt() {
        $this->datatables
                ->select('group_name,is_public,users,projects,groups.group_id')
                ->from('groups')
                ->join('(select group_id, count(person_id) as users from person_group group by group_id) C1', 'C1.group_id = groups.group_id', 'left')
                ->join('(select group_id, count(project_id) as projects from project_group group by group_id) C2', 'C2.group_id = groups.group_id', 'left')
        ;
        return $this->datatables->generate();
    }

    public function set_groups($pid, $groups) {
        //clear previous set of group
        $this->db->where('person_id', $pid);
        $this->db->delete('person_group');
        //insert new
        foreach ($groups as $tid) {
            $this->db->insert('person_group', [
                'group_id' => $tid,
                'person_id' => $pid
            ]);
        }
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

    public function create_user($fullname, $institusi, $jabatan, $phone, $email, $plainpassword, $status, $role_id, $groups = null) {
        //create person first
        $person = $this->db->insert('persons', [
            'person_name' => $fullname,
            'instansi' => $institusi,
            'jabatan' => $jabatan,
            'email' => $email,
            'phone' => $phone
        ]);
        if (!$person) {
            return false;
        }
        $person_id = $this->db->insert_id();
        // associate group
        $this->set_groups($person_id, $groups);
        //then create user
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

    public function create_person($person_name, $institusi, $jabatan, $phone=null, $groups=[]) {
        $person = $this->db->insert('persons', [
            'person_name' => $person_name,
            'instansi' => $institusi,
            'jabatan' => $jabatan,
            'phone' => $phone
        ]);
        if (!$person) {
            return false;
        } else {
            $pid = $this->db->insert_id();
            $this->set_groups($pid, $groups);
            return $pid;
        }
    }

    public function delete_person($pid) {
        return $this->db->delete('persons', ['person_id' => $pid]);
    }

    public function update_last_access($uid) {
        $this->db->where('user_id', $uid);
        $this->db->set('last_access', date("Y-m-d H:i:s"));
        $this->db->update($this->table);
    }

}
