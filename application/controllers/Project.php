<?php

defined('BASEPATH') OR exit('No direct script access allowed');
/* to Change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Project extends Module_Controller {

    function __construct() {
        parent::__construct(1);
        
    }

    /**
     * Dashboard home.
     * Shows statistical information (e.g chart) about projects under logged user's privilege.
     * May display different information for different role.
     */
    function index() {
        $data['pagetitle']='Project';
        $this->template->display('dashboard',$data);
    }
}
