<?php
/**
 * Created by VeHo.
 * Year: 2022-01-28
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScheduleAccessory extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'schedule_accessories';

    protected $fillable = [
        'schedule_id',
        'accessory_id',
        'status',
    ];

    protected $dates = ['deleted_at'];

    protected $casts = [
        'data' => 'array'
    ];

}
