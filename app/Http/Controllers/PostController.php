<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{
    public function getList(Request $request)
    {
        $limit = (int)$request->get('limit', 15);

        return Post::frontendOrder()
            ->paginate($limit);
    }
}
