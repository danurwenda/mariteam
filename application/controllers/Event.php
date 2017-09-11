<?php

defined('BASEPATH') OR exit('No direct script access allowed');
/* to Change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Event extends Module_Controller {

    function __construct() {
        parent::__construct(1);
        $this->load->model('events_model');
        $this->load->model('documents_model');
        $this->load->library('Datatables');
    }

    /**
     */
    function index() {
        $data['pagetitle'] = 'Event';
        $data['admin'] = $this->logged_user->role_id == 1;
        $this->template->display('event_table', $data);
    }

    function submit() {
        $this->events_model->create(
                //logged user
                $this->logged_user->user_id,
                //assigned to
                $this->input->post('pic'),
                //name
                $this->input->post('name'),
                //dates (adjust the format to comply SQL datetime format)
                date_format(date_create($this->input->post('start_time')), "Y-m-d H:i:s"), date_format(date_create($this->input->post('end_time')), "Y-m-d H:i:s"),
                //description
                $this->input->post('description'),
                //location
                $this->input->post('location'),
                //related projects
                $this->input->post('projects')
        );
        redirect('event');
    }

    function create() {
        if ($this->logged_user->role_id != 1) {
            //forbidden
            redirect('event');
        }
        $data['pagetitle'] = 'Add Event';
        $data['admin'] = $this->logged_user->role_id == 1;
        $data['users'] = $this->db->get('persons')->result();

        $this->template->display('event_form', $data);
    }

    function update() {
        $this->events_model->update(
                //event id
                $this->input->post('event_id'),
                //assigned to
                $this->input->post('pic'),
                //name
                $this->input->post('name'),
                //dates (adjust the format to comply SQL datetime format)
                date_format(date_create($this->input->post('start_time')), "Y-m-d H:i:s"), date_format(date_create($this->input->post('end_time')), "Y-m-d H:i:s"),
                //description
                $this->input->post('description'),
                //location
                $this->input->post('location'),
                //related projects
                $this->input->post('projects')
        );
        redirect('event');
    }

    function edit($event_id) {
        $event = $this->events_model->get_event($event_id);
        if ($event) {
            $data['admin'] = $this->logged_user->role_id == 1;
            $pic = $this->users_model->get_person($event->pic);
            $user_pic = $this->users_model->get_user_by_person($pic->person_id);
            $data['owner'] = $user_pic && $this->logged_user->user_id == $user_pic->user_id;
            $data['pagetitle'] = 'Edit Event';
            $data['event'] = $event;
            $data['users'] = $this->db->get('persons')->result();
            $this->template->display('event_form', $data);
        } else {
            //event not found
            redirect('event');
        }
    }

    /**
     * TODO : check permission
     */
    function delete() {
        if ($this->logged_user->role_id == 1) {
            $event_id = $this->input->post('event_id');

            //delete all event documents
            foreach ($this->documents_model->get_documents('events.reports', $event_id) as $doc) {
                $this->documents_model->delete($doc->document_id);
            }
            foreach ($this->documents_model->get_documents('events.materials', $event_id) as $doc) {
                $this->documents_model->delete($doc->document_id);
            }
            echo json_encode(['success' => $this->db->delete('events', ['event_id' => $event_id])]);
        }
    }

    function events_dt() {
        if ($this->input->is_ajax_request()) {
            echo $this->events_model->get_dt();
        }
    }

    function docs_dt() {
        if ($this->input->is_ajax_request()) {
            $prid = $this->input->post('event_id');
            $type = $this->input->post('type');
            $event = $this->events_model->get_event($prid);
            $event_owner = $this->users_model->get_person($event->pic);
            $docs = $this->events_model->get_docs_dt($prid, $type);
            $decoded = json_decode($docs);
            foreach ($decoded->data as &$doc) {
                // add info about permission to delete
                // user may delete a file only if he is
                $doc[] = // the owner of this document
                        ($doc[5] === $this->logged_user->user_id) ||
//event owner
                        ($event_owner->person_id == $this->logged_user->person_id) ||
// or an admin
                        ($this->logged_user->role_id == 1);
            }
            echo json_encode($decoded);
        }
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
                    //event
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

        if ($can_delete) {
            echo json_encode($this->documents_model->delete($document_id));
        } else {
            echo json_encode(['error' => 'deleting document failed']);
        }
    }

}
