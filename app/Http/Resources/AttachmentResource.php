<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use OpenApi\Attributes as OA;

/**
 * @OA\Schema(
 *   schema="AttachmentResource",
 *   title="Attachment",
 *   description="Attachment resource",
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="name", type="string", example="document.pdf"),
 *   @OA\Property(property="originalName", type="string", example="my_document.pdf"),
 *   @OA\Property(property="mime", type="string", example="application/pdf"),
 *   @OA\Property(property="extension", type="string", example="pdf"),
 *   @OA\Property(property="size", type="integer", example=102400),
 *   @OA\Property(property="path", type="string", example="attachments/2023/10/document.pdf"),
 *   @OA\Property(property="description", type="string", nullable=true, example="Documento de identidad"),
 *   @OA\Property(property="alt", type="string", nullable=true, example="Foto de perfil"),
 *   @OA\Property(property="url", type="string", example="https://api.example.com/storage/attachments/2023/10/document.pdf"),
 *   @OA\Property(property="relativeUrl", type="string", example="/storage/attachments/2023/10/document.pdf"),
 * )
 */
class AttachmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"                => $this->id,
            "name"              => $this->name,
            "originalName"      => $this->original_name,
            "mime"              => $this->mime,
            "extension"         => $this->extension,
            "size"              => $this->size,
            "path"              => $this->path,
            "description"       => $this->description,
            "alt"               => $this->alt,
            "url"               => $this->url,
            "relativeUrl"       => $this->relative_url,
        ];
    }
}
