<?php

class Language_model extends MY_Model {

    public function __construct() {
        parent::__construct('language');
    }

    public function getLanguage(){
        $this->db->order_by('default','DESC');
        $query=$this->db->get($this->table);
        if($query->num_rows()>0){
            foreach($query->result_array() as $l){
                $language[$l['language_id']]=$l;
            }
        }
        return $language;
    }

    public function getDefault(){
        $this->db->order_by('default','DESC');
        $this->db->where('default','1');
        $query=$this->db->get($this->table);
        if($query->num_rows()>0){
            foreach($query->result_array() as $l){
                $temp=$l;
            }
        }
        return $temp;
    }

    public function updateDefault($id){
        $data=array('default'=>'0');
        $this->db->update('language',$data);
        $this->db->where('language_id', $id);
        $data=array('default'=>'1');
        $r=$this->db->update('language',$data);
        return $r;
    }

}

?>
