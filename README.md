# WeixinQy
> php开发的企业微信接口

## 目录说明

* WeixinQy.php【核心文件】
* accesstoken 【缓存文件夹,access_token会根据secret名生成一个php文件】
* lib【调用的子类文件夹】
    * Urllib.php【post和get请求的相关类】
    * Message.php【发送信息和接收信心相应的相关类】   
    * User.php【用户和部门管理的类】
    * WXBIzMsgCrypt.php等其他文件【接收信息调用的相关类】

## 使用实例

### 实例对象

```
    require("WeixinQy.php");
    $WxQy=new WeixinQy($corpid,$corpsecret);
```

### 用户管理

```
/*新建用户*/
    $WxQy->user()->createUser(array(数组形式，元素请参考官网));
/*获取用户信息*/
    $WxQy->user()->getUser(要获取的用户id);
/*更新用户信息*/
    $WxQy->user()->updateUser(array(数组形式，元素请参考官网));
/*删除用户*/
    $WxQy->user()->getUser(要删除的用户id);
/*批量删除用户*/
    $WxQy->user()->batchDelete(array("useridlist"=>array("用户1id","用户2id")));

/*获取部门成员*/
    $WxQy->user()->simpleList(部门id);
/*获取部门成员详情*/
    $WxQy->user()->userList(部门id);

/*userid转换成openid接口*/
    $WxQy->user()->convertOpenid(userid);
/*openid转换成userid接口*/
    $WxQy->user()->convertUserid(openid);
/*二次验证*/
    $WxQy->user()->authSucc(userid);

```

### 部门管理
```
/*创建部门*/
    $WxQy->user()->createDepartment(部门数据数组)
/*更新部门*/
    $WxQy->user()->updateDepartment(部门数据数组)
/*删除部门*/
    $WxQy->user()->deleteDepartment(部门id) 
/*获取部门列表*/
    $WxQy->user()->departmentList(部门id)
```

### 网页授权

```
/*结合html实现二维码登录*/
    $WxQy->webLogin(要显示二维码的元素ID,$appid,$agentid,跳转的url);
```

### 接收消息

#### 验证url

```
/*url验证完成后就可以丢一边了*/
    $WxQy->message()->responseVerify("token","encodingAesKey");
```

#### 监听响应

```
    $WxQy->message()->listen("token","encodingAesKey");
```

#### 被动发送消息

```
/*发送文本*/
    $WxQy->message()->resText(文本);
/*发送图片*/
    $WxQy->message()->resImage(媒体id);
/*发送语音*/
    $WxQy->message()->resVoice(媒体id);
/*发送视频*/
    $WxQy->message()->resVideo(媒体id,标题,描述);
/*发送新闻*/
    $WxQy->message()->resNews(新闻数据数组);
```

