<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class AvailableUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /**
         * @internal
         * Параметр userId создан исключительно для тестовой среды,
         * чтобы была возможность проверить необходимый функционал
         * без реализации авторизации
         */
        if(env('APP_ENV') !== 'production') {
            $userId = (int)$request->userId;
        } else {
            $userId = Auth::id();
        }

        if($userId < 1) {
            abort(403);
        }

        $user = User::findOrFail($userId);

        // и дальше не надо будет проверять на число
        $request->merge([
            'userId' => $userId,
            'user' => $user
        ]);

        return $next($request);
    }
}
