<?php

namespace App\Http\Controllers\smk_systems\Synchronize_mailList;

use App\Http\Controllers\smk_systems\Init_Data\Data\InitData;
use App\Http\Controllers\smk_systems\YuCtrl;
use App\Model\smk_saas_msg_notice;
use App\Model\maillist_department;
use App\Model\maillist_department_url;
use App\Model\maillist_member;
use App\Model\maillist_position;
use App\Model\smk_saas_application_party_url;
use App\Model\smk_saas_application_user_url;
use Cache;
use Log;

class Synchronize_mailList_with_MsgCtrl extends YuCtrl
{
    private $notice_model, $member_model, $department_model, $department_url_model,
        $position_model, $app_user_url_model, $app_party_url_model;

    public function __construct(){
        $this->notice_model = new smk_saas_msg_notice();
        $this->member_model = new maillist_member();
        $this->department_model = new maillist_department();
        $this->department_url_model = new maillist_department_url();
        $this->position_model = new maillist_position();
        $this->app_user_url_model = new smk_saas_application_user_url();
        $this->app_party_url_model = new smk_saas_application_party_url();
    }

    /**
     * 定时获取消息标的数据
     * @param smk_saas_msg_notice $notice_model
     */
    public function get_msg_on_time(){
        $data = $this->notice_model->all()->toArray();
        if (!empty($data)) {
            foreach ($data as $v) {
                //$this->smk_saas_msg_notice->where('smk_id', $v['smk_id'])->where('provider_id', $v['provider_id'])->where('corp_id', $v['corp_id'])->delete();
                if ($v['is_contact'] == 1) {
                    $this->synchronize_mailList($v['corp_id']);
                }else{
                    $this->set_visible_range($v['privilege']);
                }
                $init_data = new InitData();
                $init_data->init_data($v['smk_id'],$v['corp_id']);
                $this->notice_model->where('smk_id', $v['smk_id'])->where('provider_id', $v['provider_id'])->where('corp_id', $v['corp_id'])->delete();
            }
        }
    }

    /**
     * 同步通讯录
     * @param $corp_id
     */
    public function synchronize_mailList($corp_id) {
        $this->synchronize_department($corp_id);
        $this->synchronize_member($corp_id);
    }

    /**
     * 设置应用可见范围
     * @param $app_user_url_model
     * @param $app_party_url_model
     * @param $privilege
     */
    public function set_visible_range( $privilege) {
        if (!empty($privilege)) {
            $privilege = explode('_', $privilege);
            $privilege_cache_key = isset($privilege[0])?$privilege[0]:'';
            $app_id = isset($privilege[1])?$privilege[1]:0;
            if (Cache::has($privilege_cache_key)) {
                $privilege_value = Cache::get($privilege_cache_key);
                if (isset($privilege_value['allow_party'])&&!empty($privilege_value['allow_party'])) {
                    foreach ($privilege_value['allow_party'] as $party_id) {
                        $app_party_data = array(
                            'app_id' => $app_id,
                            'party_id' => $party_id
                        );
                        $this->app_party_url_model->updateOrCreate([
                            'id' => 0
                        ],$app_party_data);
                    }
                }
                if (isset($privilege_value['allow_user'])&&!empty($privilege_value['allow_user'])) {
                    foreach ($privilege_value['allow_user'] as $userid) {
                        $app_user_data = array(
                            'app_id' => $app_id,
                            'userid' => $userid
                        );
                        $this->app_user_url_model->updateOrCreate([
                            'id' => 0
                        ],$app_user_data);
                    }
                }
                Cache::forget($privilege_cache_key);
            }
        }
    }

    /**
     * 同步部门
     * @param $corp_id
     * @param $model
     */
    private function synchronize_department($corp_id){
        $url = 'department_list';
        $system_data= $this->ajax_decode($url, ['corp_id'=>$corp_id]);
        if (isset($system_data->code) && $system_data->code==0) {
            $wx_data = isset($system_data->data)?$system_data->data:[];
            if ($wx_data) {
                foreach ($wx_data as $k=>$v) {
                    if ( isset($v->parentid) && $v->parentid == 0 ) {
                        $this->recursive_department($corp_id, $wx_data);
                        break;
                    }
                }
            }
        }
    }

    /**
     * 递归写入部门
     * @param $model
     * @param $corp_id
     * @param $wx_data
     * @param int $wx_pid
     * @param int $local_pid
     */
    private function recursive_department($corp_id, $wx_data, $wx_pid = 0, $local_pid = 0){
        if (!empty($wx_data)) {
            foreach ($wx_data as $k=>$v) {
                if ($v->parentid == $wx_pid) {
                    $wx_id = isset($v->id) ? $v->id : 0;
                    $insert_data = array(
                        'enterprise_id'=>$corp_id,
                        'name'=>isset($v->name)?$v->name:'',
                        'order'=>$this->get_department_order($local_pid),
                        'pid'=>$wx_pid,
                        'wxid'=>$wx_id,
                        'parentid'=>$local_pid,
                    );
                    $department_data = $this->department_model->updateOrCreate([
                        'pid' => $wx_pid,
                        'enterprise_id' => $corp_id,
                    ],$insert_data);
                    $department_id = $department_data->id;
                    unset($wx_data[$k]);
                    $this->recursive_department($corp_id, $wx_data, $wx_id, $department_id);
                }
            }
        }

    }

    /**
     * 同步成员
     * @param $corp_id
     * @param $model
     * @param $url_model
     */
    private function synchronize_member($corp_id){
        $r = $this->department_model->where('pid', 0)->where('enterprise_id', $corp_id)->first();
        if (!empty($r)) {
            $url = 'get_department_all_member_detail';
            $pam = array(
                'dep_id' => $r['wxid'],
                'fetch_child' => 1,
                'corp_id' => $corp_id,
            );
            #Log::info(json_encode($pam));
            $system_data = $this->ajax_decode($url, $pam);
            #Log::info(json_encode($system_data));
            if (isset($system_data->code) && $system_data->code==0) {
                $data = $system_data->data;
                foreach ($data as $v) {
                    $user_id = isset($v->userid)?$v->userid:'';
                    $member_insert_data = array(
                        'userid'=>$user_id,
                        'name'=>isset($v->name)?$v->name:'',
                        'position'=>isset($v->position)?$this->position_id($v->position, $corp_id):0,
                        'gender'=>isset($v->gender)?$v->gender:'',
                        'mobile'=>isset($v->mobile)?$v->mobile:'',
                        'wxemail'=>isset($v->email)?$v->email:'',
                        'avatar'=>isset($v->avatar)?$v->avatar:'',
                        'status'=>isset($v->status)?$v->status:'',
                        'enterprise_id'=>$corp_id
                    );
                    $department_data = $this->member_model->updateOrCreate([
                        'userid' => $user_id,
                        'enterprise_id' => $corp_id,
                    ],$member_insert_data);
                    $member_id = $department_data->id;
                    if (isset($v->department) && !empty($v->department)) {
                        $this->department_url_model->where('enterprise_id', $corp_id)->where('member_id',$member_id)->delete();
                        foreach ($v->department as $dep_id) {
                            $r = $this->department_model->where('enterprise_id', $corp_id)->where('wxid',$dep_id)->first();
                            if ($r['id']) {
                                $mem_dep_url_data = array(
                                    'member_id' => $member_id,
                                    'department_id' => $r['id'],
                                    'enterprise_id' => $corp_id,
                                );
                                $this->department_url_model->insert($mem_dep_url_data);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * 设置本地部门排序
     * @param $model
     * @param $pid
     * @return int
     */
    private function get_department_order($pid){
        if ($pid == 0) {
            $order = 0;
        }else{
            $order = $this->department_model->where('parentid', $pid)->max('order');
            $order = empty($order) ? 1000 : $order+1000;
        }
        return $order;
    }

    /**
     * 获取职位ID
     * @param $position_name
     * @param $corp_id
     * @return int|mixed
     */
    private function position_id($position_name, $corp_id){
        if (empty($position_name)) {
            $position_id = 0;
        }else{
            $position = $this->position_model->where('name', $position_name)->where('enterprise_id', $corp_id)->first();
            if (empty($position)) {
                $insert_data = array(
                    'name' => $position_name,
                    'enterprise_id' => $corp_id
                );
                $position_data = $this->position_model->updateOrCreate([
                    'id' => 0
                ],$insert_data);
                $position_id = $position_data->id;
            }else{
                $position_id = $position['id'];
            }
        }

        return $position_id;
    }
}
