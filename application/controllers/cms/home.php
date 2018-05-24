<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require_once(APPPATH . 'core/AdminController.php');

class Home extends AdminController {

    public function __construct() {
        parent::__construct("admin");
    }

    public function index() {
        $admin_list = $this->config->item("cms");
        if($this->session->userdata("is_admin")!=1){
            unset($admin_list["admin_list"]["Admin"]);
        }
        $this->load->view("cms/home", array("list"=>$admin_list["admin_list"]));
    }

    public function content($action = "add", $id = 0) {
        $this->index();
    }

    public function language(){
        if($this->session->userdata("language") == "english"){
            $this->session->set_userdata("language", "traditional");
        }else{
            $this->session->set_userdata("language", "english");
        }
        $this->load->model('admin_model', 'admin');
        $this->admin->update(array("language"=>$this->session->userdata("language"), "password"=>""), $this->session->userdata("my_id"));
        redirect(base_url($this->alias."/home"));
    }

}
