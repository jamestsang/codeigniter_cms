<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Member extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper(array("form"));
    }

    public function index() {
        redirect(base_lang_url("home"));
    }

    public function registration($type = "organization"){
        Resource::js(array(
            "assets/js/registration.js",
                ), true);
        $this->lang->load('common');
        $this->lang->load('form_validation');
        $typeArr = ["organization", "school", "personal"];
        if(!in_array($type, $typeArr)){
            $type = "organization";
        }
        $this->load->library('form_validation');
        $this->load->library('facebook');

        if($type == "organization"){
            $this->form_validation->set_error_delimiters('<li class="alert-error">', '</li>');
            $this->form_validation->set_rules('email', 'lang:email', 'trim|required|valid_email|callback_email_check');
            $this->form_validation->set_rules('name', 'lang:org_name', 'trim|required');
            $this->form_validation->set_rules('profile', 'lang:profile', 'callback_profile_check');
            $this->form_validation->set_rules('address', 'lang:address', 'trim|required');
            $this->form_validation->set_rules('tel', 'lang:tel', 'trim|required');
            $this->form_validation->set_rules('contact_person', 'lang:contact_person', 'trim|required');
            $this->form_validation->set_rules('br', 'lang:br', 'callback_br_check');
            $this->form_validation->set_rules('description', 'lang:description', 'trim|required');
        }else if($type == "school"){
            $this->form_validation->set_error_delimiters('<li class="alert-error">', '</li>');
            $this->form_validation->set_rules('email', 'lang:email', 'trim|required|valid_email|callback_email_check');
            $this->form_validation->set_rules('name', 'lang:school_name', 'trim|required');
            $this->form_validation->set_rules('profile', 'lang:profile', 'callback_profile_check');
            $this->form_validation->set_rules('address', 'lang:address', 'trim|required');
            $this->form_validation->set_rules('tel', 'lang:tel', 'trim|required');
            $this->form_validation->set_rules('contact_person', 'lang:contact_person', 'trim|required');
            $this->form_validation->set_rules('br', 'lang:br', 'callback_br_check');
            $this->form_validation->set_rules('description', 'lang:description', 'trim|required');
        }else if($type == "personal"){
            $this->form_validation->set_error_delimiters('<li class="alert-error">', '</li>');
            $this->form_validation->set_rules('email', 'lang:email', 'trim|required|valid_email|callback_email_check');
            $this->form_validation->set_rules('position', 'lang:position', 'trim|required');
            $this->form_validation->set_rules('profile', 'lang:profile', 'callback_profile_check');
            $this->form_validation->set_rules('name', 'lang:chi_name', 'trim|required');
            $this->form_validation->set_rules('en_name', 'lang:en_name', 'trim|required');
            $this->form_validation->set_rules('tel', 'lang:tel', 'trim|required');
            $this->form_validation->set_rules('description', 'lang:description', 'trim|required');
        }

        $this->form_validation->set_message('required', '%s'.lang("must_fill"));
        $this->form_validation->set_message('min_length', lang("min_length").'%s');
        $this->form_validation->set_message('matches', lang("matches"));
        $this->form_validation->set_message('profile_check', '%s'.lang("must_upload"));
        $this->form_validation->set_message('br_check', '%s'.lang("must_upload"));
        $this->form_validation->set_message('valid_email', lang("email_incorrect"));
        

        if ($this->form_validation->run() == FALSE) {
            $this->load->view($type, array("fb_url"=>$this->facebook->register_url()));
        } else {
            $result = $this->_registrationProcess($type);
            if($result === true){
                $this->load->view("register-success");
            }else{
                $this->load->view($type, array("error"=>array(lang("email_exist"))));
            }
        }
    }

    public function profile_check($file){
        if(!empty($_FILES['profile']['name'])){
            return true;
        }
        return false;
    }

    public function br_check($file){
        if(!empty($_FILES['br']['name'])){
            return true;
        }
        return false;
    }

    public function email_check($email){
        return true;
    }

    private function _registrationProcess($type){
        $this->load->model("member_model", "member");
        $this->load->model("image_model", "image");
        $email = $this->input->post("email", true);
        $password = uniqid('');
        if($type == "organization" || $type == "school"){
            $name = $this->input->post("name", true);
            $address = $this->input->post("address", true);
            $contact_person = $this->input->post("contact_person", true);
            $tel = $this->input->post("tel", true);
            $description = $this->input->post("description", true);
            $profile_id = $this->image->uploadSingleFile("member","profile", "profile");
            $br_id = $this->image->uploadSingleFile("member","br", "br");
            $data = array(
                "email"=>$email,
                "password"=>$password,
                "new_password"=>$password,
                "type"=>$type,
                "name"=>$name,
                "address"=>$address,
                "contact_person"=>$contact_person,
                "description"=>$description,
                "member_id"=>$member,
                "profile_id"=>$profile_id,
                "br_id"=>$br_id,
            );
            $member = $this->member->insert($data);
            if(is_array($member)){
                return $member;
            }
            
        }else if($type == "personal"){
            $position = $this->input->post("position", true);
            $address = $this->input->post("address", true);
            $chi_name = $this->input->post("name", true);
            $other_chi_name = $this->input->post("other_chi_name", true);
            $en_name = $this->input->post("en_name", true);
            $other_en_name = $this->input->post("other_en_name", true);
            $organization = $this->input->post("organization", true);
            $career = $this->input->post("career", true);
            $tel = $this->input->post("tel", true);
            $description = $this->input->post("description", true);
            $profile_id = $this->image->uploadSingleFile("member","profile", "profile");
            $data = array(
                "email"=>$email,
                "password"=>$password,
                "new_password"=>$password,
                "type"=>$type,
                "position"=>$position,
                "address"=>$address,
                "name"=>$chi_name,
                "other_chi_name"=>$other_chi_name,
                "en_name"=>$en_name,
                "other_en_name"=>$other_en_name,
                "organization"=>$organization,
                "career"=>$career,
                "tel"=>$tel,
                "description"=>$description,
                "member_id"=>$member,
                "profile_id"=>$profile_id,
            );
            $member = $this->member->insert($data);
            if(is_array($member)){
                return $member;
            }
            
        }else if($type == "facebook"){
            $position = $this->input->post("position", true);
            $address = $this->input->post("address", true);
            $chi_name = $this->input->post("name", true);
            $other_chi_name = $this->input->post("other_chi_name", true);
            $en_name = $this->input->post("en_name", true);
            $other_en_name = $this->input->post("other_en_name", true);
            $organization = $this->input->post("organization", true);
            $career = $this->input->post("career", true);
            $tel = $this->input->post("tel", true);
            $description = $this->input->post("description", true);
            $picture = $this->input->post("picture", true);
            $facebook_id = $this->input->post("fb_id", true);
            $profile_id = $this->image->saveFromUrl("member", "profile", $picture);
            $data = array(
                "email"=>$email,
                "password"=>$password,
                "new_password"=>$password,
                "type"=>"personal",
                "position"=>$position,
                "address"=>$address,
                "name"=>$chi_name,
                "other_chi_name"=>$other_chi_name,
                "en_name"=>$en_name,
                "other_en_name"=>$other_en_name,
                "organization"=>$organization,
                "career"=>$career,
                "tel"=>$tel,
                "description"=>$description,
                "member_id"=>$member,
                "profile_id"=>$profile_id,
                "is_facebook"=>1,
                "facebook_id"=>$facebook_id,
            );
            $member = $this->member->insert($data);
            if(is_array($member)){
                return $member;
            }
        }
        return true;
    }

    public function login() {
        if ($this->session->userdata('member_id')) {
            redirect(base_lang_url('home'));
        }
        $this->load->library('facebook');
        Resource::js(array(
            "assets/js/login.js",
                ), true);
        $this->load->library('form_validation');
        $this->lang->load('common');
        $this->lang->load('form_validation');
        $this->form_validation->set_error_delimiters('<li class="alert-error">', '</li>');
        $this->form_validation->set_rules('email', 'lang:email', 'trim|required|valid_email');
        $this->form_validation->set_rules('password', 'lang:password', 'trim|required|callback_login_check');

        $this->form_validation->set_message('required', '%s'.lang("must_fill"));
        $this->form_validation->set_message('valid_email', lang("email_incorrect"));
        $this->form_validation->set_message('login_check', lang("incorrect_login"));

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('login',array("fb_url"=>$this->facebook->login_url()));
        } else {
            $this->load->view('login-success');
        }
    }

    public function login_check($password) {
        $this->load->model("member_model", "member");
        $email = $this->form_validation->set_value('email');
        $result = $this->member->login($email, $password);
        if (!$result) {
            return false;
        } else {
            $this->session->set_userdata('email', $result[0]['email']);
            $this->session->set_userdata('name', $result[0]['name']);
            $this->session->set_userdata('member_id', $result[0]['member_id']);
            $this->session->set_userdata("language", "traditional");
            $this->session->set_userdata("alias", $result[0]["alias"]);
            return true;
        }
    }

    public function forget(){
        if ($this->session->userdata('member_id')) {
            redirect(base_lang_url('home'));
        }
        Resource::js(array(
            "assets/js/login.js",
                ), true);
        $this->load->library('form_validation');
        $this->lang->load('common');
        $this->lang->load('form_validation');
        $this->form_validation->set_error_delimiters('<li class="alert-error">', '</li>');
        $this->form_validation->set_rules('email', 'lang:email', 'trim|required|valid_email');

        $this->form_validation->set_message('required', '%s'.lang("must_fill"));
        $this->form_validation->set_message('valid_email', lang("email_incorrect"));
        $this->form_validation->set_message('login_check', lang("incorrect_login"));

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('forget-password');
        } else {
            $result = $this->_forgetPasswordProcess($type);
            if($result === true){
                $this->load->view('forget-password-success', array("message"=>langc("hash_aleady_sent")));
            }else{
                $this->load->view("forget-password", array("error"=>array($result)));
            }
        }
    }

    private function _forgetPasswordProcess(){
        $this->load->model("member_model", "member");
        $this->load->model("member_request_model", "member_request");
        $this->load->library("mailer");
        $email = $this->input->post("email", true);
        if($this->member->checkEmail($email) <= 0){
            return langc("email_not_exist");
        }
        $this->member->setState(array(
            "email"=>$email,
            "approved"=>1,
            "is_facebook"=>0
        ));
        $member = $this->member->getAll(1,1);
        if(empty($member)){
            return langc("acc_not_active");
        }
        $this->member_request->setState(array(
            "member_id"=>$member[0]["member_id"],
            "expired_date >="=>date("Y-m-d H:i:s"),
        ));
        $request = $this->member_request->getAll(1,1);
        if(!empty($request)){
            return langc("hash_aleady_sent");
        }
        $hash = md5(uniqid(rand(), true));
        $expired_date = date("Y-m-d H:i:s", strtotime("+30 mins"));
        $this->member_request->insert(array(
            "expired_date"=>$expired_date,
            "hash"=>$hash,
            "member_id"=>$member[0]["member_id"]
        ));
        $mail = $this->mailer->getMailer();
        $mail->setFrom('info@sbhk.com', 'SBHK');
        $mail->addAddress($member[0]["email"], $member[0]["name"]);
        //$mail->addBCC("james.tsang.tk@gmail.com");
        $mail->isHTML(true);
        $mail->Subject = 'Forget password';
        $mail->Body    = '<a href="'.base_lang_url("member/resetPassword/".$hash).'">Click here to reset Password</a>';
        $mail->send();
        return true;
    }

    public function resetPassword($hash = false){
        if($hash === false){
            redirect(base_lang_url());
        }
        $this->lang->load('common');
        $this->lang->load('form_validation');
        $this->load->model("member_model", "member");
        $this->load->model("member_request_model", "member_request");
        $this->load->library("mailer");
        $this->member_request->setState(array(
            "expired_date >="=>date("Y-m-d H:i:s"),
            "hash"=>$hash
        ));
        $request = $this->member_request->getAll(1,1);
        if(empty($request)){
            redirect(base_lang_url());
        }
        $this->member->setState(array(
            "member_id"=>$request[0]["member_id"],
            "approved"=>1,
            "is_facebook"=>0
        ));
        $member = $this->member->getAll(1,1);
        if(empty($member)){
            $this->load->view('forget-password-success', array("message"=>langc("acc_not_active")));
            return;
        }
        $this->member_request->fakeDelete($request[0]["member_request_id"]);
        $password = uniqid('');
        $this->member->update(array("password"=>$password, "confirm_password"=>$password), $member[0]["member_id"]);
        $mail = $this->mailer->getMailer();
        $mail->setFrom('info@sbhk.com', 'SBHK');
        $mail->addAddress($member[0]["email"], $member[0]["name"]);
        //$mail->addBCC("james.tsang.tk@gmail.com");
        $mail->isHTML(true);
        $mail->Subject = 'Reset password';
        $mail->Body    = 'Your password: '.$password;
        $mail->send();
        $this->load->view('forget-password-success', array("message"=>langc("reset_success")));
    }

    public function page($alias = FALSE){
        if(!$alias){
            redirect(base_lang_url());
        }
        $this->load->model("member_model", "member");
        $member = $this->member->getByAlias(urldecode($alias));
        if(empty($member)){
            redirect(base_lang_url());
        }
        $member_id = $member["member_id"];
        $this->lang->load('common');
        $this->lang->load('form_validation');
        $this->load->model("article_model", "article");
        $this->load->model("image_model", "image");

        $this->article->defaultLanguage();
        $this->article->setSelect("a.*, b.alias as member_alias, b.name");
        $this->article->simpleJoin("member b", "a.member_id = b.member_id");
        $this->article->setState("a.type", "video");
        $this->article->setState("a.status", "approved");
        $this->article->setState("a.member_id", $member_id);
        $videos = $this->article->getAll(1,12);
        $videoLen = count($videos);
        for($i=0;$i<$videoLen;$i++){
            $video = $videos[$i];
            $videos[$i]["thumbnail"] = $this->image->getImageLink("other", array("width" => 354, "height" => 169, "crop" => "y"), $video["banner_id"]);
        }

        $this->article->resetState();
        $this->article->defaultLanguage();
        $this->article->setState("a.type", "photo");
        $this->article->setState("a.status", "approved");
        $this->article->setState("a.member_id", "$member_id");
        $photos = $this->article->getAll(1,12);
        $photoLen = count($photos);
        for($i=0;$i<$photoLen;$i++){
            $photo = $photos[$i];
            $photos[$i]["thumbnail"] = $this->image->getImageLink("other", array("width" => 354, "height" => 169, "crop" => "y"), $photo["banner_id"]);
        }

        $this->article->resetState();
        $this->article->defaultLanguage();
        $this->article->setState("a.type", "news");
        $this->article->setState("a.status", "approved");
        $this->article->setState("a.member_id", "$member_id");
        $news_list = $this->article->getAll(1,15);
        $newsLen = count($news_list);
        for($i=0;$i<$newsLen;$i++){
            $news = $news_list[$i];
            if($i>2){
                $news_list[$i]["thumbnail"] = $this->image->getImageLink("other", array("width" => 108, "height" => 92, "crop" => "y"), $news["banner_id"]);
            }else{
                $news_list[$i]["thumbnail"] = $this->image->getImageLink("other", array("width" => 354, "height" => 169, "crop" => "y"), $news["banner_id"]);
            }
        }

        $this->load->view("member-page", [
            "videos"=>$videos,
            "photos"=>$photos,
            "news"=>$news_list,
        ]);
    }

    public function facebook_registration(){
        $this->load->library('facebook');
        $this->load->model("member_model", "member");
        if($this->facebook->is_authenticated()){
            // Get user facebook profile details
            $fbUserProfile = $this->facebook->request('get', '/me?fields=id,first_name,last_name,email,link,picture');

            // Preparing data for database insertion
            $userData['oauth_provider'] = 'facebook';
            $userData['oauth_uid'] = $fbUserProfile['id'];
            $userData['first_name'] = $fbUserProfile['first_name'];
            $userData['last_name'] = $fbUserProfile['last_name'];
            $userData['email'] = $fbUserProfile['email'];
            $userData['picture'] = $fbUserProfile['picture']['data']['url'];
            $userData['link'] = $fbUserProfile['link'];
            
            $this->member->setState(["facebook_id"]);
            if($this->member->get_total() > 0){
                $this->load->view("personal-facebook", array("error"=>array(lang("already_reg"))));
                return;
            }

             Resource::js(array(
                "assets/js/registration.js",
                    ), true);
            $this->lang->load('common');
            $this->lang->load('form_validation');
            $typeArr = ["organization", "school", "personal"];
            $this->load->library('form_validation');
            $this->load->library('facebook');

            $this->form_validation->set_error_delimiters('<li class="alert-error">', '</li>');
            $this->form_validation->set_rules('email', 'lang:email', 'trim|required|valid_email|callback_email_check');
            $this->form_validation->set_rules('position', 'lang:position', 'trim|required');
            $this->form_validation->set_rules('name', 'lang:chi_name', 'trim|required');
            $this->form_validation->set_rules('en_name', 'lang:en_name', 'trim|required');
            $this->form_validation->set_rules('tel', 'lang:tel', 'trim|required');
            $this->form_validation->set_rules('description', 'lang:description', 'trim|required');

            $this->form_validation->set_message('required', '%s'.lang("must_fill"));
            $this->form_validation->set_message('min_length', lang("min_length").'%s');
            $this->form_validation->set_message('matches', lang("matches"));
            $this->form_validation->set_message('br_check', '%s'.lang("must_upload"));
            $this->form_validation->set_message('valid_email', lang("email_incorrect"));
            

            if ($this->form_validation->run() == FALSE) {
                $this->load->view("personal-facebook", $userData);
            } else {
                $result = $this->_registrationProcess("facebook");
                if($result === true){
                    $this->load->view("register-success");
                }else{
                    $this->load->view("personal-facebook", array("error"=>array(lang("email_exist"))));
                }
            }

        }else{
            redirect(base_lang_url());
        }
    }

    public function facebook_login() {
        $this->load->model("member_model", "member");
        $this->load->library('facebook');
        if($this->facebook->is_authenticated()){
            $fbUserProfile = $this->facebook->request('get', '/me?fields=id,first_name,last_name,email,link,picture');
            $fb_id = $fbUserProfile['id'];
            $this->member->setState([
                "approved"=>1,
                "status"=>1,
                "facebook_id"=>$fb_id 
            ]);
            $result = $this->member->getAll(1, 1);
            if(!empty($result)){
                $this->session->set_userdata('email', $result[0]['email']);
                $this->session->set_userdata('name', $result[0]['name']);
                $this->session->set_userdata('member_id', $result[0]['member_id']);
                $this->session->set_userdata("language", "traditional");
                $this->session->set_userdata("alias", $result[0]["alias"]);
                $this->load->view('login-success');
            }else{
                redirect(base_lang_url("member/registration/personal"));
            }

        }
    }
    
}
