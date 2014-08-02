<?php
if( !defined('IN') ) die('bad request');
include_once( CROOT . 'controller' . DS . 'core.class.php' );

class appController extends coreController
{
	public $data;

	public function __construct()
	{
		parent::__construct();
		if (get_db_config('use_ssl') == '1') {
			$this->usessl();
		}
		$this->get_config();
	}

    /**
     * 是否启用ssl访问 
     * 
     * @desc
     * 
     * @access private
     * @return void
     * @exception none
     */
	private function usessl()
	{
		if (!array_key_exists("HTTP_X_PROTO", $_SERVER)) {
			$uri = $_SERVER['SCRIPT_URI'];
			$uri = str_replace("http", "https", $uri);
			header("Location:$uri");
		}
	}

    /**
     * 从数据库获取配置，没有配置返回false 
     * 
     * @desc
     * 
     * @access private
     * @return void
     * @exception none
     */
	private function get_config()
	{
		$this->data['site_name'] = get_db_config('site_name');
		$this->data['keywords'] = get_db_config('keywords');
		$this->data['devteam'] = get_db_config('devteam');
	}
}
