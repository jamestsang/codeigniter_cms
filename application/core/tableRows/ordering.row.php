<?php

require_once 'TableRow.php';

class orderingRow extends TableRow{
    
    public function html() {
        $cb = $this->cb();
        if($cb !==false){
            return $cb;
        }
        $content = $this->value["ordering"];
        return $content;
    }

}
