<?php

namespace App\Http\Middleware;

use App\Models\Comment;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AvailableComment
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $commentId = (int)$request->commentId;
        if($commentId < 1) {
            abort(404);
        }

        $comment = Comment::with('user')
            ->findOrFail($commentId);

        $request->merge([
            'comment' => $comment,
            'commentUserId' => (int)$comment->user_id
        ]);

        return $next($request);
    }
}
