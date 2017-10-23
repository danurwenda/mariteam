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
        $this->load->helper('text');
        $sSearch = urldecode($encoded);
        $sstr = strtoupper($sSearch);
        $data['active_menu'] = 0;
        $data['pagetitle'] = 'Search';
        //search in projects, tasks (and events)
        $this->db->select('project_name, description, project_id');
        $this->db->where("UPPER(description) LIKE '%" . $sstr . "%' or UPPER(project_name) LIKE '%" . $sstr . "%'");
        $projects = $this->db->get('projects')->result();
        foreach ($projects as $p) {
            if (strlen($p->description) == 0) {
                $text = 'No description';
            } else {
                $text = excerpt($p->description, $sSearch);
                if (strlen($text) == 0) {
                    $text = word_limiter($p->description);
                }
            }
            $p->text = $text;
            $p->title = highlight_phrase($p->project_name, $sSearch,"<span class=\"highlighted\">","</span>");
            $p->link = site_url('project/edit/' . $p->project_id);
        }
        $data['results'] = $projects;
        $this->db->select('task_name, description, project_id');
        $this->db->where("UPPER(description) LIKE '%" . $sstr . "%' or UPPER(task_name) LIKE '%" . $sstr . "%'");
        $tasks = $this->db->get('tasks')->result();
        foreach ($tasks as $p) {
            if (strlen($p->description) == 0) {
                $text = 'No description';
            } else {
                $text = excerpt($p->description, $sSearch);
                if (strlen($text) == 0) {
                    $text = word_limiter($p->description).'halu';
                }
            }
            $p->text = $text;
            $p->title = highlight_phrase($p->task_name, $sSearch,"<span class=\"highlighted\">","</span>");
            $p->link = site_url('project/edit/' . $p->project_id);
        }
        $data['results'] = array_merge($data['results'], $tasks);
        $this->template->display('search', $data);
    }

}
