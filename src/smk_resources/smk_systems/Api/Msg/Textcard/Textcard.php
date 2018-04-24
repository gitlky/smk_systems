<?php
/**
 * Created by IntelliJ IDEA.
 * User: yu
 * Date: 18-1-29
 * Time: 下午2:25
 */

namespace App\Http\Controllers\smk_systems\Api\Msg\Textcard;

use App\Http\Controllers\smk_systems\Api\Msg\MsgTitle;
use App\Http\Controllers\smk_systems\Api\Msg\YuMsg;
use Session;
use Log;

class Textcard extends YuMsg
{
    private $msg_title;
    private $smk_id;
    private $corp_id;

    /**
     * @return mixed
     */
    public function getCorpId()
    {
        return $this->corp_id;
    }

    /**
     * @param mixed $corp_id
     */
    public function setCorpId($corp_id)
    {
        $this->corp_id = $corp_id;
    }

    /**
     * @return mixed
     */
    public function getSmkId()
    {
        return $this->smk_id;
    }

    /**
     * @param mixed $smk_id
     */
    public function setSmkId($smk_id)
    {
        $this->smk_id = $smk_id;
    }

    /**
     * Textcard constructor.
     */
    public function __construct()
    {
        $this->msg_title = new MsgTitle();
    }

    public function send(array $dep, array $user, $title, $content, $url, $is_db = false, $btn_text = '详情', $corp_id = null, $smk_id = null)
    {
        if (null != $smk_id) {
            $this->setSmkId($smk_id);
        } elseif(!$this->smk_id){
            $this->setSmkId(Session::get(config('qy_cfg.the_key.wx.smk_id')));
        }
        $this->set_user($this->msg_title, $user, $dep, $is_db, 'textcard', $this->smk_id, $corp_id);
        $entity = new \App\Http\Controllers\smk_systems\Api\Msg\Entity\Textcard($this->smk_id);
        $entity->setTitle($title);
        $entity->setUrl($url);
        if (is_array($content)) {
            $entity->set_html_content($content[0], $content[1], $content[2]);
        } else {
            $entity->setContent($content);
        }
        $entity->setText($btn_text);
        $data = $this->msg_title->msgTitle($entity->get_to_wx());
        #Log::info($data);
        $data = $this->send_http('send_msg', $data);
        #dump($data);
        return $data;
    }

}
