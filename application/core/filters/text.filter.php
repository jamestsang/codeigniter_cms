<?php

require_once 'FilterField.php';

class textFilter extends FilterField{
    
    public function html() {
        $placeholder = langc(isset($this->config["placeholder"])?$this->config["placeholder"]:ucfirst(str_replace('_', ' ', $this->field)));
        $boxType = (isset($this->config["textType"])?$this->config["textType"]:"text");
        
        return '<div class="form-group">
                    <label class="sr-only" for="'.$this->field.'">'.$placeholder.'</label>
                    <input type="'.$boxType.'" class="form-control" id="'.$this->field.'" name="'.$this->field.'" placeholder="'.$placeholder.'" value="'.$this->value.'">
                  </div>';
    }

}
