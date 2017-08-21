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
class Documents_model extends CI_Model {

    public $table = 'documents';
    public $primary_key = 'document_id';

    public function __construct() {
        parent::__construct();
    }

    public function add_document($user, $source, $source_id, $uuid, $filename, $size) {
        return $this->db->insert($this->table, [
                    'dir' => $uuid,
                    'filename' => $filename,
                    'source_table' => $source,
                    'source_id' => $source_id,
                    'created_by' => $user,
                    'size' => $size
        ]);
    }

    public function get_document($doc_id) {
        $this->db->where($this->primary_key, $doc_id);
        $q = $this->db->get($this->table);
        if ($q->num_rows() > 0) {
            return $q->row();
        } else
            return null;
    }

    public function get_documents($source, $source_id) {
        $this->db->where('source_table', $source)
                ->where('source_id', $source_id);
        return $this->db->get($this->table)->result();
    }

    public function del_document($doc_id) {
        return $this->db->where($this->primary_key, $doc_id)->delete($this->table);
    }

}
