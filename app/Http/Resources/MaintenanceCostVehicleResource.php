<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
class MaintenanceCostVehicleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        $date = Carbon::parse($this->inspection_expiration_date);
        $now = Carbon::now('Asia/Tokyo');
        $remaining_days = $now->diffInDays($date);
        foreach ($this->plate_history as $key => &$plate) {
            $plate['date'] = date('Y-m-d', strtotime($plate['date']));
        }
        foreach ($this->mileage_history as $key => &$mileage) {
            $mileage['date'] = date('Y-m-d', strtotime($mileage['date']));
        }
        return [
            "vehicle_id" => $this->id,
            "no_number_plate" => $this->plate_history[count($this->plate_history) - 1]->no_number_plate,
            "mileage" => $this->mileage_history[count($this->mileage_history) - 1]->mileage,
            "type" => $this->type,
            "first_registration" => $this->first_registration,
            "inspection_expiration_date" => $this->inspection_expiration_date,
            "remaining_days" => $remaining_days,
            "leasing_company" => $this->vehicle_lease ? $this->vehicle_lease->leasing_company : '',
            "leasing_period" => $this->vehicle_lease ? $this->vehicle_lease->leasing_period : '',
            "start_of_leasing" => $this->vehicle_lease ? $this->vehicle_lease->start_of_leasing : '',
            "garage" => $this->vehicle_lease ? $this->vehicle_lease->garage : '',
            "tel" => $this->vehicle_lease ? $this->vehicle_lease->tel : '',
            "end_of_leasing" => $this->vehicle_lease ? $this->vehicle_lease->end_of_leasing : '',
            "remark_01" => (isset($this->vehicle_data->remark_01)) ? $this->vehicle_data->remark_01 : null,
            "remark_02" => (isset($this->vehicle_data->remark_02)) ? $this->vehicle_data->remark_02 : null,
            "plate_history" => $this->plate_history()->orderBy('date', 'DESC')->get(['*']),
            "mileage_history" => $this->mileage_history()->orderBy('mileage', 'ASC')->get(['*']),
            "maintenance_data" => [
                "battery" => [
                    "name" => (isset($this->vehicle_data->battery)) ? $this->vehicle_data->battery : null,
                    "date" => (isset($this->vehicle_data->battery_date)) ? $this->vehicle_data->battery_date : null
                ],
                "tire" => [
                    "name" => (isset($this->tire_size)) ? $this->tire_size : null,
                    "date" => (isset($this->vehicle_data->tire_replacement_date)) ? $this->vehicle_data->tire_replacement_date : null
                ],

                "glass" => [
                    "name" => (isset($this->vehicle_data->glass)) ? $this->vehicle_data->glass : null,
                    "date" => (isset($this->vehicle_data->glass_date)) ? $this->vehicle_data->glass_date : null
                ],
                "starter_motor" => [
                    "name" => (isset($this->vehicle_data->starter_motor)) ? $this->vehicle_data->starter_motor : null,
                    "date" => (isset($this->vehicle_data->starter_motor_date)) ? $this->vehicle_data->starter_motor_date : null
                ],
                "alternator" => [
                    "name" => (isset($this->vehicle_data->alternator)) ? $this->vehicle_data->alternator : null,
                    "date" => (isset($this->vehicle_data->alternator_date)) ? $this->vehicle_data->alternator_date : null
                ],
                "body_id" => [
                    "name" => (isset($this->vehicle_data->body_id)) ? $this->vehicle_data->body_id : null,
                    "date" => (isset($this->vehicle_data->body_id_date)) ? $this->vehicle_data->body_id_date : null,
                ],
                "camera_monitor" => [
                    "name" => (isset($this->vehicle_data->camera_monitor)) ? $this->vehicle_data->camera_monitor : null,
                    "date" => (isset($this->vehicle_data->camera_monitor_date)) ? $this->vehicle_data->camera_monitor_date : null,
                ],
                "gate" => [
                    "name" => (isset($this->vehicle_data->gate)) ? $this->vehicle_data->gate : null,
                    "date" => (isset($this->vehicle_data->gate_date)) ? $this->vehicle_data->gate_date : null
                ],
                "other" => [
                    "name" => (isset($this->vehicle_data->other)) ? $this->vehicle_data->other : null,
                    "date" => (isset($this->vehicle_data->other_date)) ? $this->vehicle_data->other_date : null
                ]
            ]
        ];
    }
}
