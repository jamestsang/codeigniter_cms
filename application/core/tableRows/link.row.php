<?php

require_once 'TableRow.php';

class linkRow extends TableRow{
    
    public function html() {
        $cb = $this->cb();
        if($cb !==false){
            return $cb;
        }
        if (isset($this->setting["url"])) {
            $href = $this->generate_link($this->setting["url"]["href"], $this->setting["url"]["params"], $this->value);
            if($href === false){
                $content = "---";
            }else{
                $content = '<a href="' . $href . '" target="'.@$this->setting["url"]["target"].'">' . $this->setting["name"] . '</a>';
            }
        } else {
            $content = $this->setting["name"];
        }
        return $content;
    }

}
