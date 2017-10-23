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

    function tex() {
        $x = "Northen Bali Connectivity Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.";
        echo $x;
        echo '<br/>';
        echo '<br/>';
        echo '<br/>';
        echo excerpt($x, 'bali');
    }

    function __construct() {
        parent::__construct();
        $this->load->library('public_template');
        $this->load->model('projects_model');
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
        $this->public_template->display('public/dashboard', $data);
    }

    public function get_project_chart_data() {
        echo json_encode($this->projects_model->get_chart_data(true));
    }

    /**
     * Table view of projects
     */
    public function projects() {
        $data['page'] = 'projects';
        $this->public_template->display('public/projects_table', $data);
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

    public function projects_dt() {
        if ($this->input->is_ajax_request()) {
            echo $this->projects_model->get_dt2(true);
        }
    }

    function get_groups() {
        echo json_encode($this->projects_model->get_groups());
    }

    /**
     * Detailed view of a project
     * @param int $project_id project id
     */
    public function project($project_id) {
        $project = $this->projects_model->get_project($project_id);
        if ($project) {
            $data['page'] = 'project';
            $data['topics'] = $this->db->get('topics')->result();
            $data['project'] = $project;
            $this->public_template->display('public/project_form', $data);
        } else {
            // send to project table
            redirect('publik/projects');
        }
    }

    public function get_topics() {
        echo json_encode($this->projects_model->get_topics());
    }

    public function docs_dt() {
        if ($this->input->is_ajax_request()) {
            echo $this->projects_model->get_docs_dt($this->input->post('project_id'));
        }
    }

    public function tasks_dt() {
        if ($this->input->is_ajax_request()) {
            echo $this->projects_model->get_tasks_dt($this->input->post('project_id'));
        }
    }

    function get_task($task_id) {
        $this->db->select('person_name,task_name,task_id,description,status,assigned_to,weight,end_date')
                ->join('persons', 'persons.person_id=tasks.assigned_to');
        echo json_encode($this->db->get_where('tasks', ['task_id' => $task_id])->row());
    }

    function get_task_comment($task_id) {
        //bedakan antara komen yang dibuat oleh current user dengan 
        //yang dibuat oleh orang lain
        //array of object dengan element :
        //initial, user, time, self, content
        $comments = [];

        $this->db
                ->select('users.user_id, content, time, person_name')
                ->join('users', 'users.user_id=task_comments.user_id')
                ->join('persons', 'persons.person_id=users.person_id');
        $q = $this->db->get_where('task_comments', ['task_id' => $task_id]);
        foreach ($q->result() as $comment) {
            $comments[] = [
                'user' => $comment->person_name,
                'self' => false,
                'content' => $comment->content,
                'time' => $comment->time
            ];
        }
        echo json_encode($comments);
    }

    function get_task_docs($task_id) {
        $this->load->model('documents_model');
        echo json_encode($this->documents_model->get_documents('tasks', $task_id));
    }

    public function get_timeline() {
        $project_id = $this->input->get('project_id');
        $timeline = $this->projects_model->get_tasks_timeline($project_id);
        $timeline['canWrite'] = false;
        $ret = [
            'ok' => true,
            'project' => $timeline
        ];
        echo json_encode($ret);
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
