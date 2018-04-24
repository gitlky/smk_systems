<?php
/**
 * Created by IntelliJ IDEA.
 * User: yu
 * Date: 18-1-29
 * Time: 下午3:02
 */

namespace App\Http\Controllers\smk_systems\Api\Msg\Entity;


use App\Http\Controllers\smk_systems\Api\Msg\YuMsgEntity;

class Textcard extends YuMsgEntity
{
    private $title;
    private $content;
    private $url;
    private $text;

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


    public function set_html_content($a, $b, $c)
    {
        $this->content = '<div class="gray">'.$a.'</div><div class="normal">'.$b.'</div><div class="highlight">'.$c.'</div>';
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
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }


    public function __construct($smk_id)
    {
        $this->smk_id = $smk_id;
    }

    function get_to_wx()
    {
        return array(
            'title' => $this->getTitle(),
            'description' => $this->getContent(),
            'url' => $this->getUrl(),
            'btntxt' => $this->getText()
        );
    }
}