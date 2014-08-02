<?php
if( !defined('IN') ) die('bad request');
include_once( AROOT . 'controller'.DS.'app.class.php' );

class articleController extends appController
{
	function __construct()
	{
		parent::__construct();
    }

    /**
     * 获取一篇发布的文章 
     * 
     * @desc
     * 
     * @access public
     * @return void
     * @exception none
     */
    public function get()
	{
        $sid = v('sid');
        if (!$sid) {
            $this->notfound();
        }
        $sid = s($sid);
        $sql = "SELECT * FROM `article` WHERE `md5_id`='$sid' AND `status`=1";
        $retval = get_line($sql);
        if (!$retval) {
            // header 404 error
            $this->notfound();
        }
        $this->data['title'] = h($retval['title']);
        $this->data['html'] = $retval['content'];
        $this->data['timeline'] = $retval['create_time'];
        $url = sprintf("https://%s/?c=article&a=get&sid=%s", $_SERVER['SERVER_NAME'], $sid);
		$this->data['qrcode'] = $this->getqrcode($url);
		$ad_sql = "select image_url from advertisement where weixin_app_id=".$retval['weixin_app_id'];
		$ad_url = get_var($ad_sql);
		$this->data['ad_image_url'] = $ad_url;
        render($this->data, 'article');
    }

    /**
     * 获取一篇文章的二维码地址 
     * 
     * @desc
     * 
     * @access private
     * @param $value
     * @return void
     * @exception none
     */
    private function getqrcode($value)
    {
        $url = "https://qrcodeonline.sinaapp.com/rest.php?data=%s";
        $url = sprintf($url, $value);
        $ret = file_get_contents($url);
        $retval = json_decode($ret, TRUE);
        if (!is_array($retval)) {
            return FALSE;
        } else {
            return $retval['data'];
        }
    }

    private function notfound()
    {
        header("HTTP/1.1 404 Not Found");
        header("Status: 404 Not Found");
        echo('404 NOT Found');
        exit();
    }
}	
