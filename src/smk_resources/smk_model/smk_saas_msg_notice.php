<?php

namespace App\Model;

//use Illuminate\Database\Eloquent\Model;

class smk_saas_msg_notice extends BaseModel
{
    protected $table = 'smk_saas_msg_notice';
    public $timestamps = false;
    protected $fillable = ['smk_id','provider_id','is_contact','privilege', 'suite_id','corp_id'];

}
