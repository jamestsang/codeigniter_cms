<?php

include_once 'form_field.php';

class checkbox_field extends form_field{
    
    public function html() {
        $dataArray = array();
        if ($this->options == null) {
            $dataArray = $this->other["option"];
        } else {
            $dataArray = $this->options;
        }
        if (!is_array($dataArray)) {
            if (strpos($dataArray, "{DB}") >= 0) {
                $params = explode("@", str_replace("{DB}", "", $dataArray));
                $this->CI->load->model($params[0] . "_model", "select_model");
                if (count($params) > 3) {
                    $this->CI->select_model->setState("language", $params[3]);
                }
                $dataArray = $this->CI->select_model->toArray($params[1], $params[2]);
            }
        }

        $opt_html = "";
        $temp_array = is_array(@unserialize($this->value))?unserialize($this->value):array();
        if (!is_array($temp_array)) {
            $temp_array = array();
        }

        if (!empty($dataArray)) {
            foreach ($dataArray as $val => $text) {
                $opt_html .= '
					<div class="checkbox">
					  <label>
						<input type="checkbox" name="' . $this->field_name . '[]" value="' . $val . '" class="' . $this->other["class"] . '" ' . $this->other["alt"] . ' title="' . $text . '" ' . (in_array($val, $temp_array) ? 'checked="checked"' : '') . ' />
						<span class="">' . langc($text) . '</span>
					  </label>
					</div>
				';
            }
        }
        return '
			<div class="form-group">
				<label class="col-sm-2 control-label">' . $this->other["display_name"] . '</label>
				<div class="col-sm-10">
					' . $opt_html . '
				</div>
			</div>
		';
    }
    
}
