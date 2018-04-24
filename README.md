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
  
   2. 
