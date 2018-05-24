<?php

class Member_model extends MY_Model {

    public function __construct() {
        parent::__construct('member', "member_id", "DESC");
    }
    
    public function getByAlias($alias = false){
        if($alias === false){
            $alias = explode("/", uri_string())[0];
        }
        $this->resetState();
        $this->setState(array(
            "alias"=>$alias,
            "status"=>1,
            "approved"=>1
        ));
        $result = $this->getAll();
        $this->resetState();
        return $result[0];
    }

    private function uniSalt(){
        return substr(sha1(mt_rand()),0,22);
    }

    private function gethash($password, $salt) {
        return sha1($salt . $str);
    }

    public function check_password($hash, $password, $salt) {
        $new_hash = $this->gethash($password, $salt);

        return ($hash == $new_hash);
    }

    public function login($email, $password) {
        parent::setState("status", 1);
        parent::setState("approved", 1);
        parent::setState("email", $email);
        $data = $this->getAll();
        if (!empty($data)) {
            if ($this->check_password($data[0]["password"], $password, $data[0]["salt"])) {
                //parent::update(array('password' => $this->gethash($password)), $data[0]['member_id']);
                return $data;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function checkUsername($username) {
        parent::setState("username", $username);
        return parent::get_total();
    }

    public function checkEmail($email) {
        parent::setState("email", $email);
        return parent::get_total();
    }

    public function insert($dataArray) {
        if ($this->checkEmail($dataArray["email"]) != 0) {
            return array("status" => "error", "msg" => "Email Exist");
        } else {
            $alias = $this->to_slug( $dataArray["name"], "-");
            if(!$this->checkAliasUni($alias)){
                $alias = $alias."-".($this->get_max("member_id") + 1);
            }
            $dataArray["alias"] = $alias;
            $salt = $this->uniSalt();
            $dataArray["salt"] = $salt;
            $dataArray['password'] = $this->gethash($dataArray['password'], $salt);
            $dataArray['status'] = 1;
            $id = $this->activeInsert($dataArray);
            return $id;
        }
    }
    
    public function insertWithoutEmail($dataArray) {
        $id = $this->activeInsert($dataArray);
        return $id;
    }

    public function update($dataArray, $id) {
        if (@$dataArray['password'] == '') {
            unset($dataArray['password']);
        } else if ($dataArray['password'] != '' && $dataArray['password'] != $dataArray['confirm_password']) {
            return false;
        } else {
            $salt = $this->uniSalt();
            $dataArray["salt"] = $salt;
            $dataArray['password'] = $this->gethash($dataArray['password'], $salt);
        }

        $result = $this->activeUpdate($dataArray, array('member_id' => $id));
        if (!$result)
            return false;
        return $result;
    }

    public function forgetPassword($mobile) {
        $this->setState("mobile", $mobile);
        $data = $this->getAll();
        if (empty($data)) {
            return false;
        }
        $hash = md5(uniqid(rand(), true));
        $array = array('hash' => $hash);
        $this->update($array, $data[0]['member_id']);
        return array("hash"=>$hash, "member_id"=>$data[0]['member_id']);
    }

    public function resetPassword($hash) {
        $this->resetState(true);
        $this->setState("hash", $hash);
        $data = $this->getAll();
        if (!empty($data)) {
            $newPassword = $data[0]['new_password'];
            $dataArray['password'] = $newPassword;
            $dataArray['confirm_password'] = $newPassword;
            $dataArray['new_password'] = '';
            $dataArray['hash'] = '';
            $this->update($dataArray, $data[0]['member_id']);
            return true;
        } else {
            return false;
        }
    }

    
    public function updateField($field, $value, $id){
        $result = $this->activeUpdate(array($field=>$value), array('member_id' => $id));
        return $result;
    }
}

?>
