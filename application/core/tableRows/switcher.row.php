<?php

require_once 'TableRow.php';

class switcherRow extends TableRow{
    
    public function html() {
        $cb = $this->cb();
        if($cb !==false){
            return $cb;
        }
        $status = $this->value[$this->setting["field"]];
        $link = $this->CI->input->getFullPath(array("action", "id", "field")) . "&action=switch&field=" . $this->setting["field"] . "&id=" . $this->value[$this->main_id];
        $icon = "";
        if ((@$this->setting["switcher_reverse"] === true && $status == 1) || (@$this->setting["switcher_reverse"] !== true && $status == 0)) {
            $icon = '<span class="glyphicon glyphicon-star"></span>';
        } else {
            $icon = '<span class="glyphicon glyphicon-star-empty"></span>';
        }
        return '<a href="' . $link . '" class="switcher">' . $icon . '</a>';
    }

}
