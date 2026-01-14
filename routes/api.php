<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PostController;
use App\Http\Middleware\AvailableUser;
use App\Http\Middleware\ProtectComment;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AvailableEntity;
use App\Http\Middleware\AvailableComment;

Route::prefix('/news')
	->name('news.')
	->group(function() {

		Route::get('/list', [NewsController::class, 'getList'])
			->name('list');
	});

Route::prefix('/post')
	->name('post.')
	->group(function() {

		Route::get('/list', [PostController::class, 'getList'])
			->name('list');
	});

Route::prefix('/comment')
	->name('comment.')
	->group(function() {

		Route::get('/list', [CommentController::class, 'getList'])
			->middleware([
				AvailableEntity::class
			])
			->name('list');

		/**
		 * @internal
		 * Для CRUD методов комментария исключила проверку csrf токена
		 * чтобы не реализовывать метод получения токена
		 */

		Route::put('/create', [CommentController::class, 'create'])
			->middleware([
				AvailableUser::class,
				AvailableEntity::class
			])
			->name('create');

		Route::prefix('/{commentId}')
			->middleware([
				AvailableComment::class
			])
			->group(function() {

					Route::get('/read', [CommentController::class, 'read'])
						->name('read');

					Route::middleware([
						AvailableUser::class,
						ProtectComment::class
					])
						->group(function() {
							Route::patch('/update', [CommentController::class, 'update'])
								->name('update');

							Route::delete('/delete', [CommentController::class, 'delete'])
								->name('delete');
						});

			});

	});
