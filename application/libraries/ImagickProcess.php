<?php

class ImagickProcess {

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
    private $blur;
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
                    "blur" => ""
                ), $dataArray
        );

        $this->width = $dataArray['width'];
        $this->height = $dataArray['height'];
        $this->file = $dataArray['file'];
        $this->crop = $dataArray['crop'];
        $this->bg = $dataArray['bg'];
        $this->watermark = $dataArray['wm'];
        $this->rotate = $dataArray['rot'];
        $this->wmRight = $dataArray['wcon'];
        $this->blur = $dataArray["blur"];
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
        if(is_object($this->file)){
            $this->img = new Imagick();
            $this->img->readImageBlob($this->file->body);
            $this->name = basename($this->file->headers["filename"]);
            $this->filetype = strtolower(substr(strrchr($this->file->headers["filename"], "."), 1));
        }else{
            $this->img = new Imagick($this->file);
            $this->name = basename($this->file);
            $this->filetype = strtolower(substr(strrchr($this->name, "."), 1));
        }
        
        $d = $this->img->getImageGeometry(); 
        $this->src_width = $d['width']; 
        $this->src_height = $d['height'];
        
        switch ($this->mode) {
            case'normal':$this->normalImg();
                break;
            case'crop':$this->cropImg();
                break;
            case'bg':$this->bgImg();
                break;
            case'origin': $this->getOrg();
                break;
            case'resizeWidth':$this->resizeByWidth();
                break;
            case'resizeHeight':$this->resizeByHeight();
                break;
        }

        if($this->blur == "y"){
            $this->blur();
        }
        
        if (trim($this->watermark) != '') {
            $this->waterMark();
        }

    }
    
    private function initInfo() {
        if(is_object($this->file)){
            
        }else{
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
    
    private function pic($no_output = false) {
        $this->getPicCode();
        header('Content-Description: File Transfer');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: max-age=86400');
        header('Expires: '. gmdate('D, d M Y H:i:s \G\M\T', time() + 86400));
        header('Pragma: public');
        if ($this->filetype == "gif") {
            header("Content-type: image/gif");
            $this->img->setImageFormat('gif');
        } else if ($this->filetype == "jpg" || $this->filetype == "jpeg") {
            header("Content-type: image/jpeg");
            $this->img->setImageFormat('jpeg');
        } else if ($this->filetype == "png") {
            header("Content-type: image/png");
            $this->img->setImageFormat('png');
        }
        $output = $this->img->getImageBlob();
        
        if ($this->cache === true) {
            file_put_contents($this->cache_path . '/' . $this->cache_name, $output);
        }
        
        if($no_output === false){
            header('Content-Length: ' . strlen($output));
            echo $output;
        }
    }

    public function getBlob() {
        $this->getPicCode();
        if ($this->filetype == "gif") {
            $this->img->setImageFormat('gif');
        } else if ($this->filetype == "jpg" || $this->filetype == "jpeg") {
            $this->img->setImageFormat('jpeg');
        } else if ($this->filetype == "png") {
            $this->img->setImageFormat('png');
        }
        return array(
                    "type"=>$this->filetype,
                    "blob"=>$this->img->getImageBlob()
                );
    }

    private function getOrg() {
        
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
        $this->img->thumbnailImage($d_width, $d_height);
    }

    private function resizeByWidth() {
        $this->img->thumbnailImage($this->width, 0);
    }

    private function resizeByHeight() {
        $this->img->thumbnailImage(0, $this->height);
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
        
        $this->img->cropThumbnailImage($this->width, $this->height);
    }

    private function bgImg() {
        $background = '#'.$this->bg;
        if($this->filetype == "png" && $this->bg == "transparent"){
            $background = 'None';
        }

        $this->img->scaleImage($this->width,$this->height,true);
        $this->img->setImageBackgroundColor($background);
        $w = $this->img->getImageWidth();
        $h = $this->img->getImageHeight();
        $this->img->extentImage($this->width,$this->height,($w-$this->width)/2,($h-$this->height)/2);
    }
    
    private function blur(){
        $this->img->gaussianBlurImage(13, 13);
    }

    private function waterMark() {
        $imagewidth = $this->img->getImageWidth();
        $imageheight = $this->img->getImageHeight();
        $watermark = new Imagick($this->watermark);

        $watermarkwidth = $watermark->getImageWidth();
        $watermarkheight = $watermark->getImageHeight();

        if ($watermarkwidth >= $imagewidth || $imageheight <= $watermarkheight) {
            $this->waterMarkResize($watermark, $imagewidth, $imageheight);
        }
        if (trim($this->rotate) != '' && $this->rotate == 'y') {
            $this->waterMarkRotate($watermark, $imagewidth, $imageheight);
        }
        
        $startwidth = (($imagewidth - $watermark->getImageWidth()) / 2);
        $startheight = (($imageheight - $watermark->getImageHeight()) / 2);
        if (trim($this->wmRight) != '' && $this->wmRight == 'y') {
            $this->waterMarkSetToCorner($watermark, $imagewidth, $imageheight, $startwidth, $startheight);
        }
        $this->img->compositeImage($watermark, imagick::COMPOSITE_OVER, $startwidth, $startheight);
        $this->img->setimagecompressionquality( 100 );
    }
    
    private function waterMarkResize(&$watermark, $imWidth, $imHeight){
        $wmWidth = $watermark->getImageWidth();
        $wmHeight = $watermark->getImageHeight();
        $sr = $wmWidth / $wmHeight;
        $dr = $imWidth / $imHeight;
        if ($sr > $dr) {
            $d_width = ($wmWidth > $this->width) ? $imWidth : $wmWidth;
            $d_height = $wmHeight * $d_width / $wmWidth;
        } elseif ($sr < $dr) {
            $d_height = ($wmHeight > $imHeight) ? $imHeight : $wmHeight;
            $d_width = $wmWidth * $d_height / $wmHeight;
        } else {
            if ($imWidth> $imHeight) {
                $d_width = ($wmWidth > $imWidth) ? $imWidth : $wmWidth;
                $d_height = $wmHeight * $d_width / $wmWidth;
            } elseif ($imWidth < $imHeight) {
                $d_height = ($wmHeight > $imHeight) ? $imHeight : $wmHeight;
                $d_width = $wmWidth * $d_height / $wmHeight;
            } else {
                $d_height = ($wmHeight > $imHeight) ? $imHeight : $wmHeight;
                $d_width = ($wmWidth > $imWidth) ? $imWidth : $wmWidth;
            }
        }
        $watermark->thumbnailImage($d_width, $d_height);
    }
    
    private function waterMarkRotate(&$watermark, $imWidth, $imHeight) {
        $rotateWidth = sqrt(($imWidth * $imWidth) + ($imHeight * $imHeight)) - 100;
        $watermark->scaleImage(($rotateWidth <= 0) ? $imWidth : $rotateWidth, $imHeight);
        $watermark->rotateImage(new ImagickPixel("None"), 35);
        return $watermark;
    }

    private function waterMarkSetToCorner(&$watermark, $imagewidth, $imageheight,&$startwidth, &$startheight) {
        $watermark->scaleImage((($imagewidth / 10) * 3), 0);
        $startwidth = $imagewidth - $watermark->getImageWidth() - 12;
        $startheight = $imageheight - $watermark->getImageHeight() - 6;
    }

    private function error($text, $width = 100, $height = 100) {
        
        /* Create Imagick objects */
        $image = new Imagick();
        $draw = new ImagickDraw();
        $color = new ImagickPixel('#000000');
        $background = new ImagickPixel('white'); // Transparent

        /* Font properties */
        //$draw->setFont('Arial');
        $draw->setFontSize(15);
        $draw->setFillColor($color);
        $draw->setStrokeAntialias(true);
        $draw->setTextAntialias(true);

        /* Get font metrics */
        $metrics = $image->queryFontMetrics($draw, $text);

        /* Create text */
        $draw->annotation(0, $metrics['ascender'], $text);

        /* Create image */
        $image->newImage($width, $height, $background);
        $image->setImageFormat('jpeg');
        //$image->drawImage($draw);
        $draw->setGravity (Imagick::GRAVITY_CENTER);
        $image->annotateImage($draw, 0, 0, 0, $text);
        
        header("Content-type: image/jpeg");
        
        echo $image;
        return;
    }
    
    //Generate user placeholder
    public static function userPlaceholder($name, $isChinese, $width, $height, $bg){
        /* Create Imagick objects */
        $image = new Imagick();
        $draw = new ImagickDraw();
        $color = new ImagickPixel('#8c9192');
        $background = new ImagickPixel($bg); // Transparent

        /* Font properties */
        $draw->setFont(APPPATH.'/fonts/dffn_r3.ttc');
        $draw->setTextEncoding('UTF-8');
        $draw->setFontSize(250);
        $draw->setFillColor($color);
        $draw->setStrokeAntialias(true);
        $draw->setTextAntialias(true);

        /* Get font metrics */
        $metrics = $image->queryFontMetrics($draw, $name);

        /* Create text */
        $draw->annotation(0, $metrics['ascender'], $name);

        /* Create image */
        $image->newImage(500, 500, $background);
        $image->setImageFormat('png');
        //$image->drawImage($draw);
        $draw->setGravity (Imagick::GRAVITY_CENTER);
        $image->annotateImage($draw, 0, 0, 0, $name);
        $image->thumbnailImage($width, $height);
        
        return $image->getImageBlob();
    }
}

?>