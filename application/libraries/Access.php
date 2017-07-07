<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Access
 *
 * @author Administrator
 */
class Access {

    private $CI;
    private $users_model;

    //put your code here
    function __construct() {
        $this->CI = &get_instance();
        $auth = $this->CI->config->item('auth');
        $this->CI->load->helper('cookie');
        $this->CI->load->model('users_model');
        $this->users_model = &$this->CI->users_model;
    }

    /**
     * Check whether given username matches given password
     * @param type $plain_username
     * @param type $plain_password
     * @return boolean
     */
    function login($plain_username, $plain_password) {
        $match = $this->users_model->auth($plain_username, $plain_password);
        if ($match) {
            // match
            // find that user, put on session
            $this->CI->session->user_id = $this->users_model->get_login_info($plain_username)->user_id;
            return true;
        }
        return false;
    }

    function is_login() {
        return (($this->CI->session->userdata('user_id')) ? TRUE : FALSE);
    }

    function logout() {
        $this->CI->session->unset_userdata('user_id');
    }

}
