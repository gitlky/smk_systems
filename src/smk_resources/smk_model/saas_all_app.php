<?php
/**
 * Created by lky_command.
 * User: Yu
 * Date: 2017-12-28
 * Time: 11:39:56
 */
namespace App\Model;

class saas_all_app extends BaseModel
{
    protected $table = 'saas_all_app';
    protected $primaryKey = "smk_id";
    protected $fillable = ['smk_id','suite_name'];

}