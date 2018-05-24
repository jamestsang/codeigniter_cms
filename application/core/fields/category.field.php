<?php

include_once 'form_field.php';

class category_field extends form_field {

    public function html() {
        $this->CI->load->model("category_model", "cat_model");
        $this->CI->cat_model->setState("extension", $this->other["extension"]);
        $this->CI->cat_model->setState("language", 1);
        $list = $this->CI->cat_model->getAll();

        $listData = array();

        if (!empty($list)) {
            foreach ($list as $cat) {
                $listData[$cat["category_id"]] = $cat["title"];
            }
        }

        $attrs = 'class="form-control ' . $this->other["class"] . '" id="' . $this->name . '" ' . $this->other["alt"] . ' title="' . $this->other["title"] . '"';

        return '
			<div class="form-group">
				<label class="col-sm-2 control-label" for="' . $this->name . '">' . $this->other["display_name"] . '</label>
				<div class="col-sm-10">
					' . form_dropdown($this->field_name, $listData, $this->value, $attrs) . '
				</div>
			</div>
		';
    }

}
