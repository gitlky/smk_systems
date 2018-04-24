<?php

namespace App\Http\Controllers\smk_systems\Api\Event;
use Carbon\Carbon;
use Log;

class MsgType extends System_for_WxCtrl
{

    private $corp_id,$user_id,$time_stamp;

    /**
     * MsgType constructor.
     */
    public function __construct($corp_id,$user_id)
    {
        $this->time_stamp = Carbon::now()->timestamp;
        $this->corp_id = $corp_id;
        $this->user_id = $user_id;
    }

    public function text($content)
    {

        $newsTplHead = "<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[text]]></MsgType><Content>$content</Content></xml>";

        $header = sprintf($newsTplHead, $this->user_id, $this->corp_id, $this->time_stamp);

        return $header;
    }

    public function news($array,$suite_id)
    {

        $newsTplHead = "<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[news]]></MsgType><ArticleCount>".count($array)."</ArticleCount><Articles>";

        $newsTplBody = "<item><Title><![CDATA[%s]]></Title><Description><![CDATA[%s]]></Description><PicUrl><![CDATA[%s]]></PicUrl><Url><![CDATA[%s]]></Url></item>";

        $newsTplFoot = "</Articles></xml>";

        $header = sprintf($newsTplHead, $this->user_id, $this->corp_id, $this->time_stamp);

        foreach ($array as $arr){
            $header.=sprintf($newsTplBody, $arr['Title'], $arr['Description'], $arr['PicUrl'],$arr['Url']);
        }

        return $header.$newsTplFoot;
    }

}
