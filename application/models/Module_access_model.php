<?php

defined('BASEPATH')OR
        exit('No direct script access allowed');
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Access_model
 *
 * @author Administrator
 */
class Module_access_model extends CI_Model {

    public $table = 'module_access';
    public $primary_key = 'module_access_id';

    public function __construct() {
        parent::__construct();
    }

    public function get_displayed_modules($role_id) {
        return $this->db
                        ->join('modules', 'modules.module_id=module_access.module_id')
                        ->where(
                                [
                                    'role_id' => $role_id,
                                    'can_access' => true
                                ]
                        )
                        ->get($this->table)->result();
    }

    public function can_access($role_id, $module_id) {
        $q = $this->db
                ->get_where(
                $this->table, [
            'role_id' => $role_id,
            'module_id' => $module_id,
            'can_access' => true
                ]
        );
        return ($q->num_rows() > 0);
    }

}
