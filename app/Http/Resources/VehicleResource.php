<?php
/**
 * Created by VeHo.
 * Year: 2022-01-04
 */

namespace App\Http\Resources;

class VehicleResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
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

// {
//     "4": [
//         {
//             "vehicle_id": 153,
//             "no_number_plate": "つくば800あ3193",
//             "first_register": "2015-7",
//             "date": "2022-4-1",
//             "result": null,
//             "color": 1
//         },
//         {
//             "vehicle_id": 155,
//             "no_number_plate": "足立800い2835",
//             "first_register": "2015-7",
//             "date": "2022-4-2",
//             "result": null,
//             "color": 1
//         },
