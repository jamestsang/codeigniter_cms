<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class MY_Input extends CI_Input {

    public function getFilter($exclude = array()) {
        $get = parent::get();
        $param = "";
        if (!empty($get)) {
            foreach ($get as $name => $value) {
                if (!in_array($name, $exclude)) {
                    $param .= "&" . $name . "=" . urlencode($value);
                }
            }
        }
        return $param;
    }

    public function getFullPath($exclude = array()) {
        return base_url(uri_string()) . "?" . $this->getFilter($exclude);
    }

}
