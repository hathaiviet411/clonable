<?php
/**
 * Created by VeHo.
 * Year: 2022-01-04
 */

namespace Repository;

use App\Models\MaintenanceSchedule;
use App\Repositories\Contracts\MaintenanceScheduleRepositoryInterface;
use Repository\BaseRepository;
use Illuminate\Foundation\Application;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;

class MaintenanceScheduleRepository extends BaseRepository implements MaintenanceScheduleRepositoryInterface
{

    private $vehicleReposiroty;
    private $maintenanceCost;
     public function __construct(Application $app)
     {
        parent::__construct($app);
        $this->vehicleReposiroty = new VehicleRepository($app);
        $this->maintenanceCost = new MaintenanceCostRepository($app);
     }

    /**
       * Instantiate model
       *
       * @param MaintenanceSchedule $model
       */

    public function model()
    {
        return MaintenanceSchedule::class;
    }

    public function loadSchedule(int $year, int $department, string $numberOfPlate = null) {
        $startAccountingYear = $year . "-04-01";
        $endAccountingYear = ($year + 1) . "-03-31";
        $vehicles = $this->vehicleReposiroty->model->where('department_id', $department)
        ->with([
            'plate_history' => function ($query) {
                $query->orderBy('date', 'DESC')->select(['*']);
            }
        ]);
        if ($numberOfPlate) {
            $vehicles = $vehicles->whereHas('plate_history', function ($query) use ($numberOfPlate) {
                $query->where('no_number_plate', $numberOfPlate);
            });
        }
        $vehicles = $vehicles->get(['*'])->keyBy('id')->toArray();
        $vehiclesIds = [];
        foreach ($vehicles as $key => $vehicle) {
            $vehiclesIds[] = $vehicle['id'];
        }
        $maintenanceCost = $this->maintenanceCost->model->whereBetween('scheduled_date', [$startAccountingYear, $endAccountingYear])
        ->whereIn('type', [1, 2])
        ->whereIn('vehicle_id',  $vehiclesIds)
        ->get(['*']);
        $result = [];
        foreach ($maintenanceCost as $key => $cost) {
            $numberPlate = $vehicles[$cost->vehicle_id]['plate_history'][0]['no_number_plate'];
            if (!isset($result[$numberPlate])) {
                $result[$numberPlate] = [];
            }
            $result[$numberPlate][] = [
                "color" => $this->checkColor($vehicles[$cost->vehicle_id]['inspection_expiration_date'], $cost->scheduled_date),
                "date" => $cost->scheduled_date,
                "expiration_date" => $vehicles[$cost->vehicle_id]['inspection_expiration_date'],
                "first_register" => $vehicles[$cost->vehicle_id]['first_registration'],
                "no_number_plate" => $numberPlate,
                "result" => $cost->maintained_date,
                "result_remark" => "",
                "schedule_remark" => "",
                "vehicle_id" => $cost->vehicle_id,
                "department_id" => $vehicles[$cost->vehicle_id]['department_id']
            ];
        }
        return $result;
    }

    public function finalSchedule(int $year, int $department, string $numberOfPlate = null): array
    {
        return $this->logicOfMaintanenceSchedule($year, $department, $numberOfPlate);
    }

    private function logicOfMaintanenceSchedule(int $year, int $department, string $numberOfPlate = null): array {

        if ($vehicle = $this->vehicleReposiroty->findByNumberOfPlate($numberOfPlate)) {
            $vehiclesDatas = $this->vehicleReposiroty->vehiclesDatas($year, $department, $vehicle->id);
        } else $vehiclesDatas = $this->vehicleReposiroty->vehiclesDatas($year, $department);

        $yearShift = $this->createYearShift($year);
        foreach ($vehiclesDatas as $key => $vehicle) {
            $plates = $vehicle->plate_history->toArray();
            $this->scheduleEachThreeMonth($vehicle->id, $plates[0]['no_number_plate'], $vehicle->first_registration, $vehicle->inspection_expiration_date, $yearShift);
            $this->scheduleEachDay($yearShift, $year);
        }
        return $yearShift;
    }

    private function scheduleEachDay(array &$yearShift, int $year): void {
        foreach ($yearShift as $month => &$monthShift) {
            $nextYear = $year + 1;
            if ($month <= 3) {
                $yearMonth = $nextYear . "-" . $month;
            } else if ($month >= 4) {
                $yearMonth = $year . "-" . $month;
            }
            $dayInMonth = Carbon::parse($yearMonth)->daysInMonth;
            $day = 0;
            foreach ($monthShift as $key => &$vehicleSchedule) {
                $day ++;
                $vehicleSchedule['date'] = $yearMonth . "-" . $day;
                if ($day == $dayInMonth) $day = 0;
            }
        }
    }

    private function scheduleEachThreeMonth(int $vehicle_id, string $vehicle_plate, string $firstOfRegister, string $expirationDate, array &$yearShift) {
        $count = 1;
        $month = (int)date('m', strtotime($firstOfRegister));
        $monthFirstOfRegister = $month;
        while ($count <= 4) {
            $yearShift[$month][] = [
                "vehicle_id" => $vehicle_id,
                "no_number_plate" => $vehicle_plate,
                "first_register" => $firstOfRegister,
                "expiration_date" => $expirationDate,
                "date" => null,
                "result" => null,
                "color" => ($month == $monthFirstOfRegister) ? 2 : 1, // 0 gray 1 blue 2 yeallow,
                "schedule_remark" => "",
                "result_remark" => ""
            ];
            if ($month + 3 <= 12) $month += 3;
            else if ($month + 3 > 12) $month = ($month + 3) - 12;
            $count += 1;
        }
    }

    private function createYearShift(int $year): array {
        $result = [
            4 => [],
            5 => [],
            6 => [],
            7 => [],
            8 => [],
            9 => [],
            10 => [],
            11 => [],
            12 => [],
            1 => [],
            2 => [],
            3 => [],
        ];
        // foreach ($result as $key => &$month) {
        //     $yearMonth = $year . "-" . $key;
        //     $dayInMonth = Carbon::parse($yearMonth)->daysInMonth;
        //     for ($i = 1; $i <= $dayInMonth; $i++) {
        //         $month[$i] = [];
        //     }
        // }
        return $result;
    }

    private function checkColor($firstOfRegister, $scheduled_date) {
        $monthFirstOfRegister = date('m', strtotime($firstOfRegister));
        $monthScheduledDate = date('m', strtotime($scheduled_date));
        if ($monthFirstOfRegister == $monthScheduledDate) {
            return 2;
        }
        return 1;
    }
}

// logic
// select toàn bộ vehicle data, order by ID asc => vì lịch được phân bổ theo tháng và id nhỏ hơn sẽ lấy ngày nhỏ hơn.
// trong 1 tháng, 1 số khung(id) nhưng 2 biển số => ưu tiên biển số mới nhất.(khi thay đổi biển số, sẽ phải hiển sắp xếp vào array như 1 xe mới.) // skip
// tạo schedule result theo năm.(từ 1 -> 12 cùng năm hoặc 4 to 3 năm sau) // done
// trong schedule result phân theo tháng, trong tháng có ngày, trong ngày có thông tin các xe cần maintanence //done
// tính toán dựa trên số lượng xe cần maintanence từng ngày, ví dụ ngày 1 có 2 xe cần maintanence,
// các ngày còn lại có 01 xe cần maintanence -> xe tiếp theo sẽ đẩy vào ngày 02, trong cùng tháng đó,
// trong quá trình tạo lịch, tính toán đồng thời 3 màu gray, blue, yellow cho từng thời điểm trong năm
// dựa theo ngày hiện tại Carbon::now để tính gray, end of year để tình yeallow, còn lại là blue.
// sau khi hoàn tất schedule, căn cứ vào schedule để query maintanence cost và tiến hành add maintanence cost.
//
