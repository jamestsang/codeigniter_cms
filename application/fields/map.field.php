<?php

class map_field extends form_field{
    
    public function html() {
        Resource::js("https://maps.googleapis.com/maps/api/js?key=AIzaSyBN1wLzEb2s02aHOyq2P4ekyEDCFJ2TlO8");
        Resource::js("assets/cms/js/map-finder.js?has_street=0");
        
        return '<div class="form-group">
                <label class="col-sm-2 control-label">'.$this->other["display_name"].'</label>
                <div class="col-sm-10">
                  <div class="map-finder"></div>
                </div>
              </div>';
    }

}

