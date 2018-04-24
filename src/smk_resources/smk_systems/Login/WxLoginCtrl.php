<?php

namespace App\Http\Controllers\smk_systems\Login;

use App\Http\Controllers\smk_systems\YuCtrl;
use App\Model\maillist_member;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WxLoginCtrl extends YuCtrl
{
    public function wx_login(Request $req, maillist_member $maillist_member)
    {
        $code = $req->code;
        $suite_id = $req->state;
        //dump($suite_id);
        $url_name = $req->name;

        $data = $this->ajax("qy_wx_login_info", array(
            'code' => $code,
            'suite' => $suite_id
        ));
        $a = $req->all();
        $noteach = ['name','code','state'];
        $data = json_decode($data);
        $user_info = isset($data->code) && $data->code == 0 ? $data->data : null;
        $smkid = isset($user_info->smk_id) ? $user_info->smk_id : null;

        $UserId = $user_info->UserId;
        #dump($UserId);die;
        #die;
        $user_info = $maillist_member->where('userid', $user_info->UserId)->where('enterprise_id', $user_info->CorpId)->first();

        if (!empty($user_info)) {
            $user_info = $user_info->toArray();

            $pam = array(
                $this->cfg('the_key.wx.user') => $user_info['id'],
                $this->cfg('the_key.wx.user_wx_id') => $UserId,
                $this->cfg('the_key.wx.corp') => $user_info['enterprise_id'],
                $this->cfg('the_key.wx.smk_id') => $smkid,
            );
            foreach ($a as $key=>$v){
                if(!in_array(Str::lower($key),$noteach)){
                    $pam[$key] = $v;
                }
            }
            if($req->nocg_url==1){
                $x1 = explode('#',$url_name);
                if(count($x1)>1){
                    $url_name = $x1[0];
                }
                $a = http_build_query($pam);
                if(strpos($url_name,'?')){
                    $url_name .='&'.$a;
                }else{
                    $url_name .='?'.$a;
                }
                if(count($x1)>1){
                    $url_name .='#'.$x1[1];
                }
                return redirect($url_name);
            }else{
                $a = json_decode($req->d);
                if($a){
                    foreach ($a as $k=>$y){
                        $pam[$k] = $y;
                    }
                }
                return redirect()->route($url_name, $pam);
            }

        }
    }
}
