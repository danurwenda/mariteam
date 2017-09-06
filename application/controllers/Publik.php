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
    }

    /**
     * Home page
     */
    public function index() {
        $data['page'] = 'home';
        $this->public_template->display('public/dashboard', $data);
    }

    public function get_project_chart_data() {
        echo json_encode($this->projects_model->get_chart_data());
    }

    /**
     * Table view of projects
     */
    public function projects() {
        $data['page'] = 'projects';
        $this->public_template->display('public/projects_table', $data);
    }

    public function projects_dt() {
        if ($this->input->is_ajax_request()) {
            echo $this->projects_model->get_dt();
        }
    }

    /**
     * Detailed view of a project
     * @param int $project_id project id
     */
    public function project($project_id) {
        $data['page'] = 'project';
        $data['topics'] = $this->db->get('topics')->result();
        $data['project'] = $this->projects_model->get_project($project_id);
        $this->public_template->display('public/project_form', $data);
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
                ->select('users.user_id, content, time, users.user_name')
                ->join('users', 'users.user_id=task_comments.user_id');
        $q = $this->db->get_where('task_comments', ['task_id' => $task_id]);
        foreach ($q->result() as $comment) {
            $comments[] = [
                'user' => $comment->user_name,
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
        $ret = [
            'ok' => true,
            'project' => $this->projects_model->get_tasks_timeline($project_id)
        ];
        echo json_encode($ret);
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
