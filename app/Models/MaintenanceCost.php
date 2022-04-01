<?php
/**
 * Created by VeHo.
 * Year: 2022-01-04
 */

namespace App\Models;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;

class MaintenanceCost extends Model
{
    use HasFactory;
    use SoftDeletes;

    const ID = 'id';
    const TYPE = 'type';
    const TYPE_TEXT = 'type_text';
    const CHARGE_TYPE = 'charge_type';
    const SCHEDULED_DATE = 'scheduled_date';
    const SCHEDULED_DATE_DISPLAY = 'scheduled_date_display';
    const SCHEDULE_MONTH = 'schedule_month';
    const SCHEDULE_YEAR = 'schedule_year';
    const MAINTAINED_DATE = 'maintained_date';
    const MAINTAINED_DATE_DISPLAY = 'maintained_date_display';
    const VEHICLE_ID = 'vehicle_id';
    const NO_NUMBER_PLATE = 'no_number_plate';
    const MILEAGE_LAST_TIME = 'mileage_last_time';
    const MILEAGE_CURRENT = 'mileage_current';
    const TOTAL_AMOUNT_EXCLUDING_TAX = 'total_amount_excluding_tax';
    const DISCOUNT = 'discount';
    const TOTAL_AMOUNT_INCLUDING_TAX = 'total_amount_including_tax';
    const NOTE = 'note';
    const STATUS = 'status';
    const STATUS_TEXT = 'status_text';
    const CREATED_BY = 'created_by';
    const UPDATED_BY = 'updated_by';

    protected $table = 'maintenance_costs';

    protected $appends = array(self::STATUS_TEXT, self::TYPE_TEXT, self::SCHEDULED_DATE_DISPLAY, self::MAINTAINED_DATE_DISPLAY);

    protected $fillable = [
        self::TYPE,
        self::CHARGE_TYPE,
        self::SCHEDULED_DATE,
        self::SCHEDULE_MONTH,
        self::SCHEDULE_YEAR,
        self::MAINTAINED_DATE,
        self::VEHICLE_ID,
        self::MILEAGE_LAST_TIME,
        self::MILEAGE_CURRENT,
        self::TOTAL_AMOUNT_EXCLUDING_TAX,
        self::DISCOUNT,
        self::TOTAL_AMOUNT_INCLUDING_TAX,
        self::NOTE,
        self::STATUS,
    ];

    protected $dates = ['deleted_at'];

    protected $casts = [
        'data' => 'array'
    ];

    public function getMaintainedDateDisplayAttribute()
    {
        if (Arr::get($this->attributes, 'maintained_date')) {
            if ($this->type == TYPE_ACCESSORY_CHANGE) {
                return Carbon::parse(Arr::get($this->attributes, 'maintained_date'))->format('Y-m');
            } else {
                return Arr::get($this->attributes, 'maintained_date');
            }
        } else {
            return '';
        }
    }


    public function getScheduledDateDisplayAttribute()
    {
        if (Arr::get($this->attributes, 'scheduled_date')) {
            if ($this->type == TYPE_ACCESSORY_CHANGE) {
                return Carbon::parse(Arr::get($this->attributes, 'scheduled_date'))->format('Y-m');
            } else {
                return Arr::get($this->attributes, 'scheduled_date');
            }
        } else {
            return '';
        }
    }

    protected $hidden = ['pivot', 'vehicle'];

    //implement the attribute
    public function getStatusTextAttribute()
    {
        if ($this->status) {
            return Arr::get(LIST_STATUS, $this->status);
        }
        return '';
    }

    public function getTypeTextAttribute()
    {
        if ($this->type) {
            return Arr::get(LIST_TYPE, $this->type);
        }
        return '';
    }

    public function maintenance_accessories() {
        return $this->hasMany('App\Models\MaintenanceAccessory', 'maintenance_cost_id', 'id');
    }

    public function wage() {
        return $this->hasMany('App\Models\MaintenanceWage', 'maintenance_cost_id', 'id');
    }

    public function vehicle() {
        return $this->hasOne('App\Models\Vehicle', 'id', 'vehicle_id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
