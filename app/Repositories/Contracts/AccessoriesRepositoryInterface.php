<?php
/**
 * Created by VeHo.
 * Year: 2022-01-04
 */

namespace App\Repositories\Contracts;


interface AccessoriesRepositoryInterface extends BaseRepositoryInterface
{
    //
    public function getPaginate($accessoryName = null, $sortBy = null, $sortType = 0);
}
