<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class Attachment extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'name',
        'original_name',
        'mime',
        'extension',
        'size',
        'path',
        'description',
        'alt',
        'hash',
        'disk',
    ];

    protected $hidden = [
        'hash',
        'disk',
        'storage_path',
        'full_path',
        'created_at',
        'updated_at',
        'deleted_at',
        'attachable_type',
        'attachable_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = [
        'url',
        'relative_url',
        'title',
        'file_path'
    ];

    protected function filePath(): Attribute {
        return Attribute::make(
            get: fn () => "{$this->path}/{$this->name}.{$this->extension}"
        );
    }

    protected static function normalizePath(?string $path): string {
        $path = (string) $path;

        if (trim($path) === '') {
            return 'uploads/' . date('Y/m/d');
        }

        $norm = Str::of($path)
            ->replace('\\', '/')
            ->replace('..', '')
            ->replaceMatches('/\/+/', '/')
            ->trim('/');

        return $norm->isNotEmpty() ? $norm->toString() : 'uploads/' . date('Y/m/d');
    }

    protected function extension(): Attribute {
        return Attribute::make(
            get: fn (?string $value) => $value ? Str::lower($value) : null,
            set: fn (?string $value) => $value ? Str::lower($value) : null,
        );
    }

    protected function path(): Attribute {
        return Attribute::make(
            get: fn (?string $value) => $value ? trim($value, "/") : null,
            set: fn (?string $value) => self::normalizePath($value)
        );
    }

    protected function url(): Attribute {
        return Attribute::make(
            get: fn () => $this->getAttachmentUrl()
        );
    }

    protected function relativeUrl(): Attribute {
        return Attribute::make(
            get: function () {
                $url = $this->getAttachmentUrl();
                if (!$url) return null;
                $parts = parse_url($url);
                return $parts['path'] ?? $url;
            }
        );
    }

    protected function storagePath(): Attribute {
        return Attribute::make(
            get: fn () => "public/{$this->path}/{$this->name}.{$this->extension}"
        );
    }

    protected function fullPath(): Attribute {
        return Attribute::make(
            get: fn () => storage_path("app/{$this->storage_path}")
        );
    }

    protected function title(): Attribute {
        return Attribute::make(
            get: function () {
                if ($this->original_name && $this->original_name !== 'blob') {
                    return $this->original_name;
                }
                return "{$this->name}.{$this->extension}";
            }
        );
    }

    public function getActivityLogOptions(): LogOptions {
        return LogOptions::defaults()
            ->logFillable()
            ->useLogName('Attachment')
            ->dontSubmitEmptyLogs()
            ->logOnlyDirty();
    }

    public function getAttachmentUrl(): ?string {
        $isProd = config('app.env') === 'production';
        $path = $this->file_path;
        $storagePath = Storage::url( $path );
        return $isProd ? secure_url($storagePath) : url($storagePath);
    }

    public function uploadFile(UploadedFile $file, ?string $path = null, ?string $disk = null): bool {
        $disk = $disk ?? $this->disk ?? 'public';
        $path = self::normalizePath($path ?? $this->path ?? 'uploads/' . date('Y/m/d'));
        $filename = "{$this->name}.{$this->extension}";

        Storage::disk($disk)->makeDirectory($path);

        $ok = Storage::disk($disk)->putFileAs($path, $file, $filename);

        if (!$ok) {
            Log::error('No se pudo guardar el archivo', compact('disk', 'path', 'filename'));
            return false;
        }

        $this->path = $path;
        $this->disk = $disk;

        return true;
    }

    public function relativePath(): string {
        return "{$this->path}/{$this->name}.{$this->extension}";
    }

    public function fileExists(): bool {
        return Storage::disk($this->disk ?? 'public')->exists($this->relativePath());
    }

    public function deleteFile(): bool {
        try {
            if ($this->fileExists()) {
                return Storage::disk($this->disk ?? 'public')->delete($this->relativePath());
            }
            return true;
        } catch (\Exception $e) {
            Log::error("Error al eliminar archivo: " . $e->getMessage(), [
                'attachment_id' => $this->id,
                'relative_path' => $this->relativePath()
            ]);
            return false;
        }
    }
}
