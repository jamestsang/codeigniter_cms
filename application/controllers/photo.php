<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require_once(APPPATH . 'libraries/ImageProcess.php');
require_once(APPPATH . 'libraries/ImagickProcess.php');

class Photo extends MY_Controller {

    private $member_id;
    private $asset_path = 'assets';
    private $cache_path = 'assets/cache';

    public function __construct() {
        parent::__construct();
        $this->load->model("image_model", "image");
    }

    public function index() {
        $link = $this->input->get("p");
        if(!$link){
            $this->_placeholder(array());
        }
        
        $array = $this->image->decodeLink($link);

        $id = $array["id"];
        $method = $array["method"];
        $setting = $array["setting"];
        if($array === false){
            $this->_placeholder(array());
        }
        if($id == null){
            $this->_placeholder(array("width"=>$setting["width"], "height"=>$setting["height"], "bg"=>"ffffff"));
        }
        if ($method == "other") {
            $this->_other($id, $setting, $link);
        }else if($method == "placeholder"){
            $this->_placeholder($setting);
        }
    }

    private function _other($id, $setting, $link) {
        $cache_name = str_replace("/", "_", $link);
        $no_cache = $this->_outputCache($cache_name);
        
        if ($no_cache) {
            $image = $this->image->getById($id);
            if (empty($image) || $image[0]["deleted"] == 1) {
                exit;
            }

            $setting = array_merge(
                    array( "file" => $image[0]["path"]),
                    $setting
            );
            $this->_cacheImage($setting, $cache_name);
        }
    }
    
    private function _placeholder($setting) {
        $setting = array_merge(
                array( "file" => "assets/images/placeholder.png"),
                $setting
        );
        $this->_cacheImage($setting);
    }

    public function debug($id = false) {
        if ($id === false) {
            exit;
        }
        $this->load->model("image_model", "image");
        $image = $this->image->getById($id);
        if (empty($image) || $image[0]["deleted"] == 1) {
            exit;
        }

        $setting = array_merge(
                array(
            "width" => "",
            "height" => "",
            "file" => $this->getBlob($image[0]["path"]),
            "crop" => "",
            "bg" => "",
            "wm" => "",
            "rot" => "",
            "wcon" => "",
                ), $_GET
        );
        $img = new ImageProcess($setting);
        $img->generate();
    }

    private function _cacheImage($setting, $filename = false, $hasCache = true) {
        $setting = array_merge(
                array(
                    "width" => "",
                    "height" => "",
                    "file" => "",
                    "crop" => "",
                    "bg" => "",
                    "wm" => "",
                    "rot" => "",
                    "wcon" => "",
                    "blur" => "",
                ), $setting
        );
        if(is_object($setting['file'])){
            $tmpPath = $setting['file']->headers["filename"];
        }else{
            $tmpPath = $setting['file'];
        }

        $path_info = pathinfo($tmpPath);
        if ($filename !== false) {
            $cache_name = $filename;
        } else {
            $cache_name = 'cache-' . str_replace('/', '-', $path_info['dirname']) . '-' . $path_info['filename'] . ($setting['width'] != '' ? '-' . $setting['width'] : '') . ($setting['height'] != '' ? '-' . $setting['height'] : '') .
                    ($setting['bg'] != '' ? '-' . $setting['bg'] : '') . ($setting['crop'] != '' ? '-' . $setting['crop'] : '') . ($setting['wm'] != '' ? '-' . urlencode($setting['wm']) : '') . ($setting['wcon'] != '' ? '-' . $setting['wcon'] : '') .
                    ($setting['rot'] != '' ? '-' . $setting['rot'] : '') . ($setting['blur'] != '' ? '-' . $setting['blur'] : '') .'.' . $path_info['extension'];
        }
        $no_cache = $this->_outputCache($cache_name);
       
        if ($no_cache === true) {
            $img = new ImageProcess($setting, $hasCache, $this->cache_path, $cache_name);
            $img->generate();
        }
    }

    private function _outputCache($cache_name){
        $cache_path = $this->cache_path;
        $cache_time = 6000 * 24 * 30;
        $no_cache = false;
        if (file_exists($cache_path . '/' . $cache_name)) {
            $cachetime = $cache_time;
            if ((time() - $cachetime < filemtime($cache_path . '/' . $cache_name))) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime_type = finfo_file($finfo, $cache_path . '/' . $cache_name);
                finfo_close($finfo);
                header('Content-Description: File Transfer');
                header('Content-Transfer-Encoding: binary');
                //header('Expires: 0');
                header('Cache-Control: max-age=86400');
                header('Expires: '. gmdate('D, d M Y H:i:s \G\M\T', time() + 86400));
                // header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Pragma: public');
                header("Content-type: $mime_type");
                header('Content-Length: ' . strlen(file_get_contents($cache_path . '/' . $cache_name)));
                readfile($cache_path . '/' . $cache_name);
            } else {
                $no_cache = true;
                @unlink($cache_path . '/' . $cache_name);
            }
        } else {
            $no_cache = true;
        }
        return $no_cache;
    }
}

?>