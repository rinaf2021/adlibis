<?php
namespace App\Enums;
use App\Models\Post;
use App\Models\Comment;
use App\Models\News;

enum EntityType: string
{
	case COMMENT = 'C';

	case NEWS = 'N';

	case POST = 'P';

	case USER = 'U';

	public static function getEntityModel(string $type): string|null {
		return match($type) {
			EntityType::POST->value => Post::class,
			EntityType::COMMENT->value => Comment::class,
			EntityType::NEWS->value => News::class,
			default => null
		};
	}
}