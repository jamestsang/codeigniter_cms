<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class CI_Row {

    public $CI;
    private $rowData;
    private $config;
    private $main_id;

    public function __construct() {
        $this->CI = &get_instance();
    }

    public function initialize($config = array(), $main_id = "id") {
        if (sizeof($config) > 0) {
            $this->config = $config;
        }
        $this->main_id = $main_id;
    }

    public function getRow($data) {
        $html = '';
        if (!empty($this->config)) {
            foreach ($this->config as $setting) {
                //$content = $this->$setting["type"]($setting, $data);
                $class = "";
                if(isset($setting["mobile"]) && $setting["mobile"] === false){
                    $class = "hidden-xs";
                }
                $content = $this->getRowClass($setting["type"], $data, $setting);
                $html .= '<td class="'.$class.'">' . $content . '</td>';
            }
        }
        return $html;
    }
    
    private function getRowClass($type, $data, $setting){
        $default = array("action", "field", "image", "switcher", "ordering", "link");
        if(in_array($type, $default)){
            require_once(APPPATH . 'core/tableRows/'.$type.'.row.php');
            $rowClass = $type."Row";
        }else{
            if(file_exists(APPPATH . 'TableRows/'.$type.'.row.php')){
                require_once(APPPATH . 'core/tableRows/TableRow.php');
                require_once(APPPATH . 'TableRows/'.$type.'.row.php');
                $rowClass = $type."Row";
            }else{
                return false;
            }
        }
        $row = new $rowClass($setting, $data, $this->main_id);
        return $row->html();
    }
}
