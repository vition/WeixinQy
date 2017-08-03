<?php
/**
 * @Author: vition
 * @Date:   2017-08-02 09:45:11
 * @Last Modified by:   vition
 * @Last Modified time: 2017-08-03 09:13:36
 */

class User extends Urllib{
	private $accessToken;


	function __construct($accessToken){
		$this->accessToken=$accessToken;
	}

	function createUser($userDataArr){
		$resultDataJson=$this->post("https://qyapi.weixin.qq.com/cgi-bin/user/create?access_token=".$this->accessToken,$userDataArr);
		$result=json_decode($resultDataJson);
		if($result->errmsg=="mobile existed:".$userDataArr["userid"]){
			return "用户名：".$userDataArr["name"]."；ID:".$userDataArr["userid"]."已经存在";
		}else if($result->errmsg=="created"){
			return "success";
		}else{
			return $resultDataJson;
		}
	}
	function getUserInfo($code,$detailed=false){
		$resultData=json_decode($this->get("https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo?access_token={$this->accessToken}&code={$code}"));
		if($detailed==true){
			if($resultData->UserId!=""){
				return $this->getUser($resultData->UserId);
			}
		}
		return $resultData;
		
	}
	function getUser($userid){
		return json_decode($this->get("https://qyapi.weixin.qq.com/cgi-bin/user/get?access_token={$this->accessToken}&userid={$userid}"));
	}

	function updateUser($userDataArr){
		$resultDataJson=$this->post("https://qyapi.weixin.qq.com/cgi-bin/user/update?access_token=".$this->accessToken,$userDataArr);
		$result=json_decode($resultDataJson);
		if($result->errmsg=="updated"){
			return "success";
		}else if($result->errmsg=="userid not found"){
			return "用户不存在";
		}else{
			return $resultDataJson;
		}
	}

	function deleteUser($userid){
		$resultDataJson=$this->get("https://qyapi.weixin.qq.com/cgi-bin/user/delete?access_token={$this->accessToken}&userid={$userid}");
		$result=json_decode($resultDataJson);
		if($result->errmsg=="deleted"){
			return "success";
		}else if($result->errmsg=="userid not found"){
			return "用户不存在";
		}else{
			return $resultDataJson;
		}
	}

	function batchDelete($useridlistArr){
		//array("useridlist"=>array("test1","test2"))
		$resultDataJson=$this->post("https://qyapi.weixin.qq.com/cgi-bin/user/batchdelete?access_token=".$this->accessToken,$useridlistArr);
		$result=json_decode($resultDataJson);
		if($result->errmsg=="deleted"){
			return "success";
		}else if($result->errmsg=="userid not found"){
			return "用户不存在";
		}else{
			return $resultDataJson;
		}
	}

	function simpleList($department_id){
		$resultDataJson=$this->get("https://qyapi.weixin.qq.com/cgi-bin/user/simplelist?access_token={$this->accessToken}&department_id={$department_id}&fetch_child=1");
		return json_decode($resultDataJson);
	}

	function userList($department_id){
		$resultDataJson=$this->get("https://qyapi.weixin.qq.com/cgi-bin/user/list?access_token={$this->accessToken}&department_id={$department_id}&fetch_child=1");
		return json_decode($resultDataJson);
	}

	function convertOpenid($idArray){
		//array("userid"=>"","agentid"=>);
		$resultDataJson=$this->post("https://qyapi.weixin.qq.com/cgi-bin/user/convert_to_openid?access_token=".$this->accessToken,$idArray);
		return json_decode($resultDataJson);
	}

	function convertUserid($openid){
		//array("openid"=>"");
		$resultDataJson=$this->post("https://qyapi.weixin.qq.com/cgi-bin/user/convert_to_userid?access_token=".$this->accessToken,$openid);
		return json_decode($resultDataJson);
	}

	function authSucc($userid){
		$resultDataJson=$this->get("https://qyapi.weixin.qq.com/cgi-bin/user/authsucc?access_token={$this->accessToken}&userid={$userid}");
		return json_decode($resultDataJson);
	}

	function createDepartment($departmentDataArray){
		//array("name"=>"","parentid"=>"","order"=>,"id"=>);
		$resultDataJson=$this->post("https://qyapi.weixin.qq.com/cgi-bin/department/create?access_token=".$this->accessToken,$departmentDataArray);
		return json_decode($resultDataJson);
	}
	function updateDepartment($departmentDataArray){
		//array("name"=>"","parentid"=>"","order"=>,"id"=>);
		$resultDataJson=$this->post("https://qyapi.weixin.qq.com/cgi-bin/department/update?access_token=".$this->accessToken,$departmentDataArray);
		$result=json_decode($resultDataJson);
		if($result->errmsg=="updated"){
			return "success";
		}else if($result->errmsg=="userid not found"){
			return "部门不存在";
		}else{
			return $resultDataJson;
		}
	}

	function deleteDepartment($departmentId){
		$resultDataJson=$this->get("https://qyapi.weixin.qq.com/cgi-bin/department/delete?access_token={$this->accessToken}&id={$departmentId}");
		$result=json_decode($resultDataJson);
		if($result->errmsg=="deleted"){
			return "success";
		}else if($result->errmsg=="userid not found"){
			return "用户不存在";
		}else{
			return $resultDataJson;
		}
	}

	function departmentList($department_id){
		$resultDataJson=$this->get("https://qyapi.weixin.qq.com/cgi-bin/department/list?access_token={$this->accessToken}&id={$department_id}");
		return json_decode($resultDataJson);
	}
}
// access_token 	是 	调用接口凭证
// userid 			是 	成员UserID。对应管理端的帐号，企业内必须唯一。不区分大小写，长度为1~64个字节
// name 			是 	成员名称。长度为1~64个字节
// english_name 	否 	英文名。长度为1-64个字节。
// mobile 			是 	手机号码。企业内必须唯一
// department 		是 	成员所属部门id列表,不超过20个
// order 			否 	部门内的排序值，默认为0。数量必须和department一致，数值越大排序越前面。有效的值范围是[0, 2^32)
// position 		否 	职位信息。长度为0~64个字节
// gender 			否 	性别。1表示男性，2表示女性
// email 			否 	邮箱。长度为0~64个字节。企业内必须唯一
// telephone 		否 	座机。长度0-64个字节。
// isleader 		否 	上级字段，标识是否为上级。
// avatar_mediaid 	否 	成员头像的mediaid，通过多媒体接口上传图片获得的mediaid
// enable 			否 	启用/禁用成员。1表示启用成员，0表示禁用成员
// extattr 			否 	自定义字段。自定义字段需要先在WEB管理端“我的企业” — “通讯录管理”添加，否则忽略未知属性的赋值 