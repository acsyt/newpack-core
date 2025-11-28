<?php

namespace App\Http\Controllers;

use App\Http\Resources\CountryResource;
use App\Queries\CountryQuery;
use OpenApi\Attributes as OA;

/**
 * @OA\Tag(name="Countries")
 */
class CountryController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/countries",
     *     summary="Get all countries",
     *     tags={"Countries"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="filter[name]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filter[code]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Countries list", @OA\JsonContent(ref="#/components/schemas/CountryResource")),
     * )
     */
    public function index()
    {
        $countries = CountryQuery::make()->get();
        return CountryResource::collection($countries);
    }
}
