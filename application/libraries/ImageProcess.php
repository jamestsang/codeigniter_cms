<?php

class ImageProcess {

    private $width;
    private $height;
    private $file;
    private $crop;
    private $bg;
    private $watermark;
    private $rotate;
    private $src_width;
    private $src_height;
    private $name;
    private $img;
    private $mode;
    private $filetype;
    private $wmRight;
    private $cache;
    private $cache_path;
    private $cache_name;

    /**
     * Class constructor, receive data.
     * @param Array $dataArray
     */
    public function __construct($dataArray, $cache = false, $path = 'cache/images', $cache_name = '') {
        $dataArray = array_merge(
                array(
            "width" => "",
            "height" => "",
            "file" => "",
            "crop" => "",
            "bg" => "",
            "wm" => "",
            "rot" => "",
            "wcon" => "",
                ), $dataArray
        );

        $this->width = $dataArray['width'];
        $this->height = $dataArray['height'];
        if (preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $dataArray['file'])) {
            $this->file = base64_decode($dataArray['file']);
        } else {
            $this->file = $dataArray['file'];
        }
        $this->crop = $dataArray['crop'];
        $this->bg = $dataArray['bg'];
        if (preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $dataArray['wm'])) {
            $this->watermark = base64_decode($dataArray['wm']);
        } else {
            $this->watermark = $dataArray['wm'];
        }
        $this->rotate = $dataArray['rot'];
        $this->wmRight = $dataArray['wcon'];
        $this->cache = $cache;
        if ($this->cache === true) {
            $this->cache_path = $path;
            if (!is_dir($this->cache_path)) {
                mkdir($this->cache_path, 0777);
            }
            $this->cache_name = $cache_name;
        }
    }

    /**
     * Generate image.
     * @return Bool
     */
    public function generate($no_output = false) {
        if (!$this->initInfo()) {
            return false;
        }
        $this->pic($no_output);
    }

    public function getPicCode() {
        if (!$this->initInfo()) {
            return false;
        }
        list($this->src_width, $this->src_height) = getimagesize($this->file);
        $this->name = basename($this->file);
        $this->filetype = strtolower(substr(strrchr($this->name, "."), 1));
        if ($this->filetype == "gif")
            $this->img = imagecreatefromgif($this->file);
        elseif ($this->filetype == "jpg" || $this->filetype == "jpeg")
            $this->img = imagecreatefromjpeg($this->file);
        elseif ($this->filetype == "png")
            $this->img = imagecreatefrompng($this->file);

        switch ($this->mode) {
            case'normal':$im = $this->normalImg();
                break;
            case'crop':$im = $this->cropImg();
                break;
            case'bg':$im = $this->bgImg();
                break;
            case'origin':$im = $this->getOrg();
                break;
            case'resizeWidth':$im = $this->resizeByWidth();
                break;
            case'resizeHeight':$im = $this->resizeByHeight();
                break;
        }

        if (trim($this->watermark) != '') {
            $im = $this->waterMark($im);
        }

        return $im;
    }

    private function getOrg() {
        if ($this->filetype == 'png') {
            $im = imagecreatetruecolor($this->src_width, $this->src_height);
            imagealphablending($im, false);
            imagesavealpha($im, true);
            //$transparent = imagecolorallocatealpha($im, 255, 255, 255, 127);
            $transparentIndex = imagecolorallocate($im, 0x66, 0xff, 0x66);
            imagefill($im, 0, 0, $transparentIndex);
            imagecolortransparent($im, $transparentIndex);
            //imagecopyresampled($im,$this->img,0,0,0,0,$this->src_width,$this->src_height,$this->src_width,$this->src_height);
            imagecopy($im, $this->img, 0, 0, 0, 0, $this->src_width, $this->src_height);
            return $im;
        } else {
            return $this->img;
        }
    }

    private function initInfo() {
        if (trim($this->file) == '') {
            $this->error("No file specified");
            return false;
        } elseif (preg_match('/\(.\)/', $this->file)) {
            $this->error("File not found");
            return false;
        } elseif (!file_exists($this->file) || is_dir($this->file)) {
            $this->error("File not found");
            return false;
        } elseif ($this->watermark != '' && (!file_exists($this->watermark) || is_dir($this->watermark))) {
            $this->error("Water Mark not found");
            return false;
        }

        if (trim($this->width) == '' && trim($this->height) == '') {
            $this->mode = 'origin';
        } else if (trim($this->width) == '') {
            $this->mode = 'resizeHeight';
        } else if (trim($this->height) == '') {
            $this->mode = 'resizeWidth';
        } else if (trim($this->bg) == '' && trim($this->crop) == '') {
            $this->mode = 'normal';
        } else if (trim($this->crop) != '' && $this->crop == 'y') {
            $this->mode = 'crop';
        } else if (trim($this->bg) != '') {
            $this->mode = 'bg';
        }

        return true;
    }

    private function normalImg() {
        $sr = $this->src_width / $this->src_height;
        $dr = $this->width / $this->height;
        if ($sr > $dr) {
            $d_width = ($this->src_width > $this->width) ? $this->width : $this->src_width;
            $d_height = $this->src_height * $d_width / $this->src_width;
        } elseif ($sr < $dr) {
            $d_height = ($this->src_height > $this->height) ? $this->height : $this->src_height;
            $d_width = $this->src_width * $d_height / $this->src_height;
        } else {
            if ($this->width > $this->height) {
                $d_width = ($this->src_width > $this->width) ? $this->width : $this->src_width;
                $d_height = $this->src_height * $d_width / $this->src_width;
            } elseif ($this->width < $this->height) {
                $d_height = ($this->src_height > $this->height) ? $this->height : $this->src_height;
                $d_width = $this->src_width * $d_height / $this->src_height;
            } else {
                $d_height = ($this->src_height > $this->height) ? $this->height : $this->src_height;
                $d_width = ($this->src_width > $this->width) ? $this->width : $this->src_width;
            }
        }
        $im = imagecreatetruecolor($d_width, $d_height);

        if ($this->filetype == 'png') {
            imagealphablending($im, false);
            imagesavealpha($im, true);
            $transparent = imagecolorallocatealpha($im, 255, 255, 255, 127);
            imagefilledrectangle($im, 0, 0, $d_width, $d_height, $transparent);
        } else if ($this->filetype == 'gif') {
            $this->setTransparency($im, $this->img);
        }

        imagecopyresampled($im, $this->img, 0, 0, 0, 0, $d_width, $d_height, $this->src_width, $this->src_height);
        return $im;
    }

    private function resizeByWidth() {
        if ($this->src_width <= $this->width) {
            $d_width = $this->src_width;
            $d_height = $this->src_height;
        } else {
            $sr = $this->src_width / $this->src_height;
            $d_width = $this->width;
            $d_height = $this->width / $sr;
        }
        $im = imagecreatetruecolor($d_width, $d_height);

        if ($this->filetype == 'png') {
            imagealphablending($im, false);
            imagesavealpha($im, true);
            $transparent = imagecolorallocatealpha($im, 255, 255, 255, 127);
            imagefilledrectangle($im, 0, 0, $d_width, $d_width, $transparent);
        } else if ($this->filetype == 'gif') {
            $this->setTransparency($im, $this->img);
        }

        imagecopyresampled($im, $this->img, 0, 0, 0, 0, $d_width, $d_height, $this->src_width, $this->src_height);
        return $im;
    }

    private function resizeByHeight() {
        if ($this->src_height <= $this->height) {
            $d_width = $this->src_width;
            $d_height = $this->src_height;
        } else {
            $sr = $this->src_height / $this->src_width;
            $d_width = $this->height / $sr;
            $d_height = $this->height;
        }
        $im = imagecreatetruecolor($d_width, $d_height);

        if ($this->filetype == 'png') {
            imagealphablending($im, false);
            imagesavealpha($im, true);
            $transparent = imagecolorallocatealpha($im, 255, 255, 255, 127);
            imagefilledrectangle($im, 0, 0, $d_width, $d_width, $transparent);
        } else if ($this->filetype == 'gif') {
            $this->setTransparency($im, $this->img);
        }

        imagecopyresampled($im, $this->img, 0, 0, 0, 0, $d_width, $d_height, $this->src_width, $this->src_height);
        return $im;
    }

    private function pic($no_output = false) {
        $im = $this->getPicCode();
        header('Content-Description: File Transfer');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        if ($this->cache === true) {
            if ($this->filetype == "gif") {
                header("Content-type: image/gif");
                imagegif($im, $this->cache_path . '/' . $this->cache_name, 100);
            } else if ($this->filetype == "jpg" || $this->filetype == "jpeg") {
                header("Content-type: image/jpeg");
                imagejpeg($im, $this->cache_path . '/' . $this->cache_name, 100);
            } else if ($this->filetype == "png") {
                header("Content-type: image/png");
                imagepng($im, $this->cache_path . '/' . $this->cache_name, 9);
            }
        }
        ob_start();
        if ($this->filetype == "gif") {
            header("Content-type: image/gif");
            imagegif($im, null, 100);
        } else if ($this->filetype == "jpg" || $this->filetype == "jpeg") {
            header("Content-type: image/jpeg");
            imagejpeg($im, null, 100);
        } else if ($this->filetype == "png") {
            header("Content-type: image/png");
            imagepng($im, null, 9);
        }
        if($no_output === false){
            $output = ob_get_contents();
            ob_end_clean();
            header('Content-Length: ' . strlen($output));
            echo $output;
        }
        imageDestroy($im);
    }

    public function getBlob() {
        $im = $this->getPicCode();
        ob_start();
        if ($this->filetype == "gif") {
            imagegif($im, null, 100);
        } else if ($this->filetype == "jpg" || $this->filetype == "jpeg") {
            imagejpeg($im, null, 100);
        } else if ($this->filetype == "png") {
            imagepng($im, null, 9);
        }
        $blob = ob_get_contents();
        ob_end_clean();
        return $blob;
    }

    private function cropImg() {
        $ratio_orig = $this->src_width / $this->src_height;
        if ($this->width / $this->height > $ratio_orig) {
            $new_height = $this->width / $ratio_orig;
            $new_width = $this->width;
        } else {
            $new_width = $this->height * $ratio_orig;
            $new_height = $this->height;
        }

        $x_mid = $new_width / 2;  //horizontal middle
        $y_mid = $new_height / 2; //vertical middle

        $process = imagecreatetruecolor(round($new_width), round($new_height));


        if ($this->filetype == 'png') {
            imagealphablending($process, false);
            imagesavealpha($process, true);
            $transparent = imagecolorallocatealpha($process, 255, 255, 255, 127);
            imagefilledrectangle($process, 0, 0, $this->width, $this->height, $transparent);
        } else if ($this->filetype == 'gif') {
            $this->setTransparency($process, $this->img);
        }

        imagecopyresampled($process, $this->img, 0, 0, 0, 0, $new_width, $new_height, $this->src_width, $this->src_height);

        $im = imagecreatetruecolor($this->width, $this->height);

        if ($this->filetype == 'png') {
            imagealphablending($im, false);
            imagesavealpha($im, true);
            $transparent = imagecolorallocatealpha($im, 255, 255, 255, 127);
            imagefilledrectangle($im, 0, 0, $this->width, $this->height, $transparent);
        } else if ($this->filetype == 'gif') {
            $this->setTransparency($im, $this->img);
        }
        imagecopyresampled($im, $process, 0, 0, ($x_mid - ($this->width / 2)), ($y_mid - ($this->height / 2)), $this->width, $this->height, $this->width, $this->height);
        return $im;
    }

    private function bgImg() {
        $im = imagecreatetruecolor($this->width, $this->height);

        if (strlen($this->bg) == 6) {
            $this->bg = imagecolorallocate($im, hexdec(substr($this->bg, 0, 2)), hexdec(substr($this->bg, 2, 2)), hexdec(substr($this->bg, 4, 2)));
        } else {
            $this->bg = imagecolorallocate($im, 255, 255, 255);
        }

        $sr = $this->src_width / $this->src_height;
        $dr = $this->width / $this->height;

        if ($sr > $dr) {
            $d_width = ($this->src_width > $this->width) ? $this->width : $this->src_width;
            $d_height = $this->src_height * $d_width / $this->src_width;
        } elseif ($sr < $dr) {
            $d_height = ($this->src_height > $this->height) ? $this->height : $this->src_height;
            $d_width = $this->src_width * $d_height / $this->src_height;
        } else {
            if ($this->width > $this->height) {
                $d_width = ($this->src_width > $this->width) ? $this->width : $this->src_width;
                $d_height = $this->src_height * $d_width / $this->src_width;
            } elseif ($this->width < $this->height) {
                $d_height = ($this->src_height > $this->height) ? $this->height : $this->src_height;
                $d_width = $this->src_width * $d_height / $this->src_height;
            } else {
                $d_height = ($this->src_height > $this->height) ? $this->height : $this->src_height;
                $d_width = ($this->src_width > $this->width) ? $this->width : $this->src_width;
            }
        }

        $d_x = ($this->width - $d_width) / 2;
        ;
        $d_y = ($this->height - $d_height) / 2;

        /*
         * Below code set png background to alpha,
         *
         *
          if($this->filetype == 'png'){
          imagealphablending($im, false);
          imagesavealpha($im,true);
          $transparent = imagecolorallocatealpha($im, 255, 255, 255, 127);
          imagefilledrectangle($im, 0, 0, $this->width, $this->height, $transparent);
          }else{
          imagefilledrectangle($im,0,0,$this->width,$this->height,$this->bg);
          } */
        imagefilledrectangle($im, 0, 0, $this->width, $this->height, $this->bg);
        imagecopyresampled($im, $this->img, $d_x, $d_y, 0, 0, $d_width, $d_height, $this->src_width, $this->src_height);
        return $im;
    }

    private function waterMark($im) {
        //$watermark = @imagecreatefrompng($this->watermark);
        $name = basename($this->watermark);
        $filetype = strtolower(substr(strrchr($name, "."), 1));
        if ($filetype == "gif")
            $watermark = imagecreatefromgif($this->watermark);
        elseif ($filetype == "jpg" || $this->filetype == "jpeg")
            $watermark = imagecreatefromjpeg($this->watermark);
        elseif ($filetype == "png")
            $watermark = imagecreatefrompng($this->watermark);
        elseif ($filetype == "bmp")
            $watermark = $this->imagecreatefrombmp($this->watermark);
        imagealphablending($watermark, false);
        imagesavealpha($watermark, false);
        $imagewidth = imagesx($im);
        $imageheight = imagesy($im);
        $watermarkwidth = imagesx($watermark);
        $watermarkheight = imagesy($watermark);
        if ($watermarkwidth >= $imagewidth || $imageheight <= $watermarkheight) {
            $watermark = $this->waterMarkCenterResize($this->watermark, $imagewidth, $imageheight, $watermarkwidth, $watermarkheight);
        }
        if (trim($this->rotate) != '' && $this->rotate == 'y') {
            $watermark = $this->waterMarkRotate($this->watermark, $imagewidth, $imageheight, $watermarkwidth, $watermarkheight);
        }
        $startwidth = (($imagewidth - $watermarkwidth) / 2);
        $startheight = (($imageheight - $watermarkheight) / 2);
        if (trim($this->wmRight) != '' && $this->wmRight == 'y') {
            $watermark = $this->waterMarkSetToCorner($this->watermark, $imagewidth, $imageheight, $watermarkwidth, $watermarkheight, $startwidth, $startheight);
        }
        imageCopy($im, $watermark, $startwidth, $startheight, 0, 0, $watermarkwidth, $watermarkheight);
        return $im;
    }

    private function waterMarkSetToCorner($file, $imagewidth, $imageheight, &$watermarkwidth, &$watermarkheight, &$startwidth, &$startheight) {
        $newWater = new generateImage(array('file' => $file, 'width' => (($imagewidth / 10) * 6), 'height' => ($imageheight / 5)));
        $watermark = $newWater->getPicCode();
        $watermarkwidth = imagesx($watermark);
        $watermarkheight = imagesy($watermark);
        $startwidth = $imagewidth - $watermarkwidth - 12;
        $startheight = $imageheight - $watermarkheight - 6;
        return $watermark;
    }

    private function waterMarkCenterResize($file, $imWidth, $imHeight, &$resultWidth, &$resultHeight) {
        $newWater = new generateImage(array('file' => $file, 'width' => $imWidth, 'height' => $imHeight));
        $watermark = $newWater->getPicCode();
        $resultWidth = imagesx($watermark);
        $resultHeight = imagesy($watermark);
        return $watermark;
    }

    private function waterMarkRotate($file, $imWidth, $imHeight, &$resultWidth, &$resultHeight) {
        $rotateWidth = sqrt(($imWidth * $imWidth) + ($imHeight * $imHeight)) - 100;
        $newWater = new generateImage(array('file' => $file, 'width' => ($rotateWidth <= 0) ? $imWidth : $rotateWidth, 'height' => $imHeight));
        $watermark = $newWater->getPicCode();
        $watermark = imagerotate($watermark, 35, -1);
        imagealphablending($watermark, true);
        imagesavealpha($watermark, true);
        $resultWidth = imagesx($watermark);
        $resultHeight = imagesy($watermark);
        return $watermark;
    }

    private function error($msg, $width = 71, $height = 71) {
        $im = imagecreate($width, $height);
        $bg = imagecolorallocate($im, 255, 255, 255);
        $color = imagecolorallocate($im, 90, 90, 90);
        imagestring($im, 1, 15, 30, $msg, $color);
        header("Content-type: image/jpeg");
        imagejpeg($im);
        imagedestroy($im);
    }

    private function setTransparency($new_image, $image_source) {
        $transparencyIndex = imagecolortransparent($image_source);
        $transparencyColor = array('red' => 255, 'green' => 255, 'blue' => 255);

        if ($transparencyIndex >= 0) {
            $transparencyColor = imagecolorsforindex($image_source, $transparencyIndex);
        }

        $transparencyIndex = imagecolorallocate($new_image, $transparencyColor['red'], $transparencyColor['green'], $transparencyColor['blue']);
        imagefill($new_image, 0, 0, $transparencyIndex);
        imagecolortransparent($new_image, $transparencyIndex);
    }

}

?>