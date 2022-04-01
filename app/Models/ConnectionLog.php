<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class ConnectionLog extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "connections_logs";
    protected $fillable = [
        'from',
        'call_to_api',
        'status',
        'file_size',
        'file_path',
        'contents'
    ];

    protected $dates = ['deleted_at'];

    protected $casts = [
        'data' => 'array'
    ];

}
