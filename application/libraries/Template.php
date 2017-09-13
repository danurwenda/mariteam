<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * This Admin_template library was created specifically to minimize code redundancy
 * in determining modules that can be accessed by the logged in user
 *
 * @author Administrator
 */
class Template {

    protected $_ci;
    private $user;

    /**
     * User role is passed to the constructor since this class determines 
     * what is displayed/not displayed based on current role.
     * @param type $params an array containing logged_user role
     */
    public function __construct($params) {
        $this->_ci = &get_instance();
        $this->_ci->load->model('module_access_model', 'mpm');
        $this->user = $params['user'];
    }

    function display($template, $data = null) {
        //get top menus from mpm
        //by default, no menus are displayed
        //only logout menu
        $menus = $this->_ci->mpm->get_displayed_modules($this->user->role_id);

        $data['menus'] = $menus;
        $data['_loggeduser'] = $this->user;
        $data['_content'] = $this->_ci->load->view('front/' . $template, $data, true);
        $this->_ci->load->view('front/template2.php', $data);
    }

}
