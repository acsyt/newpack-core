<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductSubclassResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,
            'slug' => $this->slug,
            'productClassId' => $this->product_class_id,
            'productClass' => $this->whenLoaded('productClass', function () {
                return [
                    'id' => $this->productClass->id,
                    'code' => $this->productClass->code,
                    'name' => $this->productClass->name,
                ];
            }),
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
