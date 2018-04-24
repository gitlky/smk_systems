<?php

namespace App\Model;

class maillist_position extends BaseModel
{
    protected $table = 'maillist_position';
    protected $fillable = ['id','enterprise_id','name','describe','whether'];
    public $timestamps = false;
}
