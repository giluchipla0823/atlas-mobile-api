<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param \Illuminate\Http\Request $request
     * @return string|void|null
     * @throws AuthorizationException
     */
    protected function redirectTo($request)
    {
        if ($request->is('api/*')) {
            throw new AuthorizationException('Unauthorized.');
        }

        if (!$request->expectsJson()) {
            abort('401');
        }
    }
}
