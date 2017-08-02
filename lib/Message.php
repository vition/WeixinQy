<?php
/**
 * @Author: vition
 * @Date:   2017-08-02 14:36:30
 * @Last Modified by:   vition
 * @Last Modified time: 2017-08-02 16:49:31
 */
include_once "WXBizMsgCrypt.php";
class Message extends Urllib{
	private $wxcpt;
	function __construct($accessToken,$corpid){
		$this->accessToken=$accessToken;
		$this->corpid=$corpid;
	}

	function send($dataArray){
		// array("touser"=>"1000000107","msgtype"=>"text","agentid"=>"0","text"=>array("content"=>"这是测试信息"))
		$resultDataJson=$this->post("https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token=".$this->accessToken,$dataArray);
		$result=json_decode($resultDataJson);
		return $result;
	}

	function responseVerify($token,$encodingAesKey){
		$sVerifyMsgSig = $_GET["msg_signature"];
		$sVerifyTimeStamp =$_GET["timestamp"];
		$sVerifyNonce =$_GET["nonce"];
		$sVerifyEchoStr = $_GET["echostr"];
		$sEchoStr = "";
		$wxcpt = new WXBizMsgCrypt($token, $encodingAesKey, $this->corpid);
		$errCode = $wxcpt->VerifyURL($sVerifyMsgSig, $sVerifyTimeStamp, $sVerifyNonce, $sVerifyEchoStr, $sEchoStr);
		if ($errCode == 0) {    
		    echo $sEchoStr;  
		} else {  
			print("ERR: " . $errCode . "\n\n"); 
		}
	} 

	function response($token,$encodingAesKey){
		$sVerifyMsgSig = $_GET["msg_signature"];
		$sVerifyTimeStamp =$_GET["timestamp"];
		$sVerifyNonce =$_GET["nonce"];
		$sVerifyEchoStr = $_GET["echostr"];
		$sEchoStr = "";
		$wxcpt = new WXBizMsgCrypt($token, $encodingAesKey, $this->corpid);
		$sReqData = $this->get("php://input");

		$sMsg = "";  //解析之后的明文  
	    $errCode = $wxcpt->DecryptMsg($sVerifyMsgSig, $sVerifyTimeStamp, $sVerifyNonce, $sReqData, $sMsg);  
	    if ($errCode == 0) {   
			$xml = new DOMDocument();  
			$xml->loadXML($sMsg);   
			$reqToUserName = $xml->getElementsByTagName('ToUserName')->item(0)->nodeValue;  
			$reqFromUserName = $xml->getElementsByTagName('FromUserName')->item(0)->nodeValue;  
			$reqCreateTime = $xml->getElementsByTagName('CreateTime')->item(0)->nodeValue;  
			$reqMsgType = $xml->getElementsByTagName('MsgType')->item(0)->nodeValue;  
			$reqContent = $xml->getElementsByTagName('Content')->item(0)->nodeValue; 
			$reqEvent= $xml->getElementsByTagName('Event')->item(0)->nodeValue; 
			$reqMsgId = $xml->getElementsByTagName('MsgId')->item(0)->nodeValue;  
			$reqAgentID = $xml->getElementsByTagName('AgentID')->item(0)->nodeValue;   
			$reqLatitude = $xml->getElementsByTagName('Latitude')->item(0)->nodeValue;   
			$reqLongitude = $xml->getElementsByTagName('Longitude')->item(0)->nodeValue;   

			$mycontent=$reqMsgType;

			switch ($reqMsgType) {
				case 'event':
					# 进入应用
					return false;
					break;
				case 'text':
					# 发送文本
					return false;
					break;
				case 'image':
					# 发送图片
					return false;
					break;
				case 'voice':
					# 发送语音
					return false;
					break;
				case 'location':
					# 发送位置
					return false; 
					break;
				case 'video':
					# 发送视频
					return false;
					break;
				case 'link':
					return false;
					break;
				default:
					$mycontent="欢迎";
					break;
			}
				$sRespData =   
				"<xml>  
				<ToUserName><![CDATA[".$reqFromUserName."]]></ToUserName>  
				<FromUserName><![CDATA[".$corpId."]]></FromUserName>  
				<CreateTime>".$sReqTimeStamp."</CreateTime>  
				<MsgType><![CDATA[text]]></MsgType>  
				<Content><![CDATA["."操作类型：".$mycontent."]]></Content>  
				</xml>";  
				$sEncryptMsg = ""; //xml格式的密文  $reqFromUserName.
				$errCode = $wxcpt->EncryptMsg($sRespData, $sReqTimeStamp, $sReqNonce, $sEncryptMsg);  
				if ($errCode == 0) {    
					print($sEncryptMsg);  
				} else {  
					print($errCode . "\n\n");  
				}  
			
	    } else {  
	    	print($errCode . "\n\n");  
	    }  
	}
}