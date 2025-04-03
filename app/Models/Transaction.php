<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;

    protected $table = 'transactions';
    protected $guarded = [];


    public function getStatusAttribute($status)
    {
        switch ($status) {
            case 0:
                $status = 'ناموفق';
                break;
            case 1:
                $status = 'موفق';
                break;

            default:
                $status = '';
                break;
        }
        return $status;
    }

}
