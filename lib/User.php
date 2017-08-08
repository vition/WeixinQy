<?php
/**
 * @Author: vition
 * @Date:   2017-08-02 09:45:11
 * @Last Modified by:   vition
 * @Last Modified time: 2017-08-03 12:16:55
 */

class User extends Urllib{
	private $accessToken;

	/**
	 * [__construct 构造方法]
	 * @param [type] $accessToken [accessToken]
	 */
	function __construct($accessToken){
		$this->accessToken=$accessToken;
	}

	/**
	 * [createUser 新建用户]
	 * @param  [数组] $userDataArr [用户的信息]
	 * @return [type]              [description]
	 */
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
	/**
	 * [getUserInfo 通过code获取用户信息，一般在验证登录时使用]
	 * @param  [type]  $code     [通过url得到的code]
	 * @param  boolean $detailed [默认值返回简单的用户信息，为true则查询详细的资料]
	 * @return [type]            [description]
	 */
	function getUserInfo($code,$detailed=false){
		$resultData=json_decode($this->get("https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo?access_token={$this->accessToken}&code={$code}"));
		if($detailed==true){
			if($resultData->UserId!=""){
				return $this->getUser($resultData->UserId);
			}
		}
		return $resultData;
		
	}
	/**
	 * [getUser 通过用户id获取用户信息]
	 * @param  [type] $userid [用户id]
	 * @return [type]         [description]
	 */
	function getUser($userid){
		return json_decode($this->get("https://qyapi.weixin.qq.com/cgi-bin/user/get?access_token={$this->accessToken}&userid={$userid}"));
	}

	/**
	 * [updateUser 更改用户信息]
	 * @param  [数组] $userDataArr [用户数据]
	 * @return [type]              [description]
	 */
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

	/**
	 * [deleteUser 删除用户]
	 * @param  [type] $userid [用户id]
	 * @return [type]         [description]
	 */
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

	/**
	 * [batchDelete 批量删除用户]
	 * @param  [数组] $useridlistArr [用户id]
	 * @return [type]                [description]
	 */
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

	/**
	 * [simpleList 获取部门成员]
	 * @param  [type] $department_id [部门id]
	 * @return [type]                [description]
	 */
	function simpleList($department_id){
		$resultDataJson=$this->get("https://qyapi.weixin.qq.com/cgi-bin/user/simplelist?access_token={$this->accessToken}&department_id={$department_id}&fetch_child=1");
		return json_decode($resultDataJson);
	}
	/**
	 * [userList 获取部门成员详情]
	 * @param  [type] $department_id [部门id]
	 * @return [type]                [description]
	 */
	function userList($department_id){
		$resultDataJson=$this->get("https://qyapi.weixin.qq.com/cgi-bin/user/list?access_token={$this->accessToken}&department_id={$department_id}&fetch_child=1");
		return json_decode($resultDataJson);
	}

	/**
	 * [convertOpenid userid转换成openid接口]
	 * @param  [type] $idArray [userid]
	 * @return [type]          [description]
	 */
	function convertOpenid($idArray){
		//array("userid"=>"","agentid"=>);
		$resultDataJson=$this->post("https://qyapi.weixin.qq.com/cgi-bin/user/convert_to_openid?access_token=".$this->accessToken,$idArray);
		return json_decode($resultDataJson);
	}

	/**
	 * [convertUserid openid转换成userid接口]
	 * @param  [type] $openid [openid]
	 * @return [type]         [description]
	 */
	function convertUserid($openid){
		//array("openid"=>"");
		$resultDataJson=$this->post("https://qyapi.weixin.qq.com/cgi-bin/user/convert_to_userid?access_token=".$this->accessToken,$openid);
		return json_decode($resultDataJson);
	}
	/**
	 * [authSucc 二次验证]
	 * @param  [type] $userid [userid]
	 * @return [type]         [description]
	 */
	function authSucc($userid){
		$resultDataJson=$this->get("https://qyapi.weixin.qq.com/cgi-bin/user/authsucc?access_token={$this->accessToken}&userid={$userid}");
		return json_decode($resultDataJson);
	}

	/**
	 * [createDepartment 创建部门]
	 * @param  [数组] $departmentDataArray [部门数据]
	 * @return [type]                      [description]
	 */
	function createDepartment($departmentDataArray){
		//array("name"=>"","parentid"=>"","order"=>,"id"=>);
		$resultDataJson=$this->post("https://qyapi.weixin.qq.com/cgi-bin/department/create?access_token=".$this->accessToken,$departmentDataArray);
		return json_decode($resultDataJson);
	}
	/**
	 * [updateDepartment 更新部门]
	 * @param  [数组] $departmentDataArray [部门数据]
	 * @return [type]                      [description]
	 */
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
	/**
	 * [deleteDepartment 删除部门]
	 * @param  [数组] $departmentId [部门id]
	 * @return [type]                      [description]
	 */
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
	/**
	 * [departmentList 获取部门列表]
	 * @param  [type] $department_id [description]
	 * @return [type]                [description]
	 */
	function departmentList($department_id){
		$resultDataJson=$this->get("https://qyapi.weixin.qq.com/cgi-bin/department/list?access_token={$this->accessToken}&id={$department_id}");
		return json_decode($resultDataJson);
	}
}