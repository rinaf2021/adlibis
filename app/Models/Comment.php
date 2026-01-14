<?php

namespace App\Models;

use App\Enums\EntityType;
use App\Observers\CommentObserver;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Pagination\LengthAwarePaginator;

#[ObservedBy(CommentObserver::class)]
class Comment extends Model
{
    use  HasFactory;

    protected $table = 'comments';

    protected $fillable = [
        'user_id',
        'body',
        'entity'
    ];

    protected $hidden = [
        'user_id',
        'entity'
    ];

    #[Scope]
    protected function frontend(Builder $query)
    {
        $query->orderBy('updated_at', 'DESC')
            ->whereNotNull('user_id');
    }

    protected function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => (new Carbon($value))->format('d.m.Y')
        );
    }

    protected function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => (new Carbon($value))->format('d.m.Y')
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public static function fillEntityData(
        LengthAwarePaginator $arComments,
        Model $requestEntityData = null
    )
    {
        $arEntities = [];

        if($requestEntityData === null) {
            $arEntityIds = [];

            foreach($arComments as $comment) {
                [$entity, $entityId] = explode(':', $comment->entity);
                $arEntityIds[$entity][] = $entityId;
            }

            if(count($arEntityIds)) {
                foreach($arEntityIds as $type => $entityIds) {

                    $entityIds = array_values(array_unique($entityIds));
                    if(count($entityIds) < 1) {
                        continue;
                    }

                    $model = EntityType::getEntityModel($type);

                    if($model !== null) {
                        $rows = $model::whereIn('id', $entityIds)->get();
                        $arEntities[$type] = $rows;
                    }
                }
            }
        }

        foreach($arComments as $comment) {
            [$entity, $entityId] = explode(':', $comment->entity);
            $entityData = $requestEntityData === null
                ? $arEntities[$entity]->where('id', $entityId)->first()
                : $requestEntityData;

            if($entityData !== null) {
                $entityData->type = $entity;
            }

            $comment->entityData = $entityData;
        }

        return $arComments;
    }
}
