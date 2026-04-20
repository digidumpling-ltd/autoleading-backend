<?php

namespace Webkul\Shop\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthenticateCustomer
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = 'customer')
    {
        if (! auth()->guard($guard)->check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => '',
                ], 401);
            }

            return redirect()->route('shop.customer.session.index');
        } else {
            if (! auth()->guard($guard)->user()->status) {
                auth()->guard($guard)->logout();

                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => trans('shop::app.customers.login-form.not-activated'),
                    ], 401);
                }

                session()->flash('warning', trans('shop::app.customers.login-form.not-activated'));

                return redirect()->route('shop.customer.session.index');
            }

            // NOTE: Verification status checks for account access are deferred to Story 2.3
            // For now, allow all authenticated customers to access their account
            // regardless of verification_status
        }

        return $next($request);
    }
}
