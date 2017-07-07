<?php

defined('BASEPATH') OR exit('No direct script access allowed');
/* to Change this template, choose Tools | Templates
 * and open the template in the editor.
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

    /**
     * Full view untuk update user
     * @param type $user_id
     */
    function edit($user_id) {
        $data['pagetitle'] = 'Edit User';
        $data['user'] = $this->users_model->get_user($user_id);
        $data['roles'] = $this->db->get('roles')->result();
        $this->template->display('people_form', $data);
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
        $this->form_validation->set_rules('status', 'Status', []);
        $this->form_validation->set_rules('role', 'Role', []);
        if ($this->form_validation->run() == true) {
            $data['updated'] = true;
            $this->users_model->update_user(
                    $user_id, $this->input->post('email'), $this->input->post('password'), $this->input->post('status'), $this->input->post('role')
            );
        }
        $this->template->display('people_form', $data);
    }

    /**
     * Check whether the specified email is owned by current user OR a new email
     * @param type $email
     */
    function check_self_or_unique($email, $user_id) {
        echo 'emil ' . $email;
        echo 'uid ' . $user_id;
        if ($this->users_model->get_user($user_id)->email === $email || !$this->users_model->get_login_info($email)) {
            return true;
        } else {
            $this->form_validation->set_message('check_self_or_unique', 'This %s is already taken');
            return false;
        }
    }

}
