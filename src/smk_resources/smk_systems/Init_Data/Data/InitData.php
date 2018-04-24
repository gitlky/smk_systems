<?php

namespace App\Http\Controllers\smk_systems\Init_Data\Data;
use Log;

class InitData
{
    private $corp_id;
    private $smk_id;
    public function init_data($smk_id,$corp_id)
    {
        $this->smk_id = $smk_id;
        $this->corp_id = $corp_id;
        $r = null;
        switch ($smk_id){
            /*case 1:
                break;
            case 2:
                break;
            case 4:
                $r = new DangJianData();
                break;*/
        }
        if(null!=$r){
            //$r->do_resolution($corp_id);
        }
        Log::info("初始化数据完成");
    }

}
