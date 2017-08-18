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
        $data['admin'] = $this->logged_user->role_id == 1;
        $this->template->display('project_table', $data);
    }

    function get_stats() {
        $this->db->select('name,count(project_id) as total')
                ->join('project_statuses', 'project_statuses.status_id=projects.project_status')
                ->group_by('name')
        ;
        $q = $this->db->get('projects');

        echo json_encode($q->result());
    }

    function get_task($task_id) {
        $this->db->select('user_name,task_name,task_id,description,is_done,assigned_to,weight,due_date')
                ->join('users', 'users.user_id=tasks.assigned_to');
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
            'user' => $this->logged_user->user_name
        ]);
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
                'self' => ($this->logged_user->user_id === $comment->user_id),
                'content' => $comment->content,
                'time' => $comment->time
            ];
        }
        echo json_encode($comments);
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

    function delete_task() {
        $task_id = $this->input->post('task_id');
        echo json_encode(['success' => $this->db->delete('tasks', ['task_id' => $task_id])]);
    }

    function delete_doc() {
        $doc_id = $this->input->post('doc_id');
        //find doc
        $doc = $this->projects_model->get_document($doc_id);
        if ($doc) {
            $this->load->helper('file');
            //delete from disk
            $path = './uploads/' . $doc->dir;
            echo json_encode(['disk' => (@unlink($path . '/' . $doc->filename) && @rmdir($path) ),
                //delete from db
                'db' => $this->db->where('document_id', $doc_id)->delete('documents')]);
        }
    }

    function projects_dt() {
        echo json_encode($this->query_projects(true));
    }

    private function query_projects($all) {
        if ($this->input->is_ajax_request()) {

            $this->datatables
                    ->distinct('projects.project_id')
                    // additional field to search into : project description, task name, task description
                    ->add_search_column(['projects.description', 'tasks.description', 'task_name'])
                    ->select('project_name,user_name, project_status,projects.due_date, projects.project_id')
                    ->join('tasks', 'tasks.project_id=projects.project_id', 'left')
                    ->join('users', 'users.user_id=projects.assigned_to', 'left')
                    ->from('projects');
            $json = $this->datatables->generate(); //already in json form
            $ret = json_decode($json);
            // it's an array inside an object so we have to use reference
            foreach ($ret->data as &$row) {
                $prid = $row[4];

                //calculate progress            
                $this->db->select_sum('weight');
                $weight_sum = $this->db->get_where('tasks', ['project_id' => $prid]);
                $project_weight = $weight_sum->row()->weight;
                if ($project_weight > 0) {
                    //calculate the total weight of those tasks done
                    $this->db->select_sum('weight');
                    $done_sum = $this->db->get_where('tasks', ['project_id' => $prid, 'is_done' => true]);
                    $progress = round(10000 * ($done_sum->row()->weight) / $project_weight) / 100;
                } else {
                    $progress = -1;
                }
                $row[4] = $progress;
                $row[] = $prid;
            }
            return $ret;
        }
    }

    function docs_dt() {
        echo $this->query_docs(true);
    }

    private function query_docs($all) {
        if ($this->input->is_ajax_request()) {
            $project_id = $this->input->post('project_id');
            if (!$all && $this->logged_user->role_id != 1) {
                //check whether current user has access to this particular project
                $this->db
                        ->or_group_start()
                        ->where([
                            'projects.assigned_to' => $this->logged_user->user_id
                        ])
                        ->or_where([
                            'tasks.assigned_to' => $this->logged_user->user_id
                        ])
                        ->group_end()
                        ->join('tasks', 'tasks.project_id=projects.project_id', 'left')
                        ->where(['projects.project_id' => $project_id]);
                $q = $this->db->get('projects');
                if ($q->num_rows() == 0) {
                    return json_encode([]);
                }
            }
            $this->datatables
                    ->where('project_id', $project_id)
                    ->select('filename,size,created_at,document_id,dir')
                    ->from('documents');
            return $this->datatables->generate();
        }
    }

    function tasks_dt() {
        echo $this->query_tasks(true);
    }

    private function query_tasks($all) {
        if ($this->input->is_ajax_request()) {
            $project_id = $this->input->post('project_id');
            if (!$all && $this->logged_user->role_id != 1) {
                //check whether current user has access to this particular project
                $this->db
                        ->or_group_start()
                        ->where([
                            'projects.assigned_to' => $this->logged_user->user_id
                        ])
                        ->or_where([
                            'tasks.assigned_to' => $this->logged_user->user_id
                        ])
                        ->group_end()
                        ->join('tasks', 'tasks.project_id=projects.project_id', 'left')
                        ->where(['projects.project_id' => $project_id]);
                $q = $this->db->get('projects');
                if ($q->num_rows() == 0) {
                    return json_encode(['q' => $this->db->last_query()]);
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
        $data['pagetitle'] = 'Add Project';
        $data['admin'] = $this->logged_user->role_id == 1;
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
        $project = $this->projects_model->get_project($project_id);
        $data['project'] = $project;
        $data['users'] = $this->db->get('users')->result();
        $data['topics'] = $this->db->get('topics')->result();
        $data['admin'] = $this->logged_user->role_id == 1;
        $data['owner'] = $this->logged_user->user_id == $project->assigned_to;
        $data['statuses'] = $this->db->get('project_statuses')->result();
        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', 'Name', 'required');
        $this->form_validation->set_rules('due_date', 'Due Date', 'required');
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
        if (!$project) {
            //project not found
            redirect('project');
        } else {
            $data['admin'] = $this->logged_user->role_id == 1;
            $data['owner'] = $this->logged_user->user_id == $project->assigned_to;
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

    function uploads($project_id) {
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
            if (isset($_GET["done"])) {
                $result = $uploader->combineChunks("uploads");
            }
            // Handles upload requests
            else {
                // Call handleUpload() with the name of the folder, relative to PHP's getcwd()
                $result = $uploader->handleUpload("uploads");

                // To return a name used for uploaded file you can use the following line.
                $result["uploadName"] = $uploader->getUploadName();
            }
            if ($result['success']) {
                //insert to db
                $result['inserted'] = $this->projects_model->add_document(
                        //current user
                        $this->logged_user->user_id,
                        //project
                        $project_id,
                        //uuid
                        $result['uuid'],
                        //file name
                        $result['uploadName'],
                        //size
                        $uploader->getUploadSize()
                );
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

        if (isset($_POST["_method"]) && $_POST["_method"] != null) {
            return $_POST["_method"];
        }

        return $_SERVER["REQUEST_METHOD"];
    }

}
