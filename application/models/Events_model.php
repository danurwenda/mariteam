<?php

defined('BASEPATH') OR
        exit('No direct script access allowed');
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * An event takes place on a specific time and location, and might be associated with a Project.
 *
 * @author Administrator
 */
class Events_model extends CI_Model {

    public $table = 'events';
    public $primary_key = 'event_id';

    public function __construct() {
        parent::__construct();
    }

    public function create(
    //logged user
    $user_id,
    //assigned to
            $assigned_to,
    //name
            $name,
    //dates (adjust the format to comply SQL datetime format)
            $start_date, $end_date,
    //description
            $description,
    //location
            $location,
    //projects
            $projects) {
        $this->db->insert($this->table, [
            'created_by' => $user_id,
            'pic' => $assigned_to,
            'event_name' => $name,
            'start_time' => $start_date,
            'end_time' => $end_date,
            'description' => $description,
            'location' => $location
        ]);

        //associate with projects

        $pid = $this->db->insert_id();
        $this->set_projects($pid, $projects);
    }

    public function update(//event id
    $ev_id,
    //assigned to
            $assigned_to,
    //name
            $name,
    //dates (adjust the format to comply SQL datetime format)
            $start_date, $end_date,
    //description
            $description,
    //location
            $location,
    //projects
            $projects) {
        $this->db->where($this->primary_key, $ev_id);
        $this->db->update($this->table, [
            'pic' => $assigned_to,
            'event_name' => $name,
            'start_time' => $start_date,
            'end_time' => $end_date,
            'description' => $description,
            'location' => $location
        ]);
        $this->set_projects($ev_id, $projects);
    }

    public function set_projects($pid, $projs) {
        //clear previous set of event
        $this->db->where('event_id', $pid);
        $this->db->delete('project_event');
        //insert new
        foreach ($projs as $projid) {
            $this->db->insert('project_event', [
                'event_id' => $pid,
                'project_id' => $projid
            ]);
        }
    }

    public function get_dt() {
        $this->datatables
                ->add_search_column(['description'])
                ->select('event_name, person_name, start_time, location, event_id')
                ->join('persons', 'persons.person_id=events.pic', 'left')
                ->from('events');
        return $this->datatables->generate();
    }

    /**
     * Retrieves information about the event and related projects
     * @param type $id event id
     */
    public function get_event($id) {
        $this->db
                ->join('persons', 'persons.person_id=events.pic')
                ->where($this->primary_key, $id)->limit(1);
        $q = $this->db->get($this->table);
        if ($q->num_rows() > 0) {
            $p = $q->row();
            //add topics
            $p->projects = [];
            $p->project_ids = [];
            $this->db->join('projects', 'projects.project_id=project_event.project_id');
            $projects = $this->db->get_where('project_event', ['event_id' => $p->event_id]);
            foreach ($projects->result() as $pt) {
                $p->project_ids[] = $pt->project_id;
                $p->projects[$pt->project_id] = $pt->project_name;
            }
            return $p;
        } else {
            return false;
        }
    }

    public function get_docs_dt($project_id, $type) {
        $this->datatables
                ->where('source_id', $project_id)
                ->where('source_table', 'events.' . $type)
                ->select('filename,size,created_at,document_id,dir,created_by')
                ->from('documents');
        return $this->datatables->generate();
    }

}
