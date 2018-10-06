<?php
/*
  @Author: ananayarora
  @Date:   2016-10-12T16:47:46+05:30
  @Last modified by:   ananayarora
  @Last modified time: 2016-10-21T01:38:02+05:30
*/
    class Functions {
        var $o;
        var $c;
        public function __construct() {
            $this->c = new Conf();
            $this->o = new MysqliDb($this->c->host, $this->c->username, $this->c->password, $this->c->db);
        }

        public function getUser($id) {
            $this->o->where("id",$id);
            return $this->o->getOne("users");
        }

        public function checkUsername($username) {
            $this->o->where("username",$username);
            $this->o->get("users");
            return $this->o->count;
        }

        public function validateEmail($email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                return true;
            } else {
                return false;
            }
        }

        public function checkEmail($email) {
            $this->o->where("email",$email);
            $this->o->get("users");
            return $this->o->count;
        }
        public function session() {
            if (!isset($_SESSION)) {
                session_start();
            }
        }
        public function secure($data) {
            return $this->o->escape(htmlspecialchars($data));
        }

        public function updateUser($userid, $data) {
            $this->o->where("id",$userid);
            $this->o->get("users");
            if ($this->o->count) {
                $this->o->update("users",$data);
                return true;
            } else {
                return false;
            }
        }

        public function initiateFacebookLogin($fb) {
            $username = strtolower($fb['first_name'].$fb['last_name']).substr($fb['fbid'], -3, 2);
            if ($fb['email'] == "" && !$this->checkUsername($username)) {
                $insertData = Array(
                    'fbid'=>$fb['fbid'],
                    'username'=>strtolower($fb['first_name'].$fb['last_name']).substr($fb['fbid'], -3, 2),
                    'first_name'=>$fb['first_name'],
                    'last_name'=>$fb['last_name']
                );
                $id = $this->o->insert("users",$insertData);
                $_SESSION['uid'] = $id;
                header("Location: ".$this->c->base."/addemail.php");
            } else {
                $this->session();
                $this->o->where("username", $username);
                $k = $this->o->getOne("users");
                if ($this->o->count) {
                    // The profile exists
                    if ($k['fbid'] == $fb['fbid']) {
                        // Logged in through Facebook
                        $_SESSION['uid'] = $k['id'];
                        header("Location: ".$this->c->base."/dashboard.php");
                    }
                }
            }
        }
    }
?>
