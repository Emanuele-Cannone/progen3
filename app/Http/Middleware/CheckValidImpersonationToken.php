<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\Response;

class CheckValidImpersonationToken
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param \Closure(Request): (Response) $next
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (session()->has('impersonate') && session()->has('impersonateToken')) {

            if (Carbon::parse(session()->get('impersonateToken'))->diffInMinutes(Carbon::now()) > config('impersonate.duration')) {
                Auth::loginUsingId(session()->get('impersonator'));
                session()->forget(['impersonate', 'impersonateToken', 'impersonator']);
                return redirect('/')->with('status', 'Impersonation session has expired.');
            }
        }

        return $next($request);
    }
}
