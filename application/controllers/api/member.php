<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Member extends MY_Controller {

    public function __construct() {
        parent::__construct(false);
    }

    public function index(){
        
    }

    public function emailNotExist(){
    	$email = $this->input->get("email");
    	$this->load->model("member_model", "member");
    	$total = $this->member->checkEmail($email);
    	if($total > 0){
    		echo "false";
    	}else{
    		echo "true";
    	}
    }

}