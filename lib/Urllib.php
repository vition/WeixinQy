<?php
/**
 * @Author: oaWeb_admin
 * @Date:   2017-08-02 10:51:59
 * @Last Modified by:   vition
 * @Last Modified time: 2017-08-02 14:46:02
 */

class Urllib{

	function get($url){
		return file_get_contents($url);
	}

	function post($url,$dataArray=array()) {
		$postDataJson = json_encode($dataArray,JSON_UNESCAPED_UNICODE);
		$options = array(
		'http' => array(
		'method' => 'POST',//or GET
		'header' => 'Content-type:application/x-www-form-urlencoded',
		'content' => $postDataJson,
		'timeout' => 15 * 60 // 超时时间（单位:s）
		)
		);
		$context = stream_context_create($options);
		return file_get_contents($url, false, $context);
	}

	/**
	 * @param  [url地址]
	 * @param  [数据]
	 * @return [type]
	 */
	function curlPost($url,$dataArray=array()){
		$o='';
		foreach ($dataArray as $k=>$v){
			$o.="$k=".urlencode($v).'&';
		}
		$postData=substr($o,0,-1);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //如果需要将结果直接返回到变量里，那加上这句。
		return curl_exec($ch);
	}

}