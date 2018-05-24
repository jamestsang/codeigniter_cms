<?php

include_once 'form_field.php';

class editor_field extends form_field{
    
    public function html() {
        return '
			<div class="form-group">
				<label class="col-sm-2 control-label" for="' . $this->name . '">' . $this->other["display_name"] . '</label>
				<div class="col-sm-10">
					<textarea class="form-control ' . $this->other["class"] . '" name="' . $this->field_name . '" id="' . $this->name . '" ' . $this->other["alt"] . ' title="' . $this->other["title"] . '" rows="3">' . $this->value . '</textarea>
				</div>
			</div>
		';
    }
    
}
