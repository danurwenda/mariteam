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
    }

    public function add_document($user, $project, $uuid, $filename, $size) {
        return $this->db->insert('documents', [
                    'dir' => $uuid,
                    'filename' => $filename,
                    'source_id' => $project,
                    'source_table'=>'projects',
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

    public function get_table_data($logged_user_id = null) {
        if (isset($logged_user_id)) {
            $this->db->where('projects.assigned_to', $logged_user_id)
                    ->or_where('tasks.assigned_to', $logged_user_id);
        }
        $this->db
                ->distinct()
                ->select('projects.project_id, project_name,project_statuses.name status, projects.due_date, user_name');
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

    public function edit_task($task_id, $project_id, $task_name, $desc, $due_date, $assigned_to, $weight, $done) {
        $this->db->where('task_id', $task_id);
        return $this->db->update('tasks', [
                    'task_name' => $task_name,
                    'description' => $desc,
                    'project_id' => $project_id,
                    'assigned_to' => $assigned_to,
                    'due_date' => $due_date,
                    'weight' => $weight,
                    'is_done' => $done
        ]);
    }

    public function add_task($project_id, $task_name, $desc, $due_date, $created_by, $assigned_to, $weight) {
        return $this->db->insert('tasks', [
                    'task_name' => $task_name,
                    'description' => $desc,
                    'project_id' => $project_id,
                    'created_by' => $created_by,
                    'assigned_to' => $assigned_to,
                    'due_date' => $due_date,
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
                ->join('users','users.user_id=projects.assigned_to')
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

    public function update($id, $user, $name, $due_date, $description, $topics, $status) {
        $this->db->where('project_id', $id);
        //update username
        if (isset($user)) {
            $this->db->set('assigned_to', $user);
        }
        $this->db->set('project_name', $name);
        $this->db->set('project_status', $status);
        $this->db->set('due_date', $due_date);
        $this->db->set('description', $description);
        //do update
        $this->db->update($this->table);
        //update topics
        $this->set_topics($id, $topics);
    }

    public function auth($username, $password) {
        $this->db->where('email', $username);
        $q = $this->db->get($this->table);
        if ($q->num_rows() == 0) {
            return false;
        }
        $user = $q->row();
        return password_verify($password, $user->hash);
    }

    public function create($creator, $user, $name, $due_date, $description, $topics) {
        $this->db->insert($this->table, [
            'created_by' => $creator,
            'assigned_to' => $user,
            'project_name' => $name,
            'due_date' => $due_date,
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

    public function delete($uid) {
        $this->db->delete($this->table, ['user_id' => $uid]);
    }

    public function update_last_access($uid) {
        $this->db->where('user_id', $uid);
        $this->db->set('last_access', date("Y-m-d H:i:s"));
        $this->db->update($this->table);
    }

}
