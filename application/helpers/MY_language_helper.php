<?php
	function lang($string, $id = '')
	{	
		$CI =& get_instance();
		$line = $CI->lang->line($string);

		if ($id != '')
		{
			$line = '<label for="'.$id.'">'.$line."</label>";
		}

		return (!$line)?$string:$line;
	}

	function langCode(){
		$CI =& get_instance();
		return $CI->lang->langCode();
	}

	function switch_uri($lang){
		$CI =& get_instance();
		return $CI->lang->switch_uri($lang);
	}

	function langId(){
		$CI =& get_instance();
		return $CI->lang->langId();
	}
?>