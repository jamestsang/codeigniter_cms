<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Aboutus extends MY_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        redirect(base_lang_url("home"));
    }

    public function detail($alias = false){
        if($alias == false){
            redirect(base_lang_url("home"));
        }
        $this->load->model("about_model", "about");
        $this->load->model("image_model", "image");
        $this->about->setState(array(
            "alias"=>urldecode($alias),
            "status"=>1,
        ));
        // $this->article->defaultLanguage();
        $abouts = $this->about->getAll(1,2);
        $about = array();
        if(count($abouts) > 0){
            foreach($abouts as $data){
                if($data["language"] == langId()){
                    $about[0] = $data;
                }
            }
            if(empty($video)){
                $this->about->resetState();
                $about = $this->about->getById($abouts[0]["about_id"], langId());
            }
        }
        if(empty($about)){
            redirect(base_lang_url("home"));
        }
        if($about[0]["meta_title"] != ""){
            Meta::addTitle($about[0]["meta_title"]);
            Meta::setFB("title", $about[0]["meta_title"]);
        }else{
            Meta::addTitle($about[0]["title"]);
            Meta::setFB("title", $about[0]["title"]);
        }
        if($about[0]["meta_description"] != ""){
            Meta::setDescription($about[0]["meta_description"]);
        }else{
            Meta::setDescription(ellipsize($about[0]["content"], 160));
        }
        Meta::setFB("type", "article");
        Meta::setFB("image", $this->image->getImageLink("other", array("width"=>"600", "height"=>"315", "bg"=>"ffffff"), $about[0]["banner_id"]));
        Meta::setFB("image:width", "600");
        Meta::setFB("image:height", "315");
        $id = $about[0]["about_id"];
        // $thumbnail = $this->image->getImages("about", $id, "image", true);
        $img_url = $this->image->getImageLink("other", array("width"=>"2083", "height"=>"993", "bg"=>"ffffff"), $about[0]["banner_id"]);
        $this->load->view("about-detail", ["about"=>$about[0], "banner"=>$img_url]);
    }
    
}
