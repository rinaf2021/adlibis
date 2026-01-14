<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'body' => 'required'
        ]);

        if($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ]);
        }

        $comment = Comment::create([
            'body' => $this->safe($request->body),
            'entity' => $this->safe($request->entity),
            'user_id' => $request->userId
        ]);

        $comment->user = $request->user;
        $comment->entityData = $request->entityData;

        return response()->json($comment);
    }

    public function read(Request $request)
    {
        return response()->json($request->comment);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'body' => 'required'
        ]);

        if($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ]);
        }

        $comment = $request->comment;

        $request->comment->update([
            'body' => $this->safe($request->body)
        ]);

        $comment->body = $this->safe($request->body);

        return response()->json($comment);
    }

    public function delete(Request $request)
    {
        $request->comment->delete();

        return response()->json([
            'message' => 'Комментарий успешно удален'
        ]);
    }

    public function getList(Request $request)
    {
        $limit = (int)$request->get('limit', 15);
        $entity = $request->get('entity', 'N');

        $qComment = Comment::frontend()
            ->with('user');

        if($entity !== 'N') {
            $qComment = $qComment->where('entity', $entity);
        }

        $comments = $qComment->paginate($limit);

        Comment::fillEntityData(
            $comments,
            ($request->has('entityData') ? $request->entityData : null)
        );

        return $comments;
    }

    protected function safe(string $string)
    {
        return htmlspecialchars(strip_tags(trim($string)));
    }
}
