<?php
/**
 * Created by lky_command.
 * User: Yu
 * Date: 2017-12-20
 * Time: 01:54:55
 */
namespace App\Model;

class admin_manager extends BaseModel
{
    protected $table = 'admin_manager';
    protected $fillable = ['id','username','userid','password','last_login_ip','corp_id','super_admin','is_default'];

}