<?php

namespace App\Model;

class maillist_department extends BaseModel
{
    protected $table = 'maillist_department';
    protected $fillable = ['id','parentid','enterprise_id','name','order','pid','wxid'];
    public $timestamps = false;

}
