<?php
/**
 * @Author: vition
 * @Date:   2017-08-02 14:36:30
 * @Last Modified by:   369709991@qq.com
 * @Last Modified time: 2017-08-03 05:37:32
 */
include_once "WXBizMsgCrypt.php";
class Message extends Urllib{
	protected $wxcpt;
	protected $sVerifyTimeStamp;
	protected $sVerifyNonce;
	protected $accessToken;
	protected $corpId;


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

	function listen($token,$encodingAesKey){
		$sReqMsgSig = $_GET["msg_signature"];
		$this->sReqTimeStamp =$_GET["timestamp"];
		$this->sReqNonce =$_GET["nonce"];
		
		$this->wxcpt = new WXBizMsgCrypt($token, $encodingAesKey, $this->corpid);
		$sReqData = $this->get("php://input");

		$sMsg = "";  //解析之后的明文  
	    $errCode = $this->wxcpt->DecryptMsg($sReqMsgSig, $this->sReqTimeStamp, $this->sReqNonce, $sReqData, $sMsg);  
	    if ($errCode == 0) {   
			$xml = new DOMDocument();  
			$xml->loadXML($sMsg);   
			$xmlArray=array("ToUserName","FromUserName","CreateTime","MsgType","Content","PicUrl","MediaId","MsgId","AgentID","Format","ThumbMediaId","Location_X","Location_Y","Scale","Label","Title","Description")
			
			$resutlXml=(object) null;
			foreach ($xmlArray as $xmlNode) {
				$temp=$xml->getElementsByTagName($xmlNode);
				if($temp->length>0){
					$resutlXml->$xmlNode=$temp->item(0)->nodeValue;
				}
			}
			return $resutlXml;
			// $mycontent=$reqMsgType;

			// switch ($reqMsgType) {
			// 	case 'event':
			// 		# 进入应用
			// 		return false;
			// 		break;
			// 	case 'text':
			// 		# 发送文本
			// 		return false;
			// 		break;
			// 	case 'image':
			// 		# 发送图片
			// 		return false;
			// 		break;
			// 	case 'voice':
			// 		# 发送语音
			// 		return false;
			// 		break;
			// 	case 'location':
			// 		# 发送位置
			// 		return false; 
			// 		break;
			// 	case 'video':
			// 		# 发送视频
			// 		return false;
			// 		break;
			// 	case 'link':
			// 		return false;
			// 		break;
			// 	default:
			// 		$mycontent="欢迎";
			// 		break;
			// }
				// $sRespData =   
				// "<xml>  
				// <ToUserName><![CDATA[".$reqFromUserName."]]></ToUserName>  
				// <FromUserName><![CDATA[".$corpId."]]></FromUserName>  
				// <CreateTime>".$sReqTimeStamp."</CreateTime>  
				// <MsgType><![CDATA[text]]></MsgType>  
				// <Content><![CDATA["."操作类型：".$mycontent."]]></Content>  
				// </xml>";  
				// $sEncryptMsg = ""; //xml格式的密文  $reqFromUserName.
				// $errCode = $wxcpt->EncryptMsg($sRespData, $sReqTimeStamp, $sReqNonce, $sEncryptMsg);  
				// if ($errCode == 0) {    
				// 	print($sEncryptMsg);  
				// } else {  
				// 	print($errCode . "\n\n");  
				// }  
			
	    } else {  
	    	print($errCode . "\n\n");  
	    }  
	}

	private function response($sRespData){
		// $xmlArray=array("ToUserName","FromUserName","CreateTime","MsgType","Content","PicUrl","MediaId","MsgId","AgentID","Format","ThumbMediaId","Location_X","Location_Y","Scale","Label","Title","Description")
		// $sRespData =   
		// "<xml>  
		// <ToUserName><![CDATA[".$resutlXml->FromUserName."]]></ToUserName>  
		// <FromUserName><![CDATA[".$this->corpid."]]></FromUserName>  
		// <CreateTime>".$this->sReqTimeStamp."</CreateTime>  
		// <MsgType><![CDATA[text]]></MsgType>  
		// <Content><![CDATA["."操作类型：".$mycontent."]]></Content>  
		// </xml>";  
		$sEncryptMsg = ""; //xml格式的密文  $reqFromUserName.
		$errCode = $this->wxcpt->EncryptMsg($sRespData, $this->sReqTimeStamp, $this->sReqNonce, $sEncryptMsg);  
		if ($errCode == 0) {    
			print($sEncryptMsg);  
		} else {  
			print($errCode . "\n\n");  
		}  
	}

	/*不同的发送方式不同*/
	function resText(){
		$sRespData =   
		"<xml>  
		<ToUserName><![CDATA[".$resutlXml->FromUserName."]]></ToUserName>  
		<FromUserName><![CDATA[".$this->corpid."]]></FromUserName>  
		<CreateTime>".$this->sReqTimeStamp."</CreateTime>  
		<MsgType><![CDATA[text]]></MsgType>  
		<Content><![CDATA["."操作类型：".$mycontent."]]></Content>  
		</xml>";  
		//猜想
		//$xmlBase="<xml>
		//<ToUserName><![CDATA[toUser]]></ToUserName>
		//<FromUserName><![CDATA[fromUser]]></FromUserName> 
		//<CreateTime>1348831860</CreateTime>
		//<Content><![CDATA[this is a test]]></Content>
		//</xml>";
		// $xml = new DOMDocument("1.0","UTF-8"); 
		// $xml->loadXML($xmlBase); 
		// $main=$xml->getElementsByTagName('xml');
		// $abc=$xml->createElement("MsgType","<![CDATA[text]]>");
		// $main->item(0)->appendChild($abc);
		//  echo $xml->saveXML();
		$this->response($sRespData);
	}
}