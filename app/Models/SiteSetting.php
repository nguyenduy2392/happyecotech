<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    public const CACHE_KEY = 'site_settings.map';

    public const GROUPS = [
        'general' => 'Chung',
        'contact' => 'Liên hệ',
        'social' => 'Mạng xã hội',
        'seo' => 'SEO mặc định',
        'footer' => 'Footer',
        'app' => 'Tải ứng dụng',
        'smtp' => 'Email / SMTP',
    ];

    protected $fillable = ['group', 'key', 'value', 'type'];

    protected static function booted(): void
    {
        $flush = fn () => Cache::forget(self::CACHE_KEY);
        static::saved($flush);
        static::deleted($flush);
    }

    /**
     * Settings thô dạng key => [value, type], có cache.
     * CHỈ cache scalar để an toàn khi serialize giữa các tiến trình
     * (không cache Eloquent model như MediaAsset).
     *
     * @return array<string,array{value:?string,type:string}>
     */
    public static function map(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            return static::query()->get()
                ->mapWithKeys(fn (SiteSetting $s) => [$s->key => ['value' => $s->value, 'type' => $s->type]])
                ->all();
        });
    }

    public static function getValue(string $key, mixed $default = null): mixed
    {
        $raw = self::map()[$key] ?? null;

        if ($raw === null) {
            return $default;
        }

        // Media (MediaAsset) được resolve NGOÀI cache để tránh serialize model.
        return self::castRaw($raw['value'], $raw['type']) ?? $default;
    }

    private static function castRaw(?string $value, string $type): mixed
    {
        return match ($type) {
            'boolean' => (bool) $value,
            'integer' => (int) $value,
            'json' => json_decode((string) $value, true),
            'media' => $value ? MediaAsset::find($value) : null,
            default => $value,
        };
    }

    public static function put(string $key, mixed $value, string $group = 'general', string $type = 'string'): void
    {
        $stored = match ($type) {
            'boolean' => $value ? '1' : '0',
            'json' => json_encode($value, JSON_UNESCAPED_UNICODE),
            default => is_null($value) ? null : (string) $value,
        };

        static::updateOrCreate(
            ['key' => $key],
            ['value' => $stored, 'group' => $group, 'type' => $type],
        );
    }

    public function castValue(): mixed
    {
        return self::castRaw($this->value, $this->type);
    }
}
