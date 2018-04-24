<?php

namespace App\Http\Controllers\smk_systems\Api\Event;

use App\Http\Controllers\smk_systems\YuCtrl;
use App\Model\maillist_member;
use Illuminate\Http\Request;

class System_for_WxCtrl extends YuCtrl
{


    private $u, $c, $s;

    /**
     * 微信端所有的操作事件都来到这个地方统一处理
     */
    public function Wx_Event(Request $req, maillist_member $maillist_member)
    {
        $corp = $req->corp_id;
        $user = $req->FromUserName;

        $this->u = $user;
        $this->c = $req->ToUserName;
        $this->s = $req->suite_id;

        $event = $req->Event;
        $eventkey = $req->EventKey;
        $smk_id = $req->smk_id;
        $user = $maillist_member->where('userid', $user)->where('enterprise_id', $corp)->first();
        if (null == $user) {
            return null;
        }
        $user = $user->toArray();
        $id = isset($user['id']) ? $user['id'] : null;
        $user_avatar = isset($user['avatar']) ? $user['avatar'] : null;
        $user_id = isset($user['userid']) ? $user['userid'] : null;

        //微信端做的各种操作，比如点击，关注，取消关注等等
        switch ($event) {
            case 'click':
                $a = $this->app($smk_id, $corp, $id, $event, $eventkey);
                return $a;
                break;
            case 'subscribe':

                break;
            case 'unsubscribe':

                break;
        }
    }

    /**
     * @param $smk_id
     * @param $corp_id
     * @param $user_id
     * @param $event
     * @param $event_value
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     *
     */
    private function app($smk_id, $corp_id, $user_id, $event, $event_value)
    {
        //此处的应用需要
        switch ($smk_id) {
            //党建
            case 4://在click事件里自定义类并实现Yu_Click_Event
                break;
        }
    }
}
