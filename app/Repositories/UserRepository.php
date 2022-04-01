<?php


namespace App\Repositories;

use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Repository\BaseRepository;
use Spatie\Permission\Models\Role;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(Application $app)
    {
        parent::__construct($app);
    }

    public function model()
    {
        return User::class;
    }

    public function getAll()
    {
        // return User::with('roles')->find($id);
    }

    public function getOne($id)
    {
        $user = $this->model->find($id);
        $user->getRoleNames();
        return $user;
    }

    public function getOneByCode($userCode)
    {
        return $this->model->find($userCode);
    }

    public function getPagination(UserRequest $request)
    {
        $sortby = $request->has('sortby') ? $request->sortby : 'users.id';
        if ($sortby == 'name') {
            $sortby = 'users.user_name';
        }
        if ($sortby == 'role') {
            $sortby = 'roles.name';
        }
        if ($sortby == 'department') {
            $sortby = 'departments.name';
        }

        $sorttype = $request->has('sorttype') && $request->sorttype ? 'desc' : 'asc';

        $query = User::select("users.id", "users.user_code", "users.department_id", "users.user_name",
            "roles.name as role_name", "departments.name as departments_name", "model_has_roles.model_type")
            ->leftJoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->leftJoin('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->leftJoin('departments', 'departments.id', '=', 'users.department_id');

        if ($request->has('user_code'))
            $query->where('user_code', 'LIKE', '%' . $request->user_code . '%');
        if ($request->has('name')) {
            $query->where(function ($query) use ($request) {
                $query->where('user_name', 'LIKE', '%' . $request->name . '%');
            });
        }
        $query->when($request->get('department_id'), function ($query) use ($request) {
            return $query->where('department_id', $request->get('department_id'));
        });

        $result = $query->orderBy($sortby, $sorttype)->paginate($request->per_page);
        return $result;
    }

    public function create(array $attributes)
    {
        $status = DB::transaction(function () use ($attributes) {
            $user = $this->model->create([
                'user_code' => $attributes['user_code'],
                'department_id' => $attributes['department_id'],
                'user_name' => $attributes['user_name'],
                'password' => $attributes['password'],
                'created_by' => Auth::id(),
            ]);

            if ($role = Role::findById($attributes['roles'], 'api')) {
                $user->syncRoles($role);
                return true;
            } else {
                DB::rollBack();
                return false;
            }
        });
        return $status;
    }

    public function update(array $request, $id)
    {
        $status = DB::transaction(function () use ($request, $id) {
            $user = $this->model->where('id',$id)->first();
            if (!isset($request['current_password']) || !Hash::check($request['current_password'], $user->password)) {
                return false;
            }
            $user->user_name = $request['user_name'];
            $user->department_id = $request['department_id'];

            if (isset($request['password']) && strlen($request['password'] >= 8)) {
                $user->password = $request['password'];
            }
            $user->save();

            if ($role = Role::findById($request['roles'], 'api')) {
                $user->syncRoles($role);
                return true;
            } else {
                DB::rollBack();
                return false;
            }
        });
        return $status;
    }

    public function destroy($id)
    {
        return $this->model->where('id', $id)->delete($id);
    }
}
