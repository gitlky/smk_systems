<?php
/**
 * Created by IntelliJ IDEA.
 * User: yu
 * Date: 18-1-29
 * Time: 上午9:42
 */

namespace App\Http\Controllers\smk_systems\Api\Msg\Entity;

use App\Http\Controllers\smk_systems\Api\Msg\YuMsgEntity;


class Articles extends YuMsgEntity
{
    private $title;
    private $description;
    private $url;
    private $picurl;
    private $btntxt;


    /**
     * Articles constructor.
     */
    public function __construct($smk_id)
    {
        $this->smk_id = $smk_id;
    }


    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $this->click_url($url);
    }

    /**
     * @return mixed
     */
    public function getPicurl()
    {
        return $this->picurl;
    }

    /**
     * @param mixed $picurl
     */
    public function setPicurl($picurl,$is_public = true)
    {
        if($is_public){
            $picurl = asset($picurl);
        }
        $this->picurl = $picurl;
    }

    /**
     * @return mixed
     */
    public function getBtntxt()
    {
        return $this->btntxt;
    }

    /**
     * @param mixed $btntxt
     */
    public function setBtntxt($btntxt)
    {
        $this->btntxt = $btntxt;
    }

    public function get_to_wx()
    {
        return array(
            'title'=>$this->title,
            'description'=>$this->description,
            'url'=>$this->url,
            'picurl'=>$this->picurl,
            'btntxt'=>$this->btntxt
        );
    }

}