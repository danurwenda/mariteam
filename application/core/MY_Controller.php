<?php

defined('BASEPATH') OR
        exit('No direct script access allowed');
/*
 * Semacam middleware kalau di laravel
 */

class Member_Controller extends CI_Controller {

    protected $logged_user;

    function __construct() {
        parent::__construct();
        $this->load->library('access');
        if (!$this->is_login()) {
            redirect('/auth/login'); //redirect to Auth Controller
        } else {
            $this->load->model('users_model');
            $this->logged_user = $this->users_model->get_user($this->session->user_id);
            // update last_access field
            $this->users_model->update_last_access($this->logged_user->user_id);
            $this->load->library(
                    'template', [
                'user' => $this->logged_user
                    ]
            );
        }
    }

    function is_login() {
        return $this->access->is_login();
    }

}

/**
 * Base class for controller that represent a specific module. When this 
 * controller is accessed, it will check whether logged user has the privilege
 * to access this module.
 */
class Module_Controller extends Member_Controller {

    protected $module_id;

    function __construct($module_id) {
        parent::__construct();
        $this->module_id = $module_id;
        //load module_privilege
        $this->load->model('module_access_model', 'mpm');
        if (!$this->mpm->can_access($this->logged_user->role_id, $module_id)) {
            //redirect to homepage
            redirect();
        }
    }

}

class Police_Controller extends Module_Controller {

    protected $polres_id;
    protected $policeDB;

    function __construct($module_id) {
        parent::__construct($module_id);
        //validasi asal polres
        //TODO : move to model
        $this->policeDB = $this->load->database('polri', true);
        //try to find the polres
        $pols = $this->policeDB->get_where('polres_user', [
            'user_id' => $this->logged_user->user_id
        ]);
        if ($pols->num_rows() > 0) {
            //ambil yang pertama
            $this->polres_id = $pols->row()->polres_id;
        } else {
            redirect('/dashboard');
        }
    }

}

class Admin_Controller extends Member_Controller {

    protected $module_id;

    function __construct($module_id) {
        parent::__construct();
        $this->module_id = $module_id;
        //load module_privilege
        $this->load->model('module_access_model', 'mpm');
        if (!$this->mpm->can_access($this->logged_user->role_id, $module_id)) {
            //redirect to dashboard
            redirect('/dashboard');
        } else {
            $this->load->library(
                    'adtemplate', ['role_id' => $this->logged_user->role_id]
            );
        }
    }

}

/**
 * Description of MY_Controller
 *
 * @author Administrator
 */
class MY_Controller extends CI_Controller {

    function __construct() {
        parent::__construct();
    }

}
