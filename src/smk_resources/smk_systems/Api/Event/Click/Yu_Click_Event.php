<?php

namespace App\Http\Controllers\smk_systems\Api\Event\Click;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

abstract class  Yu_Click_Event
{
    protected $user,$corp,$suite;
    abstract function __construct($return_corp,$return_user,$return_suite);
    abstract public function hand($corp_id,$user_id,$smk_id,$event_key,$key_value);
}
