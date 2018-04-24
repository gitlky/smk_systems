<?php

namespace App\Http\Controllers\smk_systems;

use App\Http\Controllers\smk_systems\Services\WxService;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use File;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;
use Request;
use Session;
use Log;

class YuCtrl extends Controller
{
    public function ajax($url, $data = null, $to_wx = false,$storage=false)
    {
        $WxService = new WxService();
        if ($to_wx) {
            $d = $WxService->upload_file($url, $data ,$to_wx,$storage);
        }else{
            $d = $WxService->ajax($url,$data);
        }
        return $d;
    }

    public function ajax_decode($url, $data = null, $to_wx = false,$storage=false)
    {
        return json_decode($this->ajax($url,$data, $to_wx,$storage));
    }

    public function see_json($a = array(), $code = 0, $msg = 'successful')
    {
        $pam = array(
            'code' => $code,
            'msg' => $msg,
        );
        if ($code == 0) {
            $pam['data'] = $a;
        }
        return response()->json($pam);
    }

    public function uuid()
    {
        return str_replace("-","",Uuid::uuid4()->toString());
    }

    public function cfg($str)
    {
        $str = config('qy_cfg.'.$str);
        return $str;
    }

    public function time_stamp()
    {
        return Carbon::now()->timestamp;
    }

    public function get_corp_id($id)
    {
        $data=$this->ajax_decode('corp_id',['corp_id'=>$id]);
        return isset($data->data->corp_id)?$data->data->corp_id:null;
    }


    /**
    参数:Uploads/$CorpID(corp_id)/wx(此处注意是后台还是微信)/image(此处注意文件类型 image,file.others,excel)/
     */
    public function mk_upload_dir($path){
        $path = str_replace("\\","/",$path);
        $dir = explode('/',$path);
        $p = public_path();
        foreach ($dir as $d){
            if(empty($d)){
                continue;
            }
            $p = $p.'/'.$d;
            if(!File::isDirectory($p)){
                File::makeDirectory($p,  $mode = 0777, $recursive = false);
            }
        }
    }

    /***
    上传文件:
     * $file_key:type="file" name="$file_key"
     * $dir_path:'/Uploads/$CorpId/wx/others'
     **/
    public function upload_file_for_wx($file_key,$dir_path)
    {
        $file_name = $file_key;
        if (Request::hasFile($file_name)&&Request::file($file_name)->isValid()) {
            $file = Request::file($file_name);
            $names = $this->uuid();
            $ext = $file->getClientOriginalExtension();
            $originalName = $file->getClientOriginalName();
            $this->mk_upload_dir($dir_path);
            $file_Name = empty($ext)?$names:$names.'.'.$ext;
            $file->move(public_path($dir_path), $file_Name);
            return [
                'name'=>$originalName,
                'path'=>str_replace("//",'/',$dir_path.'/'.$file_Name)
            ];
        }
    }

    /***
     * 上传素材到微信
     * @param $type 文件类型，分别有图片（image）、语音（voice）、视频（video），普通文件（file）
     * @param $path 上传文件的路基，public下开始
     * @param $corp_id
     * @return 微信返回值
     */
    public function upload_file_to_wx($type, $path, $corp_id,$storage=false){
        $sys_return_data = $this->ajax_decode('get_token', ['corp_id'=>$corp_id]);
        if (isset($sys_return_data->code) && $sys_return_data->code==0) {
            $token = $sys_return_data->data;
            $url = "media/upload?type=$type&access_token=$token";
            return $this->ajax_decode($url, $path, true,$storage);
        }else{
            return $sys_return_data;
        }
    }

    public function u($name,$pam=array(),$suite_id){
        $url = urlencode(route('wx_login',array(
            'name'=>$name,
            'param'=>1,
            'd'=>json_encode($pam)
        )));
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$suite_id&redirect_uri=$url&response_type=code&scope=snsapi_userinfo&state=$suite_id#wechat_redirect";
        return $url;
    }

    public function m(Model $m){
        return $m->getTable();
    }


    public function smk_id()
    {
        $smk_id = Session::has($this->cfg('the_key.wx.smk_id')) ? Session::get($this->cfg('the_key.wx.smk_id')) : null;
        return $smk_id;
    }

    public function corp_id()
    {
        $corp_id = Session::has($this->cfg('the_key.wx.corp')) ? Session::get($this->cfg('the_key.wx.corp')) : null;
        return $corp_id;
    }

    /**
     * member表的自增ID
     * @return null
     */
    public function user_id()
    {
        $user = Session::has($this->cfg('the_key.wx.user')) ? Session::get($this->cfg('the_key.wx.user')) : null;
        return $user;
    }

    /**
     * member的userid
     * @return null
     */
    public function user_wx_id()
    {
        #dump($this->cfg('the_key.wx.user_wx_id'));
        $user_wx_id = Session::has($this->cfg('the_key.wx.user_wx_id')) ? Session::get($this->cfg('the_key.wx.user_wx_id')) : null;
        return $user_wx_id;
    }

    public function arrayToXml($data)
    {
        if (!is_array($data) || count($data) <= 0) {
            return false;
        }
        $xml = "<xml>";
        foreach ($data as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }

    //将XML转为array
    public function xmlToArray($xml)
    {
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $values;
    }


    public function Log($obj)
    {
        Log::info('****************************************project debug info start:****************************************');
        Log::info($obj);
        Log::info('****************************************project debug info end:****************************************');
    }

}
