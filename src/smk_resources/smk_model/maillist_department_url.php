<?php

namespace App\Model;

class maillist_department_url extends BaseModel
{
    protected $table = 'maillist_department_url';
    protected $fillable = ['id','member_id','department_id','enterprise_id'];

    public $timestamps = false;
}
