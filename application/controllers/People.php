<?php

defined('BASEPATH') OR exit('No direct script access allowed');
/* This Controller can only be accessed by Administrator
 */

class People extends Module_Controller {

    function __construct() {
        parent::__construct(2);
        $this->load->library('Datatables');
    }

    function check_email() {
        $person_id = $this->input->post('person_id');
        $email = $this->input->post('email');
        if ($person_id) {
            $this->db->where('person_id !=', $person_id);
        }
        $valid = $this->db->get_where('persons', ['email' => $email])->num_rows() == 0;
        echo json_encode($valid);
    }

    /**
     * Dashboard home.
     * Shows statistical information (e.g chart) about projects under logged user's privilege.
     * May display different information for different role.
     */
    function index() {
        $data['active_menu'] = 2;
        $data['pagetitle'] = 'People';
        $data['ps'] = $this->users_model->get_table();
        $this->template->display('people_table', $data);
    }

    function groups_dt() {
        if ($this->input->is_ajax_request()) {
            echo $this->users_model->get_groups_dt();
        }
    }

    /**
     * TODO : check permission
     */
    function delete_group() {
        $group_id = $this->input->post('group_id');
        echo json_encode(['success' => $this->db->delete('groups', ['group_id' => $group_id])]);
    }

    function delete() {
        $success = $this->users_model->delete_person($this->input->post('person_id'));
        echo json_encode(['success' => $success]);
    }

    /**
     * Full view untuk update user
     * @param type $person_id
     */
    function edit($person_id) {
        $user = $this->users_model->get_person($person_id);
        if (!$user) {
            //user not found
            //redirect to table
            redirect('people');
        } else {
            $data['active_menu'] = 2;
            $data['pagetitle'] = 'Edit User';
            $data['person'] = $user;
            $data['groups'] = $this->db->get('groups')->result();
            $data['roles'] = $this->db->get('roles')->result();
            $this->template->display('people_form', $data);
        }
    }

    function create() {
        // name
        $name = $this->input->post('person_name');
        // instansi
        $instansi = $this->input->post('instansi');
        // jabatan
        $jabatan = $this->input->post('jabatan');
        // phone
        $phone = $this->input->post('phone');
        // groups
        $groups = $this->input->post('groups');
        if ($groups === null) {
            $groups = [];
        }
        if ($this->input->post('user')) {
            $succ = $this->users_model->create_user(
                    $name, $instansi, $jabatan, $phone,
                    // is a user?
                    $this->input->post('email'),
                    // password
                    $this->input->post('password'), // <-- may be empty
                    $this->input->post('status'), $this->input->post('role'), $groups
            );
        } else {
            $succ = $this->users_model->create_person(
                    $name, $instansi, $jabatan, $phone, $groups
            );
        }
        echo json_encode(['success' => $succ]);
    }

    function update() {
        $user = $this->input->post('user');
        $password = $this->input->post('password');
        $groups = $this->input->post('groups');
        if ($groups === null) {
            $groups = [];
        }
        if (!isset($user)) {
            // updating existing user
            // $user_id is set to an integer
            // or simply updating person
            // $user_id is not set
            $user = $this->input->post('user_id');
        } else {
            // create new user
            // make sure the password is set
            if (empty($password)) {
                $password = 'mariteam';
            }
        }
        $succ = $this->users_model->update(
                // person id
                $this->input->post('person_id'),
                // name
                $this->input->post('person_name'),
                // instansi
                $this->input->post('instansi'),
                // jabatan
                $this->input->post('jabatan'),
                // phone
                $this->input->post('phone'),
                // is a user?
                $user, $this->input->post('email'),
                // password
                $password, // <-- may be empty
                $this->input->post('status'), $this->input->post('role'), $groups
        );
        echo json_encode(['success' => $succ]);
    }

    function create_user_simple() {
        $person_name = $this->input->post('person_name');
        $institusi = $this->input->post('institusi');
        $jabatan = $this->input->post('jabatan');
        $is_user = $this->input->post('is_user');
        $change = false;
        if ($is_user) {
            //create user
            $change = $this->users_model->create_user(
                    //fullname
                    $person_name,
                    //instansi,
                    $institusi,
                    //jabatan
                    $jabatan,
                    //phone,
                    null,
                    //email
                    $this->input->post('email'),
                    //plain password
                    'mariteam',
                    //status, default : active
                    1,
                    //role_id, default : editor
                    2
            );
        } else {
            //create person only
            $change = $this->users_model->create_person(
                    //fullname
                    $person_name,
                    //instansi,
                    $institusi,
                    //jabatan
                    $jabatan,
                    //phone,
                    null,
                    //groups
                    []
            );
        }
        echo json_encode([
            'success' => $change,
            'person_name' => $person_name
        ]);
    }

    function create_user() {
        $data['active_menu'] = 2;
        $data['pagetitle'] = 'Add User';
        $data['roles'] = $this->db->get('roles')->result();
        $data['groups'] = $this->db->get('groups')->result();
        $this->template->display('people_form', $data);
    }

    function create_group() {
        $inserted = $this->db->insert('groups', [
            'group_name' => $this->input->post('group_name'),
            'is_public' => null !== $this->input->post('is_public')
        ]);
        if ($this->input->is_ajax_request()) {
            echo json_encode(['success' => $inserted]);
        } else {
            //back to table view
            redirect('project/create');
        }
    }

}
