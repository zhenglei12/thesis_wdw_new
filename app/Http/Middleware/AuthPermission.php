<?php


namespace App\Http\Middleware;

use App\Http\Constants\CodeMessageConstants;
use Auth;
use Route;
use Spatie\Permission\Exceptions\UnauthorizedException;

class AuthPermission
{
    /**
     * FunctionName：handle
     * Description：认证权限
     * Author：cherish
     * @param $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        $permission = Route::currentRouteName();

        if (Auth::user()->name == "admin") {
            return $next($request);
        }

        if (Auth::user()->can($permission)) {
            return $next($request);
        }
        throw \ExceptionFactory::business(CodeMessageConstants::FORBIDDEN);

    }
}
