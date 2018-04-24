<?php
/**
 * Created by IntelliJ IDEA.
 * User: yu
 * Date: 18-1-29
 * Time: 下午5:54
 */

namespace App\Http\Controllers\smk_systems\Api\Msg\Image;

use App\Http\Controllers\smk_systems\Api\Msg\Entity\FileEntity;
use App\Http\Controllers\smk_systems\Api\Msg\MsgTitle;
use App\Http\Controllers\smk_systems\Api\Msg\YuMsg;
use Session;
use Log;

class ImageMsg extends YuMsg
{
    private $msg_title;
    /**
     * Textcard constructor.
     */
    public function __construct()
    {
        $this->msg_title = new MsgTitle();
    }

    public function send(array $dep, array $user, $image_path, $is_db = false)
    {
        $this->set_user($this->msg_title,$user,$dep,$is_db,'image');
        $fileEntity = new FileEntity();
        $fileEntity->setPath($image_path);
        $fileEntity->setCorpId(Session::get(config('qy_cfg.the_key.wx.corp')));
        $data = $this->msg_title->msgTitle($fileEntity->getMediaId('image'));
        $data = $this->send_http('send_msg', $data);
        return isset($data->code)&&$data->code==0?0:1;
    }
}
