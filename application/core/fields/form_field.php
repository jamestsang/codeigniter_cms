<?php

abstract Class form_field {
    
    protected $field_name;
    protected $name;
    protected $value;
    protected $options;
    protected $other;
    protected $CI;

    public function __construct($field_name, $name, $value, $options = null, $other = array()) {
        $this->field_name = $field_name;
        $this->name = $name;
        $this->value = $value;
        $this->options = $options;
        $this->other = array_merge(array("class"=>"", "title"=>"", "alt"=>""), $other);
        $this->CI = &get_instance();
    }
    
    public abstract function html();
}
