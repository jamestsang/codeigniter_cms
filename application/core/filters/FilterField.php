<?php

abstract class FilterField{
    protected $field;
    protected $config;
    protected $value;
    protected $options;
    
    public function __construct($field, $config, $value="", $options = array()) {
        $this->field = $field;
        $this->config = $config;
        $this->value = $value;
        $this->options = $options;
    }
    
    abstract public function html();
}

