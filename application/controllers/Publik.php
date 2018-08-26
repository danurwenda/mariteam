<?php

defined('BASEPATH') OR
        exit('No direct script access allowed');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Controller for visitor pages
 *
 * @author danur
 */
class Publik extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('public_template');
        $this->load->model('projects_model');
        $this->load->library('access');
    }

    public function download($uuid) {
        $doc = $this->db->get_where('documents', ['dir' => $uuid]);
        if ($doc->num_rows() > 0) {
            $this->load->helper('download');
            $doc = $doc->row();
            $data = file_get_contents('uploads/' . $doc->dir . '/' . $doc->filename); // Read the file's contents
            $name = $doc->filename;
            force_download($name, $data);
        }
    }

    /**
     * Home page
     */
    public function index() {
        $data['page'] = 'home';
        $data['_loggeduser'] = $this->access->is_login() ? $this->users_model->get_user($this->session->user_id) : null;
        $this->public_template->display('public/dashboard', $data);
    }

    public function get_project_chart_data() {
        echo json_encode($this->projects_model->get_chart_data(true));
    }

    public function get_project_chart_data_by_dep() {
        echo json_encode($this->projects_model->get_chart_data_by_dep(true));
    }

    /**
     * Fetching list of projects, 30 items per page
     * @return type
     */
    public function projects_s2() {
        $q = $this->input->get('q');
        $page = $this->input->get('page') || 1;

        $this->db->start_cache();
        //filtering criteria, without paging..
        $this->db
                ->or_like('UPPER(project_name)', strtoupper($q));
        $this->db->stop_cache();

        // count all
        $all = $this->db->get('projects')->num_rows();

        // put paging on
        $this->db->limit(30, 30 * ($page - 1));
        $this->db->select('project_id id,project_name');
        $items = $this->db
                ->get('projects')
                ->result();

        echo json_encode([
            'total_count' => $all,
            'items' => $items
        ]);
    }

    function get_groups() {
        echo json_encode($this->projects_model->get_groups());
    }

    public function get_groups_elmt() {
        echo json_encode($this->projects_model->get_groups_elmt());
    }

    public function get_topics() {
        echo json_encode($this->projects_model->get_topics());
    }

    public function docs_dt() {
        if ($this->input->is_ajax_request()) {
            echo $this->projects_model->get_docs_dt($this->input->post('project_id'));
        }
    }

    /**
     * Data provider for FullCalendar library
     */
    public function calendar() {
        $this->db->select('start_time start, end_time end, event_id id, event_name title,description,location, person_name pic');
        $this->db->join('persons', 'persons.person_id=events.pic');
        $this->db->or_where('start_time > ', $this->input->get('start'));
        $this->db->or_where('end_time < ', $this->input->get('end'));
        $events = $this->db->get('events')->result();

        foreach ($events as $e) {
            $projects = null;
            //find related projects
            $this->db->join('projects', 'projects.project_id=project_event.project_id');
            $pes = $this->db->get_where('project_event', ['event_id' => $e->id])->result();
            foreach ($pes as $pe) {
                $projects[$pe->project_id] = $pe->project_name;
            }
            $e->projects = $projects;
        }

        echo json_encode($events);
    }

    /**
     * Table view of events
     */
    public function events() {
        $data['page'] = 'events';
        $this->public_template->display('public/dashboard', $data);
    }

    /**
     * Detailed view of an event
     * @param type $event_id event id
     */
    public function event($event_id) {
        
    }

}
