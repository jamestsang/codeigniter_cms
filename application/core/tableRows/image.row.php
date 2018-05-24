<?php

require_once 'TableRow.php';

class imageRow extends TableRow{
    
    public function html() {
        $cb = $this->cb();
        if($cb !==false){
            return $cb;
        }
        $href = $this->setting["url"]["href"];
        $params = $this->setting["url"]["params"];
        $args = array();
        
        if (!empty($params) && is_array($params)) {
            foreach ($params as $param) {
                $args[] = $this->value[$param];
            }
        }
        
        $src = vsprintf($href, $args);
        $content = '<img src="'.base_url("cms/" .$src).'" alt="" class="small-image"/>';
        return $content;
    }

}
