<?php

namespace App\Http\Middleware;


use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Routing\Middleware;
use App\Http\Controllers\Auth\BasicAuthController;

use Closure;

class BasicAuth{

    public function __construct(BasicAuthController $admin)
    {
        $this->admin = $admin;
    }

    
    public function handle($request, Closure $next)
    {
        return $this->admin->basic() ?: $next($request);
    }
}
