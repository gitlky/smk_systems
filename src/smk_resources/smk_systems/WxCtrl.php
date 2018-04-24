<?php

namespace App\Http\Controllers\smk_systems;

use App\Http\Controllers\smk_systems\Services\Wx_Service;
use App\Model\admin_manager;
use App\Model\maillist_department;
use App\Model\maillist_member;
use App\Model\part_politicaloutlook;
use App\Model\part_politicaloutlook_member_uri;
use Cache;
use File;
use Request;
use Session;

class WxCtrl extends Wx_Service
{

    const view_path = "wx.";

    /***
    微信端的返回界面
     **/
    public function see_view($path, $data = [], $bk_json = false)
    {
        return $bk_json ? response()->json($data) : view($path)->with($data);
    }

    /***
    微信端的分页
     **/
    public function size()
    {
        return 1;
    }

    /***
    将微信端选择人员的ID转换为本地数据库的ID
     **/
    public function get_member($member)
    {
        $member = json_decode($member);
        $dep = isset($member->dep) ? $member->dep : null;
        $member = isset($member->member) ? $member->member : null;
        $dep_array = collect();
        if (is_array($dep) && count($dep) > 0) {
            $dep_model = new maillist_department();
            $dep_model=$dep_model->where('enterprise_id', $this->corp_id())->get();
            if (!empty($dep_model)) {
                $dep_model = $dep_model->toArray();
                $m1 = collect($dep_model);
                $m1->each(function ($item) use ($dep, $dep_array) {
                    foreach ($dep as $d) {
                        if ($item['wxid'] == $d->id) {
                            $dep_array->push($item['id']);
                        }
                    }
                });
            }
        }
        $member_array = collect();
        if (is_array($member) && count($member) > 0) {
            $member_model = new maillist_member();
            $member_model = $member_model->where('enterprise_id', $this->corp_id())->get();
            if (!empty($member_model)) {
                $member_model = $member_model->toArray();
                $m1 = collect($member_model);
                $m1->each(function ($item) use ($member, $member_array) {
                    foreach ($member as $m) {
                        if ($item['userid'] == $m->id) {
                            $member_array->push($item['id']);
                        }
                    }
                });
            }
        }
        return array(
            'member' => $member_array->toArray(),
            'dep' => $dep_array->toArray()
        );
    }

    /***
    微信端抛出异常的界面
     ***/
    public function see_err($msg = "错误", $title = "提示")
    {
        $pam = array(
            'title' => $title,
            'content' => $msg,
            'success' => true,
        );
        return $this->see_view('wx.err', $pam);
    }

    /***
    将微信端的表情转换为可以显示出来的图片
     ***/
    public function face2html($face,$tp=null)
    {
        if($face=="图片"){
            return asset($tp);
        }
        $facemap = [
            'weixiao,微笑',
            'piezui,撇嘴',
            'se,色',
            'fadai,发呆',
            'deyi,得意',
            'liulei,流泪',
            'haixiu,害羞',
            'bizui,闭嘴',
            'shui,睡',
            'daku,大哭',
            'ganga,尴尬',
            'fanu,发怒',
            'tiaopi,调皮',
            'ciya,呲牙',
            'jingya,惊讶',
            'nanguo,难过',
            'ku,酷',
            'lenghan,冷汗',
            'zhuakuang,抓狂',
            'tu,吐',
            'touxiao,偷笑',
            'keai,可爱',
            'baiyan,白眼',
            'aoman,-傲慢',
            'jie,饥饿',
            'kun,困',
            'jingkong,惊恐',
            'liuhan,流汗',
            'hanxiao,憨笑',
            'dabing,大兵',
            'fendou,奋斗',
            'zhouma,咒骂',
            'yiwen,疑问',
            'xu,嘘',
            'yun,晕',
            'zhemo,折磨',
            'shuai,衰',
            'kulou,骷髅',
            'qiaoda,敲打',
            'zaijian,再见',
            'cahan,擦汗',
            'koubi,抠鼻',
            'guzhang,鼓掌',
            'qiudale,糗大了',
            'huaixiao,坏笑',
            'zuohengheng,左哼哼',
            'youhengheng,右哼哼',
            'haqian,哈欠',
            'bishi,鄙视',
            'weiqu,委屈',
            'kuaikule,快哭了',
            'yinxian,阴险',
            'qinqin,亲亲',
            'xia,吓',
            'kelian,可怜',
            'caidao,菜刀',
            'xigua,西瓜',
            'pijiu,啤酒',
            'lanqiu,篮球',
            'pingpang,乒乓',
            'kafei,咖啡',
            'fan,饭',
            'zhutou,猪头',
            'meigui,玫瑰',
            'diaoxie,凋谢',
            'shiai,示爱',
            'aixin,爱心',
            'xinsui,心碎',
            'dangao,蛋糕',
            'shandian,闪电',
            'zhadan,炸弹',
            'dao,刀',
            'zuqiu,足球',
            'piaochong,瓢虫',
            'bianbian,便便',
            'yueliang,月亮',
            'taiyang,太阳',
            'liwu,礼物',
            'yongbao,拥抱',
            'qiang,强',
            'ruo,弱',
            'woshou,握手',
            'shengli,胜利',
            'baoquan,抱拳',
            'gouyin,勾引',
            'quantou,拳头',
            'chajin,差劲',
            'aini,爱你',
            'no,NO',
            'ok,OK'
        ];
        foreach ($facemap as $f) {
            $fx = explode(',', $f);
            if ($fx[1] == $face) {
                return asset('Wx/Images/face/'.$fx[0].'.gif');
            }
        }
    }


    public function is_gcd()
    {
        $userid = $this->user_id();
        $member_model = new maillist_member();
        $member_rela = new part_politicaloutlook_member_uri();
        $zzmm = new part_politicaloutlook();
        $t1 = $member_model->getTable();
        $t2 = $member_rela->getTable();
        $t3 = $zzmm->getTable();
        $m = $member_model
            ->select($t1.'.id')
            ->leftJoin($t2,$t2.'.member_id','=',$t1.'.id')
            ->leftJoin($t3,$t2.'.Politics_id','=',$t3.'.id')
            ->where($t1.'.enterprise_id','=',$this->corp_id())
            ->where($t3.'.state','=','2')
            ->where($t1.'.id','=',$userid)
            ->groupBy($t1.'.id')
            ->count();
        if($m>0){
            return true;
        }else{
            return false;
        }
    }

    public function is_admin($user_id = null, $user_wx_id = null){
        $manager_model = new admin_manager();
        if ($user_id) {
            $manager = $manager_model->where('userid', $user_id)->corp_data('corp_id')->first();

        }elseif($user_wx_id){
            $manager = $manager_model->where('username', $user_id)->corp_data('corp_id')->first();

        }else{
            $manager = $manager_model->where('userid', $this->user_id())->corp_data('corp_id')->first();
        }
        return $manager;
    }

}
