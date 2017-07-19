<?php

defined('BASEPATH') OR exit('No direct script access allowed');
/* to Change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Project extends Module_Controller {

    function __construct() {
        parent::__construct(1);
        $this->load->model('projects_model');
        $this->load->library('Datatables');
    }

    /**
     * Table view. If user logged in as a Contributor, then only projects 
     * in which they involve are shown.
     */
    function index() {
        $data['pagetitle'] = 'Project';
        $data['user_role'] = $this->logged_user->role_id;
        if ($this->logged_user->role_id == 1) {
            $data['ps'] = $this->projects_model->get_table_data();
        } else {
            $data['ps'] = $this->projects_model->get_table_data($this->logged_user->user_id);
        }
        $this->template->display('project_table', $data);
    }

    function get_task($task_id) {
        echo json_encode($this->db->get_where('tasks', ['task_id' => $task_id])->row());
    }

    function edit_task() {
        if ($this->input->is_ajax_request()) {
            $project_id = $this->input->post('project_id');
            $task_name = $this->input->post('task_name');
            $desc = $this->input->post('desc');
            $user = $this->input->post('assigned_to');
            $date = $this->input->post('due_date');
            $date = date_format(date_create($date), "Y-m-d H:i:s");
            $weight = $this->input->post('weight');
            $task_id = $this->input->post('task_id');
            if (empty($task_id)) {
                $change = $this->projects_model->add_task(
                        $project_id, $task_name, $desc, $date, $this->logged_user->user_id, $user, $weight
                );
            } else {
                //edit
                $change = $this->projects_model->edit_task($task_id, $project_id, $task_name, $desc, $date, $user, $weight, $this->input->post('is_done')
                );
            }
            echo json_encode(['success' => $change]);
        } else {
            //back to table view
            redirect('project/create');
        }
    }

    function update_task() {
        
    }

    function delete_task() {
        $task_id = $this->input->post('task_id');
        echo json_encode(['success' => $this->db->delete('tasks', ['task_id' => $task_id])]);
    }

    function tasks_dt() {
        echo $this->query_tasks();
    }

    private function query_tasks() {
        if ($this->input->is_ajax_request()) {
            $project_id = $this->input->post('project_id');
            if ($this->logged_user->role_id != 1) {
                //check whether current user has access to this particular project
                $this->db->where([
                    'assigned_to' => $this->logged_user->user_id,
                    'project_id' => $project_id]);
                if ($this->db->get('projects')->num_rows() == 0) {
                    return json_encode([]);
                }
            }
            $this->datatables
                    ->where('project_id', $project_id)
                    ->select('task_name,user_name, due_date, is_done, weight, task_id')
                    ->join('users', 'users.user_id=tasks.assigned_to', 'left')
//                    ->add_column('DT_RowId', 'row_$1', 'individu_id')
                    ->from('tasks');
            return $this->datatables->generate();
        }
    }

    function delete() {
        if ($this->logged_user->role_id == 1) {
            $project_id = $this->input->post('project_id');
            echo json_encode(['success' => $this->db->delete('projects', ['project_id' => $project_id])]);
        }
    }

    function create() {
        if ($this->logged_user->role_id != 1) {
            //forbidden
            redirect('project');
        }
        $data['user_role'] = $this->logged_user->role_id;
        $data['pagetitle'] = 'Add Project';
        $data['users'] = $this->db->get('users')->result();
        $data['topics'] = $this->db->get('topics')->result();
        $data['statuses'] = $this->db->get('project_statuses')->result();
        $this->load->library('form_validation');
        $this->form_validation->set_rules('assigned_to', 'Assigned User', 'required');
        $this->form_validation->set_rules('name', 'Display Name', ['trim', 'required', 'strip_tags']);
        $this->form_validation->set_rules('due_date', 'Due Date', 'required');
        if ($this->form_validation->run() == true) {
            $data['updated'] = true;
            $this->projects_model->create(
                    //logged user
                    $this->logged_user->user_id,
                    //assigned to
                    $this->input->post('assigned_to'),
                    //name
                    $this->input->post('name'),
                    //due date (adjust the format to comply SQL datetime format)
                    date_format(date_create($this->input->post('due_date')), "Y-m-d H:i:s"),
                    //description
                    $this->input->post('description'),
                    //topic
                    $this->input->post('topics')
            );
            //return to table view
            redirect('project');
        } else {
            $this->template->display('project_form', $data);
        }
    }

    function update() {
        $data['pagetitle'] = 'Edit Project';
        $project_id = $this->input->post('project_id');
        $data['project'] = $this->projects_model->get_project($project_id);
        $data['users'] = $this->db->get('users')->result();
        $data['topics'] = $this->db->get('topics')->result();
        $data['user_role'] = $this->logged_user->role_id;
        $data['statuses'] = $this->db->get('project_statuses')->result();
        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', 'Name', 'required');
        $this->form_validation->set_rules('due_date', 'Due Date', 'required');
        if ($data['user_role'] == 1) {
            $this->form_validation->set_rules('assigned_to', 'Assigned User', 'required');
        }
        if ($this->form_validation->run() == true) {
            $data['updated'] = true;
            $this->projects_model->update(
                    $project_id,
                    //assigned to
                    $this->input->post('assigned_to'),
                    //name
                    $this->input->post('name'),
                    //due date (adjust the format to comply SQL datetime format)
                    date_format(date_create($this->input->post('due_date')), "Y-m-d H:i:s"),
                    //description
                    $this->input->post('description'),
                    //topic
                    $this->input->post('topics'),
                    //status
                    $this->input->post('status')
            );
            //redirect to edit
            redirect('project/edit/' . $project_id);
        } else {
            $this->template->display('project_form', $data);
        }
    }

    function edit($project_id) {
        $project = $this->projects_model->get_project($project_id);
        $data['user_role'] = $this->logged_user->role_id;
        if (!$project) {
            //project not found
            redirect('project');
        } else {
            $data['pagetitle'] = 'Edit Project';
            $data['project'] = $project;
            $data['users'] = $this->db->get('users')->result();
            $data['topics'] = $this->db->get('topics')->result();
            $data['statuses'] = $this->db->get('project_statuses')->result();
            $this->template->display('project_form', $data);
        }
    }

    function create_topic() {
        $inserted = $this->db->insert('topics', ['topic_name' => $this->input->post('topic_name')]);
        if ($this->input->is_ajax_request()) {
            echo json_encode(['success' => $inserted]);
        } else {
            //back to table view
            redirect('project/create');
        }
    }

    function get_topics() {
        $r = $this->db
                ->where('UPPER(topic_name) LIKE', '%' . strtoupper($this->input->get('term', true)) . '%')
                ->get('topics')
                ->result_array();
        echo json_encode($r);
    }

}
