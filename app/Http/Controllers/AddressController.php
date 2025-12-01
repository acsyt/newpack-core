<?php

namespace App\Http\Controllers;

use App\Models\ZipCode;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

/**
 * @OA\Tag(name="Addresses")
 */
class AddressController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/addresses/lookup/{zipCode}",
     *     summary="Lookup address details by Zip Code",
     *     tags={"Addresses"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="zipCode",
     *         in="path",
     *         required=true,
     *         description="Zip Code",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Address details including hierarchy and suburbs",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="zip_code", type="string", example="64000"),
     *                 @OA\Property(
     *                     property="country",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="code", type="string")
     *                 ),
     *                 @OA\Property(
     *                     property="state",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="name", type="string")
     *                 ),
     *                 @OA\Property(
     *                     property="city",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="name", type="string")
     *                 ),
     *                 @OA\Property(
     *                     property="suburbs",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="name", type="string")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Zip Code not found")
     * )
     */
    public function lookup(string $zipCode)
    {
        $zipCodeStr = preg_replace('/[^a-zA-Z0-9]/', '', $zipCode);

        if (empty($zipCodeStr)) return response()->json(['message' => 'Invalid Zip Code format'], 400);

        $zipCode = ZipCode::with(['city.state.country', 'suburbs'])
            ->where('name', $zipCodeStr)
            ->firstOrFail();

        $city = $zipCode->city;
        $state = $city->state;

        return response()->json([
            'data' => [
                'zipCode' => $zipCode->name,
                'state' => [
                    'id' => $state->id,
                    'name' => $state->name,
                ],
                'city' => [
                    'id' => $city->id,
                    'name' => $city->name,
                ],
                'suburbs' => $zipCode->suburbs->map(function ($suburb) {
                    return [
                        'id' => $suburb->id,
                        'name' => $suburb->name,
                    ];
                }),
            ],
        ]);
    }
}
