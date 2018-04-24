<?php

namespace App\Http\Controllers\smk_systems\Services;

use anlutro\cURL\cURL;
use Cache;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Log;

class WxService
{
    public function ajax($url, $data = null)
    {
        $curl = new cURL();
        $url = 'http://system.cdsmartlink.com/api/qyh/'.$url;
        #dump($url);
        $data['provider_id'] = 1;
        if(null==$data){
            $data = array(
                'provider_id'=>config('qy_cfg.provider_id')
            );
        }else{
            $data['provider_id']=config('qy_cfg.provider_id');
        }
        #dump($data);
        #die;
        $request = $curl->newRequest('post', $url, $data)
            ->setHeader('Accept-Charset', 'utf-8')
            ->setHeader('Accept-Language', 'en-US')
            ->setOption(CURLOPT_TIMEOUT,10)
            ->setCookies($_COOKIE);
        $response = $request->send()->body;
        return $response;
    }

    private function get($url,$data=array())
    {
        $curl = new cURL();
        $url = $curl->buildUrl($url, $data);
        $response = $curl->get($url);
        return $response->body;
    }

    //获取JS api ticket
    public function get_jsapi_ticket($corpid,$smk_id)
    {
        Cache::forget( md5("lky") . '_' . $corpid);
        $ticket = Cache::remember(
            md5("lky") . '_' . $corpid,
            115, function () use ($corpid,$smk_id) {
            $data = $this->ajax("get_jsapi_ticket", array(
                'corp_id'=>$corpid,
                'smk_id'=>$smk_id
            ));
            $data = json_decode($data);
            return isset($data->code) && $data->code == 0 ? $data->data : null;
        });
        return $ticket;
    }

    //获取JSJDK签名算法
    public function get_signature($noncestr, $jsapi_ticket, $timestamp, $url)
    {
        Log::info($url);
        $noncestr = 'noncestr='.$noncestr;
        $jsapi_ticket = 'jsapi_ticket='.$jsapi_ticket;
        $timestamp = 'timestamp='.$timestamp;
        $url = 'url=' . $url;
        $str = $jsapi_ticket.'&'.$noncestr.'&'.$timestamp.'&'.$url;
        return sha1($str);
    }

    //获取JS配置文件
    public function wx_js_config($smk_id, $url, $corp_id, array $api_list, $debug = false)
    {
        $js_ticket = $this->get_jsapi_ticket($corp_id,$smk_id);
        $data=json_decode($this->ajax('corp_id',['corp_id'=>$corp_id]));
        $corp_id=isset($data->data->corp_id)?$data->data->corp_id:null;
        $timestamp = Carbon::now()->timestamp;
        $noncestr = $timestamp.'smk';
        $signature = $this->get_signature($noncestr, $js_ticket, $timestamp, $url);
        $api_str = "";
        foreach ($api_list as $key=>$a) {
            $api_str .= "'$a'";
            if($key!=count($api_list)-1){
                $api_str.=",";
            }
        }
        $debug=$debug?"true":"false";
        $js = "{
            beta:true,
            debug:$debug, 
            appId:'$corp_id', 
            timestamp:$timestamp,
            nonceStr: '$noncestr', 
            signature:'$signature',
            jsApiList: [$api_str] 
        }";
        return $js;
    }

    //获取文件
    public function get_media($media_id,$token)
    {
        $url = 'https://qyapi.weixin.qq.com/cgi-bin/media/get';
        $pam = array(
            'access_token'=>$token,
            'media_id'=>$media_id
        );
        $data = $this->get($url,$pam);
        return $data;
    }

    /***
    上传文件到微信
     *
     * 文件路径是基于的public文件夹之下的路径，请注意！！！！！！
     **/
    public function upload_file($url, $file_path, $to_wx = false,$storage=false)
    {
        //dump(public_path($file_path));
        if ($to_wx) {
            $url = 'https://qyapi.weixin.qq.com/cgi-bin/'.$url;
        }
        $client = new Client();
        #dump($file_path);
        $p = $storage?$file_path:public_path($file_path);
        #dump($p);
        $response = $client->request('POST', $url, [
            'multipart' => [
                [
                    'name'     => 'file_name',
                    'contents' => fopen($p, 'r')
                ],
            ]
        ]);
        $data = $response->getBody();
        return $data;
    }
}
