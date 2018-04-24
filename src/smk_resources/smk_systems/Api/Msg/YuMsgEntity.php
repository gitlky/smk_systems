<?php
/**
 * Created by IntelliJ IDEA.
 * User: yu
 * Date: 18-1-29
 * Time: 下午2:31
 */

namespace App\Http\Controllers\smk_systems\Api\Msg;

use App\Http\Controllers\smk_systems\Services\WxService;

abstract class YuMsgEntity
{
    protected $smk_id;
    abstract function __construct($smk_id);

    abstract function get_to_wx();

    public function click_url($url)
    {
        $wx_service = new WxService();
        $url = route('wx_login', array(
            'nocg_url' => 1,
            'name' => $url
        ));
        $url = $wx_service->ajax('click_url', ['url' => $url, 'smk_id' => $this->smk_id]);
        $url = json_decode($url);
        return isset($url->data) ? $url->data : '';
    }


}