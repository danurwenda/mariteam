<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Tools extends CI_Controller {

    function __construct() {
        parent::__construct();
    }

    /**
     * Sebenarnya ini buat ganti status project di tengah malam
     * @param type $to
     */
    public function message() {
        if (is_cli()) {
            $this->db->query("update `projects` set project_status=3 WHERE date(now())>end_date and project_status=1");
        }
    }

}
