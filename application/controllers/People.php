<?php

defined('BASEPATH') OR exit('No direct script access allowed');
/* This Controller can only be accessed by Administrator
 */

class People extends Module_Controller {

    function __construct() {
        parent::__construct(2);
    }

    /**
     * Dashboard home.
     * Shows statistical information (e.g chart) about projects under logged user's privilege.
     * May display different information for different role.
     */
    function index() {
        $data['pagetitle'] = 'People';
        $data['ps'] = $this->users_model->get_all_joined();
        $this->template->display('people_table', $data);
    }

    function delete() {
        $this->users_model->delete($this->input->post('user_id'));
    }

    /**
     * Full view untuk update user
     * @param type $user_id
     */
    function edit($user_id) {
        $user = $this->users_model->get_user($user_id);
        if (!$user) {
            //user not found
            //redirect to table
            redirect('people');
        } else {
            $data['pagetitle'] = 'Edit User';
            $data['user'] = $user;
            $data['roles'] = $this->db->get('roles')->result();
            $this->template->display('people_form', $data);
        }
    }

    function update() {
        $data['pagetitle'] = 'Edit User';
        $user_id = $this->input->post('user_id');
        $data['user'] = $this->users_model->get_user($user_id);
        $data['roles'] = $this->db->get('roles')->result();
        $this->load->library('form_validation');
        $this->form_validation->set_rules('email', 'Username', ['trim', 'required', 'strip_tags',
            ['username_callable',
                function($email) {
                    if ($this->users_model->get_user($this->input->post('user_id'))->email === $email || !$this->users_model->get_login_info($email)) {
                        return true;
                    } else {
                        $this->form_validation->set_message('username_callable', 'This %s is already taken');
                        return false;
                    }
                }
        ]]);
        $this->form_validation->set_rules('password', 'Password', 'matches[passconf]');
        $this->form_validation->set_rules('passconf', 'Password Confirmation', 'matches[password]');
        $this->form_validation->set_rules('status', 'Status', 'required');
        $this->form_validation->set_rules('role', 'Role', 'required');
        $this->form_validation->set_rules('name', 'Display Name', ['trim', 'required', 'strip_tags']);
        if ($this->form_validation->run() == true) {
            $data['updated'] = true;
            $this->users_model->update(
                    $user_id, $this->input->post('email'), $this->input->post('password'), $this->input->post('name'), $this->input->post('status'), $this->input->post('role')
            );
        }
        $this->template->display('people_form', $data);
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
                    //email
                    $this->input->post('email'),
                    //plain password
                    'mariteam',
                    //fullname
                    $person_name,
                    //instansi,
                    $institusi,
                    //jabatan
                    $jabatan,
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
                    $jabatan
            );
        }
        echo json_encode([
            'success' => $change,
            'person_name' => $person_name
        ]);
    }

    function create_user() {
        $data['pagetitle'] = 'Add User';
        $data['roles'] = $this->db->get('roles')->result();
        $this->load->library('form_validation');
        $this->form_validation->set_rules('email', 'Username', ['trim', 'required', 'strip_tags', 'is_unique[persons.email]']);
        $this->form_validation->set_rules('name', 'Display Name', ['trim', 'required', 'strip_tags']);
        $this->form_validation->set_rules('password', 'Password', 'required');
        $this->form_validation->set_rules('passconf', 'Password Confirmation', 'required|matches[password]');
        $this->form_validation->set_rules('status', 'Status', 'required');
        $this->form_validation->set_rules('role', 'Role', 'required');
        if ($this->form_validation->run() == true) {
            $data['updated'] = true;
            $this->users_model->create(
                    $this->input->post('email'), $this->input->post('password'), $this->input->post('name'), $this->input->post('status'), $this->input->post('role')
            );
            //return to table view
            redirect('people');
        } else {
            $this->template->display('people_form', $data);
        }
    }

}
