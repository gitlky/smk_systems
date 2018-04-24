<?php

namespace App\Http\Controllers\smk_systems\Services;

use App\Http\Controllers\smk_systems\Api\Msg\Image\ImageMsg;
use App\Http\Controllers\smk_systems\Api\Msg\News\NewsArticle;
use App\Http\Controllers\smk_systems\Api\Msg\Text\TextMsg;
use App\Http\Controllers\smk_systems\Api\Msg\Textcard\Textcard;
use App\Http\Controllers\smk_systems\YuCtrl;
use App\Http\Service\Msg\File\FileMsg;
use App\Model\admin_article;
use App\Model\maillist_member;
use Cache;
use Carbon\Carbon;
use File;
use Request;
use Session;
use Log;

class Wx_Service extends YuCtrl
{
    public function my_name()
    {
        $id = $this->user_id();
        $m = new maillist_member();
        $d = $m->find($id)->toArray();
        return isset($d['name']) ? $d['name'] : '';
    }


    /**下载文件**/
    public function down_load_file($name)
    {
        $wxService = new WxService();
        $dir_path = 'Uploads/' . $this->corp_id() . '/wx/image/';
        $file_name = $this->uuid() . '.png';
        $token = $this->token();//微信的token
        $a = $wxService->get_media($name, $token);
        $this->mk_upload_dir($dir_path);
        $filename = $dir_path . $file_name;
        File::put($filename, $a);
        return '/' . $dir_path . $file_name;
    }

    /**获取当前企业的token**/
    public function token()
    {
        $token = $this->ajax_decode('get_access_token', [
            'smk_id' => $this->smk_id(),
            'corp_id' => $this->corp_id()
        ]);
        return isset($token->data->token) && $token->code == 0 ? $token->data->token : null;
    }


    /***************************发送消息类的封装*********************************/

    /**
     * 主动向用户推送textcard消息
     **/
    public function send_textcard2_user(
        $title,
        $content,
        $url, /**点击时打开的连接(全名,以get形式传递参数)**/
        $user = [],
        $dep = [], $is_db = false)
    {
        if (!is_array($user)) {
            throw new \Exception('user must be array like this:["user_user_id"]');
        }
        if (!is_array($dep)) {
            throw new \Exception('dep must be array like this:["dep_wx_id"]');
        }
        $textcard = new Textcard();
        return $textcard->send($dep, $user, $title, $content, $url, $is_db);
    }

    /****
     * 主动向用户发送图文消息
     ***/
    public function send_news2_user($dep, $member, array $data, $is_db_id = false)
    {
        $news = new NewsArticle();
        return $news->send_news($dep, $member, $data, $is_db_id);
    }


    /***
     * 主动向用户发送文件消息
     ***/
    public function send_file_to_user($path, $user = [], $dep = [], $is_db_id = false, $storage = false)
    {
        if (!is_array($user)) {
            throw new \Exception('user must be array like this:["user_user_id"]');
        } else {
            if (count($user) < 1) {
                $user = [$this->user_wx_id()];
            }
        }
        if (!is_array($dep)) {
            throw new \Exception('dep must be array like this:["dep_wx_id"]');
        }
        $f = new FileMsg();
        $x = $f->send_file($dep, $user, $path, $is_db_id, $storage);
        return $x;
    }

    /****
     * 主动向用户发送文字消息
     ***/
    public function send_text2_user(array $dep, array $member, $content, $is_db_id = false)
    {
        $text = new TextMsg();
        return $text->send_msg($dep, $member, $content, $is_db_id);
    }

    /****
     * 主动向用户发送图片消息
     ***/
    public function send_image2_user($path, $user = [], $dep = [], $is_db_id = false)
    {
        $img = new ImageMsg();
        return $img->send($dep, $user, $path, $is_db_id);
    }


    /***************************发送消息类的封装*********************************/
    public function send_art_news_list($id, $url = null)
    {
        $admin_article = new admin_article();
        $c = $admin_article->with('get_at_member')->find($id);
        $is_dirf = $c->is_drafts;
        if ($is_dirf != 0) {
            return;
        }
        $dep = collect();
        $mem = collect();
        $mems = $c->get_at_member;
        $mems->each(function ($items) use ($dep, $mem) {
            $is_dep = $items->is_dep;
            if ($is_dep == 1) {
                $dep->push($items->member_id);
            } else {
                $mem->push($items->member_id);
            }
        });
        $img = $c->img_url;
        $wx_ser = new Wx_Service();
        $u = route('article_detail', ['id' => $id]);
        if (null != $url) {
            $u .= '&content_url=' . urlencode($url);
        }
        $x = [[
            'title' => $c->title,
            'description' => $c->content,
            'url' => $u,
            'picurl' => asset($img),
        ]];
        //dump($dep->toArray());
        //dump($mem->toArray());
        $wx_ser->send_news2_user($dep->toArray(), $mem->toArray(), $x, true);
        //dump($n);
    }


    /***************************微信支付**********************************/

    public function corp_user_id($user_id, $corp_id, $smk_id)
    {
        $pam = array(
            'corp_id' => $corp_id,
            'user_id' => $user_id,
            'smk_id' => $smk_id
        );
        $data = $this->ajax('userid2wxid', $pam);
        $data = json_decode($data);
        if (null != $data) {
            if (isset($data->code) && $data->code == 0) {
                return $data->data;
            }
        }
        return null;
    }

    public function do_pay($open_id,$corp_id,$desc,$total_fee,$c_ip)
    {
        $pam = array(
            'open_id' => $open_id,
            'corp_id' => $corp_id,
            'c_ip' => $c_ip,
            'desc'=>$desc,
            'total_fee'=>$total_fee
        );
        $data = $this->ajax('do_order', $pam);
        $data = json_decode($data);
        if (null != $data) {
            if (isset($data->code) && $data->code == 0) {
                return $data->data;
            }
        }
        return null;
    }

    public function corp_id_of_wx($corp_id){
        $data=json_decode($this->ajax('corp_id',['corp_id'=>$corp_id]));
        $corp_id=isset($data->data->corp_id)?$data->data->corp_id:null;
        return $corp_id;
    }

}
