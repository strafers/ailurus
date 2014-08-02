<?php
if( !defined('IN') ) die('bad request');
include_once( AROOT . 'controller'.DS.'app.class.php' );
include_once( AROOT . 'lib'.DS.'weichat.class.php' );
include_once( AROOT . 'lib'.DS.'default_chat.class.php' );

class messageController extends appController
{
	function __construct()
	{
		parent::__construct();
		session_start();
	}
	

    /**
     * 上行信息处理类 
     * 
     * @desc
     * 
     * @access public
     * @return void
     * @exception none
     */
	public function index()
    {
    	$id = intval(v('app_id'));
		$sql = "SELECT * FROM `weixin_app` WHERE `id`=$id";
    	$ret = get_line($sql);
    	if (!$ret) {
    		die('error');
    	}
        
        if (array_key_exists('echostr', $_GET)) {
            $this->verify();
        }

    	$action_class = $ret['action_class'];
    	$file_path = sprintf("%s/lib/%s", AROOT, $action_class);
    	if (!file_exists($file_path)) {
    		$class_name = 'defaultChat';
    	} else {
    		require_once($file_path);
    		if (!class_exists($ret['app_name'].'Chat')) {
    			$class_name = 'defaultChat';
    		} else {
    			$class_name = sprintf("%sChat", $ret['app_name']);    			
    		}
    	}

        $token = $ret['token'];
    	$wechat = new $class_name($token, TRUE);
    	$wechat->run();
	}

    /**
     * 微信应用的认证地址 
     * 
     * @desc
     * 
     * @access public
     * @return void
     * @exception none
     */
    public function verify()
    {
        $app_id = $_GET['app_id'];
        $app_id = intval($app_id);
        $sql = "SELECT * FROM `weixin_app` WHERE `id`=$app_id";
        $ret = get_line($sql);
        if ($ret) {
            $token = $ret['token'];
            if ($this->checkSignature($token)) {
                $echoStr = $_GET["echostr"];
                echo $echoStr;
            }
        }
        exit();
    }

    /**
     * 微信校验签名 
     * 
     * @desc
     * 
     * @access private
     * @param $token
     * @return void
     * @exception none
     */
    private function checkSignature($token)
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];    
                
        $token = $token;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );
        
        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }
}
