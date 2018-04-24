<?php
/**
 * Created by lky_command.
 * User: Yu
 * Date: 2017-12-20
 * Time: 06:31:19
 */
namespace App\Model;

class smk_saas_applicatioin extends BaseModel
{
    protected $table = 'smk_saas_applicatioin';
    public $timestamps = false;
    protected $fillable = ['id','smk_id','provider_id','corp_id','suite_id'];

}