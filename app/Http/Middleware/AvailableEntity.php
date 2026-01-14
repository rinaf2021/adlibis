<?php

namespace App\Http\Middleware;

use App\Enums\EntityType;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AvailableEntity
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
         * Если идет фильтраци по сущности, то мы проверяем
         * существование этой сущности и передаем вниз по
         * коду это значение, чтобы не делать доп запросов
         *
         * Если сущность не находим - дальнейшая обрабтка бессмыслена, поэтому
         * обрубаем 404 ошибкой
         */
        $requestEntityData = null;
        $entity = $request->get('entity', 'N');
        if($entity !== 'N') {
            [$type, $entityId] = explode(':', $entity);

            $model = EntityType::getEntityModel($type);

            if($model !== null) {
                $entityId = (int)$entityId;
                $entity = $model::find($entityId);
                if($entity === null) {
                    abort(404);
                }

                $entity->type = $type;
                $requestEntityData = $entity;
            }
        }

        $request->merge([
            'entityData' => $requestEntityData
        ]);

        return $next($request);
    }
}
