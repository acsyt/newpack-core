<?php

namespace App\Http\Resources;
use App\Enums\WarehouseType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

/**
 * @OA\Schema(
 *   schema="WarehouseResource",
 *   title="Warehouse",
 *   description="Warehouse resource",
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="type", type="string", example="main"),
 *   @OA\Property(property="typeName", type="string", example="Principal"),
 *   @OA\Property(property="name", type="string", example="Almacén Central"),
 *   @OA\Property(property="active", type="boolean", example=true),
 *   @OA\Property(property="createdBy", type="integer", nullable=true, example=1),
 *   @OA\Property(property="createdAt", type="string", format="date-time"),
 *   @OA\Property(property="updatedAt", type="string", format="date-time"),
 *   @OA\Property(property="warehouseLocations", type="array", @OA\Items(ref="#/components/schemas/WarehouseLocationResource"), nullable=true),
 *   @OA\Property(property="warehouseLocationsCount", type="integer", example=0),
 * )
 */
class WarehouseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                        => $this->id,
            'type'                      => $this->type,
            'typeName'                  => WarehouseType::humanReadableType($this->type),
            'name'                      => $this->name,
            'active'                    => $this->active,
            'createdBy'                 => $this->created_by,
            'createdAt'                 => $this->created_at,
            'updatedAt'                 => $this->updated_at,
            'warehouseLocations'        => WarehouseLocationResource::collection($this->whenLoaded('warehouseLocations')),
            'warehouseLocationsCount'   => $this->warehouse_locations_count,
            'stocksCount'               => $this->stocks_count,
        ];
    }
}
