<?php

class MY_Model extends CI_Model {

    protected $table;
    protected $default_order;
    protected $default_direction;
    protected $has_ordering;
    protected $state;
    protected $select;
    protected $join;
    public static $lang;
    public $extraState;
    protected $multi_language = false;

    public function __construct($table = "", $ordering = "id", $direction = "DESC", $has_ordering = true, $multi_language = false) {
        parent::__construct();
        $this->table = $table;
        $this->default_order = $ordering;
        $this->default_direction = $direction;
        $this->state = array("a.deleted"=>0);
        $this->select = "a.*";
        $this->has_ordering = $has_ordering;
        $this->join = array();
        $this->multi_language = $multi_language;

        self::$lang = $this->session->userdata('default_language');
    }

    public function checkAliasUni($alias, $id = 0){
        $this->setState("alias", $alias);
        if($id != 0){
            $this->setState($this->table."_id !=", $id);
        }
        $result = $this->getAll();
        return (count($result) == 0);
    }

    public function to_slug($string, $separator = '-') {
        $re = "/(\\s|\\".$separator.")+/mu";
        $str = @trim($string);
        $subst = $separator;
        $result = preg_replace($re, $subst, $str);
        return $result;
    }

    public function setState($name, $value = "") {
        if (is_array($name)) {
            foreach ($name as $key => $value) {
                $this->setState($key, $value);
            }
        } else {
            $this->state[$name] = $value;
        }
    }

    public function simpleJoin($table, $pair){
        $this->join[$table] = $pair;
    }
    
    public function resetJoin(){
        $this->join =array();
    }
    
    public function getState($name) {
        return $this->state[$name];
    }
    
    public function getAllState() {
        return $this->state;
    }

    public function setSelect($select) {
        $this->select = $select;
    }

    public function defaultLanguage(){
        $this->setState("a.language", self::$lang);
    }

    public function resetState($resetDelete = false) {
        if ($resetDelete) {
            $this->state = array();
        } else {
            $this->state = array("a.deleted"=>0);
        }
    }

    protected function processState() {
        $this->db->select($this->select, false);
        if (!empty($this->state)) {
            foreach ($this->state as $name => $value) {
                if (is_array($value)) {
                    $this->db->where_in($name, $value);
                } else {
                    if (is_numeric($name)) {
                        $this->db->where($value);
                    } else {
                        $this->db->where($name, $value);
                    }
                }
            }
            //$this->state = array();
        }
        if(!empty($this->extraState)){
            $this->db->where($this->extraState, null, false);
        }
        if(!empty($this->join)){
            foreach($this->join as $table=>$pair){
                $this->db->join($this->db->dbprefix($table), $pair, "left");
            }
        }
        // if($this->multi_language === true){
        //     $this->db->where("language", self::$lang);
        // }
    }

    private function getOrdering($ordering) {
        return $ordering === false ? $this->default_order : $ordering;
    }

    private function getDirection($direction) {
        return $direction === false ? $this->default_direction : $direction;
    }

    public function switcher($field_name, $condition, $attr) {
        $sql = "UPDATE " . $this->db->dbprefix($this->table) . " set $field_name= ($field_name+1)%2 where $condition='$attr' ";
        return $this->db->query($sql);
    }

    public function get_max($param) {
        $this->db->select_max($param, 'max_value');
        $query = $this->db->get($this->table);
        if ($query->num_rows() > 0) {
            $row = $query->row();
            return $row->max_value;
        }
        return 0;
    }

    public function delete($id) {
        $result = $this->db->delete($this->table, array($this->table . '_id' => $id));
        //$this->rmdir_r(FCPATH.'source/upload/' . $this->table . '/' . $id);
        return $result;
    }

    public function fakeDelete($id) {
        $result = $this->activeUpdate(array("deleted" => 1), array($this->table . '_id' => $id));
        if (!$result)
            return false;
        return $result;
    }

    public function get_total() {
        $this->db->from($this->table.' a');
        $this->processState();
        
        return $this->db->count_all_results();
    }

    public function getById($id, $language = false) {
        $this->db->from($this->table.' a');
        $this->processState();
        $this->db->where("a.".$this->table . '_id', $id);
        if ($language !== false) {
            $this->db->where("a.language", $language);
        }
        $query = $this->db->get();
        // echo $this->db->last_query();
        return $query->result_array();
    }

    public function getAll($page = false, $perPage = false, $orderBy = false, $direction = false) {
        $this->db->from($this->table.' a');
        $this->processState();

        $this->db->order_by($this->getOrdering($orderBy) . ' ' . $this->getDirection($direction));
        if ($page !== false && $perPage !== false) {
            $startPage = ($page * $perPage) - $perPage;
            $this->db->limit($perPage, $startPage);
        }
        $query = $this->db->get();
        // echo $this->db->last_query();
        return $query->result_array();
    }

    public function __call($method, $arguments) {
        if ($method == 'insert') {
            if (count($arguments) == 1) {
                return call_user_func_array(array($this, 'insert_single'), $arguments);
            } else if (count($arguments) == 2) {
                return call_user_func_array(array($this, 'insert_multi'), $arguments);
            }
        }

        if ($method == 'update') {
            if (count($arguments) == 2) {
                return call_user_func_array(array($this, 'update_single'), $arguments);
            } else if (count($arguments) == 3) {
                return call_user_func_array(array($this, 'update_multi'), $arguments);
            }
        }
    }

    public function insert_single($dataArray) {
        //$id = $this->get_max($this->table . "_id") + 1;
        //$dataArray[$this->table . "_id"] = $id;
        if ($this->has_ordering) {
            $ordering = $this->get_max("ordering") + 1;
            $dataArray["ordering"] = $ordering;
        }
        $id = $this->activeInsert($dataArray);
        return $id;
    }

    public function update_single($dataArray, $id) {
        $result = $this->activeUpdate($dataArray, array($this->table . '_id' => $id));
        if (!$result)
            return false;
        return $result;
    }

    public function insert_multi($dataArray, $language) {
        $id = $this->get_max($this->table . "_id") + 1;
        $default = array($this->table . "_id" => $id);

        if ($this->has_ordering) {
            $ordering = $this->get_max("ordering") + 1;
            $default["ordering"] = $ordering;
        }
        
        if(!empty($dataArray["default_checkbox"])){
            $default = array_merge($dataArray["default_checkbox"], $default);
            unset($dataArray["default_checkbox"]);
        }

        $data = array();
        $this->splitData($default, $data, $dataArray);

        foreach ($language as $lan) {
            $data[$lan['language_id']]['language'] = $lan['language_id'];
            $arr = array_merge($data[$lan['language_id']], $default);
            $this->activeInsert($arr);
        }

        return $id;
    }

    public function update_multi($dataArray, $id, $language) {
        $default = array();
        if(!empty($dataArray["default_checkbox"])){
            $default = $dataArray["default_checkbox"];
            unset($dataArray["default_checkbox"]);
        }
        $data = array();
        $this->splitData($default, $data, $dataArray);
        foreach ($language as $lan) {
            if (empty($data[$lan['language_id']])) {
                $data[$lan['language_id']] = array();
            }
            $arr = array_merge($data[$lan['language_id']], $default);
            $this->resetState();
            $this->setState(array(
                $this->table . '_id'=>$id,
                "language" => $lan['language_id']
            ));
            $total = $this->get_total();
            if($total > 0){
                $r = $this->activeUpdate($arr, array($this->table . '_id' => $id, 'language' => $lan['language_id']));
            }else{
                $this->resetState();
                $this->setState(array(
                    $this->table . '_id'=>$id,
                    "language !=" => $lan['language_id']
                ));
                $otherRecord = $this->getAll(1,1);
                $arr[$this->table . '_id'] = $id;
                $arr['language'] = $lan['language_id'];
                if($otherRecord!=""){
                    $arr["ordering"] = $otherRecord[0]["ordering"];
                    $arr["deleted"] = $otherRecord[0]["deleted"];
                    $arr["status"] = $otherRecord[0]["status"];
                }
                $r = $this->activeInsert($arr);
            }
            if (!$r)
                return false;
        }
        return $r;
    }

    public function updateOrdering($item_id, $ordering) {
        $data = array(
            'ordering' => $ordering
        );

        $this->db->where($this->table . '_id', $item_id);
        $this->db->update($this->table, $data);
    }

    public function offer($id) {
        return $this->switcher('offer', 'id', $id);
    }

    public function splitData(&$default, &$data, $input) {
        foreach ($input as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $lang => $subValue) {
                    $data[$lang][$key] = $subValue;
                }
            } else {
                $default[$key] = $value;
            }
        }
    }

    public function getCol($tb = false, $hidden = array(), $show = array(), $seq = array(), $extend = array()) {
        if ($tb === false) {
            $tb = $this->table;
        }
        $sql = "show COLUMNS FROM " . $this->db->dbprefix($tb);
        $query = $this->db->query($sql);
        $result = array();
        foreach ($query->result_array() as $k => $v) {
            if (($v['Type'] == 'text' || 
                 strpos($v['Type'], 'varchar') !== false || 
                 strpos($v['Type'], 'enum') !== false || 
                 $v['Type'] == "longtext" || 
                 $v['Type'] == "mediumtext") && 
                 !in_array($v['Field'], $hidden) || 
                 in_array($v['Field'], $show)) {
                if ($v['Type'] == "text" || $v['Type'] == "longtext" || $v['Type'] == "mediumtext") {
                    $result[$v['Field']] = array(
                        'type' => 'editor'
                    );
                } else if (strpos($v['Type'], 'enum') !== false) {
                    $opts = array();
                    $option = str_replace('enum', '', $v['Type']);
                    eval('$option=array' . $option . ';');
                    if (!empty($option) && is_array($option)) {
                        foreach ($option as $o) {
                            $opts[$o] = $o;
                        }
                    }
                    $result[$v['Field']] = array(
                        'type' => 'select',
                        'data' => $opts
                    );
                } else {
                    $result[$v['Field']] = array(
                        'type' => 'textbox'
                    );
                }
            }
        }
        if (!empty($extend)) {
            foreach ($extend as $value) {
                $result[$value] = array("type" => "textbox");
            }
        }
        if (!empty($seq)) {
            foreach ($seq as $s) {
                $seqArr[$s] = $result[$s];
                unset($result[$s]);
            }
            $seqArr = array_merge($seqArr, $result);
            $result = $seqArr;
        }
        return $result;
    }

    public function activeUpdate($dataArray, $where = array(), $skip = array(), $table = false) {

        if ($table === false) {
            $table = $this->db->dbprefix($this->table);
        }
        $sql = "SHOW COLUMNS FROM $table";
        $query = $this->db->query($sql);
        $field = array();
        if ($query->num_rows() != '') {
            foreach ($query->result_array() as $col) {
                $field[] = $col['Field'];
            }
        }

        if ($dataArray != '') {
            foreach ($dataArray as $k => $v) {

                if (in_array($k, $field) && !in_array($k, $skip)) {
                    if (is_array($v)) {
                        $this->db->set($k, serialize($v));
                    } else {
                        $this->db->set($k, $v, ($v != "NOW()"));
                    }
                }
            }
        }
        $this->db->where($where);
        $query = $this->db->update($table);
        return $query;
    }

    public function activeInsert($dataArray, $skip = array(), $table = false) {
        if ($table === false) {
            $table = $this->db->dbprefix($this->table);
        }
        $sql = "SHOW COLUMNS FROM $table";
        $query = $this->db->query($sql);
        $field = array();
        if ($query->num_rows() != '') {
            foreach ($query->result_array() as $col) {
                $field[] = $col['Field'];
            }
        }
        if ($dataArray != '') {
            foreach ($dataArray as $k => $v) {
                if (in_array($k, $field) && !in_array($k, $skip)) {
                    if (is_array($v)) {
                        $this->db->set($k, serialize($v));
                    } else {
                        if ($v != "") {
                            $this->db->set($k, $v, ($v != "NOW()"));
                        }
                    }
                }
            }
        }

        $query = $this->db->insert($table);
        return $this->db->insert_id();
    }
    
    //Have bug only support chat_response table
    public function activeInsertBatch($dataArray, $skip = array(), $table = false){
        if ($table === false) {
            $table = $this->db->dbprefix($this->table);
        }
        $sql = "SHOW COLUMNS FROM $table";
        $query = $this->db->query($sql);
        $field = array();
        if ($query->num_rows() != '') {
            foreach ($query->result_array() as $col) {
                $field[] = $col['Field'];
            }
        }
        $allowed = array_diff($field, $skip);
        
        $clear_date = array();
        if ($dataArray != '') {
            foreach ($dataArray as $record) {
                $clear_date[] = array_intersect_key($record, array_flip($allowed));
            }
        }

        $this->db->insert_batch($table, $clear_date);
        return $this->db->insert_id();
    }

    public function toArray($value = false, $text = "title"){
        if($value === false){
            $value = $this->table."_id";
        }
        $this->setSelect($value.", ".$text);
        $data = $this->getAll();
        $result = array();
        if(!empty($data)){
            foreach($data as $record){
                $result[$record[$value]] = $record[$text];
            }
        }
        return $result;
    }
}

?>
