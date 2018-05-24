<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require_once(APPPATH . 'core/AdminController.php');

Class CategoryController extends AdminController{
    
    protected $extension;
    protected $category_model;


    public function __construct($table, $extension = false) {
        parent::__construct($table);
        if($extension === false){
            $this->extension = $table;
        }else{
            $this->extension = $extension;
        }
    }
    
    //Instance get model
    protected function _getCategoryModel() {
        if (empty($this->category_model)) {
            $this->load->model('category_model', 'cat_model');
            $this->category_model = $this->cat_model;
        }
        $this->category_model->setState("extension", $this->extension);
        return $this->category_model;
    }
    
    //Set Category Link
    protected function _setCatLink($section){
        $this->add_page_link = $section."/category_content/add";
        $this->edit_page_link = $section."/category_content/edit";
        $this->list_page_link = $section."/category";
        $this->ordering_link = $section."/updateCategoryOrdering";
    }
    
    //basic function of index page.
    public function category() {
        $this->_listCat();
    }
    
    //Listing
    //basic function of lising page.
    protected function _listCat($config = array(), $template = "general-list") {
        //Post back
        $this->_catList_post_back();
        //End Post back
        //Require library
        $data = $this->_catList_process();
        $this->load->library("listing");
        //End Require library
        //Listing config user can overrider by param input.
        $init_config["canAdd"] = true;
        $init_config["canSort"] = false;
        $init_config["main_id"] = "category_id";
        $init_config["edit_link"] = "category_content";
        $init_config["list_setting"] = array();
        $init_config["list_setting"][] = array(
            "type" => "field",
            "field" => "id",
            "name" => "ID",
            "link" => "edit_page"
        );
        $init_config["list_setting"][] = array(
            "type" => "edit"
        );
        $init_config["list_setting"][] = array(
            "type" => "delete"
        );
        $this->listing->initialize(array_merge($init_config, $config));
        //End config
        //Output data
        $data["config"] = $config;
        $data["table"] = $this->listing->create_table($data["data"]);
        $this->load->view('cms/' . $template, $data);
    }
    
    protected function _catList_process() {
        //require Library
        $this->load->library('pagination');
        $model = $this->_getCategoryModel();
        //End require Library
        //Page Setting
        if ($this->input->get("show_page")) {
            $record_per_page = $this->input->get("show_page");
        } else {
            if (!$this->session->userdata('per_page')) {
                $record_per_page = 10;
            } else {
                $record_per_page = $this->session->userdata('per_page');
            }
        }
        $this->session->set_userdata('per_page', $record_per_page);
        $page = $this->input->get("page") ? $this->input->get("page") : 1;
        //End Page Setting
        //Language Filter
        if ($this->multi_language) {
            $model->setState("language", 1);
        }
        //End Language Filter
        //Sorting setting
        $sort = $this->input->get("sort");
        $dir = $this->input->get("dir");
        $ordering = $this->input->get("ordering") ? true : false;
        //End Sorting setting
        //Fetch Data
        $total = $model->get_total();
        $result = $model->getAll($page, $record_per_page, $sort, $dir);
        $model->resetState();
        $total_page = ceil($total / $record_per_page);
        //End Fetch Data
        //Page nav Config
        $this->config->load("page_config", true);
        $page_config = $this->config->item("page_config");

        $page_config['base_url'] = $this->input->getFullPath(array("page"));
        $page_config['total_rows'] = $total;
        $page_config['per_page'] = $record_per_page;
        $this->pagination->initialize($page_config);
        $page_container = $this->pagination->create_links();
        //End Page nav Config
        //Output data
        $data = array(
            "data" => $result,
            "page_container" => $page_container,
            "page" => $page,
            "total" => $total,
            "record_per_page" => $record_per_page,
            "ordering" => $ordering,
            "warning_msg" => $this->session->flashdata('warning_msg'),
            "top_addon" => ""
        );
        return $data;
    }

    protected function _catList_post_back() {
        $action = $this->input->get("action");
        if ($action == "switch") {
            $model = $this->_getCategoryModel();
            $id = $this->input->get("id");
            $field = $this->input->get("field");
            if ($field && $id) {
                $result = $model->switcher($field, "category_id", $id);
                if ($result !== false) {
                    $msg = CMS::msg('success', "Success! ", 'Update Success.');
                } else {
                    $msg = CMS::msg('warning', "Warning! ", 'Update failed.');
                }
                $this->session->set_flashdata('warning_msg', $msg);
            }
            redirect($this->input->getFullPath(array("action", "id", "field")));
        } else if ($action == "delete") {
            $model = $this->_getCategoryModel();
            $id = $this->input->get("id");
            if ($id && is_numeric($id)) {
                $result = $model->fakeDelete($id);
                if ($result !== false) {
                    $msg = CMS::msg('success', "Success! ", 'Delete Success.');
                } else {
                    $msg = CMS::msg('warning', "Warning! ", 'Delete failed.');
                }
                $this->session->set_flashdata('warning_msg', $msg);
            }
            redirect($this->input->getFullPath(array("action", "id")));
        }
    }

    public function updateCategoryOrdering() {
        $id_list = $this->input->post("ids");
        $order_list = $this->input->post("ordering");
        if (!empty($id_list)) {
            $model = $this->_getCategoryModel();
            foreach ($id_list as $key => $id) {
                $model->updateOrdering($id, $order_list[$key]);
            }
        }
    }

    //End Listing
    
    // Edit Form
    public function category_content($action = "add", $id = 0) {
        //Post back process.
        $this->_cat_post_back($action, $id);
        //Fetch data
        $data = $this->_cat_form_process($action, $id);
        //Generate form
        $this->_cat_form($data, array());
    }

    protected function _cat_form($data, $config = array(), $template = "general-edit-page") {
        $this->load->library("editing");
        $model = $this->_getCategoryModel();
        $init_config["custom_field"] = array();
        $init_config["hiddenArr"] = array(); //array("field_name");
        $init_config["showArr"] = array(); //array("field_name");
        $init_config["seqArr"] = array(); //array("field_name");
        $init_config["shareElement"] = array(); //array("field_name");
        $init_config["extend_field"] = array(); //array("field_name"=>"Type");
        $init_config["media"] = array(); //array("field_name"=>array("section"=>"", "id"=>"", "folder"=>"", "caption"=>"", limit=>""));
        $this->editing->initialize(array_merge($init_config, $config), $model);
        if ($this->multi_language) {
            $data["form"] = $this->editing->create_form($data["dataArray"], $this->multi_language);
        } else {
            $data["form"] = $this->editing->create_form($data["dataArray"]);
        }
        $this->load->view('cms/' . $template, $data);
    }

    protected function _cat_form_process($action, $id) {
        $model = $this->_getCategoryModel();
        $dataArray = array();
        if ($action == "add") {
            
        } else if ($action == "edit" && $id != 0) {
            $data = $model->getById($id);
            $dataArray = array();
            if (sizeof($data) == 0) {
                $msg = CMS::msg('warning', "Warning! ", 'No such record in database.', $this->list_page_link);
                $this->session->set_flashdata('warning_msg', $msg);
                redirect("cms/" . $this->add_page_link);
            } else {
                if ($this->multi_language) {
                    foreach ($data as $value) {
                        $dataArray[$value["language"]] = $value;
                    }
                } else {
                    $dataArray = $data;
                }
            }
        }

        $data['action'] = $action;
        $data['id'] = $id;
        $data['warning_msg'] = $this->session->flashdata('warning_msg') ? $this->session->flashdata('warning_msg') : $this->internal_msg;
        $data['dataArray'] = $dataArray;

        return $data;
    }

    protected function _cat_post_back($action = "add", $id = 0) {
        if ($this->input->post("post_back")) {
            $model = $this->_getCategoryModel();
            $return = "";
            if ($this->input->get("return")) {
                $return = '?return=' . urlencode($this->input->get("return"));
            }
            $dataArray = array_merge($this->input->post(), array("extension"=>$this->extension));
            if ($action == 'edit') {
                
                if ($this->multi_language) {
                    $result = $model->update($dataArray, $id, $GLOBALS['language_list']);
                } else {
                    $result = $model->update($dataArray, $id);
                }
                if ($result === false || is_array($result)) {
                    $msg = CMS::msg('warning', "Fail!", is_array($result) ? $result["msg"] : 'Record update failure.');
                    $this->internal_msg = $msg;
                } else {
                    $msg = CMS::msg('success', "Update!", 'Record update success.');
                    $this->session->set_flashdata('warning_msg', $msg);
                    redirect("cms/" . $this->edit_page_link . '/' . $id . $return);
                }
            } else {
                if ($this->multi_language) {
                    $result = $model->insert($dataArray, $GLOBALS['language_list']);
                } else {
                    $result = $model->insert($dataArray);
                }
                if ($result === false || is_array($result)) {
                    $msg = CMS::msg('warning', "Fail!", is_array($result) ? $result["msg"] : 'Record create failure.');
                    $this->internal_msg = $msg;
                } else {
                    $msg = CMS::msg('success', "Create", 'Record create success.');
                    $this->session->set_flashdata('warning_msg', $msg);
                    redirect("cms/" . $this->edit_page_link . '/' . $result . $return);
                }
            }
        }
    }

    // End Edit Form
}
