<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require_once(APPPATH . 'core/AdminController.php');

class Upload extends AdminController {

    public function __construct() {
        parent::__construct("image");
        $this->form_class = "image";
        $this->page_name = "Image";
    }

    public function home($section, $id, $folder, $limit, $caption = "no") {
        if ($section !== false && $id !== false && $folder !== false) {
            $path = 'assets/upload/';
            if (!is_dir($path . $section)) {
                @mkdir($path . $section);
                @chmod($path . $section, 0777);
            }
            $path = $path . $section . '/';
            if (!is_dir($path . $id)) {
                @mkdir($path . $id);
                @chmod($path . $id, 0777);
            }
            $path = $path . $id . '/';
            if (!is_dir($path . $folder)) {
                @mkdir($path . $folder);
                @chmod($path . $folder, 0777);
            }
        }

        $model = $this->_getModel();
        $model->setState("section", $section);
        $model->setState("section_id", $id);
        $model->setState("folder", $folder);
        $result = $model->getAll();
        $total = $model->get_total();

        $config["section"] = $section;
        $config["id"] = $id;
        $config["folder"] = $folder;
        $config["limit"] = $limit;
        $config["caption"] = $caption;
        $config["image_list"] = $result;
        $config["total"] = $total;

        $this->load->view('cms/upload', $config);
    }

    public function check() {
        echo 0;
    }

    public function getJson($section, $id, $folder) {
        $model = $this->_getModel();
        $model->setState("section", $section);
        $model->setState("section_id", $id);
        $model->setState("folder", $folder);
        $result = $model->getAll();
        $images = array();
        if (!empty($result)) {
            foreach ($result as $image) {
                $images[] = array(
                    "id" => $image["image_id"],
                    "path" => $image["path"],
                    "caption" => $image["caption"],
                    "name" => $image["filename"],
                    "ordering" => $image["ordering"]
                );
            }
        }
        echo json_encode($images);
    }

    public function add($section, $id, $folder) {
        $model = $this->_getModel();
        $model->uploadImage($section, $id, $folder);
    }
    
    public function ex_add($section, $id, $folder) {
        $path = FCPATH . "assets/upload/" . $section . "/" . $id . "/" . $folder;
        $result = array();
        $files = $_FILES;
        $model = $this->_getModel();
        if (count($files) > 0) {
            foreach ($files as $field => $data) {
                foreach ($data['error'] as $file => $error) {
                    if ($error == 0) {
                        $ext = strtolower(strrchr(stripslashes($data['name'][$file]), "."));
                        $filename = uniqid('');
                        //$filename = date('Y-m-d-h-i-s').'-'.$filename;
                        $filename = $filename . $ext;
                        $upflag = move_uploaded_file($data['tmp_name'][$file], "$path/$filename");
                        if ($upflag) {
                            //$result[]=array("org"=$data['name'][$file], "new"=>$filename);
                            $data = array(
                                "section" => $section,
                                "section_id" => $id,
                                "folder" => $folder,
                                "org_name" => $data['name'][$file],
                                "filename" => $filename,
                                "path" => "assets/upload/" . $section . "/" . $id . "/" . $folder . "/" . $filename
                            );
                            $model->insert($data);
                        }
                    }
                }
            }
        }
    }

    public function delete($id) {
        $model = $this->_getModel();
        $image = $model->getById($id);
        if (!empty($image)) {
            @unlink(FCPATH . $image[0]["path"]);
            $model->delete($id);
        }
    }

    public function updateCaption($id) {
        $model = $this->_getModel();
        $caption = $this->input->post("caption");
        $data = array(
            "caption" => $caption,
        );
        $model->update($data, $id);
    }

    public function updateOrdering() {
        $id_list = $this->input->post("image");
        $ordering_list = $this->input->post("ordering");
        $model = $this->_getModel();
        if (!empty($id_list)) {
            foreach ($id_list as $key => $id) {
                $model->updateOrdering($id, $ordering_list[$key]);
            }
        }
    }

}
