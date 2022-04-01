<?php
/**
 * Created by VeHo.
 * Year: 2022-01-28
 */

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedule extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'schedules';

    const VEHICLE_ID = 'vehicle_id';
    const MONTH = 'month';
    const TYPE = 'type';
    const LIST_ACCESSORY = 'list_accessory';

    protected $fillable = [
        self::VEHICLE_ID,
        self::MONTH,
        self::TYPE,
        self::LIST_ACCESSORY,
    ];

    protected $dates = ['deleted_at'];

    protected $casts = [
        'data' => 'array',
        'list_accessory' => 'array',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
