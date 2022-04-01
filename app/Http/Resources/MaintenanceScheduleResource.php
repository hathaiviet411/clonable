<?php
/**
 * Created by VeHo.
 * Year: 2022-01-04
 */

namespace App\Http\Resources;

class MaintenanceScheduleResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->format($this['data']);
    }

    private function format($data) {
        $result = [];
        foreach ($data as $key => $motnhSchedule) {
            foreach ($motnhSchedule as $key => $day) {
                if (isset($result[$day['vehicle_id']])) {
                    $result[$day['vehicle_id']][] = $day;
                } else {
                    $result[$day['vehicle_id']][] = $day;
                }
            }
        }
        return $result;
    }
}
