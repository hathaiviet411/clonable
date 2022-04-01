<?php


namespace App\Repositories\Contracts;

use App\Http\Requests\UserRequest;

/**
 * Interface BaseRepositoryInterface
 *
 * @package App\Repositories
 */
interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function getPagination(UserRequest $request);
}
