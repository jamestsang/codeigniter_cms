<?php

include_once 'form_field.php';

class hidden_field extends form_field {

    public function html() {
       return '<input type="hidden" name="' . $this->name . '" value="' . (isset($this->other["value"])?$this->other["value"]:$this->value) . '" />';
    }

}
