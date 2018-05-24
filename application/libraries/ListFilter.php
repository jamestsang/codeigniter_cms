<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class CI_ListFilter {

    public $CI;
    public $config;
    public $filters;

    public function __construct() {
        $this->CI = &get_instance();
    }

    public function initialize($filters = array(), $setting = array()) {
        $this->config = $setting;
        $this->filters = $filters;
    }

    private function _getFilter($field_name, $setting, $value="", $option=false){
        $default = array("text", "select");
        $type = $setting["field"];
        if(in_array($type, $default)){
            require_once(APPPATH . 'core/filters/'.$type.'.filter.php');
            $fieldClass = $type."Filter";
        }else{
            if(file_exists(APPPATH . 'filters/'.$type.'.filter.php')){
                require_once(APPPATH . 'core/filters/FilterField.php');
                require_once(APPPATH . 'filters/'.$type.'.filter.php');
                $fieldClass = $type."Filter";
            }else{
                return false;
            }
        }
        
        $field = new $fieldClass($field_name, $setting, $value, $option);
        return $field->html();
    }
    
    public function create_filter() {
        $body="";
        
        foreach ($this->config as $key => $setting) {
            $body.=$this->_getFilter($key, $setting, @$this->filters[$key], empty($setting["data"])?false:$setting["data"])."&nbsp;";
        }
        
        
        
        $html = '<div class="filter-wrapper"><form class="form-inline list-filter-form" method="get" action="'.$this->CI->input->getFullPath(array("filter")).'">
                    '.$body.'
                    <button type="submit" class="btn btn-default">'.lang("Filter").'</button>
                    <a href="'.$this->CI->input->getFullPath(array("filter")).'"><button type="button" class="btn btn-default">'.lang("Reset").'</button></a>
                </form></div>';
        return $html;
    }

}
