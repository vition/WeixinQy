# WeixinQy
> php开发的企业微信接口

#### 目录说明
* WeixinQy.php【核心文件】
* accesstoken 【缓存文件夹,access_token会根据secret名生成一个php文件】
* lib【调用的子类文件夹】
    * Urllib.php【post和get请求的相关类】
    * Message.php【发送信息和接收信心相应的相关类】   
    * User.php【用户和部门管理的类】
    * WXBIzMsgCrypt.php等其他文件【接收信息调用的相关类】
    
### 使用实例

#### 实例对象

```
require("WeixinQy.php");
$WxQy=new WeixinQy($corpid,$corpsecret);
```

#### 用户管理

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
```

#### 网页授权

```
$WxQy->webLogin(要显示二维码的元素ID,$appid,$agentid,跳转的url);
```

### 接收消息

#### 验证url
```
/*url验证完成后就可以丢一边了*/
$WxQy->message()->responseVerify("token","encodingAesKey");
```

#### 接收消息响应
```
$WxQy->message()->response("token","encodingAesKey");
```
