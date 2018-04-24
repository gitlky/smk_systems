<?php


namespace smk_vendor\smk_package\smk_command;

use Illuminate\Console\Command;
use File;

class MigrateWx extends Command
{
    protected $signature = 'wx:migrate';

    public function handle(){
        $version = $this->getApplication()->getVersion();

        if ($version < 5.3) {
            $this->error("your laravel version is less than 5.3,please upgrade");
        }else{

            $this->move_route();
            $this->move_middleware();

            $this->move_controller();
            $this->move_model();

            $this->line("successful");

        }
    }

    public function move_model(){
        $sources = dirname(dirname(__FILE__)) . '/smk_resources/smk_model';
        $destination_path = app_path('Model');
        File::copyDirectory($sources, $destination_path);
    }

    private function move_controller(){
        $sources = dirname(dirname(__FILE__)) . '/smk_resources/smk_systems';
        $destination_path = app_path('Http/Controllers/smk_systems');
        File::copyDirectory($sources, $destination_path);
    }

    private function move_middleware(){
        $this->copy_file('smk_middleware/WxLogin.php', app_path('Http/Middleware'), 'WxLogin.php');
    }

    private function move_route(){
        $route_path = base_path('routes/web.php');

        $route_str = "require_once 'smk_systems/smk_system.php';";

        $this->write($route_path, $route_str, true);

        $this->copy_file('smk_routes/smk_systems/smk_system.php', base_path('routes/smk_systems'), 'smk_system.php');
    }

    private function copy_file($file_path, $destination_path, $file_name){
        $my_dir = dirname(dirname(__FILE__)) . '/smk_resources/';

        if (!File::isDirectory($destination_path) || !File::exists($destination_path)) {
            File::makeDirectory($destination_path, $mode = 0755, $recursive = false);
        }
        File::copy($my_dir.$file_path, $destination_path.'/'.$file_name);
    }

    private function write($path, $content, $is_append = false)
    {
        if (!File::exists($path)) {
            $this->line("文件不存在");
            return;
        }
        if ($is_append) {
            File::append($path, $content);
        } else {
            File::put($path, $content);
        }
    }
}