<?php

namespace App\Http\Controllers\smk_systems\Init_Data;

use App\Http\Controllers\Controller;
use File;

class DataTool extends Controller
{
    public function synData(array $pam,$db)
    {
        $str = "";
        if(count($pam)<1){
            return;
        }
        foreach ($pam as $p){
            $str.='array(';
            foreach ($p as $k=>$v){
                $str.="'$k'=>'$v',";
            }
            $str.='),
            ';
        }
        $p = "<?php
use Illuminate\\Database\\Seeder;
use Illuminate\\Support\\Facades\\DB;
class $db extends Seeder{
     public function run()
    {
        \$pam = [
            $str
        ];
        DB::table('$db')->insert(\$pam);
    }
 }";
        $file = database_path('seeds/'.$db.'.php');
        File::put($file,$p);
    }
}
