<?php
/*
 * 安装小熊猫微信开发框架
 */
define('ROOT', dirname(dirname(__FILE__)));
require_once(ROOT.'/lib/app.function.php');
$lvyi_db = new SaeMysql();
$lvyi_sql = "SELECT * FROM `ailurus_user` where `id`=1";
$lvyi_result = $lvyi_db->getLine( $lvyi_sql );
if( $lvyi_result && ($lvyi_result['email'] == 'admin@admin.com') ){
    Header( "HTTP/1.1 301 Moved Permanently") ; 
	Header( "Location: http://".$_SERVER['HTTP_HOST'].'/' ); 
	exit();
}

//install
$sql = file_get_contents( './sql.sql' );
//do
runquery( $sql );	
//report
if ( $lvyi_db->errno() != 0 ) {
    die( "Error:" . $lvyi_db->errmsg() );
}

// generl admin user
$sql = "TRUNCATE TABLE  `ailurus_user` ";
$lvyi_db->runSql($sql);
$email = 'admin@admin.com';
$salt = rand_str(10);
$password = rand_str(8);
$md5_password = md5($password.$salt);
$nickname = 'admin';
$role = 'admin';
$sql = "insert into ailurus_user (email,nickname,role,password,salt) values ('$email','$nickname','$role','$md5_password','$salt')";
$lvyi_db->runSql($sql);
header("Content-type:text/html;charset=utf-8");
echo "Install successfully!<br/>";
echo "管理员登陆账号:admin@admin.com<br/>";
echo "管理员登陆密码:$password<br/>";
$callback_url = "http://".$_SERVER['HTTP_HOST']."/";
echo "去登录：<a href='".$callback_url."'>".$callback_url."</a>";
	
$lvyi_db->closeDb();

function runquery($sql) 
{
	global $lvyi_db;
	$sql = str_replace("\r", "\n", $sql );
	$ret = array ();
	$num = 0;
	foreach (explode(";\n", trim($sql)) as $query) {
		$queries = explode("\n", trim($query));
		foreach ($queries as $query) {
			$ret[$num] .= $query[0] == '#' || $query[0] . $query[1] == '--' ? '' : $query;
		}
		$num++;
	}
	unset ($sql);
	$strtip = "";
	foreach ($ret as $query) {
		$query = trim($query);
		if ($query) {
			if (substr($query, 0, 12) == 'CREATE TABLE') {
				$name = preg_replace("/CREATE TABLE\s*([a-z0-9_]+)\s*.*/is", "\\1", $query);
				$res = $lvyi_db->runSql(createtable($query, 'utf8'));
				$tablenum++;
			} else {
				$res = $lvyi_db->runSql($query);
			}
		}
	}
	return true;
}

function createtable($sql, $dbcharset) 
{
	$type = strtoupper(preg_replace("/^\s*CREATE TABLE\s+.+\s+\(.+?\).*(ENGINE|TYPE)\s*=\s*([a-z]+?).*$/isU", "\\2", $sql));
	$type = in_array($type, array (
		'MYISAM',
		'HEAP'
	)) ? $type : 'MYISAM';
	return preg_replace("/^\s*(CREATE TABLE\s+.+\s+\(.+?\)).*$/isU", "\\1", $sql) .
	 " ENGINE=$type DEFAULT CHARSET='utf8'";
}


?>
