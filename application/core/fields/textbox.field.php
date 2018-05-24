<?php

include_once 'form_field.php';

class textbox_field extends form_field{
    
    public function html() {
        return '
			<div class="form-group">
				<label class="col-sm-2 control-label" for="' . $this->name . '">' . $this->other["display_name"] . '</label>
				<div class="col-sm-10">
					<input type="' . (@$this->other["box_type"] != '' ? $this->other["box_type"] : 'text') . '" name="' . $this->field_name . '" class="form-control ' . $this->other["class"] . '" id="' . $this->name . '" value="' . $this->value . '" ' . $this->other["alt"] . ' title="' . $this->other["title"] . '" />
				</div>
			</div>
		';
    }
    
}
