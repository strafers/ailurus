<?php
if( !defined('IN') ) die('bad request');
include_once( AROOT . 'controller'.DS.'app.class.php' );

class defaultController extends appController
{
	function __construct()
	{
		parent::__construct();
		session_start();
		if (get_uid()) {
			header('Location:/?c=admin');
		}
	}
    

    /**
     * 默认首页 
     * 
     * @desc
     * 
     * @access public
     * @return void
     * @exception none
     */
	public function index()
	{
		$this->data['title'] = $this->data['top_title'] = '首页';
		$this->data['hash'] = $_SESSION['login_hash'] = rand_str(10);
		$this->data['css'][] = 'header.css';
		$this->data['css'][] = 'footer.css';
		$this->data['css'][] = 'index.css';
		$this->data['js'][] = 'login.js';
		render( $this->data, 'front');
	}
    

    /**
     * 登录 
     * 
     * @desc
     * 
     * @access public
     * @return void
     * @exception none
     */
	public function login()
	{
		$email = v('email');
		$password = v('password');
		$hash = v('hash');

		if (!$hash != $_SESSION['hash']) {
			render_json(1, 'do not commit without project!');
		}

		try {
			check_login($email, $password);
		} catch (Exception $e) {
			render_json($e->getCode(), $e->getMessage());
		}
		$data['redirect_url'] = '/?c=admin&a=edit_config';
		render_json(0, 'login success', $data);
	}

    /**
     * 退出登录 
     * 
     * @desc
     * 
     * @access public
     * @return void
     * @exception none
     */
	public function logout()
	{
		unset($_SESSION);
		session_destroy();
		header('Location: /index.php');
	}
}
