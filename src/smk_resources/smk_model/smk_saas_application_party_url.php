<?php
/**
 * Created by lky_command.
 * User: Yu
 * Date: 2017-12-20
 * Time: 06:56:57
 */
namespace App\Model;

class smk_saas_application_party_url extends BaseModel
{
    protected $table = 'smk_saas_application_party_url';
    public $timestamps = false;
    protected $fillable = ['id','app_id','party_id'];

}