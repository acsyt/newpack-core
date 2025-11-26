<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use App\Models\Attachment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class AttachmentService {
    /**
     * Create an attachment
     *
     * @param UploadedFile $file
     * @param string $pathToSave
     * @return Attachment
     */
    public function createAttachment(UploadedFile $file, string $pathToSave = ""): Model {
        $pathToSave = $this->determineUploadPath($pathToSave);
        $originalName = $this->sanitizeFileName($file->getClientOriginalName());
        $fileInfo = $this->generateFileInfo($file, $pathToSave, $originalName);

        $attachment = new Attachment($fileInfo);
        $attachment->save();

        if (!$attachment->uploadFile($file, $pathToSave)) throw new \Exception("Error al subir el archivo.");

        return $attachment;
    }

    /**
     * Sube un archivo y devuelve su URL.
     *
     * @param UploadedFile $file Archivo a subir.
     * @param string $pathToSave Ruta donde se guardará el archivo.
     * @param string $disk Disco donde se almacenará el archivo.
     * @return string URL del archivo subido. Ejemplo: 'uploads/2021/01/01/uuid.jpg'
     */
    public function uploadFileAndGetUrl(UploadedFile $file, string $pathToSave, string $disk = 'public'): string {
        $uniqueName = $this->generateUniqueFileName($file);
        $file->storeAs($pathToSave, $uniqueName, $disk);
        return "{$pathToSave}/{$uniqueName}";
    }

    /**
     * Elimina un adjunto y su archivo asociado.
     *
     * @param int $attachmentId ID del adjunto a eliminar.
     * @return bool Verdadero si la operación fue exitosa, falso en caso contrario.
     */
    public function deleteAttachment(int $attachmentId): bool {
        $attachment = Attachment::find($attachmentId);
        if (!$attachment) return false;

        $filePath = $this->generateFilePath($attachment);

        if (Storage::disk( $attachment->disk )->exists($filePath)) {
            Storage::disk( $attachment->disk )->delete($filePath);
        }

        return $attachment->delete();
    }

    public function fileExistsByPath(string $path, string $disk = 'public'): bool {
        return Storage::disk($disk)->exists($path);
    }

    /**
     * Verifica si el archivo asociado a un adjunto existe en el almacenamiento.
     *
     * @param int $attachmentId ID del adjunto a verificar.
     * @return bool Verdadero si el archivo existe, falso en caso contrario.
     */
    public function fileExists(int $attachmentId): bool {
        $attachment = Attachment::find($attachmentId);
        if ($attachment) {
            $filePath = "{$attachment->path}/{$attachment->name}.{$attachment->extension}";
            return Storage::disk($attachment->disk)->exists($filePath);
        }
        return false;
    }

    public function saveAttachments($request, Model $model, $pathToSave, array $attachmentFields): void {
        foreach ($attachmentFields as $fileInput => $dbColumn) {
            if ($request->hasFile($fileInput)) {
                if ($model->$dbColumn) {
                    $this->deleteAttachment($model->$dbColumn);
                }
                $file = $request->file($fileInput);
                $attachment = $this->createAttachment($file, $pathToSave);
                $model->$dbColumn = $attachment->id;
            }
        }
        $model->save();
    }

    /**
     * Validates the uploaded file.
     *
     * @param UploadedFile $file
     * @throws \Exception
     */
    protected function validateFile(UploadedFile $file) {
        $allowedExtensions = config('filesystems.attachments.allowed_extensions', []);
        if (!$file->isValid() || !in_array($file->getClientOriginalExtension(), $allowedExtensions)) {
            throw new \Exception('Archivo no válido o tipo de archivo no permitido');
        }
    }

    /**
     * Determines the upload path, using a default if none is provided.
     *
     * @param string $pathToSave
     * @return string
     */
    protected function determineUploadPath(string $pathToSave): string {
        $defaultUploadPath = config('filesystems.attachments.default_upload_path');
        return empty($pathToSave) ? $defaultUploadPath . date('Y/m/d') : $pathToSave;
    }

    /**
     * Sanitizes the file name to remove unwanted characters.
     *
     * @param string $fileName
     * @return string
     */
    protected function sanitizeFileName(string $fileName): string {
        return preg_replace("/[^a-zA-Z0-9\._-]/", "", $fileName);
    }

    /**
     * Genera un nombre de archivo único.
     *
     * @param UploadedFile $file
     * @return string
     */
    protected function generateUniqueFileName(UploadedFile $file): string {
        $extension = $file->getClientOriginalExtension();
        return Str::uuid() . ".{$extension}";
    }

    /**
     * Generates the file information array for attachment creation.
     *
     * @param UploadedFile $file
     * @param string $pathToSave
     * @param string $originalName
     * @return array
     */
    protected function generateFileInfo(UploadedFile $file, string $pathToSave, string $originalName): array {
        $extension = $file->guessExtension() ?? $file->getClientOriginalExtension();
        return [
            'extension' => $extension,
            'hash' => hash_file('md5', $file->getRealPath()),
            'mime' => $file->getClientMimeType(),
            'name' => Str::uuid(),
            'original_name' => $originalName,
            'path' => $pathToSave,
            'size' => $file->getSize(),
            'disk' => 'public',
        ];
    }



    /**
     * Genera la ruta completa del archivo en el almacenamiento.
     *
     * @param Attachment $attachment
     * @return string
     */
    protected function generateFilePath(Attachment $attachment): string {
        return "{$attachment->path}/{$attachment->name}.{$attachment->extension}";
    }



}
