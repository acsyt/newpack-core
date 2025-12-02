<?php

namespace App\Http\Controllers;

use App\Actions\Inventory\ReceiveTransferAction;
use App\Http\Requests\Inventory\ReceiveTransferRequest;
use App\Http\Resources\TransferResource;
use App\Queries\TransferQuery;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Transfers')]
class TransferController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/inventory/transfers",
     *     summary="List all transfers",
     *     tags={"Transfers"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filter[status]", in="query", required=false, @OA\Schema(type="string"), description="Filter by status (pending, shipped, completed, cancelled)"),
     *     @OA\Parameter(name="filter[source_warehouse_id]", in="query", required=false, @OA\Schema(type="integer"), description="Filter by source warehouse"),
     *     @OA\Parameter(name="filter[destination_warehouse_id]", in="query", required=false, @OA\Schema(type="integer"), description="Filter by destination warehouse"),
     *     @OA\Parameter(name="filter[search]", in="query", required=false, @OA\Schema(type="string"), description="Search by transfer number"),
     *     @OA\Parameter(name="include", in="query", required=false, @OA\Schema(type="string"), description="Include relationships (items, sourceWarehouse, destinationWarehouse, etc)"),
     *     @OA\Parameter(name="sort", in="query", required=false, @OA\Schema(type="string"), description="Sort by field (prefix with - for descending)"),
     *     @OA\Response(
     *         response=200,
     *         description="List of transfers",
     *         @OA\JsonContent(type="object")
     *     )
     * )
     */
    public function index()
    {
        $transfers = TransferQuery::make()->paginated();
        return TransferResource::collection($transfers);
    }

    /**
     * @OA\Get(
     *     path="/api/inventory/transfers/{id}",
     *     summary="Get transfer by ID",
     *     tags={"Transfers"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Transfer ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Transfer details",
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(response=404, description="Transfer not found")
     * )
     */
    public function show($id)
    {
        $transfer = TransferQuery::make()->findByIdOrFail($id);
        return response()->json(new TransferResource($transfer));
    }

    /**
     * @OA\Post(
     *     path="/api/inventory/transfers/{id}/receive",
     *     summary="Receive a shipped transfer",
     *     tags={"Transfers"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Transfer ID"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"items"},
     *             @OA\Property(property="receiving_notes", type="string", nullable=true),
     *             @OA\Property(
     *                 property="items",
     *                 type="array",
     *                 @OA\Items(
     *                     required={"transfer_item_id", "quantity_received"},
     *                     @OA\Property(property="transfer_item_id", type="integer"),
     *                     @OA\Property(property="quantity_received", type="number")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Transfer received successfully"
     *     )
     * )
     */
    public function receive($id, ReceiveTransferRequest $request)
    {
        $data = $request->validated();
        $data['transfer_id'] = $id;

        $transfer = ReceiveTransferAction::handle($id, $data);

        return response()->json([
            'data' => new TransferResource($transfer),
            'message' => 'Transfer received successfully'
        ], 200);
    }
}
