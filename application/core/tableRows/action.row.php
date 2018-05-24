<?php

require_once 'TableRow.php';

class actionRow extends TableRow{
    
    public function html() {
        $cb = $this->cb();
        if($cb !==false){
            return $cb;
        }
        if (!empty($this->setting["actions"])) {
            $content = "";
            foreach ($this->setting["actions"] as $action) {
                if ($action["type"] == "edit") {
                    $icon = '<span class="glyphicon glyphicon-pencil"></span>';
                } else if ($action["type"] == "delete") {
                    $icon = '<span class="glyphicon glyphicon-remove"></span>';
                }else{
                    $icon = '<span class="'.$action["icon"].'"></span>';
                }
                $href = $this->generate_link($action["url"]["href"], $action["url"]["params"], $this->value);
                $href = '<a href="' . $href . '" class="'.$action["type"].'-btn">' . $icon . '</a>&nbsp;&nbsp;';
                if (!empty($action["exclude"])) {
                    $show = true;
                    foreach ($action["exclude"] as $fieldName => $filters) {
                        if (in_array($this->value[$fieldName], $filters)) {
                            $show = false;
                            break;
                        }
                    }
                    if ($show) {
                        $content .= $href;
                    }
                } else {
                    $content .= $href;
                }
            }
            return $content;
        }
    }

}
