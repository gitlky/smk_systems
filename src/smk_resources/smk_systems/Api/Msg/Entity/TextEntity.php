<?php
/**
 * Created by IntelliJ IDEA.
 * User: yu
 * Date: 18-1-29
 * Time: 下午5:30
 */

namespace App\Http\Controllers\smk_systems\Api\Msg\Entity;

use App\Http\Controllers\smk_systems\Api\Msg\YuMsgEntity;

class TextEntity extends YuMsgEntity
{

    private $content;

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    public function __construct($smk_id)
    {
        $this->smk_id = $smk_id;
    }

    function get_to_wx()
    {
        return array(
            'content'=>$this->getContent()
        );
    }
}