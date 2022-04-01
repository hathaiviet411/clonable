<?php
/**
 * Created by VeHo.
 * Year: 2022-02-08
 */

namespace App\Http\Resources;

class SystemConfigResource extends BaseResource
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
}
