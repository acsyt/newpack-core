<?php

namespace App\Http\Controllers;

use App\Http\Resources\StateResource;
use App\Queries\StateQuery;
use OpenApi\Attributes as OA;

/**
 * @OA\Tag(name="States")
 */
class StateController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/states",
     *     summary="Get all states",
     *     tags={"States"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="filter[name]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filter[code]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filter[country_id]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="States list", @OA\JsonContent(ref="#/components/schemas/StateResource")),
     * )
     */
    public function index()
    {
        $states = StateQuery::make()->get();
        return StateResource::collection($states);
    }
}
