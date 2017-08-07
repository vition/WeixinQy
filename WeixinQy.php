<?php
/**
 * @Author: vition
 * @Date:   2017-08-02 09:45:11
 * @Last Modified by:   369709991@qq.com
 * @Last Modified time: 2017-08-08 00:02:25
 */

include_once "lib/Urllib.php";

class WeixinQy extends Urllib{

	protected $corpid;/*企业微信CorpID*/
	protected $corpsecret;/*应用或者管理员secret*/
	protected $aTFile;/*相关的accesstoken文件*/
	protected $accessToken;/*储存access_token*/
	protected $Jssdk;
	protected $User; /*实例化用户管理类*/
	protected $Message;/*实例化消息管理类*/

	/**
	 * [__construct 构造方法]
	 * @param [type] $corpid     [企业微信CorpID]
	 * @param [type] $corpsecret [应用或者管理员secret]
	 */
	function __construct($corpid,$corpsecret){
		$this->corpid=$corpid;
		$this->corpsecret=$corpsecret;
		$this->aTFile="/accesstoken/".$this->corpsecret.".php";
		$this->getToken();
	}
	
	/**
	 * [secret 重新定义corpsecret]
	 * @method   secret
	 * @Author   vition
	 * @DateTime 2017-08-08
	 * @param    [type]     $corpsecret [description]
	 * @return   [type]                 [返回当前对象]
	 */
	function secret($corpsecret){
		$this->corpsecret=$corpsecret;
		$this->aTFile="/accesstoken/".$this->corpsecret.".php";
		$this->getToken();
		return $this;
	}
	/**
	 * [getToken 获取access token，缓存机制]
	 * @return [type] [返回access token]
	 */
	private function getToken(){
		if(file_exists($this->aTFile)){
			$tokenData=json_decode(trim(substr($this->get($this->aTFile), 15)));
			if ($tokenData->expire_time < time()) {
				$this->accessToken=$this->createToken();
			}else{
				$this->accessToken= $tokenData->access_token;
			}
		}else{
			if(!is_dir("/accesstoken/")){
				mkdir("/accesstoken/");
			}
			$this->accessToken=$this->createToken();
		}
	}

	/**
	 * [createToken 生成access token]
	 * @return [type] [返回access token]
	 */
	private function createToken(){
		$accessTokenJson=$this->get("https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid={$this->corpid}&corpsecret={$this->corpsecret}");
		$ATObject=json_decode($accessTokenJson);

		if ($ATObject->access_token) {
			$aTFile = fopen($this->aTFile, "w");
			fwrite($aTFile, "<?php exit();?>" . json_encode(array("expire_time"=>time() + 7000,"access_token"=>$ATObject->access_token)));
			fclose($aTFile);
			return $ATObject->access_token;
		}
		return false;
	}

	/**
	 * [user 用户/部门管理]
	 * @return [type] [返回一个对象]
	 */
	function user(){
		if(!is_object($this->User)){
			include_once "lib/User.php";
			$this->User=new User($this->accessToken);
		}
		return $this->User;

	}
	/**
	 * [message 消息管理类]
	 * @return [type] [返回一个对象]
	 */
	function message(){
		if(!is_object($this->Message)){
			include_once "lib/Message.php";
			$this->Message=new Message($this->accessToken,$this->corpid);
		}
		return $this->Message;
	}

	/**
	 * [getAgent 获取应用]
	 * @param  [type] $agentid [应用id]
	 * @return [type]          [description]
	 */
	function getAgent($agentid){
		return $this->get("https://qyapi.weixin.qq.com/cgi-bin/agent/get?access_token={$this->accessToken}&agentid={$agentid}");
	}

	/**
	 * [webLogin web授权登录 二维码]
	 * @param  [type] $id      [要显示二维码的容器id（html）]
	 * @param  [type] $appid   [企业微信的cropID]
	 * @param  [type] $agentid [授权方的网页应用ID]
	 * @param  [type] $url     [跳转的url]
	 * @return [type]          [description]
	 */
	function webLogin($id,$appid,$agentid,$url){
		return '<script src="http://rescdn.qqmail.com/node/ww/wwopenmng/js/sso/wwLogin-1.0.0.js"></script><script> window.onload=function(){window.WwLogin({"id":"'.$id.'","appid" : "'.$appid.'","agentid" : "'.$agentid.'","redirect_uri" :"'.UrlEncode($url).'",});} </script>';
	}

	/**
	 * [jssdk 企业微信JS-SDK]
	 * @return [type] [description]
	 */
	function jssdk(){
		if(!is_object($this->Jssdk)){
			include_once "lib/jssdk.php";
			$this->Jssdk=new jssdk($this->corpid,$this->corpsecret,$this->accessToken);
		}
		return $this->Jssdk;
	}

}