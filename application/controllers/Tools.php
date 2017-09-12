<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    function __construct() {
        parent::__construct();		
        $this->load->library('access');
    }

    function index() {
        $this->access->logout();
        $this->login();
    }

    function login() {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('email', 'Username', 'trim|required|strip_tags');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');
        $this->form_validation->set_rules('token', 'token', 'callback_check_login');
        if ($this->form_validation->run() == false) {
            $this->load->view('login');
        } else {
              redirect('dashboard');
        }
    }

    function logout() {
        $this->access->logout();
        redirect();
    }

    function check_login() {
        $username = $this->input->post('email', true);
        $password = $this->input->post('password', true);

        $login = $this->access->login($username, $password);
        if ($login) {
            return true;
        } else {
            $this->form_validation->set_message('check_login', 'Wrong username or password');
            return false;
        }
    }

}
