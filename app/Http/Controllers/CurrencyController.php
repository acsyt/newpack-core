<?php

namespace App\Http\Controllers;

use App\Http\Actions\Currency\CreateCurrencyAction;
use App\Http\Actions\Currency\UpdateCurrencyAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Currency\StoreCurrencyRequest;
use App\Http\Requests\Currency\UpdateCurrencyRequest;
use App\Http\Resources\CurrencyResource;
use App\Models\Currency;
use App\Queries\CurrencyQuery;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

#[OA\Tag(name: 'Currencies')]
class CurrencyController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/currencies",
     *     summary="List all currencies",
     *     tags={"Currencies"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filter[id]", in="query", required=false, @OA\Schema(type="integer"), description="Filter by currency ID (exact match)"),
     *     @OA\Parameter(name="filter[name]", in="query", required=false, @OA\Schema(type="string"), description="Filter by name (partial match)"),
     *     @OA\Parameter(name="filter[code]", in="query", required=false, @OA\Schema(type="string"), description="Filter by code (partial match)"),
     *     @OA\Parameter(name="filter[active]", in="query", required=false, @OA\Schema(type="boolean"), description="Filter by active status (exact match)"),
     *     @OA\Parameter(name="filter[created_at]", in="query", required=false, @OA\Schema(type="string"), description="Filter by creation date range (format: YYYY-MM-DD,YYYY-MM-DD)"),
     *     @OA\Parameter(name="filter[updated_at]", in="query", required=false, @OA\Schema(type="string"), description="Filter by last update date range (format: YYYY-MM-DD,YYYY-MM-DD)"),
     *     @OA\Response(
     *         response=200,
     *         description="List of currencies",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/CurrencyResource")
     *         )
     *     )
     * )
     */
    public function findAllCurrencies()
    {
        $currencies = CurrencyQuery::make()->paginated();
        return CurrencyResource::collection($currencies);
    }

    /**
     * @OA\Get(
     *     path="/api/currencies/{id}",
     *     summary="Get currency by ID",
     *     tags={"Currencies"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Currency ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Currency details",
     *         @OA\JsonContent(ref="#/components/schemas/CurrencyResource")
     *     ),
     *     @OA\Response(response=404, description="Currency not found")
     * )
     */
    public function findOneCurrency($id)
    {
        $currency = CurrencyQuery::make()->findByIdOrFail( $id );
        return response()->json(new CurrencyResource($currency));
    }

    /**
     * @OA\Post(
     *     path="/api/currencies",
     *     summary="Create a new currency",
     *     tags={"Currencies"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreCurrencyRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Currency created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/CurrencyResource"),
     *             @OA\Property(property="message", type="string", example="Currency created successfully")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function createCurrency(StoreCurrencyRequest $request)
    {
        $data = $request->validated();
        $currency = CreateCurrencyAction::handle($data);
        return response()->json([
            'data'      => new CurrencyResource($currency),
            'message'   => 'Currency created successfully'
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/currencies/{id}",
     *     summary="Update a currency",
     *     tags={"Currencies"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Currency ID"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateCurrencyRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Currency updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/CurrencyResource"),
     *             @OA\Property(property="message", type="string", example="Currency updated successfully")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Currency not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function updateCurrency(UpdateCurrencyRequest $request, $id): Response
    {
        /** @var Currency $currency */
        $currency = CurrencyQuery::make()->findByIdOrFail($id);
        $data = $request->validated();
        $updatedCurrency = UpdateCurrencyAction::handle($currency, $data);
        return response()->json([
            'data'    => new CurrencyResource($updatedCurrency),
            'message' => 'Currency updated successfully'
        ]);
    }

}
