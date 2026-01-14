<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProtectComment
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /**
         * @internal параметры запроса userId и comment должны быть
         * получены выше по коду в посредниках AvailableUser, AvailabelComment
         */
        $userId = (int)$request->userId;
        $commentUserId = $request->commentUserId;

        // Если текущий пользователь не является хозяином комментария его нельзя удалять
        if($userId !== $commentUserId) {
            abort(403);
        }
        return $next($request);
    }
}
