<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Logs extends Model
{
    use HasFactory;
    protected $table = 'logs';

    protected $fillable = [
        'action',
        'user_name',
        'action_time',
        'payload',
        'ip_address',
        'location',

    ];
}
