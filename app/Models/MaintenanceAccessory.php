<?php
/**
 * Created by VeHo.
 * Year: 2022-01-28
 */

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceAccessory extends Model
{
    use HasFactory;

    protected $table = 'maintenance_accessories';

    const ID = 'id';
    const MAINTENANCE_COST_ID = 'maintenance_cost_id';
    const ACCESSORY_ID = 'accessory_id';
    const NAME = 'name';
    const QUANTITY = 'quantity';
    const PRICE = 'price';

    protected $fillable = [
        self::MAINTENANCE_COST_ID,
        self::ACCESSORY_ID,
        self::NAME,
        self::QUANTITY,
        self::PRICE
    ];

    public function MaintenanceCost()
    {
        return $this->hasOne(MaintenanceCost::class, MaintenanceCost::ID, self::MAINTENANCE_COST_ID);
    }

    protected $dates = ['deleted_at'];

    protected $casts = [
        'data' => 'array'
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
