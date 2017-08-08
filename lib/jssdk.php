<?php
/**
 * @Author: vition
 * @Date:   2017-08-02 09:45:11
 * @Last Modified by:   vition
 * @Last Modified time: 2017-08-08 09:24:52
 */

class JSSDK extends Urllib {
    private $appId;
    private $appSecret;
    private $access_token;
    private $jsapiFile="accesstoken/jsapi_ticket.php";
    private $jsapi_ticket;

    public function __construct($appId, $appSecret,$accesstoken) {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
        $this->access_token=$accesstoken;
    }
    /**
     * [getSignPackage 获取签名]
     * @method   getSignPackage
     * @Author   vition
     * @DateTime 2017-08-07
     * @return   [type]         [description]
     */
    public function getSignPackage() {
        $jsapiTicket = $this->getJsApiTicket();
        // 注意 URL 一定要动态获取，不能 hardcode.
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $timestamp = time();
        $nonceStr = $this->createNonceStr();
         // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

        $signature = sha1($string);

        $signPackage = array(
          "appId"     => $this->appId,
          "nonceStr"  => $nonceStr,
          "timestamp" => $timestamp,
          "url"       => $url,
          "signature" => $signature,
          "rawString" => $string,
          "accesstoken" => $this->access_token
        );
        return $signPackage; 
    }

    /**
     * [getJsApiTicket 获取JsApiTicket]
     * @method   getJsApiTicket
     * @Author   vition
     * @DateTime 2017-08-07
     * @return   [type]         [description]
     */
    private function getJsApiTicket(){
        if(file_exists($this->jsapiFile)){
            $jsapiData=json_decode(trim(substr($this->get($this->jsapiFile), 15)));
            if ($jsapiData->expire_time < time()) {
                $this->jsapi_ticket=$this->createJsApiTicket();
            }else{
                $this->jsapi_ticket= $jsapiData->jsapi_ticket;
            }
        }else{
            if(!is_dir("accesstoken/")){
                mkdir("accesstoken/");
            }
            $this->jsapi_ticket=$this->createJsApiTicket();
        }
        return $this->jsapi_ticket;
    }
    /**
     * [createJsApiTicket 生成JsApiTicket]
     * @method   createJsApiTicket
     * @Author   vition
     * @DateTime 2017-08-07
     * @return   [type]            [description]
     */
     private function createJsApiTicket(){
        $jsapiJson=$this->get("https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=".$this->access_token);

        $JTObject=json_decode($jsapiJson);
        if ($JTObject->ticket) {
            $jsapiFile = fopen($this->jsapiFile, "w+");
            fwrite($jsapiFile, "<?php exit();?>" . json_encode(array("expire_time"=>time() + 7000,"jsapi_ticket"=>$JTObject->ticket)));
            fclose($jsapiFile);
            return $JTObject->ticket;
        }
        return false;
     }
    /**
     * [createNonceStr 生成生成签名的随机串]
     * @method   createNonceStr
     * @Author   vition
     * @DateTime 2017-08-07
     * @param    integer        $length [长度]
     * @return   [type]                 [description]
     */
    private function createNonceStr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

}