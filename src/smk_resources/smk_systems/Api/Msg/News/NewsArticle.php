<?php

namespace App\Http\Controllers\smk_systems\Api\Msg\News;

use App\Http\Controllers\smk_systems\Api\Msg\MsgTitle;
use App\Http\Controllers\smk_systems\Api\Msg\YuMsg;

/**
 * Created by IntelliJ IDEA.
 * User: yu
 * Date: 18-1-29
 * Time: 上午9:41
 */
class NewsArticle extends YuMsg
{
    private $msg_title;

    /**
     * NewsArticle constructor.
     */
    public function __construct()
    {
       $this->msg_title = new MsgTitle();
    }

    public function send_news(array $dep,  $user, array $art_data, $is_db = false)
    {
        $this->set_user($this->msg_title,$user,$dep,$is_db,'news');
        $data = $this->msg_title->msgTitle(['articles'=>$art_data]);
        #dump($data);
        $data = $this->send_http('send_msg', $data);
        return $data;
    }

}