<?php

require_once(APPPATH . 'third_party/Less/Autoloader.php');
Less_Autoloader::register();

class Meta {

    private static $title = array();
    private static $description = "";
    private static $fb = array();

    public static function setTitle($str){
        self::$title = [$str];
    }

    public static function addTitle($str){
        self::$title[] = $str;
    }

    public static function setDescription($desc){
        self::$description = $desc;
        self::$fb["description"] = $desc;
    }

    public static function setFB($name, $content){
        self::$fb[$name] = $content;
    }

    public static function getTitle($reverse = false){
        if($reverse){
            return implode("-", (self::$title));
        }else{
            return implode("-", array_reverse(self::$title));
        }
        
    }

    public static function getDescription(){
        return self::$description;
    }

    public static function getFB(){
        return self::$fb;
    }
}

?>