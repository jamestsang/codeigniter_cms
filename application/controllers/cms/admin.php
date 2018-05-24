<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require_once(APPPATH . 'core/AdminController.php');

class Admin extends AdminController {

    public function __construct() {
        parent::__construct("admin");
        $this->form_class = "admin";
        $this->page_name = "Administrator";
        $this->add_page_link = "admin/content/add";
        $this->edit_page_link = "admin/content/edit";
        $this->list_page_link = "admin/index";
        $this->link_path[] = "admin";
    }

    public function index() {
        if($this->session->userdata("is_admin")!=1){
            redirect(base_url("cms/admin/profile"));
        }
        $config["canAdd"] = true;
        $config["canSort"] = false;
        $config["list_setting"] = array(
            "ID" => array(
                "type" => "field",
                "field" => $this->table . "_id",
                "sort"=>true
            ),
            "Name" => array(
                "type" => "field",
                "field" => "title",
                "sort"=>true
            ),
            "Email" => array(
                "type" => "field",
                "field" => "email",
                "sort"=>true
            ),
            "Actions" => array(
                "type" => "action",
                "actions" => array(
                    array(
                        "type" => "edit",
                        "url" => array(
                            "href" => $this->edit_page_link . "/%s?",
                            "params" => array($this->table . "_id")
                        ),
                        "exclude" => array($this->table . "_id" => array(1))
                    ),
                    array(
                        "type" => "delete",
                        "url" => array(
                            "href" => $this->list_page_link . "?action=delete&id=%s",
                            "params" => array($this->table . "_id")
                        ),
                        "exclude" => array($this->table . "_id" => array(1))
                    )
                )
            )
        );
        $this->_getModel()->setState(array(
                "type"=>"admin"
            ));
        $this->_list($config);
    }

    public function content($action = "add", $id = 0) {
        if($this->session->userdata("is_admin")!=1){
            redirect(base_url("cms/admin/profile"));
        }
        $this->_post_back($action, $id);
        $config["custom_field"] = array(
            "email"=>array("field"=>"textbox", "box_type"=>"email", "class"=>"valid"),
            "password"=>array("field"=>"textbox", "box_type"=>"password", "class"=>"valid"),
            "confirm_password"=>array("field"=>"textbox", "box_type"=>"password", "class"=>"valid"),
            //"username"=>array("class"=>"valid"),
            "title"=>array("class"=>"valid", "title"=>"Name"),
        );
        $config["hiddenArr"] = array("hash", "new_password", "salt", "type", "layout", "username", "language");
        $config["showArr"] = array();
        $config["seqArr"] = array("email", "password", "confirm_password", "title");
        $config["shareElement"] = array();
        $config["extend_field"] = array("confirm_password");
        if ($action == "edit") {
            $config["custom_field"]["password"]["class"] = "alt_valid";
            $config["custom_field"]["confirm_password"]["class"] = "alt_valid";
            $config["custom_field"]["email"]["class"] = "";
            $config["custom_field"]["email"]["alt"] = "disabled";
        }
        $data = $this->_form_process($action, $id);
        
        $this->_form($data, $config);
    }
    
    public function profile(){
        $action = "edit";
        $id = $this->session->userdata("my_id");
        $this->edit_page_link = "admin/profile";
        $this->profile_post_back($action, $id);
        $config["custom_field"] = array(
            "email"=>array("field"=>"textbox", "box_type"=>"email", "class"=>"valid"),
            "password"=>array("field"=>"textbox", "box_type"=>"password", "class"=>"valid", "title"=>"New Password"),
            "old_password"=>array("field"=>"textbox", "box_type"=>"password", "class"=>"alt_valid"),
            "confirm_password"=>array("field"=>"textbox", "box_type"=>"password", "class"=>"valid"),
            //"username"=>array("class"=>"valid"),
            "title"=>array("class"=>"valid", "title"=>"Name"),
        );
        $config["hiddenArr"] = array("hash", "new_password", "salt", "type", "layout", "username", 'language');
        $config["showArr"] = array();
        $config["seqArr"] = array("email", "old_password", "password", "confirm_password", "title");
        $config["shareElement"] = array();
        $config["extend_field"] = array("confirm_password", "old_password");
        if ($action == "edit") {
            $config["custom_field"]["password"]["class"] = "alt_valid";
            $config["custom_field"]["confirm_password"]["class"] = "alt_valid";
            $config["custom_field"]["email"]["class"] = "";
            $config["custom_field"]["email"]["alt"] = "disabled";
        }
        $data = $this->_form_process($action, $id);
        $this->_form($data, $config);
    }

    public function profile_post_back($action, $id){
        if ($this->input->post("post_back")) {
            $model = $this->_getModel();
            $old_password = $_POST["old_password"];
            if($old_password != ""){
                $data = $model->getById($id);
                if(!$model->check_password($data[0]["password"], $old_password, $data[0]["salt"])){
                    $msg = CMS::msg('warning', "Warning! ", 'Password incorrect.');
                    $this->session->set_flashdata('warning_msg', $msg);
                    redirect(base_url($this->alias."/admin/profile"));
                }
            }
        }

        parent::_post_back($action, $id);
    }
    
    public function _post_back($action = "add", $id = 0) {
        if($this->input->post("post_back")){
            $this->session->set_userdata('layout', $this->input->post("layout"));
        }
        parent::_post_back($action, $id);
    }

}
