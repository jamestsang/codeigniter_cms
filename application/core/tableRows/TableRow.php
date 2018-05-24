<?php

abstract class TableRow{
    protected $main_id;
    protected $setting;
    protected $data;
    protected $CI;
    
    public function __construct($setting, $value, $main_id) {
        $this->main_id = $main_id;
        $this->setting = $setting;
        $this->value = $value;
        $this->CI = &get_instance();
    }
    
    abstract public function html();
    
    protected function generate_link($href, $params, $data) {
        $args = array();
        
        if (!empty($params) && is_array($params)) {
            foreach ($params as $param) {
                $args[] = $data[$param];
            }

            if($args[0]==NULL){
                return false;
            }
        }
        
        if(strpos($href, 'http://') === 0 || strpos($href, 'https://') === 0 || strpos($href, 'mailto:') === 0){
            return vsprintf($href, $args);
        }
        
        
        $return_link = urlencode($this->CI->input->getFullPath());
        $alias = explode("/", uri_string())[0];
        return base_url($alias."/" . vsprintf($href, $args) . "&return=" . $return_link);
    }
    
    protected function cb(){
        $callBackResult = false;
        if(!empty($this->setting["callback"]) && !is_string($this->setting["callback"]) && is_callable($this->setting["callback"])){
            $callBackResult = $this->setting["callback"]($this->setting, $this->value);
        }
        return $callBackResult;
    }
}

