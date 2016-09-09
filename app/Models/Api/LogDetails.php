<?php namespace App\Models\Api;

use Jenssegers\Mongodb\Eloquent\Model as Moloquent;

class LogDetails extends Moloquent
{
    protected $connection = 'mongodb';

    protected $guarded = [];

    public function delLogs($channel)
    {
        //TODO:删除
        return true;
    }
}
