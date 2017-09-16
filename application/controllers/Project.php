<?php

defined('BASEPATH') OR exit('No direct script access allowed');
/* to Change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Project extends Module_Controller {

    function __construct() {
        parent::__construct(1);
        $this->load->model('projects_model');
        $this->load->model('documents_model');
        $this->load->library('Datatables');
    }

    /**
     */
    function index() {
        $data['pagetitle'] = 'Project';
        $data['active_menu'] = 1;
        $data['topics'] = $this->db->get('topics')->result();
        $data['admin'] = $this->logged_user->role_id == 1;
        $this->template->display('project_table', $data);
    }

    function get_stats() {
        echo json_encode($this->projects_model->get_chart_data());
    }

    function get_task($task_id) {
        $this->db->select('person_name,task_name,task_id,description,status,assigned_to,weight,end_date,start_date')
                ->join('persons', 'persons.person_id=tasks.assigned_to');
        echo json_encode($this->db->get_where('tasks', ['task_id' => $task_id])->row());
    }

    function add_task_comment() {
        $task_id = $this->input->post('task_id');
        $content = $this->input->post('content');
        $this->db->insert('task_comments', [
            'user_id' => $this->logged_user->user_id,
            'content' => $content,
            'task_id' => $task_id
        ]);
        $last_id = $this->db->insert_id();
        $cmt = $this->db->get_where('task_comments', ['task_comment_id' => $last_id])->row();
        echo json_encode([
            'self' => true,
            'content' => $content,
            'time' => $cmt->time,
            'user' => $this->logged_user->person_name
        ]);
    }

    function get_task_docs($task_id) {
        $task = $this->projects_model->get_task($task_id);
        $task_owner_person = $this->users_model->get_person($task->assigned_to);
        // can delete as the owner of the task
        $can_delete = ($this->logged_user->person_id == $task_owner_person->person_id);
        if (!$can_delete) {
            $project = $this->projects_model->get_project($task->project_id);
            $project_owner_person = $this->users_model->get_person($project->assigned_to);
            // can delete as the owner of the project
            $can_delete = ($this->logged_user->person_id == $project_owner_person->person_id);
        }
        $docs = $this->documents_model->get_documents('tasks', $task_id);
        foreach ($docs as $d) {
            $d->self = $can_delete ||
// or he created this document
                    ($d->created_by === $this->logged_user->user_id) ||
// or an admin
                    ($this->logged_user->role_id == 1);
        }
        echo json_encode($docs);
    }

    public function get_timeline() {
        $project_id = $this->input->get('project_id');
        $project = $this->projects_model->get_project($project_id);
        $project_owner_person = $this->users_model->get_person($project->assigned_to);
        // can delete as the owner of the project
        $can_delete = ($this->logged_user->person_id == $project_owner_person->person_id);

        $timeline = $this->projects_model->get_tasks_timeline($project_id);
        //can write if admin or project owner
        $timeline['canWrite'] = ($this->logged_user->role_id == 1) || $can_delete;
        $ret = [
            'ok' => true,
            'project' => $timeline
        ];
        echo json_encode($ret);
    }

    public function save_timeline() {
        //preparing data
        $statmap = [];
        foreach ($this->db->get('project_statuses')->result() as $stat) {
            $statmap[$stat->name] = $stat->status_id;
        }
        $proj_id = $this->input->post('project_id');
        $json = $this->input->post('timeline');
        $proj = json_decode($json);
        // do save..
        foreach ($proj->tasks as $i => $task) {
            if ($task->level === 0) {
                $update = [
                    'project_status' => $statmap[$task->status],
                    //convert millisecond to date
                    'start_date' => date("Y-m-d", ($task->start / 1000)),
                    'end_date' => date("Y-m-d", ($task->end / 1000)),
                ];
                $project_id = $task->id;
                $this->db->where('project_id', $task->id);
                $this->db->update('projects', $update);
            } else {
                //only level, dates, progress
                //status, order and dependency might be changed
                $update = [
                    'task_order' => $i,
                    'progress' => ($task->progress) / 100,
                    'level' => $task->level,
                    'start_date' => date("Y-m-d", ($task->start / 1000)),
                    'end_date' => date("Y-m-d", ($task->end / 1000)),
                    'depends' => $task->depends,
                    'status' => $statmap[$task->status],
                    'startIsMilestone' => $task->startIsMilestone,
                    'endIsMilestone' => $task->endIsMilestone
                ];
                $this->db->where('task_id', $task->id);
                $this->db->update('tasks', $update);
            }
        }
        // reload
        $ret = [
            'ok' => true,
            'project' => $this->projects_model->get_tasks_timeline($project_id, true)
        ];
        echo json_encode($ret);
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
                'self' => ($this->logged_user->user_id === $comment->user_id),
                'content' => $comment->content,
                'time' => $comment->time
            ];
        }
        echo json_encode($comments);
    }

    /**
     * TODO : check permission
     */
    function edit_task() {
        if ($this->input->is_ajax_request()) {
            $project_id = $this->input->post('project_id');
            $task_name = $this->input->post('task_name');
            $desc = $this->input->post('desc');
            $user = $this->input->post('assigned_to');
            $date = date_format(date_create($this->input->post('start_date')), "Y-m-d");
            $end_date = date_format(date_create($this->input->post('end_date')), "Y-m-d");
            $weight = $this->input->post('weight');
            $status = $this->input->post('task_status');
            $task_id = $this->input->post('task_id');
            if (empty($task_id)) {
                $change = $this->projects_model->add_task(
                        $project_id, $task_name, $desc, $date, $end_date, $this->logged_user->user_id, $user, $weight, 1
                );
                echo json_encode([
                    'success' => $change,
                    'task_id' => $this->db->insert_id()
                ]);
            } else {
                //edit
                $change = $this->projects_model->edit_task($task_id, $task_name, $desc, $date, $end_date, $user, $weight, $status
                );
                echo json_encode(['success' => $change]);
            }
        } else {
            //back to table view
            redirect('project/create');
        }
    }

    /**
     * TODO : check permission
     */
    function delete() {
        if ($this->logged_user->role_id == 1) {
            $project_id = $this->input->post('project_id');
            // find all associated tasks
            $tasks = $this->db->select('task_id')->get_where('tasks', ['project_id' => $project_id]);
            foreach ($tasks->result() as $task) {
                //delete all associated documents
                $docs = $this->documents_model->get_documents('tasks', $task->task_id);
                foreach ($docs as $doc) {
                    $this->documents_model->delete($doc->document_id);
                }
            }
            //delete all project documents
            foreach ($this->documents_model->get_documents('projects', $project_id) as $doc) {
                $this->documents_model->delete($doc->document_id);
            }
            echo json_encode(['success' => $this->db->delete('projects', ['project_id' => $project_id])]);
        }
    }

    /**
     * TODO : check permission
     */
    function delete_task() {
        $task_id = $this->input->post('task_id');
        //delete all associated documents
        $docs = $this->documents_model->get_documents('tasks', $task_id);
        foreach ($docs as $doc) {
            $this->documents_model->delete($doc->document_id);
        }
        echo json_encode(['success' => $this->db->delete('tasks', ['task_id' => $task_id])]);
    }

    function delete_doc() {
        $document_id = $this->input->post('doc_id');
        //find doc
        $doc = $this->documents_model->get_document($document_id);
        $can_delete = // the owner
                $doc && $this->logged_user->user_id === $doc->created_by;
        if (!$can_delete) {
// or an admin
            $can_delete = ($this->logged_user->role_id == 1);
        }
        // or he is the owner of associated project/tasks
        if (!$can_delete) {
            if ($doc->source_table == 'tasks') {
                $task = $this->projects_model->get_task($doc->source_id);
                $task_owner_person = $this->users_model->get_person($task->assigned_to);
                // can delete as the owner of the task
                $can_delete = ($this->logged_user->person_id == $task_owner_person->person_id);
            }
            // last resort
            if (!can_delete || $doc->source_table == 'projects') {
                $project = $this->projects_model->get_project(empty($task) ? $doc->source_id : $task->project_id);
                $project_owner = $this->users_model->get_person($project->assigned_to);
                $can_delete = ($project_owner->user_id == $this->logged_user->user_id);
            }
        }
        if ($can_delete) {
            echo json_encode($this->documents_model->delete($document_id));
        } else {
            echo json_encode(['error' => 'deleting document failed']);
        }
    }

    function projects_dt() {
        if ($this->input->is_ajax_request()) {
            echo $this->projects_model->get_dt();
        }
    }

    function docs_dt() {
        if ($this->input->is_ajax_request()) {
            $prid = $this->input->post('project_id');
            $project = $this->projects_model->get_project($prid);
            $project_owner = $this->users_model->get_person($project->assigned_to);
            $docs = $this->projects_model->get_docs_dt($prid);
            $decoded = json_decode($docs);
            foreach ($decoded->data as &$doc) {
                // add info about permission to delete
                // user may delete a file only if he is
                $doc[] = // the owner of this document
                        ($doc[5] === $this->logged_user->user_id) ||
//project owner
                        ($project_owner->person_id == $this->logged_user->person_id) ||
// or an admin
                        ($this->logged_user->role_id == 1);
            }
            echo json_encode($decoded);
        }
    }

    function tasks_dt() {
        if ($this->input->is_ajax_request()) {
            echo $this->projects_model->get_tasks_dt($this->input->post('project_id'));
        }
    }

    function create() {
        $data['active_menu'] = 1;
        if ($this->logged_user->role_id != 1) {
            //forbidden
            redirect('project');
        }
        $data['pagetitle'] = 'Add Project';
        $data['admin'] = $this->logged_user->role_id == 1;
        $data['users'] = $this->db->get('persons')->result();
        $data['groups'] = $this->db->get('groups')->result();
        $data['topics'] = $this->db->get('topics')->result();
        $data['statuses'] = $this->db->get('project_statuses')->result();
        $this->load->library('form_validation');
        $this->form_validation->set_rules('assigned_to', 'Assigned User', 'required');
        $this->form_validation->set_rules('name', 'Display Name', ['trim', 'required', 'strip_tags']);
        $this->form_validation->set_rules('end_date', 'Due Date', 'required');
        if ($this->form_validation->run() == true) {
            $data['updated'] = true;
            $this->projects_model->create(
                    //logged user
                    $this->logged_user->user_id,
                    //assigned to
                    $this->input->post('assigned_to'),
                    //name
                    $this->input->post('name'),
                    //dates (adjust the format to comply SQL datetime format)
                    date_format(date_create($this->input->post('start_date')), "Y-m-d 00:00:00"), date_format(date_create($this->input->post('end_date')), "Y-m-d 23:59:59"),
                    //description
                    $this->input->post('description'),
                    //topic
                    $this->input->post('topics'),
                    //groups
                    $this->input->post('groups')
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
        $project = $this->projects_model->get_project($project_id);
        $data['project'] = $project;
        $data['users'] = $this->db->get('persons')->result();
        $data['groups'] = $this->db->get('groups')->result();
        $data['topics'] = $this->db->get('topics')->result();
        $data['admin'] = $this->logged_user->role_id == 1;
        $data['owner'] = $this->logged_user->user_id == $project->assigned_to;
        $data['statuses'] = $this->db->get('project_statuses')->result();
        $data['active_menu'] = 1;
        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', 'Name', 'required');
        $this->form_validation->set_rules('end_date', 'Due Date', 'required');
        if ($data['admin']) {
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
                    //dates (adjust the format to comply SQL datetime format)
                    date_format(date_create($this->input->post('start_date')), "Y-m-d"), date_format(date_create($this->input->post('end_date')), "Y-m-d"),
                    //description
                    $this->input->post('description'),
                    //topic
                    $this->input->post('topics'),
                    //status
                    $this->input->post('status'),
                    //groups
                    $this->input->post('groups')
            );
            //redirect to edit
            redirect('project/edit/' . $project_id);
        } else {
            $this->template->display('project_form', $data);
        }
    }

    function edit($project_id) {
        $data['active_menu'] = 1;
        $project = $this->projects_model->get_project($project_id);
        if ($project) {
            $data['admin'] = $this->logged_user->role_id == 1;
            $pic = $this->users_model->get_person($project->assigned_to);
            $user_pic = $this->users_model->get_user_by_person($pic->person_id);
            $data['owner'] = $user_pic && $this->logged_user->user_id == $user_pic->user_id;
            $data['pagetitle'] = 'Edit Project';
            $data['project'] = $project;
            $data['groups'] = $this->db->get('groups')->result();
            $data['users'] = $this->db->get('persons')->result();
            $data['topics'] = $this->db->get('topics')->result();
            $data['statuses'] = $this->db->get('project_statuses')->result();
            $this->template->display('project_form', $data);
        } else {
            //project not found
            redirect('project');
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
        echo json_encode($this->projects_model->get_topics());
    }

    function get_groups() {
        echo json_encode($this->projects_model->get_groups(
                        $this->logged_user->person_id, $this->logged_user->role_id
        ));
    }

    function uploads($source, $source_id = null) {
        $this->load->library('UploadHandler');
        $uploader = new UploadHandler();

// Specify the list of valid extensions, ex. array("jpeg", "xml", "bmp")
        $uploader->allowedExtensions = array(); // all files types allowed by default
// Specify max file size in bytes.
        $uploader->sizeLimit = null;

// Specify the input name set in the javascript.
        $uploader->inputName = "qqfile"; // matches Fine Uploader's default inputName value by default
// If you want to use the chunking/resume feature, specify the folder to temporarily save parts.
        $uploader->chunksFolder = "chunks";

        $method = $this->get_request_method();



        if ($method == "POST") {
            header("Content-Type: text/plain");

            // Assumes you have a chunking.success.endpoint set to point here with a query parameter of "done".
            // For example: /myserver/handlers/endpoint.php?done
            if ($this->input->get("done")) {
                $result = $uploader->combineChunks("uploads");
            }
            // Handles upload requests
            else {
                // Call handleUpload() with the name of the folder, relative to PHP's getcwd()
                $result = $uploader->handleUpload("uploads");

                // To return a name used for uploaded file you can use the following line.
                $result["uploadName"] = $uploader->getUploadName();
            }
            if ($result['success'] &&
                    //insert to db
                    ($result['inserted'] = $this->documents_model->add_document(
                    //current user
                    $this->logged_user->user_id, $source,
                    //project
                    $source_id,
                    //uuid
                    $result['uuid'],
                    //file name
                    $result['uploadName'],
                    //size
                    $uploader->getUploadSize()
                    ))) {
                $result['self'] = true;
                $result['document_id'] = $this->db->insert_id();
            }

            echo json_encode($result);
        }
// for delete file requests
        else if ($method == "DELETE") {
            $result = $uploader->handleDelete("uploads");
            echo json_encode($result);
        } else {
            header("HTTP/1.0 405 Method Not Allowed");
        }
    }

// This will retrieve the "intended" request method.  Normally, this is the
// actual method of the request.  Sometimes, though, the intended request method
// must be hidden in the parameters of the request.  For example, when attempting to
// delete a file using a POST request. In that case, "DELETE" will be sent along with
// the request in a "_method" parameter.
    function get_request_method() {
        global $HTTP_RAW_POST_DATA;

        if (isset($HTTP_RAW_POST_DATA)) {
            parse_str($HTTP_RAW_POST_DATA, $_POST);
        }

        if ($this->input->post("_method")) {
            return $this->input->post("_method");
        }

        return $this->input->server("REQUEST_METHOD");
    }

}
