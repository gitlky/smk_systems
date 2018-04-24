<?php
/**
 * Created by IntelliJ IDEA.
 * User: yu
 * Date: 18-1-29
 * Time: 下午3:29
 */

namespace App\Http\Controllers\smk_systems\Api\Msg;

use App\Http\Controllers\smk_systems\Services\WxService;
use App\Model\maillist_department;
use App\Model\maillist_member;
use Session;

abstract class YuMsg
{

    protected  $wxservice ;

    protected function send_http($u,$data){
        $this->wxservice = new WxService();
        $data=$this->wxservice->ajax($u, $data);
        return $data;
    }

    protected function set_user(MsgTitle $msgTitle,$user,$dep,$is_db,$msg_type,$smkid=null,$corp_id=null){
        if(null==$smkid){
            $smkid = Session::get(config('qy_cfg.the_key.wx.smk_id'));
        }
        if(null==$corp_id){
            $corp_id = Session::get(config('qy_cfg.the_key.wx.corp'));
        }
        //如果传递的是db的id则要转换为微信的id

        if ($is_db&&$user!='all') {
            $member_model = new maillist_member();
            $dep_model = new maillist_department();
            if (count($dep) > 0)
                $d = [];
            foreach ($dep as $dp) {
                $d[] = $dep_model->find($dp)->toArray()['wxid'];
            }
            if (count($user) > 0) {
                $u = [];
                foreach ($user as $us) {
                    $u[] = $member_model->find($us)->toArray()['userid'];
                }
            }
            $dep = isset($d)&&is_array($d)&&count($d)>0?$d:[];
            $user = isset($u)&&is_array($u)&&count($u)>0?$u:[];
        }
        $msgTitle->setTouser($user);
        $msgTitle->setToparty($dep);
        $msgTitle->setSmkId($smkid);
        $msgTitle->setCorpId($corp_id);
        $msgTitle->setMsgtype($msg_type);
    }
}