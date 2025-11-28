<?php

namespace App\Http\Controllers;

use App\Http\Resources\CityResource;
use App\Queries\CityQuery;
use OpenApi\Attributes as OA;

/**
 * @OA\Tag(name="Cities")
 */
class CityController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/cities",
     *     summary="Get all cities",
     *     tags={"Cities"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="filter[name]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filter[state_id]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Cities list", @OA\JsonContent(ref="#/components/schemas/CityResource")),
     * )
     */
    public function index()
    {
        $cities = CityQuery::make()->get();
        return CityResource::collection($cities);
    }
}
