<?php
// throw exception
function e($message, $code = 1) 
{
	throw new Exception($message, $code);
}

function get_uid()
{
	if (!array_key_exists("uid", $_SESSION)) {
		return false;
	}
	$uid = intval($_SESSION['uid']);
	return $uid;
}

function check_role($uid = null)
{
	if (!$uid) {
		$uid = get_uid();
	}
	// check user exist
	$sql = "select * from ailurus_user where id=$uid";
	$ret = get_line($sql);
	if (!$ret) {
		e('user not exist', 1);
	}
	$role = $ret['role'];
	return $role;
}

function check_permission($role, $current_mod)
{
	$roles = c('roles');
	if (!array_key_exists($role, $roles)) {
		e('role not exist', 2);
	}

	$all_mod = explode(",", $roles[$role]);
	if (!in_array($current_mod, $all_mod)) {
		e('premission denied', 3);
	}
	return true;
}

function get_db_config($key) 
{
	$link = memcache_init();
	$mc_key = sprintf("$$$_dbdata_%s", $key);
	$ret = memcache_get($link, $mc_key);
	if (!$ret) {
		$key = s($key);
		$sql = "SELECT `value` FROM `config` WHERE `item`='$key'";
		$ret = get_var($sql);
		if (!$ret) return false;
		memcache_set($link, $mc_key, $ret, 0, 30);	
	}
	return $ret;
}

function check_login($email, $password)
{
	if (!$email) {
		e('email empty', 4);
	}

	if (!$password) {
		e('password empty', 5);
	}

	if (!$ret = get_line("SELECT * FROM `ailurus_user` WHERE `email` = '$email'")) {
		e('User not exist', 6);
	}

	$email = s($email);

	$password_user = md5($password.$ret['salt']);
	if ($password_user != $ret['password']) {
		e('Wrong Password', 7);
	}

	$_SESSION['uid'] = $ret['id'];
	$_SESSION['role'] = $ret['role'];
	
	return true;
}

function rand_str($length)
{
	$str = 'abcdefhijkmnprstuvwxy345678';
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $code .= $str[rand(0,strlen($str)-1)];
    }
    return $code;
}

function render_json($code, $message, $data = null)
{
	header('Content-type: application/json');
	$ret = array();
	$ret['code'] = $code;
	$ret['message'] = $message;
	if (!empty($data)) {
		$ret['data'] = $data;
	}
	echo(json_encode($ret));
	exit($code);
}

function h($str)
{
    return htmlspecialchars($str);
}
