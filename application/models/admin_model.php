<?php

class Admin_model extends MY_Model {

    public function __construct() {
        parent::__construct('admin', "admin_id", "ASC");
    }

    private function uniSalt(){
        return substr(sha1(mt_rand()),0,22);
    }

    private function gethash($password, $salt) {
        return sha1($salt . $password);
    }

    public function check_password($hash, $password, $salt) {
        $new_hash = $this->gethash($password, $salt);
        //var_dump($new_hash);

        return ($hash == $new_hash);
    }

    public function login($email, $password) {
        parent::setState("status", 1);
        parent::setState("email", $email);
        $data = $this->getAll();
        if (!empty($data)) {
            if ($this->check_password($data[0]["password"], $password, $data[0]["salt"])) {
                //parent::update(array('password' => $this->gethash($password)), $data[0]['admin_id']);
                return $data;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function checkEmail($email) {
        parent::setState("email", $email);
        return parent::get_total();
    }

    public function checkUsername($username) {
        parent::setState("username", $username);
        return parent::get_total();
    }

    public function updateEmail($email) {
        $this->db->where('username', 'admin');
        $data = array('email' => $email);
        $r = $this->db->update('admin', $data);
        return $r;
    }

    public function insert($dataArray) {
        if ($this->checkEmail($dataArray["email"]) != 0) {
            return array("status" => "error", "msg" => "Email Exist");
        } /*else if ($this->checkUsername($dataArray["username"]) != 0) {
            return array("status" => "error", "msg" => "User Name Exist");
        }*/ else {
            $salt = $this->uniSalt();
            $dataArray["salt"] = $salt;
            $dataArray['password'] = $this->gethash($dataArray['password'], $salt);
            $dataArray["status"] = 1;
            $id = $this->activeInsert($dataArray);
            return $id;
        }
    }

    public function update($dataArray, $id) {
        if ($dataArray['password'] == '') {
            unset($dataArray['password']);
        } else if ($dataArray['password'] != '' && $dataArray['password'] != $dataArray['confirm_password']) {
            return false;
        } else {
            $salt = $this->uniSalt();
            $dataArray["salt"] = $salt;
            $dataArray['password'] = $this->gethash($dataArray['password'], $salt);
        }
        unset($dataArray["email"]);//$dataArray["email"]
        $result = $this->activeUpdate($dataArray, array('admin_id' => $id));
        if (!$result)
            return false;
        return $result;
    }

}

?>
