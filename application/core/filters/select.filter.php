<?php

require_once 'FilterField.php';

class selectFilter extends FilterField{
    
    public function html() {
        $placeholder = langc((isset($this->config["placeholder"])?$this->config["placeholder"]:ucfirst(str_replace('_', ' ', $this->field))));
        $attrs = 'class="form-control" id="'.$this->field.'" title="' .$placeholder . '"';
        $dataArray = array();
        //$dataArray[""] = "-Select ".$placeholder."-";
        $dataArray = $this->options;
        if (!is_array($dataArray)) {
            if (strpos($dataArray, "{DB}") >= 0) {
                $params = explode("@", str_replace("{DB}", "", $dataArray));
                $this->CI->load->model($params[0] . "_model", "select_model");
                if (count($params) > 3) {
                    $this->CI->select_model->setState("language", $params[3]);
                }
                $dataArray = $this->CI->select_model->toArray($params[1], $params[2]);
            }
        }
        foreach ($dataArray as $key => $value) {
            $dataArray[$key] = langc($value);
        }

        $dataArray = array(""=>("- ".langc("Select").$placeholder."-")) + $dataArray;

        return '<div class="form-group">
                    <label class="sr-only" for="'.$this->field.'">'.$placeholder.'</label>
                    '.form_dropdown($this->field, $dataArray, $this->value, $attrs).'
                  </div>';
    }

}
