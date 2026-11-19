<?php

namespace App\Support;

use App\Models\MediaAsset;
use App\Models\TeamMember;
use Illuminate\Support\Str;

final class CompanyProfile
{
    public const ZALO = 'https://zalo.me/1747723032009789626';

    public const FACEBOOK = 'https://www.facebook.com/profile.php?id=61586315529756';

    public const CEO_ROLE = 'Tổng giám đốc';

    public const CEO_SLUG = 'tong-giam-doc';

    public const CEO_FILE = 'team/ceo-portrait-lg.webp';

    public const CEO_PHOTO = 'img/v2/'.self::CEO_FILE;

    public const CEO_ALT = 'Tổng giám đốc Happy Ecotech';

    public static function url(string $key): ?string
    {
        $value = setting($key);
        $url = is_string($value) ? safe_url($value) : null;

        return $url !== null && str_starts_with(strtolower($url), 'https://') ? $url : null;
    }

    public static function isChiefExecutive(TeamMember $member): bool
    {
        return $member->slug === self::CEO_SLUG || in_array(
            Str::lower(Str::ascii(Str::squish((string) $member->position))),
            ['tong giam doc', 'ceo', 'giam doc dieu hanh'],
            true,
        );
    }

    public static function chiefExecutive(bool $lock = false): ?TeamMember
    {
        return TeamMember::query()->ordered()->lock($lock ?: null)->get()->first(self::isChiefExecutive(...));
    }

    public static function attachPortrait(?TeamMember $member): void
    {
        if ($member?->photo_media_id !== null) {
            return;
        }

        $photo = MediaAsset::firstOrCreate(
            ['disk' => 'assets', 'path' => self::CEO_PHOTO],
            ['alt' => self::CEO_ALT, 'folder' => 'team'],
        );
        if ($member) {
            $member->update(['photo_media_id' => $photo->id]);

            return;
        }

        TeamMember::firstOrCreate(['slug' => self::CEO_SLUG], [
            'name' => self::CEO_ROLE,
            'position' => self::CEO_ROLE,
            'group' => 'Lãnh đạo',
            'status' => 'published',
            'photo_media_id' => $photo->id,
        ]);
    }
}
