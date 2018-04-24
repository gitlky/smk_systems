<?php

namespace App\Http\Controllers\smk_systems\Init_Data\Data;

abstract class YuData
{
    abstract protected function get_Data();
    abstract function do_resolution($corp_Id);
}
