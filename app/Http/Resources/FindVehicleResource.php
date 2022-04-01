<?php

namespace App\Http\Resources;

use App\Models\MaintenanceLease;
use Illuminate\Http\Resources\Json\JsonResource;

class FindVehicleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $lease = MaintenanceLease::where('vehicle_id', $this->id)->orderBy('id','DESC')->first();
        return [
            "vehicle_id" => $this->id,
            "department" => $this->department_id,
            "number_of_plate" => $this->plate_history[count($this->plate_history) - 1],
            "mileage" => $this->mileage_history[count($this->mileage_history) - 1],
            "garage" => ($lease) ? $lease->garage : null
        ];
    }
}
