<?php

include_once 'form_field.php';

class select_field extends form_field{
    
    public function html() {
        $attrs = 'class="form-control ' . $this->other["class"] . '" id="' . $this->name . '" ' . $this->other["alt"] . ' title="' . $this->other["title"] . '"';
        $dataArray = array();
        if (isset($this->other["option"])) {
            $dataArray = $this->other["option"];
        } else {
            $dataArray = $this->options;
        }
        if (!is_array($dataArray)) {
            if (strpos($dataArray, "{DB}") >= 0) {
                $params = explode("@", str_replace("{DB}", "", $dataArray));
                $modelName = $params[0]."_model";
                $this->CI->load->model($params[0] . "_model", $modelName);

                if (count($params) > 3) {
                    $this->CI->$modelName->setState("language", $params[3]);
                }
                $dataArray = array(0=>"Please select");
                $dataArray = array_merge($dataArray, $this->CI->$modelName->toArray($params[1], $params[2]));
            }
        }

        foreach ($dataArray as $key => $value) {
            $dataArray[$key] = langc($value);
        }

        return '
			<div class="form-group">
				<label class="col-sm-2 control-label" for="' . $this->name . '">' . $this->other["display_name"] . '</label>
				<div class="col-sm-10">
					' . form_dropdown($this->field_name, $dataArray, $this->value, $attrs) . '
				</div>
			</div>
		';
    }
    
}
