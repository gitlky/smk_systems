<?php
/**
 * Created by lky_command.
 * User: Yu
 * Date: 2018-01-04
 * Time: 09:36:25
 */
namespace App\Model;

class part_politicaloutlook_member_uri extends BaseModel
{
    protected $table = 'part_politicaloutlook_member_uri';

    protected $fillable = ['id', 'member_id', 'Politics_id', 'del','corp_id'];
    public $timestamps = false;

}