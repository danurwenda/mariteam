<?php

defined('BASEPATH') OR exit('No direct script access allowed');
/* to Change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Dashboard extends Member_Controller {

    function __construct() {
        parent::__construct();
    }

    /**
     * Dashboard home.
     * Shows statistical information (e.g chart) about projects under logged user's privilege.
     * May display different information for different role.
     */
    function index() {
        $data['pagetitle'] = 'Dashboard';
        $data['active_menu'] = 0;
        $this->template->display('dashboard', $data);
    }

    public function search($encoded) {
        $str = urldecode($encoded);
        //search in projects, tasks (and events)
        $data['pagetitle'] = 'Search';
        $data['result'] = []; // TODO : search in db
        $this->template->display('search', $data);
    }

}
