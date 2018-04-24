<?php

namespace App\Model;

class maillist_member extends BaseModel
{
    protected $table = 'maillist_member';
    protected $fillable = ['id','userid','name','position','gender','mobile','wxemail', 'is_leader', 'avatar', 'status','enterprise_id','del'];
    public $timestamps = false;

}
