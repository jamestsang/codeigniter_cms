<?php

require_once 'TableRow.php';

class fieldRow extends TableRow{
    
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
                $content = '<a href="' . $href . '" target="'.@$this->setting["url"]["target"].'">' . langc($this->value[$this->setting["field"]]) . '</a>';
            }
        } else {
            $content = langc($this->value[$this->setting["field"]]);
        }
        return $content;
    }

}

