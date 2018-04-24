<?php
/**
 * Created by lky_command.
 * User: Yu
 * Date: 2017-12-26
 * Time: 09:27:29
 */
namespace App\Model;

class part_politicaloutlook extends BaseModel
{
    protected $table = 'part_politicaloutlook';
    public $timestamps = false;
    protected $fillable = ['id','enterprise_id','name','corp_id'];

}