<?php
if( !defined('IN') ) die('bad request');
include_once( AROOT . 'controller'.DS.'app.class.php' );

class adminController extends appController
{
	function __construct()
	{
		parent::__construct();
		session_start();
		$user_id = get_uid();
		if(!$user_id){
			info_page('需要登陆');
			die();
		}
	}

	private function check_operate_permission($permission)
	{
		try {
			$role = check_role();
		} catch (Exception $e) {
			info_page($e->getMessage());
			die();
		}
		try {
			check_permission($role, "config");
		} catch (Exception $e){
			info_page($e->getMessage());
			die();
		}
	}

	public function index()
	{
		header('Location:/?c=admin&a=edit_config');
		$this->data['title'] = $this->data['top_title'] = '管理中心';
        $this->data['css'][] = 'header.css';
		$this->data['css'][] = 'footer.css';
		$this->data['css'][] = 'admin.css';
		$this->data['js'][] = 'admin.js';

		render( $this->data );
	}

	public function edit_config()
	{
		$this->data['title'] = $this->data['top_title'] = '修改配置';
		$this->data['js'][] = 'config.js';
		$this->check_operate_permission('config');

		$this->data['all_config'] = $this->all_config_value();
		render($this->data);	
	}

	private function all_config_value()
	{
		$sql = "SELECT * FROM `config`";
		$ret = get_data($sql);
		foreach($ret as $value){
		    $config[$value['item']] = $value['value'];
		}
		return $config;
	}

	public function save_config()
	{
		$this->check_operate_permission('config');
	    $site_open = intval(v('site_open'));
		$use_ssl = intval(v('use_ssl'));
		$site_name = h(s(t(v('site_name'))));
		$keywords = h(s(t(v('keywords'))));
		$devteam = h(s(t(v('devteam'))));
        if(empty($site_name) or empty($keywords) or empty($devteam)){
		    render_json(2, 'input value has empty');
		}

		$sql = "replace into `config` (item,value) values ('site_open',$site_open),('use_ssl',$use_ssl),('site_name','$site_name'),('keywords','$keywords'),('devteam','$devteam')";
		run_sql($sql);
		if(mysql_errno()){
		    render_json(1, 'update failed');
		}
		$data['redirect_url'] = '/?c=admin&a=edit_config';
	    render_json(0, 'success', $data);
	}


    public function users()
	{
		$this->data['title'] = $this->data['top_title'] = '用户';
		$this->data['js'][] = 'users.js';
		$this->check_operate_permission('config');

		$sql = "SELECT id,nickname,email,role from ailurus_user";
		$users = get_data($sql);

		$this->data['users'] = $users;
		render($this->data);
	}


    public function create_user()
	{
		$this->data['title'] = $this->data['top_title'] = '增加用户';
		$this->data['js'][] = 'users.js';
        $this->check_operate_permission('config');

        render($this->data);
	}

	public function save_user()
	{
        $this->check_operate_permission('config');
		$email = h(s(t(v('email'))));
		$nickname = h(s(t(v('nickname'))));
		$role = h(s(t(v('role'))));
		$password = h(s(t(v('password'))));
        if(empty($email) or empty($nickname) or empty($password) or empty($role)){
		    render_json(2, 'input has empty value');
		}

		$salt = rand_str(10);
		$md5_password = md5($password.$salt);

		$user_is_exist_sql = "select id from ailurus_user where email='$email'";
		if (get_var($user_is_exist_sql)) {
		    render_json(1, 'user has existed');
		}

	    $sql = "insert into ailurus_user (email,nickname,role,password,salt) values ('$email','$nickname','$role','$md5_password','$salt')";
		run_sql($sql);
		if (mysql_errno()){
		     render_json(1, 'create user failed');
		}
		$data['redirect_url'] = '/?c=admin&a=users';
		render_json(0, 'success', $data);
	}

	public function edit_user()
	{
		$this->check_operate_permission('config');
        $user_id = intval(v('user_id'));
		$sql = "select email,nickname,password,role from ailurus_user where id='$user_id'";
		$user_info = get_line($sql);
		render_json(0, 'success', $user_info);
	}


	public function update_user()
	{
		$this->check_operate_permission('config');
		$user_id = intval(v('user_id'));
        $email = h(s(t(v('email'))));
        $nickname = h(s(t(v('nickname'))));
        $role = h(s(t(v('role'))));
		$password = s(t(v('password')));
		if(empty($email) or empty($nickname) or empty($role) or empty($password)){
		    render_json(2, 'value is empty');
		}

        $user_is_exist_sql = "select id from ailurus_user where email='$email'";
		if (!get_var($user_is_exist_sql)) {
			render_json(3, 'user does not existed');
		}

		$sql = "select salt from ailurus_user where id='$user_id'";
		$salt = get_var($sql);
		$new_password = md5($password.$salt);
		#$update_sql = "replace into ailurus_user (id,email,nickname,role,password) values ('$user_id','$email','$nickname','$role','$new_password')";
		$update_sql = "update ailurus_user set email='$email',nickname='$nickname',role='$role',password='$new_password' where id='$user_id'";
		run_sql($update_sql);
		if(mysql_errno()){
		    render_json(1, 'update user failed');
		}
		$data['redirect_url'] = '/?c=admin&a=users';
		render_json(0, 'success', $data);
	}

    public function delete_user()
	{
		$this->check_operate_permission('config');
	    $user_id = intval(v('user_id'));
        $sql = "delete from ailurus_user where id='$user_id'";
		run_sql($sql);
		if(mysql_errno()){
		    render_json(1, 'delete user failed');
		}
		$this->data['redirect_url'] = '/?c=admin&a=users';
		render_json(0, 'success', $this->data);
	}

    public function weixin_apps()
	{
		$this->data['title'] = $this->data['top_title'] = '微信app';
		$this->data['js'][] = 'apps.js';

        $sql = "select * from weixin_app";
        $apps = get_data($sql);
        $this->data['verify_base_url'] = '/?c=message&app_id=';
		$this->data['weixin_apps'] = $apps;
		render($this->data);
	}


	public function create_app()
	{
		$this->data['title'] = $this->data['top_title'] = '增加微信app';
		$this->data['js'][] = 'apps.js';

	    render($this->data);	
	}

	public function edit_app()
	{
		$app_id = intval(v('app_id'));
		$sql = "select * from weixin_app where id='$app_id'";
		$app_info = get_line($sql);
		render_json(0, 'success', $app_info);
	}

	public function save_app()
	{
		$app_name = h(s(t(v('app_name'))));
		if(!preg_match("/^[a-zA-Z]+$/",$app_name)){
			render_json(4,'应用名称只能是英文字母');
		}
		$app_desc = h(s(t(v('app_desc'))));
		$token = h(s(t(v('token'))));
		if(!preg_match("/^[a-zA-Z0-9]+$/",$token)){
			render_json(5,'token只能是英文字母或数字');
		}

		if(strlen($token)<6){
		    render_json(6, 'token长度最小为7');
		}
		$action_class = h(s(t(v('action_class'))));
        if(empty($app_name) or empty($app_desc) or empty($token) or empty($action_class)){
		    render_json(2, 'input has empty value');
		}

        $sql = "select app_name from weixin_app where app_name='$app_name'";
		if(get_var($sql)){
		    render_json(3, 'weixin_app has existed');
		}

		$sql = "replace into weixin_app (app_name,app_desc,token,action_class,create_time) values ('$app_name','$app_desc','$token','$action_class',now())";
		run_sql($sql);
		if(mysql_errno()){
		    render_json(1, 'create app failed');
		}

		$data['redirect_url'] = '/?c=admin&a=weixin_apps';
		render_json(0, 'success', $data);
	}

    public function update_app()
	{
		$app_id = intval(v(app_id));
		$app_name = h(s(t(v('app_name'))));
		if(!preg_match("/^[a-zA-Z]+$/",$app_name)){
			render_json(4,'应用名称只能是英文字母');
		}
		$app_desc = h(s(t(v('app_desc'))));
		$token = h(s(t(v('token'))));
		if(!preg_match("/^[a-zA-Z0-9]+$/",$token)){
			render_json(5,'token只能是英文字母或数字');
		}

		if(strlen($token)<6){
		    render_json(6, 'token长度最小为7');
		}
		$action_class = h(s(t(v('action_class'))));
		if(empty($app_name) or empty($app_desc) or empty($token) or empty($action_class)){
			render_json(2, 'input has empty value');
		}

        $sql = "select app_name from weixin_app where app_name='$app_name'";
		if(!get_var($sql)){
			render_json(3, 'weixin_app does not existed');
		}

		$sql = "update weixin_app set app_name='$app_name',app_desc='$app_desc',token='$token',action_class='$action_class' where id='$app_id'";
		run_sql($sql);
		if(mysql_errno()){
			render_json(1, 'update app failed');
		}

		$data['redirect_url'] = '/?c=admin&a=weixin_apps';
		render_json(0, 'success', $data);
	}

	public function delete_app()
	{
	    $app_id = intval(v('app_id'));
		$sql = "delete from weixin_app where id='$app_id'";
		run_sql($sql);
		if(mysql_errno()){
		    render_json(1, 'delete weixinapp failed');
		}
		$data['redirect_url'] = '/?c=admin&a=weixin_apps';
		render_json(0, 'success', $data);
	}

    public function articles()
	{
		$this->data['title'] = $this->data['top_title'] = '全部文章';
		$this->data['js'][] = 'article.js';
		$sql = "select * from article where status=1";
		$articles = get_data($sql);
		$this->data['articles'] = $articles;
		render($this->data);
	}

	public function add_article()
	{
		$this->data['title'] = $this->data['top_title'] = '发布文章';
		$this->data['js'][] = 'article.js';
		$this->data['all_weixin_apps'] = false;
		if ($ret = $this->get_all_apps()) {
			$this->data['all_weixin_apps'] = $ret;
		}
		render($this->data);
	}

	private function get_all_apps()
	{
		// get all app
		$sql = "SELECT * FROM `weixin_app`";
		$ret = get_data($sql);

		if (count($ret) == 0) {
			return false;
		} else {
			return $ret;
		}
	}

	public function save_article()
	{
		$user_id = get_uid();
		if (!$user_id){
		    render_json(1, 'user is not exist');
		}
		$title = h(s(t(v('title'))));
		$content = s(t(v('content')));
		$weixin_app_id = intval(v('weixin_app_id'));
		$salt = rand_str(10);
		$md5_id = md5(time().$salt);
		if(empty($title) or empty($content) or empty($weixin_app_id)){
		    render_json(1, 'input has empty value');
		}

		$sql = "replace into article (title,content,md5_id,status,weixin_app_id,user_id,create_time,modify_time) values ('$title','$content','$md5_id','1','$weixin_app_id','$user_id',now(),now())";
		run_sql($sql);
		if(mysql_errno()){
		    render_json(2, 'create article error');
		}
		$data['redirect_url'] = '/?c=admin&a=articles';
		render_json(0, 'success', $data);
	}

	public function edit_article()
	{
		$this->data['title'] = $this->data['top_title'] = '修改文章';
		$this->data['js'][] = 'article.js';
	    $md5_id = h(s(t(v('id'))));
        $sql = "select * from article where md5_id='$md5_id'";
		$article = get_line($sql);  
		$this->data['all_weixin_apps'] = false;
		if ($ret = $this->get_all_apps()) {
			$this->data['all_weixin_apps'] = $ret;
		}

		$this->data['article'] = $article;
		render($this->data);
	}

	public function update_article()
	{
		$md5_id = h(s(t(v('id'))));
		$title = h(s(t(v('title'))));
		$content = s(t(v('content')));
		$weixin_app_id = intval(v('weixin_app_id'));
        if(!get_var("select id from article where status=1 and md5_id='$md5_id'")){
		    render_json(2, 'article does not exist');
		}

		$sql = "update article set title='$title',content='$content',weixin_app_id='$weixin_app_id',modify_time=now() where md5_id='$md5_id'";
		run_sql($sql);
		if(mysql_errno()){
		    render_json(1, 'update article error');
		}
		$data['redirect_url'] = '/?c=admin&a=articles';
		render_json(0, 'success', $data);
	}


	public function delete_article()
	{
	    $md5_id = h(s(t(v('id'))));
		$sql = "update article set status=0 where md5_id='$md5_id'";
        run_sql($sql);
		if(mysql_errno()){
		    render_json(1, 'delete article error');
		}
		$data['redirect_url'] = '/?c=admin&a=articles';
        render_json(0, 'success', $data);
	}


	public function add_advertisement()
	{
		$this->data['title'] = $this->data['top_title'] = '添加广告';
		$this->data['js'][] = 'advertisement.js';
		$this->data['js'][] = 'upload.js';
		$this->data['css'][] = 'fineuploader-3.7.1.min.css';
		$this->data['all_weixin_apps'] = false;
		if ($ret = $this->get_all_apps()) {
			$this->data['all_weixin_apps'] = $ret;
		}

        $sql = "select * from advertisement";
		$ads = get_data($sql);
        $this->data['ads'] = $ads;
		render($this->data);
	}

    public function save_advertisement()
	{
	    $weixin_app_id = h(s(t(v('weixin_app_id'))));
		$image_url = h(s(t(v('image_url'))));
		if(empty($weixin_app_id) or empty($image_url)){
		    render_json(1, 'input has empty value');
		}
		if(get_var("select id from advertisement where weixin_app_id='$weixin_app_id'")){
		    $sql = "update advertisement set image_url='$image_url' where weixin_app_id='$weixin_app_id'";
		} else{
		    $sql = "replace into advertisement (weixin_app_id, image_url) values ('$weixin_app_id','$image_url')";
		}
		run_sql($sql);

		if(mysql_errno()){
		    render_json(2, 'add advertisement failed');
		}
		$data['redirect_url'] = '/?c=admin&a=add_advertisement';
		render_json(0, 'success', $data);
	}

	public function save_image()
	{
        $max_size_m = 7;
		$max_size = $max_size_m*1024*1024;
		if(!isset($_FILES['attachement']['tmp_name']) || !file_exists($_FILES['attachement']['tmp_name']))
			die(json_encode(array('error'=>'no files uploaded', 'success'=>false)));
		if(filesize($_FILES['attachement']['tmp_name']) > $max_size)
			die(json_encode(array('error'=>'只支持' . $max_size_m . 'M以下的文件', 'success'=>false)));
        $mime_type = $_FILES['attachement']['type'];
        $mime_type = explode('/', $mime_type);
        $ext=trim($mime_type[1]);
		if($mime_type[0] != 'image' || !in_array($ext,array('gif','png','bmp','x-ms-bmp','jpeg','jpg')))
			die(json_encode(array('error'=>'只允许上传gif,png,bmp,jpeg,jpg格式的文件','success'=>false)));
		$fn = substr(md5(time()),0, 20).$ext;
        $st=new SaeStorage;
		$ret=$st->upload('upload',$fn,$_FILES['attachement']['tmp_name'], array('type'=>"image/$ext"));
		unlink($_FILES['attachement']['tmp_name']);
		if($ret === false)
			die(json_encode(array('error'=>'保存文件错误' . $st->errmsg(), 'success'=>false)));
        else
		{
			$orig_name=s($_FILES['attachement']['name']);
			$url=$st->getUrl('upload',$fn);
			$url=s($url);

			//$sql="insert into advertisement(image_url) values('$url')";
			//if(run_sql($sql))
			//{
			//	$id=last_id();
			die(json_encode(array('success'=>true, 'uploadName'=>$orig_name, 'newUuid'=>($fn), 'url'=>$url)));
			//}
		}




		/*if ($_FILES["file"]["error"] > 0)
		{
			render_json(1, $_FILES["file"]["error"]);
		}
		if (empty($_FILES) === false) {
			$file_name = $_FILES['file']['name'];
			$tmp_name = $_FILES['file']['tmp_name'];
			$file_size = $_FILES['file']['size'];

            $temp_arr = explode(".", $file_name);
			$file_ext = array_pop($temp_arr);
			$file_ext = trim($file_ext);
			$file_ext = strtolower($file_ext);
			//检查扩展名
			if (in_array($file_ext, array('gif', 'jpg', 'jpeg', 'png', 'bmp')) === false) {
				render_json(2, "上传文件扩展名是不允许的扩展名。\n只允许" . implode(",", array('gif', 'jpg', 'jpeg', 'png', 'bmp')) . "格式。");
			}
			$stor_file_name = substr(md5(time()),0, 20).$file_ext;
			echo "$stor_file_name";
		    $storage_ins = new SaeStorage();
		    $ret = $storage_ins->write('upload', $stor_file_name, file_get_contents($tmp_name));
		    if (!$ret) {
			    render_json(0, "上传文件失败。");
		    } else {
				$file_url = $storage_ins->getUrl('upload', $stor_file_name);
				echo $file_url;
			}


            $sql = "replace into advertisement (image_url) values ('$file_url')";
            run_sql($sql);
		    if(mysql_errno()){
		        render_json(1, '保存失败');  
		    }
		    render_json(0, 'success');
		}*/
	}

	public function delete_image()
	{
		$fn = s(t(v('id')));
		$storage = new SaeStorage();
		$storage->delete('upload', $fn);

		$data['redirect_url'] = '/?c=admin&a=advertisements';
		die(json_encode(array('success'=>true)));
	}

	function remove_advertisement()
	{
	    $id = intval(v('id'));
		$sql = "delete from advertisement where id='$id'";
		run_sql($sql);
		if(mysql_errno()){
		    render_json(1, 'remode advertisement failes');
		}
		$data['redirect_url'] = '/?c=admin&a=add_advertisement';
		render_json(0, 'success', $data);
	}
}
