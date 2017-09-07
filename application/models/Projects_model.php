<?php

defined('BASEPATH') OR
        exit('No direct script access allowed');
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Users_model
 *
 * @author Administrator
 */
class Projects_model extends CI_Model {

    public $table = 'projects';
    public $primary_key = 'project_id';

    public function __construct() {
        parent::__construct();
        $this->load->library('Datatables');
    }

    public function add_document($user, $project, $uuid, $filename, $size) {
        return $this->db->insert('documents', [
                    'dir' => $uuid,
                    'filename' => $filename,
                    'source_id' => $project,
                    'source_table' => 'projects',
                    'created_by' => $user,
                    'size' => $size
        ]);
    }

    public function get_document($doc_id) {
        $this->db->where('document_id', $doc_id);
        $q = $this->db->get('documents');
        if ($q->num_rows() > 0) {
            return $q->row();
        } else
            return null;
    }

    public function get_chart_data() {
        $this->db->select('name,count(project_id) as total')
                ->join('project_statuses', 'project_statuses.status_id=projects.project_status')
                ->group_by('name');
        $q = $this->db->get('projects');

        return $q->result();
    }

    public function get_dt() {
        $this->datatables
                ->distinct('projects.project_id')
                // additional field to search into : project description, task name, task description
                ->add_search_column(['projects.description', 'tasks.description', 'task_name'])
                ->select('project_name,person_name, project_status,projects.end_date,projects.progress, projects.project_id')
                ->join('tasks', 'tasks.project_id=projects.project_id', 'left')
                ->join('persons', 'persons.person_id=projects.assigned_to', 'left')
                ->from('projects');
        return $this->datatables->generate();
    }

    /**
     * TODO
     * @param int $task_id
     */
    public function delete_task($task_id) {
        
    }

    /**
     * TODO
     * @param int $project_id
     */
    public function delete_project($project_id) {
        
    }

    public function get_tasks_dt($project_id) {
        $this->datatables
                ->where('project_id', $project_id)
                ->select('task_name,person_name, end_date, status, weight, task_id')
                ->join('persons', 'persons.person_id=tasks.assigned_to', 'left')
                ->from('tasks');
        return $this->datatables->generate();
    }

    /**
     * See https://roberto.open-lab.com/2012/08/24/jquery-gantt-editor/
     * @param type $project_id the project id
     */
    public function get_tasks_timeline($project_id, $canWrite = false) {

        $this->db
                ->select('task_id id')
                ->select('task_name name')
                ->join('project_statuses', 'project_statuses.status_id=tasks.status')
                ->select('project_statuses.name status')
                ->select('UNIX_TIMESTAMP(start_date)  as start')
                ->select('(progress * 100) as progress', false)
                ->select('description, startIsMilestone, endIsMilestone, duration, depends, level')
                ->order_by('task_order', 'asc');
        $ori = $this->db->get_where('tasks', [
                    'project_id' => $project_id
                ])->result();
        // make associated array for easier traversing
        $tasks = [];
        foreach ($ori as $value) {
            $tasks[$value->id] = $value;
        }
        foreach ($ori as $task) {
            $task->start = $task->start * 1000;
            $task->assigs = [];
        }
        //insert the project itself as the first element
        array_splice($ori, 0, 0, $this->project_as_task($project_id));
        $ret = ['tasks' => $ori, 'canWrite' => $canWrite];
        return $ret;
    }

    private function project_as_task($project_id) {
        $this->db
                ->select('project_id id')
                ->select('project_name name')
                ->join('project_statuses', 'project_statuses.status_id=projects.project_status')
                ->select('project_statuses.name status')
                ->select('UNIX_TIMESTAMP(start_date)  as start, UNIX_TIMESTAMP(end_date)  as end')
                ->select('description');
        $p = $this->db->get_where('projects', [
                    'project_id' => $project_id
                ])->row();
        $p->level = 0;
        $p->progress = 0;
        $p->depends = null;
        $p->start = $p->start * 1000;
        $p->end = $p->end * 1000;
        $p->duration = round(($p->end - $p->start) / 1000 / 3600 / 24);
        return [$p];
    }

    public function get_task($task_id) {
        return $this->db->get_where('tasks', ['task_id' => $task_id])->row();
    }

    private function get_dependant_task($main_task_id) {
        $this->db->order_by('start_date', 'asc');
        $lvl_1 = $this->db->get_where('tasks', [
                    'dependency_task' => $main_task_id
                ])->result();
    }

    public function get_docs_dt($project_id) {
        $this->datatables
                ->where('source_id', $project_id)
                ->where('source_table', 'projects')
                ->select('filename,size,created_at,document_id,dir,created_by')
                ->from('documents');
        return $this->datatables->generate();
    }

    public function get_table_data($logged_user_id = null) {
        if (isset($logged_user_id)) {
            $this->db->where('projects.assigned_to', $logged_user_id)
                    ->or_where('tasks.assigned_to', $logged_user_id);
        }
        $this->db
                ->distinct()
                ->select('projects.project_id, project_name,project_statuses.name status, projects.end_date, user_name');
        $this->db->join('users', 'users.user_id = projects.assigned_to')
                ->join('tasks', 'tasks.project_id=projects.project_id', 'left')
                ->join('project_statuses', 'project_statuses.status_id=projects.project_status');
        $ret = $this->db->get($this->table)->result();

        foreach ($ret as $row) {
            //calculate progress            
            $this->db->select_sum('weight');
            $weight_sum = $this->db->get_where('tasks', ['project_id' => $row->project_id]);
            $project_weight = $weight_sum->row()->weight;
            if ($project_weight > 0) {
                //calculate the total weight of those tasks done
                $this->db->select_sum('weight');
                $done_sum = $this->db->get_where('tasks', ['project_id' => $row->project_id, 'is_done' => true]);
                $row->progress = round(10000 * ($done_sum->row()->weight) / $project_weight) / 100;
            } else {
                $row->progress = -1;
            }
        }
        return $ret;
    }

    public function get_login_info($u) {
        $this->db->where('email', $u)->limit(1);
        $q = $this->db->get($this->table);
        return ($q->num_rows() > 0) ? $q->row() : false;
    }

    public function edit_task(
    $task_id, $task_name, $desc, $start_date, $due_date, $assigned_to, $weight, $done) {
        $this->db->where('task_id', $task_id);
        return $this->db->update('tasks', [
                    'task_name' => $task_name,
                    'description' => $desc,
                    'assigned_to' => $assigned_to,
                    'start_date' => $start_date,
                    'end_date' => $due_date,
                    'weight' => $weight,
                    'status' => $done
        ]);
    }

    public function add_task(
    $project_id, $task_name, $desc, $due_date, $end_date, $created_by, $assigned_to, $weight) {
        return $this->db->insert('tasks', [
                    'task_name' => $task_name,
                    'description' => $desc,
                    'project_id' => $project_id,
                    'created_by' => $created_by,
                    'assigned_to' => $assigned_to,
                    'start_date' => $due_date,
                    'end_date' => $end_date,
                    'weight' => $weight
        ]);
    }

    /**
     * The information contained in the  project object returned by this 
     * function should be sufficient to fulfill the whole form
     * @param type $id
     * @return type
     */
    public function get_project($id) {
        $this->db
                ->join('persons', 'persons.person_id=projects.assigned_to')
                ->where($this->primary_key, $id)->limit(1);
        $q = $this->db->get($this->table);
        if ($q->num_rows() > 0) {
            $p = $q->row();
            //add topics
            $p->topics = [];
            $topics = $this->db->get_where('project_topic', ['project_id' => $p->project_id]);
            foreach ($topics->result() as $pt) {
                $p->topics[] = $pt->topic_id;
            }
            return $p;
        } else {
            return false;
        }
    }

    public function get_topics() {
        return $this->db
                        ->where('UPPER(topic_name) LIKE', '%' . strtoupper($this->input->get('term', true)) . '%')
                        ->get('topics')
                        ->result_array();
    }

    public function update($id, $user, $name, $due_date, $description, $topics, $status) {
        $this->db->where('project_id', $id);
        //update username
        if (isset($user)) {
            $this->db->set('assigned_to', $user);
        }
        $this->db->set('project_name', $name);
        $this->db->set('project_status', $status);
        $this->db->set('end_date', $due_date);
        $this->db->set('description', $description);
        //do update
        $this->db->update($this->table);
        //update topics
        $this->set_topics($id, $topics);
    }

    public function create($creator, $user, $name, $due_date, $description, $topics) {
        $this->db->insert($this->table, [
            'created_by' => $creator,
            'assigned_to' => $user,
            'project_name' => $name,
            'end_date' => $due_date,
            'description' => $description,
            'project_priority' => 1,
            'project_status' => 1
        ]);
        $pid = $this->db->insert_id();
        $this->set_topics($pid, $topics);
    }

    public function set_topics($pid, $topics) {
        //clear previous set of topic
        $this->db->where('project_id', $pid);
        $this->db->delete('project_topic');
        //insert new
        foreach ($topics as $tid) {
            $this->db->insert('project_topic', [
                'topic_id' => $tid,
                'project_id' => $pid
            ]);
        }
    }

}
