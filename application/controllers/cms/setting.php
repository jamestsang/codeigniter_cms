<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require_once(APPPATH . 'core/AdminController.php');
require_once(APPPATH . 'core/fields/textbox.field.php');
require_once(APPPATH . 'core/fields/editor.field.php');
require_once(APPPATH . 'core/fields/select.field.php');

class Setting extends AdminController {

    public function __construct() {
        parent::__construct("setting");
        $this->form_class = "setting";
        $this->page_name = "Setting";
        $this->link_path[] = "setting";
    }

    public function index() {
        $model = $this->_getModel();
        if ($this->input->post("post_back")) {
            $result = $model->updateByName($this->input->post());
            if ($result === false || is_array($result)) {
                $msg = CMS::msg('warning', "Fail!", is_array($result) ? $result["msg"] : 'Record update failure.');
                $this->internal_msg = $msg;
            } else {
                $msg = CMS::msg('success', "Update!", 'Record update success.');
                $this->session->set_flashdata('warning_msg', $msg);
                redirect("cms/setting");
            }
        }

        $result = $model->getAll(false, false, "group DESC, ordering ASC", " ");
        $data['warning_msg'] = $this->session->flashdata('warning_msg') ? $this->session->flashdata('warning_msg') : $this->internal_msg;

        $data["list"] = $result;
        $this->load->view("cms/setting", $data);
    }

}
