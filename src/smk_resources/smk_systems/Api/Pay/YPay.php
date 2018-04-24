<?php

namespace App\Http\Controllers\smk_systems\Api\Pay;

use App\Http\Controllers\smk_systems\Services\Wx_Service;
use App\Http\Controllers\smk_systems\WxCtrl;

use Illuminate\Http\Request;
use Log;

class YPay extends WxCtrl
{
    public function index(Request $request, Wx_Service $service)
    {
        $user_id = $this->user_wx_id();
        $corp_id = $this->corp_id();
        $smk_id = $this->smk_id();
        //获取当前用于的OPEN_ID
        $open_id = $service->corp_user_id($user_id, $corp_id, $smk_id);
        $d = $service->do_pay($open_id,$corp_id,'交纳党费',0.01,$request->getClientIp());
        return $this->see_json($d->js_json);
    }

}
