<?php

require_once(APPPATH . 'third_party/lessc.inc.php');

if (!function_exists("css")) {

    function css($src) {
        if (is_array($src)) {
            $result = '';
            foreach ($src as $s) {
                $result.=css($s);
            }
            return $result;
        } else {
            if (preg_match("/^.*\.(less)$/i", $src)) {
                $export = str_replace(".less", ".css", $src);
                try {
                    lessc::ccompile("./" . $src, "./" . $export);
                } catch (exception $ex) {
                    exit('lessc fatal error:<br />' . $ex->getMessage());
                }
            } else {
                $export = $src;
            }
            return '<link href="' . DOMAIN . $export . '" rel="stylesheet" type="text/css" />
				';
        }
    }

}

if (!function_exists("js")) {

    function js($src) {
        if (is_array($src)) {
            $result = '';
            foreach ($src as $s) {
                $result.=js($s);
            }
            return $result;
        } else {
            if(filter_var($src, FILTER_VALIDATE_URL)){
                return '<script src="' . $src . '"></script>
				';
            }else{
                return '<script src="' . DOMAIN . $src . '"></script>
				';
            }
            
        }
    }

}
?>