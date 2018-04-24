<?php

namespace App\Http\Controllers\smk_systems\Api\Saas;

use App\Http\Controllers\smk_systems\Init_Data\DataTool;
use App\Http\Controllers\smk_systems\YuCtrl;
use App\Model\saas_all_app;
use App\Model\smk_saas_applicatioin;
use App\Model\smk_saas_msg_notice;
use Illuminate\Http\Request;
use Cache;
use Log;

class SaasApiCtrl extends YuCtrl
{
    /**
     * 接入应用
     * @param Request $request
     * @param smk_saas_applicatioin $join_app_model
     * @param smk_saas_msg_notice $notice_model
     * @return \Illuminate\Http\JsonResponse
     */
    public function join_app(Request $request, smk_saas_applicatioin $join_app_model, smk_saas_msg_notice $notice_model ){
        $privilege_key = time().uniqid();
        $privilege_value = $request->input('privilege');
        $is_contact = $request->input('is_contact');
        $smk_id = $request->input('smk_id');
        $provider_id = $request->input('provider_id');
        $corp_id = $request->input('corp_id');
        $suite_id = $request->input('suite_id');
        $insert_data = array(
            'smk_id'=> $smk_id,
            'provider_id'=> $provider_id,
            'corp_id'=> $corp_id,
            'suite_id'=> $suite_id,
        );
        #Log::info($insert_data);
        /*if ($is_contact != 1) {

        }*/
        Cache::rememberForever($privilege_key, function() use ($privilege_value){
            return $privilege_value;
        });
        $app_data = $join_app_model->updateOrCreate([
            'smk_id' => $smk_id,
            'provider_id' => $provider_id,
            'corp_id' => $corp_id,
        ],$insert_data);

        $app_id = $app_data->id;
        $insert_data['privilege'] = $privilege_key.'_'.$app_id;
        $insert_data['is_contact'] = $is_contact;
        $notice_model->updateOrCreate([
            'smk_id' => 0
        ],$insert_data);
        return $this->see_json();
    }

    /**
     * 取消授权，删除全部已经接入的app
     * @param Request $request
     * @param smk_saas_applicatioin $app_model
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancel_auth(Request $request, smk_saas_applicatioin $app_model){
        $corp_id = $request->input('corp_id', null);
        $suite_id = $request->input('suite_id', null);
        if (empty($corp_id) || empty($suite_id)) {
            return $this->see_json(null, -1, '参数错误,需要传递smk_id和suite_name两个参数');
        }else{
            $app_model->where('corp_id', $corp_id)->where('suite_id', $suite_id)->delete();
            return $this->see_json();
        }
    }

    /**
     * 打印查看应用列表，测试用
     * @param Request $request
     * @param saas_all_app $all_app
     */
    public function app_list(saas_all_app $all_app){
        $url = 'qy_wx_get_all_app_detail';
        $system_data = $this->ajax_decode($url);
        #dump($system_data);
        if (isset($system_data->code) && $system_data->code == 0) {
            $data = $system_data->data;
            foreach ($data as $app) {
                $all_app->updateOrCreate([
                    'smk_id' => $app->id
                ], [
                    'smk_id' => $app->id,
                    'suite_name' => $app->suite_name
                ]);
            }

        }
    }

    /**
     * 添加应用
     * @param Request $request
     * @param saas_all_app $all_app
     * @return \Illuminate\Http\JsonResponse
     */
    public function add_app(Request $request, saas_all_app $all_app){
        if ($this->do_edit($request, $all_app)) {
            return $this->see_json();
        }else{
            return $this->see_json(null, -1, '参数错误,需要传递smk_id和suite_name两个参数');
        }
    }

    /**
     * 编辑应用
     * @param Request $request
     * @param saas_all_app $all_app
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit_app(Request $request, saas_all_app $all_app){
        if ($this->do_edit($request, $all_app)) {
            return $this->see_json();
        }else{
            return $this->see_json(null, -1, '参数错误,需要传递smk_id和suite_name两个参数');
        }
    }

    /**
     * 删除应用
     * @param Request $request
     * @param saas_all_app $all_app
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete_app(Request $request, saas_all_app $all_app){
        $smk_id = $request->input('smk_id');

        if (empty($smk_id)) {
            return $this->see_json(null, -1, '参数错误，需要传递smk_id一个参数');
        }else{
            $all_app->where('smk_id', $smk_id)->delete();
            $this->write();
            return $this->see_json();
        }
    }

    /**
     * 操作逻辑
     * @param $request
     * @param $model
     * @return bool
     */
    private function do_edit($request, $model){
        $smk_id  = $request->input('smk_id');
        $suite_name = $request->input('suite_name');
        if (empty($smk_id) || empty($suite_name)) {
            return false;
        }else{
            $model->updateOrCreate([
                'smk_id' => $smk_id
            ],[
                'smk_id' => $smk_id,
                'suite_name' => $suite_name,
            ]);
            $this->write();
            return true;
        }
    }

    private function write(){
        $a = new DataTool();
        $model = new saas_all_app();
        $data = $model->get()->toArray();
        $a->synData($data,$model->getTable());
    }
}
