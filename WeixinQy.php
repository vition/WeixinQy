<?php
/**
 * @Author: vition
 * @Date:   2017-08-02 09:45:11
 * @Last Modified by:   vition
 * @Last Modified time: 2017-08-02 12:50:08
 */
header("Content-type: text/html; charset=utf-8");
require("Urllib.php");

class WeixinQy extends Urllib{

	protected $corpid;
	protected $corpsecret;
	protected $aTFile;
	protected $accessToken;
	
	/**
	 * @param [type]
	 * @param [type]
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
		require("User.php");
		return new User($this->accessToken);
	}
}