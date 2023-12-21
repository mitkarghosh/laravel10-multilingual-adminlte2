<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Redirect;

class AdminMiddleware
{
    /**
    * Handle an incoming request.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \Closure  $next
    * @return mixed
    */
    public function handle($request, Closure $next)
    {
        if(!\Auth::guard('admin')->check()){
            return Redirect::route('admin.'.\App::getLocale().'.login');
        } else {
            $user = \Auth::guard('admin')->user();
            $roleId = $user->role_id;
            if($roleId && $roleId != 1) {
                $currentRouteName =  \Route::currentRouteName();
                $currentPage = \App\Models\RolePage::where('routeName', $currentRouteName)->first();
                $pageName = ucwords(str_replace(['admin.','.','-'], ['',' ',' '], $currentRouteName));
                if ($currentPage) {
                    $rolePermission = \App\Models\RolePermission::where('role_id','=', $roleId)->where('page_id','=', $currentPage->id)->count();
                    if ($rolePermission > 0) {
                        return $next($request);    
                    } else {
                        return \Redirect::route('admin.'.\App::getLocale().'.dashboard')->with('alert-danger', 'You do not have any permission to access "'.$pageName.'". Contact with super admin');
                    }
                } else {
                    return $next($request);        
                }
            } else {
                return $next($request);        
            }
        }

        //dd(Auth::guard('admin')->user());
        return $next($request);
    }
}
