<?php

/*
|--------------------------------------------------------------------------
| 所有系统级别的路由
|--------------------------------------------------------------------------
*/

Route::group(['namespace' => 'smk_systems'], function (){
    //和system交互的接口
    Route::group(['namespace' => 'Api\Saas', 'prefix'=>'api/saas_api'], function (){
        //接入应用
        Route::any('join_app','SaasApiCtrl@join_app');
        //取消授权
        Route::any('cancel_auth','SaasApiCtrl@cancel_auth');
        //添加应用
        Route::any('add_app','SaasApiCtrl@add_app');
        //编辑应用
        Route::any('edit_app','SaasApiCtrl@edit_app');
        //删除应用
        Route::any('delete_app','SaasApiCtrl@delete_app');
        //获取应用列表（暂时用来测试）
        Route::any('app_list','SaasApiCtrl@app_list');

    });

    //微信登陆
    Route::group(['namespace' => 'Login','prefix' => 'wx'], function (){
        Route::any('login','WxLoginCtrl@wx_login')->name('wx_login');
    });

    //jssdk
    Route::group(['namespace'=>'JS_SDK'],function (){
        Route::any('wxjs.js','YuWxCtrl@Wx')->name('wxjs');
    });

    //支付
    Route::group(['namespace' => 'Api\Pay'], function () {
        Route::any('do_pay', 'YPay@index')->name('do_pay');
    });
});
