<?php
	
	require 'conf.php';
	require 'mysqli.php';

	$o = new Conf();
	$f = new MysqliDb($o->host,$o->username,$o->pass,$o->db);

	$f->where("email",$_POST['email']);
	$f->where("pass",md5($_POST['password']));
	$f->get("users2");

	if ($f->count) {
		echo "Success";
	} else {
		echo "Invalid Email/Password";
	}

	

?>