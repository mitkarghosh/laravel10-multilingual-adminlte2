<?php
/*****************************************************/
# PageController
# Page/Class name : ApiTokenMiddleware
# Author :
# Created Date : 20-05-2019
# Functionality : handle
# Purpose : to manage api access token
/*****************************************************/
namespace App\Http\Middleware;
use App\Http\Helpers\ApiHelper;
use Closure;

class ApiTokenMiddleware
{
    /*****************************************************/
    # HomeController
    # Function name : handle
    # Author :  
    # Created Date : 20-05-2019
    # Purpose :  to manage access token and session access token
    # Params : 
    /*****************************************************/
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $functionName = ApiHelper::getFunctionNameFromUrl();
        $headers = $request->header();
        if ( array_key_exists('x-access-token', $headers) ) {
            $token  = $headers['x-access-token'][0];
            if (\Hash::check(env('APP_KEY'), $token)) { //Before logged in (checking with generate-token that was done for the first time)
                return $next($request);
            } else {    //After logged in (checking with database auth_token field value)
                $existToken = \App\Models\User::where("auth_token", $token)->count();
                if ($existToken == 0) {
                    if($functionName == "sign_up" || $functionName == "authentication"){
                        return \Response::json(ApiHelper::generateResponseBodyForLoginRegister('ATM-0003#'.$functionName, "Session access token is mismatched", false, 300));
                    }else{
                        return \Response::json(ApiHelper::generateResponseBody('ATM-0003#'.$functionName, "Session access token is mismatched", false, 300));
                    }
                    
                } else {
                    return $next($request);
                }                
            }
        } else {
            if($functionName == "sign_up" || $functionName == "authentication"){
                return \Response::json(ApiHelper::generateResponseBodyForLoginRegister('ATM-0003#'.$functionName, "Session access token is mismatched", false, 300));
            }else{
                return \Response::json(ApiHelper::generateResponseBody('ATM-0001#'.$functionName, "Access token is not provided", false, 100));
            }
        }
    }
}