<?php

defined('BASEPATH') OR exit('No direct script access allowed');
/* to Change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Logged extends Member_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('public_template');
        $this->load->model('projects_model');
    }

    /**
     * Table view of projects
     */
    public function projects() {
        $data['page'] = 'projects';
        $data['_loggeduser'] = $this->logged_user;
        $this->public_template->display('public/projects_table', $data);
    }

    public function projects_dt() {
        if ($this->input->is_ajax_request()) {
            // search all groups this user belongs to
            $groups = [];
            $this->db
                    ->join('persons', 'users.person_id=persons.person_id')
                    ->join('person_group', 'persons.person_id=person_group.person_id');
            foreach ($this->db->get_where('users', ['user_id' => $this->logged_user->user_id])->result() as $pg) {
                $groups[] = $pg->group_id;
            }

            echo $this->projects_model->get_dt2(true, $groups);
        }
    }

    function get_task($task_id) {
        $this->db->select('person_name,task_name,task_id,description,status,assigned_to,weight,end_date,start_date')
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

    /**
     * Detailed view of a project
     * @param int $project_id project id
     */
    public function project($project_id) {
        $project = $this->projects_model->get_project($project_id);
        // check access control
        if ($project && $this->can_access($project_id)) {
            $data['page'] = 'projects';
            $data['_loggeduser'] = $this->logged_user;
            $data['topics'] = $this->db->get('topics')->result();
            $data['project'] = $project;
            $this->public_template->display('public/project_form', $data);
        } else {
            // send to project table
            redirect('logged/projects');
        }
    }

    private function can_access($pid) {
        return $this->logged_user->role_id != 3 || $this->db->join('person_group', 'person_group.group_id=project_group.group_id')->get_where('project_group', ['project_id' => $pid, 'person_id' => $this->logged_user->person_id])->num_rows() > 0;
    }

    public function tasks_dt() {
        if ($this->input->is_ajax_request()) {
            echo $this->projects_model->get_tasks_dt($this->input->post('project_id'));
        }
    }

    function events_dt() {
        $this->load->model('events_model');
        if ($this->input->is_ajax_request()) {
            echo $this->events_model->get_dt();
        }
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

}
