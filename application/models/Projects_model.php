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

    public function get_chart_data_by_dep($public_only = false) {
        $this->db->select('group_name,count(project_group.project_id) as total')
                ->join('groups', 'project_group.group_id=groups.group_id')
                ->group_by('group_name');
        if ($public_only) {
            $this->db->where('is_public', 1);
        }
        $q = $this->db->get('project_group');

        return $q->result();
    }
    public function get_chart_data($public_only = false) {
        $this->db->select('name,count(projects.project_id) as total')
                ->join('project_statuses', 'project_statuses.status_id=projects.project_status')
                ->group_by('name');
        if ($public_only) {
            $this->db->join('project_group', 'project_group.project_id=projects.project_id');
            $this->db->join('groups', 'project_group.group_id=groups.group_id');
            $this->db->where('is_public', 1);
        }
        $q = $this->db->get('projects');

        return $q->result();
    }

    private function is_delayed($project_id) {
        $this->db->where('project_id', $project_id)
                ->where('end_date <', date('Y-m-d'))
                ->where('status != 2');

        return $this->db->get('tasks')->num_rows() > 0;
    }

    public function get_dt2($public_only = false) {
        $this->load->library('Datatables3');
        $this->datatables3->init();
        $case = 1;
        if ($groups = $this->input->post('groups')) {
            // handle "Menko" optgroup
            if(in_array("0", $groups)){
                $groups= array_merge($groups,["2","3","4","5"]);
            }
            // TODO : check user access to group
            $this->db->join('project_group', 'project_group.project_id=projects.project_id');
            foreach ($groups as $g) {
                $this->db->or_where('group_id', $g);
            }
        } else if (is_array($public_only)) {
            // return those projects in public groups and those in accessible groups
            $this->db->join('project_group', 'project_group.project_id=projects.project_id');
            $this->db->join('groups', 'project_group.group_id=groups.group_id');
            $this->db->group_start();
            $this->db->or_where('is_public', 1);
            foreach ($public_only as $g) {
                $this->db->or_where('groups.group_id', $g);
            }
            $this->db->group_end();
            $case = 2;
        } else if ($public_only) {
            $this->db->join('project_group', 'project_group.project_id=projects.project_id');
            $this->db->join('groups', 'project_group.group_id=groups.group_id');
            $this->db->where('is_public', 1);
            $case = 3;
        }
        // additional field to search into : project description, task name, task description
        $this->datatables3
                ->add_search_column(['projects.description', 'tasks.description', 'task_name', 'p2.person_name'])
                ->distinct()
                ->select('project_name,p1.person_name, project_status,projects.end_date,projects.progress, projects.project_id');
        $this->db
                ->join('tasks', 'tasks.project_id=projects.project_id', 'left')
                ->join('persons p1', 'p1.person_id=projects.assigned_to', 'left')
                ->join('persons p2', 'p2.person_id=tasks.assigned_to', 'left')
                ->from('projects');
        $json = $this->datatables3->generate();
        $decoded = json_decode($json);
        $decoded->c = $case;
        foreach ($decoded->data as &$project) {
            // add info about time table of this project
            // set delayed == true if this project has one or more overdue task
            // but the expected end time for this project is actually still in the future
            if ($this->datatables3->isColIdxd()) {
                $project[] = ( $project[3] > date('Y-m-d')) && $this->is_delayed($project[5]);
            } else {
                $project->delay = ( $project->end_date > date('Y-m-d')) && $this->is_delayed($project->project_id);
            }
        }
        return json_encode($decoded);
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
                ->select('task_name,person_name, end_date, status, weight,task_order, task_id')
                ->join('persons', 'persons.person_id=tasks.assigned_to', 'left')
                ->from('tasks');
        return $this->datatables->generate();
    }

    /**
     * See https://roberto.open-lab.com/2012/08/24/jquery-gantt-editor/
     * @param type $project_id the project id
     */
    public function get_tasks_timeline($project_id) {

        $this->db
                ->select('task_id id')
                ->select('task_name name')
                ->join('project_statuses', 'project_statuses.status_id=tasks.status')
                ->select('project_statuses.name status')
                ->select('UNIX_TIMESTAMP(start_date)  as start')
                ->select('progress')
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
            $task->progress = 100 * $task->progress;
            $task->start = $task->start * 1000;
            $task->assigs = [];
            $task->level = $task->level * 1;
            $task->startIsMilestone = ($task->startIsMilestone === '1');
            $task->endIsMilestone = ($task->endIsMilestone === '1');
        }
        //insert the project itself as the first element
        array_splice($ori, 0, 0, $this->project_as_task($project_id));
        $ret = ['tasks' => $ori];
        return $ret;
    }

    private function project_as_task($project_id) {
        $this->db
                ->select('project_id id')
                ->select('project_name name')
                ->join('project_statuses', 'project_statuses.status_id=projects.project_status')
                ->select('project_statuses.name status')
                ->select('UNIX_TIMESTAMP(start_date)  as start, UNIX_TIMESTAMP(end_date)  as end')
                ->select('description,duration');
        $p = $this->db->get_where('projects', [
                    'project_id' => $project_id
                ])->row();
        $p->level = 0;
        $p->progress = 0;
        $p->depends = null;
        $p->start = $p->start * 1000;
        $p->end = $p->end * 1000;
        $p->hasChild = true;
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
    $project_id, $task_name, $desc, $due_date, $end_date, $created_by, $assigned_to, $weight, $task_status = 4) {
        return $this->db->insert('tasks', [
                    'task_name' => $task_name,
                    'description' => $desc,
                    'project_id' => $project_id,
                    'created_by' => $created_by,
                    'assigned_to' => $assigned_to,
                    'start_date' => $due_date,
                    'end_date' => $end_date,
                    'weight' => $weight,
                    'status' => $task_status
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
            //add groups
            $p->groups = [];
            $groups = $this->db->get_where('project_group', ['project_id' => $p->project_id]);
            foreach ($groups->result() as $pt) {
                $p->groups[] = $pt->group_id;
            }
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

    /**
     * For select2
     * @return type
     */
    public function get_topics() {
        return $this->db
                        ->where('UPPER(topic_name) LIKE', '%' . strtoupper($this->input->get('term', true)) . '%')
                        ->get('topics')
                        ->result_array();
    }

    /**
     * For select2
     * @return type
     */
    public function get_groups($person_id = null, $role = null) {
        if ($role != 1) {
            $this->db
                    ->group_start()
                    ->where('is_public', 1);
            if ($person_id !== null) {
                $this->db->or_where("exists(SELECT * from person_group where person_id = $person_id and group_id=groups.group_id)");
            }
            $this->db->group_end();
        }
        $ret =  $this->db
                        ->where('UPPER(group_name) LIKE', '%' . strtoupper($this->input->get('term', true)) . '%')
                        ->get('groups')
                        ->result_array();
        // add hardcoded "Menko" optgroup
        $ret[]=[
            "group_id"=>"0",
            "group_name"=>"Menko",
            "group_leader"=>"3",
            "is_public"=>"1"];
        return $ret;
    }

    public function update($id, $user, $name, $start_date, $due_date, $description, $topics, $status, $groups) {
        $this->db->where('project_id', $id);
        //update username
        if (isset($user)) {
            $this->db->set('assigned_to', $user);
        }
        $this->db->set('project_name', $name);
        $this->db->set('project_status', $status);
        $this->db->set('start_date', $start_date);
        $this->db->set('end_date', $due_date);
        $this->db->set('description', $description);
        //do update
        $this->db->update($this->table);
        //update topics
        $this->set_topics($id, $topics);
        //update groups
        $this->set_groups($id, $groups);
    }

    public function create($creator, $user, $name, $start_date, $due_date, $description, $topics, $groups) {
        $this->db->insert($this->table, [
            'created_by' => $creator,
            'assigned_to' => $user,
            'project_name' => $name,
            'start_date' => $start_date,
            'end_date' => $due_date,
            'description' => $description,
            'project_priority' => 1,
            'project_status' => 1
        ]);
        $pid = $this->db->insert_id();
        $this->set_topics($pid, $topics);
        $this->set_groups($pid, $groups);
    }

    public function set_groups($pid, $groups) {
        //clear previous set of group
        $this->db->where('project_id', $pid);
        $this->db->delete('project_group');
        //insert new
        foreach ($groups as $tid) {
            $this->db->insert('project_group', [
                'group_id' => $tid,
                'project_id' => $pid
            ]);
        }
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
