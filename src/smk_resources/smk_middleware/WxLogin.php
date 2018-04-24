<?php

namespace App\Http\Middleware;

use Closure;

class WxLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $pam = array(

        );
        if(in_array($request->url(),$pam)){
            return $next($request);
        }
        $corp_str = config('qy_cfg.the_key.wx.corp');
        $user_str = config('qy_cfg.the_key.wx.user');
        $smk_str = config('qy_cfg.the_key.wx.smk_id');
        $user_wx_str = config('qy_cfg.the_key.wx.user_wx_id');
        $user = $request->input($user_str, false);
        $corp = $request->input($corp_str, false);
        $smk_id = $request->input($smk_str,false);
        $user_id = $request->input($user_wx_str,false);
        #dump($request->all());
        #die;
        $session = $request->session();
        //如果session里面没有数据
        #dump($request->all());
        #dump($session);
        if (!$session->has($corp_str)||!$session->has($user_str)) {
            if ($user == false || $corp == false) {
               if(1==1){
                   $session->put($corp_str, '4');
                   $session->put($user_str, '11');
                   $session->put($smk_str, '5');
                   $session->put($user_wx_str,'lukeyu');
               }else{
                   dump('没有用户信息');
                   die;
               }

            } else {
                $session->put($user_wx_str, $user_id);
                $session->put($corp_str, $corp);
                $session->put($user_str, $user);
                $session->put($smk_str, $smk_id);
            }
            //如果session里面有数据
        } else {
            //如果有新的数据则存入新的数据
            if ($user != false && $corp != false) {
                $session->put($corp_str, $corp);
                $session->put($user_str, $user);
                $session->put($smk_str, $smk_id);
                $session->put($user_wx_str, $user_id);
            }
        }
        #dump($request->session()->all());die;
        return $next($request);
    }
}
