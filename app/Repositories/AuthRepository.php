<?php

namespace Repository;
use App\Repositories\Contracts\AuthRepositoryInterface;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Repositories\UserRepository;
class AuthRepository  implements AuthRepositoryInterface
{
    public function __construct()
    {

    }

    /**
     *
     * Handle action login of user.
     *
     * @param LoginRequest $request
     * @param null $guard
     * @return array
     */
    public function doLogin(LoginRequest $request, $guard = null): array
    {
        $input = $request->only('user_code', 'password');
        $attempt = JWTAuth::attempt($input);
        $mes = '';
        if ($attempt) {
            return [
                'user' => User::where('user_code', $request->user_code)->first(['id', 'user_code', 'user_name', 'department_id']),
                'attempt' => $attempt,
            ];
        } else {
            $mes = 'ID、パスワードの間違った形式を入力しました';
            return [
                'attempt' => false,
                'mes' => $mes
            ];
        }
    }
    /**
     * @param array $params
     * @return bool|void
     */
}
