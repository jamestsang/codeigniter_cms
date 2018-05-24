<?php

	function base_lang_url($uri = '')
	{
		if (is_array($uri)) {
            $uri = implode('/', $uri);
        }

        $CI = & get_instance();
		$uri = $CI->lang->localized($uri);

		return $CI->config->base_url($uri);
	}

	function youtube_parse($uri = ''){
		$video_id = "";
		if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $uri, $match)) {
		    $video_id = $match[1];
		}
		return $video_id;
	}

	function base_current_url(){
		return base_url(uri_string());
	}
?>