<?php
/**
 * Created by IntelliJ IDEA.
 * User: yu
 * Date: 18-1-29
 * Time: 下午5:28
 */

namespace App\Http\Controllers\smk_systems\Api\Msg\Text;

use App\Http\Controllers\smk_systems\Api\Msg\Entity\TextEntity;
use App\Http\Controllers\smk_systems\Api\Msg\MsgTitle;
use App\Http\Controllers\smk_systems\Api\Msg\YuMsg;

class TextMsg extends YuMsg
{
    private $msg_title;
    /**
     * Textcard constructor.
     */
    public function __construct()
    {
        $this->msg_title = new MsgTitle();
    }

    public function send_msg(array $dep, array $user, $content,$is_db=false)
    {
        $this->set_user($this->msg_title,$user,$dep,$is_db,'text');
        $entity = new TextEntity(0);
        $entity->setContent($content);
        $data=$this->msg_title->msgTitle($entity->get_to_wx());
        return $this->send_http('send_msg',$data);
    }
}