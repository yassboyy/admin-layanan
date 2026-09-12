<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!session()->has('user')) {
            return redirect('/login')->withErrors(['session' => 'Sesi Anda telah berakhir. Silakan login kembali.']);
        }

        $userRole = session('user.role');
        if (!in_array($userRole, $roles)) {
            return redirect('/dashboard');
        }

        return $next($request);
    }
}
