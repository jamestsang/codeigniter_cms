<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Index extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->config->load("cms", true);
        Resource::css(array(
            "assets/plugin/bootstrap/less/bootstrap.less",
            "https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css",
            "https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css",
            "assets/plugin/AdminLTE/less/AdminLTE.less",
            "assets/plugin/iCheck/square/blue.css",
            /*"assets/css/reset.css",
            "assets/css/cms/global.less",
            "assets/css/cms/cms.less",
            "https://fonts.googleapis.com/icon?family=Material+Icons",*/
                ), false);
        Resource::js(array(
            //"assets/plugin/jquery-1.10.2.min.js",
            "assets/plugin/modernizr.js",
            "assets/plugin/jQuery/jQuery-2.1.4.min.js",
            "assets/plugin/bootstrap/js/bootstrap.min.js",
            /*"assets/plugin/jquery-migrate-1.2.1.min.js",*/
            "assets/plugin/jquery-mobile/jquery.mobile.custom.min.js",
            "assets/cms/js/validation.js",
            "assets/cms/js/main.js",

                ), false);
        $this->load->model('admin_model', 'admin');
        $this->load->helper(array("form"));
    }

    public function index() {
        if ($this->session->userdata('username')) {
            redirect(base_url('cms/home'));
        }
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="alert alert-error">', '</div>');
        $this->form_validation->set_rules('email', 'email', 'trim|required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|callback_login_check');
        if ($this->form_validation->run() == FALSE) {
            $this->load->view('cms/login');
        } else {
            redirect(base_url('cms/home'));
        }
    }

    public function login_check($password) {
        $email = $this->form_validation->set_value('email');
        $result = $this->admin->login($email, $password);
        if (!$result) {
            $this->form_validation->set_message('login_check', 'Incorrect User Name or Password.');
            return false;
        } else {
            $this->session->set_userdata('email', $result[0]['email']);
            $this->session->set_userdata('username', $result[0]['title']);
            $this->session->set_userdata('my_id', $result[0]['admin_id']);
            $this->session->set_userdata('is_admin', $result[0]['superadmin']);
            $this->session->set_userdata('language', $result[0]['language']);
            return true;
        }
    }

    public function logout() {
        $this->session->unset_userdata(array('username' => '', 'my_id' => '', 'is_admin' => '', "email"=>"", "language"=>""));
        redirect(base_url('cms/index'));
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */