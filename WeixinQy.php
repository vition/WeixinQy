<?php
/**
 * @Author: vition
 * @Date:   2017-08-02 09:45:11
 * @Last Modified by:   vition
 * @Last Modified time: 2017-08-03 09:11:12
 */

include_once "lib/Urllib.php";

class WeixinQy extends Urllib{

	protected $corpid;
	protected $corpsecret;
	protected $aTFile;
	protected $accessToken;

	/**
	 * [__construct description]
	 * @param [type] $corpid     [description]
	 * @param [type] $corpsecret [description]
	 */
	function __construct($corpid,$corpsecret){
		$this->corpid=$corpid;
		$this->corpsecret=$corpsecret;
		$this->aTFile="accesstoken/".$this->corpsecret.".php";
		$this->getToken();
	}
	
	function getToken(){
		if(file_exists($this->aTFile)){
			$tokenData=json_decode(trim(substr($this->get($this->aTFile), 15)));
			if ($tokenData->expire_time < time()) {
				$this->accessToken=$this->createToken();
			}else{
				$this->accessToken= $tokenData->access_token;
			}
		}else{
			$this->accessToken=$this->createToken();
		}
	}

	/**
	 * @return [type]
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

	/*用户管理*/
	function user(){
		include_once "lib/User.php";
		return new User($this->accessToken);
	}
	/*消息推送*/
	function message(){
		include_once "lib/Message.php";
		return new Message($this->accessToken,$this->corpid);
	}

	/**
	 * @method   getAgent
	 * @Author   vition
	 * @DateTime 2017-08-02
	 * @param    [type]
	 * @return   [type]
	 */
	function getAgent($agentid){
		return $this->get("https://qyapi.weixin.qq.com/cgi-bin/agent/get?access_token={$this->accessToken}&agentid={$agentid}");
	}

	/*网页授权 输出二维码*/
	function webLogin($id,$appid,$agentid,$url){
		echo '<script src="http://rescdn.qqmail.com/node/ww/wwopenmng/js/sso/wwLogin-1.0.0.js"></script><script> window.onload=function(){window.WwLogin({"id":"'.$id.'","appid" : "'.$appid.'","agentid" : "'.$agentid.'","redirect_uri" :"'.UrlEncode($url).'",});} </script>';
	}

}