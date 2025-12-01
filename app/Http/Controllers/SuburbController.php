<?php

namespace App\Http\Controllers;

use App\Http\Resources\SuburbResource;
use App\Queries\SuburbQuery;
use OpenApi\Attributes as OA;

/**
 * @OA\Tag(name="Suburbs")
 */
class SuburbController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/suburbs",
     *     summary="Get all suburbs",
     *     tags={"Suburbs"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="filter[name]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filter[zip_code_id]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Suburbs list", @OA\JsonContent(ref="#/components/schemas/SuburbResource")),
     * )
     */
    public function index()
    {
        $suburbs = SuburbQuery::make()->paginated();
        return SuburbResource::collection($suburbs);
    }
}
