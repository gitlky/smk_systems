<?php
/**
 * Created by IntelliJ IDEA.
 * User: yu
 * Date: 18-1-29
 * Time: 下午4:13
 */

namespace App\Http\Service\Msg\File;

use App\Http\Controllers\smk_systems\Api\Msg\Entity\FileEntity;
use App\Http\Controllers\smk_systems\Api\Msg\MsgTitle;
use App\Http\Controllers\smk_systems\Api\Msg\YuMsg;
use Session;
use Log;

class FileMsg extends YuMsg
{
    private $msg_title;
    /**
     * NewsArticle constructor.
     */
    public function __construct()
    {
        $this->msg_title = new MsgTitle();
    }

    public function send_file(array $dep, array $user, $file_path, $is_db = false,$storage=false)
    {
        $this->set_user($this->msg_title,$user,$dep,$is_db,'file');
        $fileEntity = new FileEntity();
        $fileEntity->setPath($file_path);
        $fileEntity->setCorpId(Session::get(config('qy_cfg.the_key.wx.corp')));
        $data = $this->msg_title->msgTitle($fileEntity->getMediaId('file',$storage));
        $data = $this->send_http('send_msg', $data);
        return isset($data->code)&&$data->code==0?0:1;
    }
}