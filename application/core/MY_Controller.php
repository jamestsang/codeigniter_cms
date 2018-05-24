<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

    public function __construct($is_website = true) {
        parent::__construct();
        if($is_website){
            Resource::css(array(
                "https://fonts.googleapis.com/css?family=Roboto:400,300,300italic",
                "assets/plugin/bootstrap/less/bootstrap.less",
                "assets/plugin/font-awesome/css/fontawesome-all.min.css",
                "assets/plugin/slick/slick.css",
                "assets/plugin/slick/slick-theme.css",
                "assets/plugin/fullcalendar/fullcalendar.min.css",
                "assets/plugin/aos/aos.css",
                "assets/plugin/magnific-popup/magnific-popup.css",
                "https://vjs.zencdn.net/5.19.2/video-js.css",
                "assets/css/main.less",
                "assets/css/mobile.less",
            ), false);
            Resource::js(array(
                
                    ), false);

            $validateLang = "";
            if(langCode() == "zh-hant"){
                $validateLang = "assets/js/plugin/jquery.validate/localization/messages_zh_TW.min.js";
            }else{
                $validateLang = "assets/js/plugin/jquery.validate/localization/messages_zh.min.js";
            }

            Resource::js(array(
                "https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js",
                "assets/plugin/bootstrap/js/bootstrap.min.js",
                "assets/plugin/slick/slick.min.js",
                "https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js",
                "assets/plugin/fullcalendar/fullcalendar.min.js",
                "assets/plugin/fullcalendar/gcal.min.js",
                "assets/plugin/waypoint/jquery.waypoints.min.js",
                "assets/plugin/waypoint/inview.min.js",
                "assets/plugin/aos/aos.js",
                "assets/js/plugin/jquery.validate/jquery.validate.min.js",
                $validateLang,
                "assets/js/plugin/jquery.validate/additional-methods.min.js",
                "assets/plugin/magnific-popup/jquery.magnific-popup.min.js",
                "https://vjs.zencdn.net/5.8.8/video.js",
                "assets/js/convert.js",
                "assets/js/init.js",
                "assets/js/event.js",
                    ), true);
        }
        $this->lang->load('front');
        Meta::addTitle("香港撒瑪利亞防止自殺會");
        Meta::addTitle("Lifetube");
        Meta::setFB("site_name", "香港撒瑪利亞防止自殺會");
    }

    protected function _result($status, $msg, $other = array(), $exit = true) {
        header("content-type:application/json");
        $result = array(
            "status" => $status,
            "msg" => $msg,
            "data"=>$other
        );
        echo json_encode($result);
        if ($exit === true)
            exit;
    }

}

?>