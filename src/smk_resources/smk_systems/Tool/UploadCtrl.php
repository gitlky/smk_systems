<?php

namespace App\Http\Controllers\smk_systems\Tool;

use App\Http\Controllers\smk_systems\YuCtrl;
use Illuminate\Http\Request;
use File;

class UploadCtrl extends YuCtrl
{
    public function upload_base64(Request $req)
    {
        $img = $req->img;
        $corp_id = $req->corp_id ? $req->corp_id : 'default';
        $data = $this->base64imgsave($img, $corp_id)['url'];
        return $this->upload_json($data);
    }


    public function upload_file(Request $req)
    {
        $name = "smk_file";
        $dir_name = $req->dir_name ? $req->dir_name : 'default';
        if ($req->hasFile($name) && $req->file($name)->isValid()) {
            $file = $req->file($name);
            $file_name = $file->getClientOriginalName();
            $ext = $file->getClientOriginalExtension();
            $name = $this->uuid();
            $p = 'Uploads/' . $dir_name . '/file/'.$ymd = date("Ymd");
            $path = public_path($p);
            $file->move($path, $name . '.' . $ext);
            $file_path = $p.'/'.$name . '.' . $ext;
            return $this->upload_json($file_path,0,$file_name);
        }
        return $this->upload_json('',-1,'');
    }

    function base64imgsave($img, $corp_id)
    {
        //文件夹日期
        $ymd = date("Ymd");
        //图片路径地址
        $basedir = public_path('Uploads/' . $corp_id . '/image/' . $ymd);
        $fullpath = $basedir;
        if (!is_dir($fullpath)) {
            mkdir($fullpath, 0777, true);
        }
        $types = empty($types) ? array('jpg', 'gif', 'png', 'jpeg') : $types;
        $img = str_replace(array('_', '-'), array('/', '+'), $img);
        $b64img = substr($img, 0, 100);
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $b64img, $matches)) {
            $type = $matches[2];
            if (!in_array($type, $types)) {
                return array('status' => 1, 'info' => '图片格式不正确，只支持 jpg、gif、png、jpeg哦！', 'url' => '');
            }
            $img = str_replace($matches[1], '', $img);
            $img = base64_decode($img);
            $photo = '/' . md5(date('YmdHis') . rand(1000, 9999)) . '.' . $type;
            file_put_contents($fullpath . $photo, $img);
            $ary['status'] = 1;
            $ary['info'] = '保存图片成功';
            $ary['url'] = str_replace(str_replace("\\", '/', public_path()), '', str_replace("\\", '/', $basedir . $photo));
            return $ary;
        }
        $ary['status'] = 0;
        $ary['info'] = '请选择要上传的图片';
        return $ary;
    }

    private function upload_json($src, $code = 0,$name="")
    {
        return [
            'code' => $code,
            'src' => $src,
            'name'=>$name
        ];
    }

    public function get_storge($i1,$i2,$i3)
    {
        $path=storage_path('app/aetherupload/'.$i1.'/'.$i2.'/'.$i3);
        return response()->file($path);
    }


    public function image(Request $req)
    {
        $w = $req->input('w',200);
        $h = $req->input('h',200);
        $im = @imagecreate($w, $h);
        imagecolorallocate($im, 211, 211, 211);
        $text_color = imagecolorallocate($im, 128, 128, 128);
        imagestring($im, 5, $w/2-25, $h/2-5, "$w*$h", $text_color);
        ob_start();
        imagepng($im);
        $content = ob_get_contents();
        imagedestroy($im);
        ob_end_clean();
        return $response = response()->make($content)->header('Content-Type', 'image/png');
    }

}
