<?php

namespace App\Models;

use App\Models\Concerns\HasBlameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MediaAsset extends Model
{
    use HasBlameable, SoftDeletes;

    protected $fillable = [
        'disk', 'path', 'file_name', 'original_name', 'mime_type', 'extension',
        'size', 'width', 'height', 'alt', 'caption', 'folder',
        'created_by', 'updated_by',
    ];

    protected $casts = [
        'size' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
    ];

    protected $appends = ['url'];

    protected static function booted(): void
    {
        // Tự động điền metadata khi upload qua admin (mime, size, kích thước ảnh).
        static::saving(function (MediaAsset $media) {
            if (blank($media->disk)) {
                $media->disk = 'public';
            }

            if (blank($media->path)) {
                return;
            }

            if (blank($media->file_name)) {
                $media->file_name = basename($media->path);
            }
            if (blank($media->original_name)) {
                $media->original_name = $media->file_name;
            }
            if (blank($media->extension)) {
                $media->extension = pathinfo($media->path, PATHINFO_EXTENSION) ?: null;
            }

            try {
                $disk = Storage::disk($media->disk);
                if (! $disk->exists($media->path)) {
                    return;
                }
                if (blank($media->mime_type)) {
                    $media->mime_type = $disk->mimeType($media->path) ?: null;
                }
                if (blank($media->size)) {
                    $media->size = $disk->size($media->path) ?: null;
                }
                if (blank($media->width)
                    && str_starts_with((string) $media->mime_type, 'image/')
                    && ! str_ends_with($media->path, '.svg')) {
                    $abs = $disk->path($media->path);
                    if (is_file($abs) && ($info = @getimagesize($abs))) {
                        $media->width = $info[0];
                        $media->height = $info[1];
                    }
                }
            } catch (\Throwable) {
                // Không chặn lưu nếu disk không hỗ trợ path() (ví dụ S3).
            }
        });
    }

    public function getUrlAttribute(): ?string
    {
        if (blank($this->path)) {
            return null;
        }

        return Storage::disk($this->disk ?: 'public')->url($this->path);
    }

    public function getIsImageAttribute(): bool
    {
        return str_starts_with((string) $this->mime_type, 'image/');
    }

    /**
     * Đếm số nơi đang tham chiếu media này (để cảnh báo trước khi xoá).
     *
     * @return array<string,int>
     */
    public function usageMap(): array
    {
        $map = [
            'Dịch vụ (icon)' => Service::where('icon_media_id', $this->id)->count(),
            'Dịch vụ (cover)' => Service::where('cover_media_id', $this->id)->count(),
            'Bài viết (ảnh đại diện)' => Post::where('featured_media_id', $this->id)->count(),
            'Đội ngũ (ảnh)' => TeamMember::where('photo_media_id', $this->id)->count(),
            'Gallery dịch vụ' => DB::table('service_media')->where('media_asset_id', $this->id)->count(),
            'Cài đặt website' => SiteSetting::where('type', 'media')->where('value', (string) $this->id)->count(),
        ];

        return array_filter($map, fn ($count) => $count > 0);
    }

    public function isInUse(): bool
    {
        return count($this->usageMap()) > 0;
    }
}
