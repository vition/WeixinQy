<?php
/**
 * @Author: vition
 * @Date:   2017-08-02 14:36:30
 * @Last Modified by:   vition
 * @Last Modified time: 2017-08-03 10:49:36
 */
include_once "WXBizMsgCrypt.php";
class Message extends Urllib{
	protected $sReqTimeStamp;
	protected $sReqNonce;
	protected $accessToken;
	protected $corpid;
	protected $wxcpt;
	// protected $ToUserName;
	protected $xmlBase;

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
			$xmlArray=array("ToUserName","FromUserName","CreateTime","MsgType","Content","PicUrl","MediaId","MsgId","AgentID","Format","ThumbMediaId","Location_X","Location_Y","Scale","Label","Title","Description");
			
			$resutlXml=(object) null;
			foreach ($xmlArray as $xmlNode) {
				$temp=$xml->getElementsByTagName($xmlNode);
				if($temp->length>0){
					$resutlXml->$xmlNode=$temp->item(0)->nodeValue;
				}
			}
			// $this->ToUserName=$resutlXml->FromUserName;
			$this->xmlBase="<ToUserName><![CDATA[".$resutlXml->FromUserName."]]></ToUserName>  
			<FromUserName><![CDATA[".$this->corpid."]]></FromUserName>  
			<CreateTime>". $this->sReqTimeStamp."</CreateTime>";
			return $resutlXml;
	    } else {  
	    	print($errCode . "\n\n");  
	    }  
	}

	private function response($sRespData){
		// $xmlArray=array("ToUserName","FromUserName","CreateTime","MsgType","Content","PicUrl","MediaId","MsgId","AgentID","Format","ThumbMediaId","Location_X","Location_Y","Scale","Label","Title","Description");
		$sEncryptMsg = ""; //xml格式的密文  $reqFromUserName.
		$errCode = $this->wxcpt->EncryptMsg($sRespData, $this->sReqTimeStamp, $this->sReqNonce, $sEncryptMsg);  
		if ($errCode == 0) {    
			print($sEncryptMsg);  
		} else {  
			print($errCode . "\n\n");  
		} 
	}

	/**
	 * [resText 发送文本消息]
	 * @param  [type] $mycontent [回复的文本]
	 * @return [type]            [description]
	 */
	function resText($mycontent){
		$sRespData = "<xml>
			 ".$this->xmlBase."
			<MsgType><![CDATA[text]]></MsgType>  
			<Content><![CDATA[".$mycontent."]]></Content>  
			</xml>"; 
		$this->response($sRespData);
	}
	/**
	 * [resImage 发送图片消息]
	 * @param  [type] $media_id [description]
	 * @return [type]           [description]
	 */
	function resImage($media_id){
		$sRespData = "<xml>
	       ".$this->xmlBase."
	       <MsgType><![CDATA[image]]></MsgType>
	       <Image>
	           <MediaId><![CDATA[".$media_id."]]></MediaId>
	       </Image>
	   	</xml>";
	   	$this->response($sRespData);
	}
	/**
	 * [resVoice 发送语音消息]
	 * @param  [type] $media_id [description]
	 * @return [type]           [description]
	 */
	function resVoice($media_id){
		$sRespData = "<xml>
	       ".$this->xmlBase."
	       <MsgType><![CDATA[voice]]></MsgType>
	       <Voice>
	           <MediaId><![CDATA[".$media_id."]]></MediaId>
	       </Voice>
	   	</xml>";
	   	$this->response($sRespData);
	}

	/**
	 * [resVideo 发送视频消息]
	 * @param  [type] $media_id    [媒体id]
	 * @param  [type] $title       [标题]
	 * @param  [type] $description [描述]
	 * @return [type]              [description]
	 */
	function resVideo($media_id,$title,$description){
		$sRespData = "<xml>
	       ".$this->xmlBase."
	       <MsgType><![CDATA[video]]></MsgType>
	       <Video>
	           <MediaId><![CDATA[".$media_id."]]></MediaId>
	           <Title><![CDATA[".$title."]]></Title>
	           <Description><![CDATA[".$description."]]></Description>
	       </Video>
	   	</xml>";
	   	$this->response($sRespData);
	}

	/**
	 * [resNews 发送新闻消息]
	 * @param  [type] $newsArray [description]
	 * @return [type]            [description]
	 */
	function resNews($newsArray){
		$Articles="";
		foreach ($newsArray as $item) {
			$Articles.="<item>
			           <Title><![CDATA[".$item['title']."]]></Title> 
			           <Description><![CDATA[".$item['description1']."]]></Description>
			           <PicUrl><![CDATA[".$item['picurl']."]]></PicUrl>
			           <Url><![CDATA[".$item['url']."]]></Url>
			       </item>";
		}
		$sRespData = "<xml>
	       ".$this->xmlBase."
	       <MsgType><![CDATA[video]]></MsgType>
	          <ArticleCount>".count($newsArray)."</ArticleCount>
			   <Articles>".$Articles."
			   </Articles>
	   	</xml>";
	   	$this->response($sRespData);
	}
}