<?php

include_once 'form_field.php';

class file_field extends form_field{
    
    public function html() {
    	$delField = "";
    	$preview = "";
    	$this->CI->load->model("image_model", "image");
    	if($this->value!=""){
    		$delField = '<input type="hidden" name="media_del['.str_replace(["[","]"], ["_", ""], $this->field_name).']" value="'.$this->value.'"/>';
    		$file = $this->CI->image->getById($this->value);
    		if(!empty($file)){
    			if($file[0]["type"]=="image/jpeg" || $file[0]["type"]=="image/png"){
    				$preview = '<img src="'.$this->CI->image->getImageLink("other", array("width" => 150), $this->value).'" />';
    			}else if(strpos($file[0]["type"], "video/") !== false){
    				$preview = '
						<div class="video-wrapper">
		                    <video id="video-player" class="video video-js vjs-big-play-centered" controls>
		                      <source src="'.base_url($file[0]["path"]).'" type="'.$file[0]["type"].'">
		                    </video>
		                </div>
    				';
    			}else if($file[0]["type"] == "application/pdf"){
    				$preview = '
						<a href="'.base_url($file[0]["path"]).'" target="_blank"><img src="'.asset_url("cms/images/pdf-image.png").'" style="width:100px;"/></a>
    				';
    			}else{
    				$preview = '
						<a href="'.base_url($file[0]["path"]).'" target="_blank"><img src="'.asset_url("cms/images/file-image.png").'" style="width:100px;"/></a>
    				';
    			}
    		}
    	}


        return '
			<div class="form-group">
				<label class="col-sm-2 control-label" for="' . $this->name . '">' . $this->other["display_name"] . '</label>
				<div class="col-sm-10">
					<div class="media-preview">'.$preview.'</div>
					<div class="file-input ' . $this->other["class"] . '">
                        <input type="file" name="' . $this->field_name . '" id="' . $this->name . '">
                        '.$delField.'
                        <span class="btn btn-primary">'.langc("Upload").'</span>
                        <p class="file-name"></p>
                    </div>
				</div>
			</div>
		';
    }
    
}
//<input type="' . (@$this->other["box_type"] != '' ? $this->other["box_type"] : 'text') . '" name="' . $this->field_name . '" class="form-control ' . $this->other["class"] . '" id="' . $this->name . '" value="' . $this->value . '" ' . $this->other["alt"] . ' title="' . $this->other["title"] . '" />