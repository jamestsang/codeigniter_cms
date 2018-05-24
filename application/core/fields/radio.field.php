<?php

include_once 'form_field.php';

class radio_field extends form_field {

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
        if (!empty($dataArray)) {
            foreach ($dataArray as $val => $text) {
                $opt_html .= '
					<div class="radio-inline radio-primary">
					  <label>
						<input type="radio" name="' . $this->field_name . '" value="' . $val . '" class="' . $this->other["class"] . '" ' . $this->other["alt"] . ' title="' . $text . '" ' . ($val == $this->value ? 'checked="checked"' : '') . ' />
						' . langc($text) . '
					  </label>
					</div>
				';
            }
        }
        return '
			<div class="form-group">
				<label class="col-sm-2 control-label" for="' . $this->name . '">' . $this->other["display_name"] . '</label>
				<div class="col-sm-10">
					' . $opt_html . '
				</div>
			</div>
		';
    }

}
