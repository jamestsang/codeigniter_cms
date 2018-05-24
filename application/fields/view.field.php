<?php

class view_field extends form_field{
    
    public function html() {
        $value = $this->value;
        if(@$this->other["serialized"] === true){
            $temp = unserialize($this->value);
            $value = $this->genList($temp);
        }
        return '<div class="form-group">
                <label class="col-sm-2 control-label">'.$this->other["display_name"].'</label>
                <div class="col-sm-10">
                  <p class="form-control-static">'.langc($value).'</p>
                </div>
              </div>';
    }
    
    private function genList($data, $result=""){
        $result .= '<ul style="margin-left: 20px">';
        foreach($data as $key => $val){
            if(is_array($val)){
                $result .= '<li><strong>'.$key.'</strong> :'.$this->genList($val).'</li>';
            }else{
                $result .= '<li><strong>'.$key.'</strong> : <span>'.$val.'</span></li>';
            }
        }
        $result .= '</ul>';
        return $result;
        
    }

}

