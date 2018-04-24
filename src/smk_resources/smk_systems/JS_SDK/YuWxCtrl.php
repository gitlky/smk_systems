<?php

namespace App\Http\Controllers\smk_systems\JS_SDK;

use App\Http\Controllers\smk_systems\Services\WxService;
use App\Http\Controllers\smk_systems\WxCtrl;
use Illuminate\Http\Request;
use Log;

class YuWxCtrl extends WxCtrl
{
    public function Wx(Request $req)
    {
        $url = $req->url;
        $url = urldecode($url);
        if(null==$url){
            return "alert('调用微信接口的url参数不正确')";
        }
        return $this->str($url);
    }

    private function str($url){
        $wxservice = new WxService();

        $qm = $wxservice->wx_js_config(
            $this->smk_id(),
            $url,
            $this->corp_id(),
            array('previewImage', 'closeWindow', 'openEnterpriseChat','selectEnterpriseContact', 'chooseImage', 'uploadImage'), false);
        $s= "$(function(){
            wx.config($qm);
            wx.ready(function () {
               console.log('cfg is successful');
            });
            wx.error(function (res) {
            W(res);
                alert('微信配置出错，请联系开发')
            });
});
       var yu_wx = {
            //选择对象
           select_member:function(m,d,fun,model,type){
                if(!model){
                   model= 'multi';
                }
                if(!type){
                    type = [\"department\", \"user\"];
                }
               wx.invoke(\"selectEnterpriseContact\", {
                    \"fromDepartmentId\": -1,// 必填，-1表示打开的通讯录从自己所在部门开始展示, 0表示从最上层开始
                    \"mode\": model,// 必填，选择模式，single表示单选，multi表示多选
                    \"type\": type,// 必填，选择限制类型，指定department、user中的一个或者多个
                    \"selectedDepartmentIds\":d,// 非必填，已选部门ID列表。用于多次选人时可重入
                    \"selectedUserIds\": m// 非必填，已选用户ID列表。用于多次选人时可重入
                }, function (res) {
                    if (res.err_msg == \"selectEnterpriseContact:ok\") {
                        if (typeof res.result == 'string') {
                            res.result = JSON.parse(res.result) //由于目前各个终端尚未完全兼容，需要开发者额外判断result类型以保证在各个终端的兼容性
                        }
                        try{
                            var selectedDepartmentList = res.result.departmentList;// 已选的部门列表
                        var selectedUserList = res.result.userList; // 已选的成员列表
                        var str = {
                            'dep': selectedDepartmentList,
                            'member': selectedUserList
                        }
                            fun(str);
                        }catch(e){
                            fun(null);
                        }
                    }
            });
          },
          select_img:function(fun){
            wx.checkJsApi({
                jsApiList: ['chooseImage'], // 需要检测的JS接口列表，所有JS接口列表见附录2,
                success: function (res) {
                    wx.chooseImage({
                        count: 1, // 默认9
                        sizeType: ['original', 'compressed'], // 可以指定是原图还是压缩图，默认二者都有
                        sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
                        success: function (res) {
                            var localIds = res.localIds;
                            var src = localIds[0];
                            fun(src);
                        }
                    });
                }
            });
          },
          upload_img:function(id,fun){
            wx.uploadImage({
                localId: id, // 需要上传的图片的本地ID，由chooseImage接口获得
                isShowProgressTips: 1, // 默认为1，显示进度提示
                success: function (res) {
                    fun(res.serverId);
                }
            });
          },
          close_window : function(){
            wx.closeWindow();
          },
          open_chat : function(userList, groupName, fun){
            if(!groupName){
                groupName= '讨论组';
            }
            wx.openEnterpriseChat({
            userIds: userList,    // 必填，参与会话的成员列表。格式为userid1;userid2;...，用分号隔开，最大限制为2000个。userid单个时为单聊，多个时为群聊。
            groupName: groupName,  // 必填，会话名称。单聊时该参数传入空字符串\"\"即可。
            success: function(res) {
                fun(res);
            },
            fail: function(res) {
                if(res.errMsg.indexOf('function not exist') > -1){
                    alert('版本过低请升级')
                }
            }
            });
          }
    }
";
        return $s;
    }
}
