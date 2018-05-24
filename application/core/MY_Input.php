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

    /**
     * http://blog.caesarchi.com/2010/12/codeigniter-disallowed-key-characters.html
     * http://ellislab.com/forums/viewthread/140333/
     * http://www.nowamagic.net/php/php_DisallowedKeyCharacters.php
     */
    public function _clean_input_keys($str) {
        $config = &get_config('config');
        if (!preg_match("/^[" . $config['permitted_uri_chars'] . "]+$/i", rawurlencode($str))) {
            exit('Disallowed Key Characters.');
        }

        // Clean UTF-8 if supported
        if (UTF8_ENABLED === TRUE) {
            $str = $this->uni->clean_string($str);
        }
        return $str;
    }

}
