<?php

class Image_model extends MY_Model {

    private static $hashKey1 = "^##$((2Hei1WithSolutionForest!@$@@";
    private static $hashKey2 = "%^%^JamesTsang@SolutionForestDeveloper*&$@%";
    private static $encryptMethod = "des-ede";
    
    public function __construct() {
        parent::__construct('image', "ordering", "DESC");
    }
    
    public function getImageLink($method, $setting, $id){
        return base_url("photo")."?p=".urlencode(openssl_encrypt(json_encode($setting)."_".openssl_encrypt($method."_".$id, self::$encryptMethod, self::$hashKey2),  self::$encryptMethod, self::$hashKey1));
    }
    
    public function decodeLink($link){
        $string = openssl_decrypt(($link),  self::$encryptMethod, self::$hashKey1);
        if($string === false){
            return false;
        }
        $temp = explode("_", $string);
        
        $setting = json_decode($temp[0], true);
        $string = openssl_decrypt($temp[1],  self::$encryptMethod, self::$hashKey2);
        if($string === false){
            return false;
        }
        $temp = explode("_", $string);
        $method = $temp[0];
        $id = $temp[1];
        return array(
            "id"=>$id,
            "method"=>$method,
            "setting"=>$setting,
        );
    }

    public function getImages($section, $id, $folder, $getFirst = false) {
        $this->setSelect("image_id, path, type, extension");
        $this->setState("section", $section);
        $this->setState("section_id", $id);
        $this->setState("folder", $folder);
        if ($getFirst === false) {
            $result = $this->getAll();
        } else {
            $result = $this->getAll(1, 1);
            if(!empty($result)){
                $result = $result[0];
            }
        }
        return $result;
    }

    public function uploadImage($section, $id, $folder) {
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


            $path = $path . $folder;
            $files = $_FILES;
            if (count($files) > 0) {
                foreach ($files as $field => $data) {
                    foreach ($data['error'] as $file => $error) {
                        if ($error == 0) {
                            $ext = strtolower(strrchr(stripslashes($data['name'][$file]), "."));
                            $filename = uniqid('');
                            //$filename = date('Y-m-d-h-i-s').'-'.$filename;
                            $filename = $filename . $ext;
                            $upflag = move_uploaded_file($data['tmp_name'][$file], FCPATH . "$path/$filename");
                            if ($upflag) {
                                //$result[]=array("org"=$data['name'][$file], "new"=>$filename);
                                $data = array(
                                    "section" => $section,
                                    "section_id" => $id,
                                    "folder" => $folder,
                                    "org_name" => $data['name'][$file],
                                    "filename" => $filename,
                                    "extension" => str_replace(".", "", $ext),
                                    "type" => mime_content_type("$path/$filename"),
                                    "path" => "$path/$filename"
                                );
                                $image_id = $this->insert($data);
                                return $image_id;
                            }
                        } else {
                            return false;
                        }
                    }
                }
            } else {
                return false;
            }
        }
    }

    public function deleteImage($id){
        $image = $this->getById($id);
        if(empty($image)){
            return false;
        }
        @unlink(FCPATH . $image[0]["path"]);
        $this->delete($id);
        return true;
    }

    public function singleUpload($section, $folder, $name, $type, $tmp_name, $error, $size) {
        if ($section !== false && $folder !== false) {
            $path = 'assets/upload/';
            if (!is_dir($path . $section)) {
                @mkdir($path . $section);
                @chmod($path . $section, 0777);
            }
            $path = $path . $section . '/';
            if (!is_dir($path . $folder)) {
                @mkdir($path . $folder);
                @chmod($path . $folder, 0777);
            }
            $path = $path . $folder;

            if ($error == 0) {
                $ext = strtolower(strrchr(stripslashes($name), "."));
                $filename = uniqid('');
                //$filename = date('Y-m-d-h-i-s').'-'.$filename;
                $filename = $filename . $ext;
                $upflag = move_uploaded_file($tmp_name, FCPATH . "$path/$filename");
                if ($upflag) {
                    //$result[]=array("org"=$data['name'][$file], "new"=>$filename);
                    $data = array(
                        "section" => $section,
                        "folder" => $folder,
                        "org_name" => $name,
                        "filename" => $filename,
                        "extension" => str_replace(".", "", $ext),
                        "type" => mime_content_type("$path/$filename"),
                        "path" => "$path/$filename"
                    );
                    $image_id = $this->insert($data);
                    return $image_id;
                }
            } else {
                return false;
            }

        }
    }

    public function uploadSingleFile($section, $folder, $name) {
        if ($section !== false && $folder !== false) {
            $path = 'assets/upload/';
            if (!is_dir($path . $section)) {
                @mkdir($path . $section);
                @chmod($path . $section, 0777);
            }
            $path = $path . $section . '/';
            if (!is_dir($path . $folder)) {
                @mkdir($path . $folder);
                @chmod($path . $folder, 0777);
            }


            $path = $path . $folder;
            $file = $_FILES[$name];
            if ($file['error'] == 0) {
                $ext = strtolower(strrchr(stripslashes($file['name']), "."));
                $filename = uniqid('');
                //$filename = date('Y-m-d-h-i-s').'-'.$filename;
                $filename = $filename . $ext;
                $upflag = move_uploaded_file($file['tmp_name'], FCPATH . "$path/$filename");
                if ($upflag) {
                    //$result[]=array("org"=$data['name'][$file], "new"=>$filename);
                    $data = array(
                        "section" => $section,
                        "section_id" => $id,
                        "folder" => $folder,
                        "org_name" => $file['name'],
                        "filename" => $filename,
                        "extension" => str_replace(".", "", $ext),
                        "type" => mime_content_type("$path/$filename"),
                        "path" => "$path/$filename"
                    );
                    $image_id = $this->insert($data);
                    return $image_id;
                }
            } else {
                return false;
            }

        }
    }

    public function saveFromUrl($section, $folder, $url){
        if ($section !== false && $folder !== false) {
            $path = 'assets/upload/';
            if (!is_dir($path . $section)) {
                @mkdir($path . $section);
                @chmod($path . $section, 0777);
            }
            $path = $path . $section . '/';
            if (!is_dir($path . $folder)) {
                @mkdir($path . $folder);
                @chmod($path . $folder, 0777);
            }
            $path = $path . $folder;
            $filename = uniqid('').".jpg";
            file_put_contents(FCPATH . "$path/$filename", file_get_contents($url));

            $data = array(
                "section" => $section,
                "section_id" => $id,
                "folder" => $folder,
                "org_name" => "",
                "filename" => $filename,
                "extension" => "jpg",
                "type" => mime_content_type("$path/$filename"),
                "path" => "$path/$filename"
            );
            $image_id = $this->insert($data);
            return $image_id;

        }
    }

}

?>
