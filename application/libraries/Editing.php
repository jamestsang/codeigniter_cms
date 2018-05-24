<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class CI_Editing {

    public $CI;
    private $custom_field = array(); //Custom field
    private $hiddenArr = array(); //Hidden field
    private $showArr = array(); //Show field when is hidden already
    private $seqArr = array(); //Sort Field sequance.
    private $shareElement = array(); //The Field don't care language.
    private $class_list = array();
    private $title_list = array();
    private $alt_list = array();
    private $extend_field = array();
    private $media = array();
    private $model = null;

    public function __construct() {
        $this->CI = &get_instance();
        $this->CI->load->helper("form");
    }

    public function initialize($config = array(), $model = null) {
        if (sizeof($config) > 0) {
            foreach ($config as $name => $value) {
                if (isset($this->$name)) {
                    $this->$name = $value;
                }
            }
        }
        $this->model = $model;
    }

    public function __call($method, $arguments) {
        if ($method == 'create_form') {
            if (count($arguments) == 1) {
                return call_user_func_array(array($this, 'create_form_single'), $arguments);
            } else if (count($arguments) == 2) {
                return call_user_func_array(array($this, 'create_form_multi'), $arguments);
            }
        }
    }

    private function field_process() {
        $this->showArr = array_merge($this->showArr, array_keys($this->custom_field));
        $fields = $this->model->getCol(false, $this->hiddenArr, $this->showArr, $this->seqArr, $this->extend_field);

        return $fields;
    }

    public function create_form_single($data) {

        $fields = $this->field_process();
        $html = '';

        if (!empty($this->media)) {
            foreach ($this->media as $name => $media) {
                $html .= $this->media($name, $media);
            }
        }

        if (!empty($fields)) {
            foreach ($fields as $name => $field) {
                $value = ($this->CI->input->post($name)) ? $this->CI->input->post($name) : @$data[0][$name];
                $html .= $this->getField($field["type"], $name, $name, $value, empty($field["data"])?false:$field["data"]);
            }
        }
        return $html;
    }

    public function create_form_multi($data, $is_multi) {
        $fields = $this->field_process();

        $html = '';

        if (!empty($this->media)) {
            foreach ($this->media as $name => $media) {
                $html .= $this->media($name, $media);
            }
        }

        if (!empty($this->shareElement)) {
            foreach ($fields as $name => $field) {
                if (in_array($name, $this->shareElement)) {
                    $first_value = @array_shift(array_slice($data, 0, 1));
                    $value = ($this->CI->input->post($name)) ? $this->CI->input->post($name) : @$first_value[$name];
                    $field_name = $name;
                    $html .= $this->getField($field["type"], $field_name, $name, $value, empty($field["data"])?false:$field["data"], true);
                    unset($fields[$name]);
                }
            }
        }

        $html .= '<ul class="nav nav-tabs">';
        foreach ($GLOBALS['language_list'] as $key => $language) {
            $html .= '<li class="' . ($key == 1 ? "active" : "") . '"><a href="#lang-' . $language["language_id"] . '" data-toggle="tab">' . $language["title"] . '</a></li>';
        }
        $html .= '</ul>';

        $html .= '<div class="tab-content">';
        foreach ($GLOBALS['language_list'] as $key => $language) {
            $html .= '<div class="tab-pane ' . ($key == 1 ? "active" : "") . '" id="lang-' . $language["language_id"] . '"><br />';
            if (!empty($fields)) {
                foreach ($fields as $name => $field) {
                    $field_name = $name . '[' . $language["language_id"] . ']';
                    $value = ($this->CI->input->post($name)) ? $this->CI->input->post($name) : @$data[$language["language_id"]][$name];
                    $html .= $this->getField($field["type"],$field_name, $name, $value, empty($field["data"])?false:$field["data"]);
                }
            }
            $html .= '</div>';
        }
        $html .= '</div>';

        return $html;
    }
    
    private function getField($type,$field_name, $name, $value, $option = false, $isShare = false){
        $default = array("textbox", "editor", "select", "checkbox", "radio", "category", "hidden", "file");
        $other = array(
            "class"=>"",
            "alt"=>"",
            "title"=>"",
        );
        if(!empty($this->custom_field[$name])){
            if(!empty($this->custom_field[$name]["field"])){
                $type = $this->custom_field[$name]["field"];
            }
            $other = array_merge($other, $this->custom_field[$name]);
        }

        if(in_array($type, $default)){
            require_once(APPPATH . 'core/fields/'.$type.'.field.php');
            $fieldClass = $type."_field";
        }else{
            if(file_exists(APPPATH . 'fields/'.$type.'.field.php')){
                require_once(APPPATH . 'core/fields/form_field.php');
                require_once(APPPATH . 'fields/'.$type.'.field.php');
                $fieldClass = $type."_field";
            }else{
                return false;
            }
        }
        
        if ($other["title"]!="") {
            $display_name = $other["title"];
        } else {
            $display_name = ucfirst(str_replace('_', ' ', $name));
        }
        
        $other["display_name"] = langc($display_name);
        if($type=="checkbox"){
            $field_name = "default_checkbox[".$field_name."]";
        }

        $field = new $fieldClass($field_name, $name, $value, $option, $other);
        return $field->html();
    }

    private function media($name, $setting) {
        $link = '<p class="form-control-static">'.langc("Please add record.").'</p>';
        if ($setting["id"] != 0) {
            if (!isset($setting["caption"])) {
                $setting["caption"] = "no";
            }
            if (!isset($setting["limit"])) {
                $setting["limit"] = 0;
            }
            $path = $setting["is_panel"]?"panel":"cms";
            $link = '<a href="' . (base_url() . $path."/upload/home/" . $setting["section"] . "/" . $setting["id"] . "/" . $setting["folder"] . "/" . $setting["limit"] . "/" . $setting["caption"]) . '" class="btn btn-primary upload-frame" role="button">'.langc("Upload").'</a>';
        }
        return '
			<div class="form-group">
				<label class="col-sm-2 control-label" for="' . $name . '">' . langc((@$this->nameMap[$name] != '') ? $this->nameMap[$name] : ucfirst(str_replace('_', ' ', $name))) . '</label>
				<div class="col-sm-10">
					' . $link . '
				</div>
			</div>
		';
    }

}
