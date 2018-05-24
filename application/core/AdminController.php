<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class AdminController extends CI_Controller {

    public $form_class = "";
    public $page_name = "";
    public $add_page_link = "";
    public $edit_page_link = "";
    public $list_page_link = "";
    public $ordering_link = "";
    public $multi_language = false;
    public $link_path = array();
    public $filters = array();
    protected $table;
    protected $model;
    protected $internal_msg;
    protected $isClinic = false;
    public $alias = "cms";
    public $organizationObj = null;

    public function __construct($table) {
        parent::__construct();
        $this->config->load("cms", true);
        Resource::css(array(
                "assets/plugin/bootstrap/less/bootstrap.less",
                "assets/plugin/font-awesome/css/fontawesome-all.min.css",
                "https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css",
                "assets/plugin/AdminLTE/less/AdminLTE.less",
                "assets/plugin/AdminLTE/skins/skin-yellow.min.css",
                "assets/plugin/iCheck/square/blue.css",
                "assets/plugin/ui/css/ui-lightness/jquery-ui-1.10.3.custom.min.css",
                //"assets/plugin/anytime/anytime.css",
                "assets/plugin/colorbox/colorbox.css",
                "assets/plugin/colorpicker/css/colorpicker.css",
                "assets/css/cms/cms.less",
                "https://fonts.googleapis.com/icon?family=Material+Icons",
                "https://vjs.zencdn.net/5.19.2/video-js.css",
                "assets/plugin/magnific-popup/magnific-popup.css",
        ), false);
        Resource::js(array(
                "assets/plugin/jQuery/jQuery-2.1.4.min.js",
                //"assets/plugin/jquery-1.10.2.min.js",
                "assets/plugin/bootstrap/js/bootstrap.min.js",
                "assets/plugin/modernizr.js",
                "assets/plugin/bootstrap/js/bootstrap.min.js",
                //"assets/plugin/jquery-migrate-1.2.1.min.js",
                "assets/plugin/ui/jquery-ui-1.10.3.custom.min.js",
                "assets/plugin/jquery-mobile/jquery.mobile.custom.min.js",
                //"assets/plugin/anytime/anytime.js",
                "assets/plugin/colorbox/jquery.colorbox-min.js",
                "assets/plugin/slimScroll/jquery.slimscroll.min.js",
                "assets/plugin/fastclick/fastclick.js",
                "assets/plugin/AdminLTE/js/app.min.js",
                "assets/plugin/colorpicker/js/colorpicker.js",
                "assets/plugin/tinymce/js/tinymce/tinymce.min.js",
                "https://vjs.zencdn.net/5.8.8/video.js",
                "assets/plugin/magnific-popup/jquery.magnific-popup.min.js",
                "assets/cms/js/validation.js",
                "assets/cms/js/main.js",
                "assets/cms/js/editor.js",
        ), false);
        
        $this->table = $table;
        $this->filters = $this->_filterParse();
        $this->lang->load('common', $this->session->userdata("language"));

        if($this->alias == "cms" && !$this->session->userdata('my_id')){
            redirect( base_url('cms/index'));
        }
        if($this->alias == "panel" && !$this->session->userdata('member_id')){
            redirect(base_lang_url());
        }
    }

    //Instance get model
    protected function _getModel() {
        if (empty($this->model)) {
            $this->load->model($this->table . '_model', 'template_model');
            $this->model = $this->template_model;
        }
        return $this->model;
    }
    
    //Set Link
    protected function _setLink($section){
        $this->add_page_link = $section."/content/add";
        $this->edit_page_link = $section."/content/edit";
        $this->list_page_link = $section."/index";
        $this->ordering_link = $section."/updateOrdering";
    }

    //basic function of index page.
    public function index() {
        $this->_list();
    }

    //Listing
    //basic function of lising page.
    protected function _list($config = array(), $template = "general-list", $more = array()) {
        //Post back
        $this->_list_post_back();
        //End Post back
        //Require library
        $data = $this->_list_process();
        $this->load->library("listing");
        //End Require library
        //Listing config user can overrider by param input.
        $init_config["canAdd"] = true;
        $init_config["canSort"] = false;
        $init_config["main_id"] = $this->table . "_id";
        $init_config["edit_link"] = "content";
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
        if(!empty($more)){
            $data = array_merge($data, $more);
        }
        $this->load->view(($this->alias=="cms"?"cms":"panel").'/' . $template, $data);
    }

    protected function _list_process() {
        //require Library
        $this->load->library('pagination');
        $model = $this->_getModel();
        //End require Library
        //Filter apply
        if(!empty($this->filters)){
            foreach($this->filters as $name=>$value){
                $model->setState("a.".$name, $value);
            }
        }
        ////End Filter apply
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
            "top_addon" => $this->_listTopAddOn(),
            "filterHtml" => $this->_listFilter()
        );
        return $data;
    }

    protected function _list_post_back() {
        $action = $this->input->get("action");
        if ($action == "switch") {
            $model = $this->_getModel();
            $id = $this->input->get("id");
            $field = $this->input->get("field");
            if ($field && $id) {
                $result = $model->switcher($field, $this->table . "_id", $id);
                if ($result !== false) {
                    $msg = CMS::msg('success', "Success! ", 'Update Success.');
                } else {
                    $msg = CMS::msg('warning', "Warning! ", 'Update failed.');
                }
                $this->session->set_flashdata('warning_msg', $msg);
            }
            redirect($this->input->getFullPath(array("action", "id", "field")));
        } else if ($action == "delete") {
            $model = $this->_getModel();
            $id = $this->input->get("id");
            if ($id && is_numeric($id)) {
                $result = $model->delete($id);
                if ($result !== false) {
                    $msg = CMS::msg('success', "Success! ", 'Delete Success.');
                } else {
                    $msg = CMS::msg('warning', "Warning! ", 'Delete failed.');
                }
                $this->session->set_flashdata('warning_msg', $msg);
            }
            redirect($this->input->getFullPath(array("action", "id")));
        }
        
        $this->_listTopAddOn();
    }

    public function updateOrdering() {
        $id_list = $this->input->post("ids");
        $order_list = $this->input->post("ordering");
        if (!empty($id_list)) {
            $model = $this->_getModel();
            foreach ($id_list as $key => $id) {
                $model->updateOrdering($id, $order_list[$key]);
            }
        }
    }

    //End Listing
    // Edit Form
    public function content($action = "add", $id = 0) {
        //Post back process.
        $this->_post_back($action, $id);
        //Fetch data
        $data = $this->_form_process($action, $id);
        //Generate form
        $this->_form($data, array());
    }

    protected function _form($data, $config = array(), $template = "general-edit-page") {
        $this->load->library("editing");
        $model = $this->_getModel();
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
        $this->load->view(($this->alias=="cms"?"cms":"panel").'/' . $template, $data);
    }

    protected function _form_process($action, $id) {
        $model = $this->_getModel();
        $dataArray = array();
        if ($action == "add") {
            
        } else if ($action == "edit" && $id != 0) {
            $data = $model->getById($id);
            $dataArray = array();
            if (sizeof($data) == 0) {
                $msg = CMS::msg('warning', "Warning! ", 'No such record in database.', $this->list_page_link);
                $this->session->set_flashdata('warning_msg', $msg);
                redirect(base_url($this->alias."/" . $this->add_page_link));
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

    protected function _modifyHook($action, $result){

    }

    protected function _post_back($action = "add", $id = 0) {
        if ($this->input->post("post_back")) {
            $model = $this->_getModel();
            $return = "";
            if ($this->input->get("return")) {
                $return = '?return=' . urlencode($this->input->get("return"));
            }

            if ($action == 'edit') {
                $this->_file_process();
                if ($this->multi_language) {
                    $result = $model->update($_POST, $id, $GLOBALS['language_list']);
                } else {
                    $result = $model->update($_POST, $id);
                }
                if ($result === false || is_array($result)) {
                    $msg = CMS::msg('warning', "Fail!", is_array($result) ? $result["msg"] : 'Record update failure.');
                    $this->internal_msg = $msg;
                } else {
                    $this->_modifyHook($action, $result);
                    $msg = CMS::msg('success', "Update!", 'Record update success.');
                    $this->session->set_flashdata('warning_msg', $msg);
                    redirect(base_url($this->alias."/" . $this->edit_page_link . '/' . $id . $return));
                }
            } else {
                $this->_file_process();
                if ($this->multi_language) {
                    $result = $model->insert($_POST, $GLOBALS['language_list']);
                } else {
                    $result = $model->insert($_POST);
                }

                if ($result === false || is_array($result)) {
                    $msg = CMS::msg('warning', "Fail!", is_array($result) ? $result["msg"] : 'Record create failure.');
                    $this->internal_msg = $msg;
                } else {
                    $this->_modifyHook($action, $result);
                    $msg = CMS::msg('success', "Create", 'Record create success.');
                    $this->session->set_flashdata('warning_msg', $msg);
                    redirect(base_url($this->alias."/" . $this->edit_page_link . '/' . $result . $return));
                }
            }
        }
    }

    protected function _file_process(){
        $deleteImages = $_POST["media_del"];
        $this->load->model("image_model", "image");
        foreach($_FILES as $name => $file_info){
            if(!is_array($file_info["name"])){
                if($file_info["size"] > 0){
                    $delete_img_id = $deleteImages[$name];
                    if($delete_img_id != ""){
                        $this->image->deleteImage($delete_img_id);
                    }
                    $image_id = $this->image->singleUpload($this->table, str_replace("_id", "", $name), $file_info["name"], $file_info["type"], $file_info["tmp_name"], $file_info["error"], $file_info["size"]);
                    $_POST[$name] = $image_id;
                }
            }else{
                foreach($GLOBALS['language_list'] as $lan){
                    $lan_id = $lan['language_id'];
                    if($file_info["size"][$lan_id] > 0){
                        $delete_img_id = $deleteImages[$name."_".$lan_id];
                        if($delete_img_id != ""){
                            $this->image->deleteImage($delete_img_id);
                        }
                        $image_id = $this->image->singleUpload($this->table, str_replace("_id", "", $name), $file_info["name"][$lan_id], $file_info["type"][$lan_id], $file_info["tmp_name"][$lan_id], $file_info["error"][$lan_id], $file_info["size"][$lan_id]);
                        $_POST[$name][$lan_id] = $image_id;
                    }
                }
            }
        }
    }

    // End Edit Form
    
    //Add on
    protected function _listTopAddOn(){
        return "";
    }
    
    protected function _listFilter($config=array()){
        if(!empty($config)){
            $this->load->library("listFilter");
            $this->listfilter->initialize($this->filters, $config);
            return $this->listfilter->create_filter();
        }

        return "";
    }
    
    private function _filterParse(){
        $filters = $this->input->get("filter");
        $params = array();
        if($filters){
            $temp = explode("|", $filters);
            if(!empty($temp)){
                foreach($temp as $subStr){
                    $parArray = explode(":", $subStr);
                    $params[$parArray[0]] = $parArray[1];
                }
            }
        }
        return $params;
    }
}
