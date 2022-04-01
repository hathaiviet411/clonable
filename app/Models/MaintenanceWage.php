<?php
/**
 * Created by VeHo.
 * Year: 2022-01-28
 */

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceWage extends Model
{
    use HasFactory;

    protected $table = 'maintenance_wages';

    const ID = 'id';
    const MAINTENANCE_COST_ID = 'maintenance_cost_id';
    const WORK_CONTENT = 'work_content';
    const WAGES = 'wages';

    protected $fillable = [
        self::MAINTENANCE_COST_ID,
        self::WORK_CONTENT,
        self::WAGES,
    ];

    protected $dates = ['deleted_at'];

    protected $casts = [
        'data' => 'array'
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
