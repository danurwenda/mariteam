<?php

defined('BASEPATH') OR exit('No direct script access allowed');
/* to Change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class User extends Member_Controller {

    function __construct() {
        parent::__construct();
    }

    /**
     * Dashboard home.
     * Shows statistical information (e.g chart) about projects under logged user's privilege.
     * May display different information for different role.
     */
    function profile() {
        $data['pagetitle'] = 'User Profile';
        $this->template->display('profile', $data);
    }

    function update() {
        $data['pagetitle'] = 'User Profile';
        $this->load->library('form_validation');
        $this->form_validation->set_rules('email', 'Username', 'trim|required|strip_tags|callback_check_self_or_unique');
        $this->form_validation->set_rules('password', 'Password', 'matches[passconf]');
        $this->form_validation->set_rules('passconf', 'Password Confirmation', 'matches[password]');
        if ($this->form_validation->run() == true) {
            $data['updated'] = true;
            $this->users_model->update_user($this->logged_user->user_id, $this->input->post('email'), $this->input->post('password'));
        }
        $this->template->display('profile', $data);
    }

    /**
     * Check whether the specified email is owned by current user OR a new email
     * @param type $email
     */
    function check_self_or_unique($email) {
        if ($this->logged_user->email === $email || !$this->users_model->get_login_info($email)) {
            return true;
        } else {
            $this->form_validation->set_message('check_self_or_unique', 'This %s is already taken');
            return false;
        }
    }

}
