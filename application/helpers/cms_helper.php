<?php

class CMS {

    private static $js_list = array();
    private static $css_list = array();
    
    public static function msg($type, $title, $content, $back = false) {
        if ($back !== false) {
            $back = '<a href="' . base_url("cms/" . $back) . '">Back.</a>';
        }
        return '
			<div class="alert alert-' . $type . '">
				<strong>' . $title . '</strong>
				' . $content . '
			    ' . $back . '
			</div>
		';
    }

    public static function perPageSelect($total, $per_page, $base_link) {
        $i = 0;
        $select = '<select class="per-page-select form-control">';
        if ($total > 200) {
            $total = 200;
        }
        while ($i < $total) {
            $i += 20;
            $select .= '<option value="' . $base_link . '&show_page=' . $i . '" ' . ($per_page == $i ? 'selected="selected"' : "") . '>' . $i . '</option>';
        }
        $select .= '</select>';
        return $select;
    }
    
    /*public static function addJs($path){
        self::$js_list[] = $path;
    }
    
    public static function addCss($path){
        self::$css_list[] = $path;
    }
    
    public static function getJs(){
        return self::$js_list;
    }

    public static function getCSS(){
        return self::$css_list;
    }*/
}
