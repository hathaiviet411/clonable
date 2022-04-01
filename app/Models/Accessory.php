<?php
/**
 * Created by VeHo.
 * Year: 2022-01-04
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DateTimeInterface;
use Illuminate\Support\Arr;

class Accessory extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'accessories';

    protected $fillable = [
        'name',
        'tonnage',
        'passed_year',
        'mileage',
        'remark',
        "created_by",
        "updated_by",
    ];

    protected $dates = ['deleted_at'];

    protected $appends = array('lock_name', 'lock_tonnage');

    public function getLockNameAttribute()
    {
        if (Arr::has($this->attributes, 'name')) {
            $name = trim(Arr::get($this->attributes, 'name'));
            if (in_array($name, ['エンジンオイル', 'オイルエレメント'])) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function getLockTonnageAttribute()
    {
        if (Arr::has($this->attributes, 'tonnage')) {
            $id = Arr::get($this->attributes, 'id');
            $mtAccessoryCk = MaintenanceAccessory::select('maintenance_accessories.accessory_id')
                ->leftJoin('maintenance_costs', 'maintenance_costs.id', '=', 'maintenance_accessories.maintenance_cost_id')
                ->where('maintenance_accessories.accessory_id', $id)->first();
            if ($mtAccessoryCk) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    protected $casts = [
        'data' => 'array'
    ];

    protected $hidden = ['pivot'];

//    public function getMileageAttribute()
//    {
//        return number_format($this->attributes['mileage']);
//    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
