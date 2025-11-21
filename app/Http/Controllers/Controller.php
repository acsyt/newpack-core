<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\QueryBuilder;


/**
 * @OA\Info(
 *     title="NewPack API Documentation",
 *     version="1.0.0",
 *     description="API Documentation for NewPack application",
 *     @OA\Contact(
 *         email="support@newpack.com"
 *     )
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="API Server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Enter your bearer token"
 * )
 */
abstract class Controller
{
    const PER_PAGE = 25;

    const MAX_PER_PAGE = 100;
    const MAX_LIMIT = 50;

    /**
     * @param QueryBuilder $builder
     * @param string $resource
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    protected function query(QueryBuilder $builder, string $resource): AnonymousResourceCollection
    {
        if (!is_subclass_of($resource, JsonResource::class))
            throw new CustomException('El recurso proporcionado no es válido.', 500);

        $currentPage = filter_var(request()->input('page', 1), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: 1;

        $itemsPerPage = filter_var(request()->input('pageSize', self::PER_PAGE), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: self::PER_PAGE;
        $itemsPerPage = min($itemsPerPage, self::MAX_PER_PAGE);

        $fetchAll = filter_var(request()->input('all', false), FILTER_VALIDATE_BOOLEAN);

        $data = $fetchAll
            ? $builder->get()
            : $builder->paginate($itemsPerPage, ['*'], 'page', $currentPage);

        /** @var JsonResource $resource  */
        return $resource::collection($data);
    }

    public function downloadZip(string $zipPath, string $zipFileName)
    {
        return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
    }
}
