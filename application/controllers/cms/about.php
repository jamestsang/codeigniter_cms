<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require_once(APPPATH . 'core/AdminController.php');

class About extends AdminController {

    public function __construct() {
        parent::__construct("about");
        $this->form_class = "about";
        $this->page_name = "About Us";
        $this->add_page_link = "about/content/add";
        $this->edit_page_link = "about/content/edit";
        $this->list_page_link = "about/index";
        $this->link_path[] = "about";
        $this->multi_language = true;
    }

    public function index($id = 0) {
        $config["canAdd"] = true;
        $config["canSort"] = true;
        $config["list_setting"] = array(
            "ID" => array(
                "type" => "field",
                "field" => $this->table . "_id",
                "sort"=>true
            ),
            "Title" => array(
                "type" => "field",
                "field" => "title",
                "sort"=>true,
                "url" => array(
                    "href" => $this->edit_page_link . "/%s?",
                    "params" => array($this->table . "_id")
                )
            ),
            "Ordering"=>array(
                "type"=>"ordering",
                "mobile"=>false
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
                    ),
                    array(
                        "type" => "delete",
                        "url" => array(
                            "href" => $this->list_page_link . "?action=delete&id=%s",
                            "params" => array($this->table . "_id")
                        ),
                    )
                )
            )
        );
        $this->_list($config);
    }

    public function content($action = "add", $id = 0) {
        $this->_post_back($action, $id);
        $config["custom_field"] = array(
            "content"=>["class"=>"editor"],
            "title"=>array("class"=>"valid"),
            "banner_id"=>array("field"=>"file", "title"=>"Banner")
        );
        $config["hiddenArr"] = array();
        $config["showArr"] = array();
        $config["seqArr"] = array();
        $config["shareElement"] = array("banner_id");
        $config["extend_field"] = array();
        $config["media"] = array(
            // "Banner" => array(
            //     "section" => "about",
            //     "id" => $id,
            //     "folder" => "image",
            //     "limit"=>1,
            // )
        );

        if($action == "add"){
            $config["hiddenArr"][]="alias";
        }
        
        $data = $this->_form_process($action, $id);
        
        $this->_form($data, $config);
    }

    protected function _listFilter($config = Array()){
        $config = array(
            "title"=>array(
                "field"=>"text"
            )
        );
        return parent::_listFilter($config);
    }
}
