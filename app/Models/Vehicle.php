<?php
/**
 * Created by VeHo.
 * Year: 2022-01-04
 */

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Department;

class Vehicle extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'vehicles';

    protected $fillable = [
        'department_id',
        'driving_classification',
        'tonnage',
        'truck_classification',
        'truck_classification_number',
        'truck_classification_2',
        'manufactor',
        'first_registration',
        'box_distinction',
        'inspection_expiration_date',
        'vehicle_identification_number',
        'owner',
        'etc_certification_number',
        'etc_number',
        'fuel_card_number_1',
        'fuel_card_number_2',
        'driving_recorder',
        'box_shape',
        'mount',
        'refrigerator',
        'eva_type',
        'gate',
        'humidifier',
        'type',
        'motor',
        'displacement',
        'length',
        'width',
        'height',
        'maximum_loading_capacity',
        'vehicle_total_weight',
        'in_box_length',
        'in_box_width',
        'in_box_height',
        'voluntary_insurance',
        'liability_insurance_period',
        'insurance_company',
        'agent',
        'mileage',
        'tire_size',
        'battery_size',
        'monthly_mileage',
        'remark_old_car_1',
        'remark_old_car_2',
        'remark_old_car_3',
        'remark_old_car_4'
    ];

    protected $dates = ['deleted_at'];

    protected $casts = [
        'data' => 'array'
    ];

    public function plate_history() {
        return $this->hasMany('App\Models\PlateHistory', 'vehicle_id', 'id');
    }

    public function mileage_history() {
        return $this->hasMany('App\Models\MileageHistory', 'vehicle_id', 'id');
    }

    public function vehicle_data() {
        return $this->hasMany('App\Models\VehicleData', 'vehicle_id', 'id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    static function checkDepartmentExist($convert) {
        $listConvert = [
            '東京' => '東京',
            '武蔵野' => '武蔵野',
            '横1' => '横浜第一',
            '横2' => '横浜第二',
            '横3' => '横浜第三',
            '平塚' => '平塚',
            '平塚ﾊﾟｽｺ' => '平塚',
            '千葉' => '千葉',
            '八千代' => '八千代',
            '所沢' => '所沢',
            '古河' => '古河',
            '新潟' => '新潟',
            '富山' => '富山',
            '静岡' => '静岡',
            '浜松' => '浜松',
            '名古屋' => '名古屋',
            '幹線便' => '名古屋',
            '安城' => '安城',
            '大阪' => '大阪',
            '神戸' => '神戸',
            '本社' => '本社'
        ];
        if (isset($listConvert[$convert])) {
            $department = Department::where('name', $listConvert[$convert])->first();
            if ($department) return $department->id;
        }
        return 0;
    }
}
