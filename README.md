# smk_system

smk微信企业号系统迁移,要求laravel版本最低:5.3

**Note:** 

   必须在项目正式开发之前的架构阶段，以免冲突覆盖。每个项目有所不同，架构者做相应的修改



## 使用

**1.引入system包** 

    composer require smk_vendor/smk_systems dev-master;

**2.在config/app.php的provider数组中添加:**
 
    smk_vendor\smk_package\smk_packageServiceProvider::class,

**3.在config/app.php的aliases数组中添加:**
 
    'cURL' => anlutro\cURL\Laravel\cURL::class,
 
**4.执行以下命令配**
 
    php artisan vendor:publish --provider="smk_vendor\smk_package\smk_packageServiceProvider";
    php artisan wx:migrate

**注意事项:**
   
   1. 根据自己项目引入一些其他的必须的扩展包，比如：
  
    composer require guzzlehttp/guzzle
  
   2. 必须在app/Http/Kernel.php的$routeMiddleware数组中注册中间件，并且要在所有微信的路由中使用 
    
    'wxLogin'=> \App\Http\Middleware\WxLogin::class，
    
   3. 必须在app/Console/Kernel.php的schedule方法里面添加下面代码，并在服务器中配置定时脚本执行他
     
     try {
        $schedule->call(function () {
            $model = new Synchronize_mailList_with_MsgCtrl();
            $model->get_msg_on_time();
        })->everyMinute();
     } catch (\Exception $e) {
        Log::info('定时执行1错误');
     }

   4. 必须迁移Model下面对应的数据库
   
   5. config/qy_cfg.php的provider_id和url修改成自己项目对应的，中间件WxLogin中有调试信息，根据实际情况修改
