<?php

namespace App\Http\Controllers;

use App\Http\Resources\ZipCodeResource;
use App\Models\ZipCode;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

/**
 * @OA\Tag(name="Zip Codes")
 */
class ZipCodeController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/zip-codes/{zipCode}",
     *     summary="Get Zip Code details",
     *     tags={"Zip Codes"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="zipCode",
     *         in="path",
     *         required=true,
     *         description="Zip Code string",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/ZipCodeResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Zip Code not found"
     *     )
     * )
     */
    public function show(string $zipCode)
    {
        $zipCodeModel = ZipCode::with(['suburbs', 'city.state.country'])
            ->where('name', $zipCode)
            ->firstOrFail();

        return new ZipCodeResource($zipCodeModel);
    }
}
