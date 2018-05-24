<?php

//require_once(APPPATH . 'core/filters/FilterField.php');

class dateFilter extends FilterField{
    
    public function html() {
        $placeholder = (isset($this->config["placeholder"])?$this->config["placeholder"]:ucfirst(str_replace('_', ' ', $this->field)));
        $boxType = (isset($this->config["textType"])?$this->config["textType"]:"text");
        
        return '<div class="form-group">
                    <label class="sr-only" for="'.$this->field.'">'.$placeholder.'</label>
                    <input type="'.$boxType.'" class="form-control datepicker" id="'.$this->field.'" name="'.$this->field.'" placeholder="'.langc($placeholder).'" value="'.$this->value.'">
                  </div>';
    }

}
