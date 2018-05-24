<?php

require_once(APPPATH . 'third_party/Less/Autoloader.php');
Less_Autoloader::register();

class Resource {

    private static $headCss = array();
    private static $footCss = array();
    private static $headJs = array();
    private static $footJs = array();

    public static function clearJS(){
        self::$headJs = array();
        self::$footJs = array();
    }

    public static function clearCSS(){
        self::$headCss = array();
        self::$footCss = array();
    }

    public static function css($src, $isFoot = false) {
        if (is_array($src)) {
            foreach ($src as $s) {
                self::css($s, $isFoot);
            }
            return;
        } else {
            if (preg_match("/^.*\.(less)$/i", $src)) {
                $export = str_replace(".less", ".css", $src);
                try {
                    //lessc::ccompile("./" . $src, "./" . $export);

                    if (!is_file($export) || filemtime($src) > filemtime($export)) {
                        $parser = new Less_Parser();
                        $parser->parseFile($src, asset_url());
                        $css = $parser->getCss();
                        file_put_contents($export, $css);
                    }
                } catch (exception $ex) {
                    exit('lessc fatal error:<br />' . $ex->getMessage());
                }
            } else {
                $export = $src;
            }
            if(filter_var($export, FILTER_VALIDATE_URL)===false){
               $export = DOMAIN . $export;
            }
            if ($isFoot) {
                self::$footCss[] = $export;
            } else {
                self::$headCss[] = $export;
            }
        }
    }

    public static function js($src, $isFoot = false) {
        if (is_array($src)) {
            foreach ($src as $s) {
                self::js($s, $isFoot);
            }
            return;
        } else {
            $returnSrc = DOMAIN . $src;
            if (filter_var($src, FILTER_VALIDATE_URL)) {
                $returnSrc = $src;
            }
            if ($isFoot) {
                self::$footJs[] = $returnSrc;
            } else {
                self::$headJs[] = $returnSrc;
            }
        }
    }

    public static function getJS($isFoot = false) {
        if ($isFoot) {
            $data = self::$footJs;
        } else {
            $data = self::$headJs;
        }

        if (!empty($data)) {
            foreach ($data as $src) {
                echo '<script src="' . $src . '"></script>
                    ';
            }
        }
    }

    public static function getCSS($isFoot = false) {
        if ($isFoot) {
            $data = self::$footCss;
        } else {
            $data = self::$headCss;
        }
        if (!empty($data)) {
            foreach ($data as $src) {
                echo '<link href="' . $src . '" rel="stylesheet" type="text/css" />
                    ';
            }
        }
    }

    public static function getgoogl($longUrl) {

        $apiKey = 'google key';
        //Get API key from : http://code.google.com/apis/console/

        $postData = array('longUrl' => $longUrl, 'key' => $apiKey);
        $jsonData = json_encode($postData);

        $curlObj = curl_init();

        curl_setopt($curlObj, CURLOPT_URL, 'https://www.googleapis.com/urlshortener/v1/url?key=' . $apiKey);
        curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curlObj, CURLOPT_HEADER, 0);
        curl_setopt($curlObj, CURLOPT_HTTPHEADER, array('Content-type:application/json'));
        curl_setopt($curlObj, CURLOPT_POST, 1);
        curl_setopt($curlObj, CURLOPT_POSTFIELDS, $jsonData);

        $response = curl_exec($curlObj);

        //change the response json string to object
        $json = json_decode($response);

        curl_close($curlObj);

        return $json->id;
    }

}

?>