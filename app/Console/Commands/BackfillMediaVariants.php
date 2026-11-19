<?php

namespace App\Console\Commands;

use App\Http\Middleware\CachePublicHtml;
use App\Models\MediaAsset;
use App\Support\MediaVariants;
use Illuminate\Console\Command;
use Throwable;

class BackfillMediaVariants extends Command
{
    protected $signature = 'media:backfill-variants';

    protected $description = 'Generate missing responsive WebP variants for local media';

    public function handle(): int
    {
        if (! function_exists('imagewebp')) {
            $this->error('PHP GD with WebP support is required.');

            return self::FAILURE;
        }

        $failed = 0;
        foreach (MediaAsset::whereIn('disk', ['assets', 'public'])->lazyById() as $asset) {
            try {
                MediaVariants::generate($asset);
            } catch (Throwable $exception) {
                $failed++;
                $this->error($exception->getMessage());
            }
        }
        CachePublicHtml::invalidate();

        return $failed === 0 ? self::SUCCESS : self::FAILURE;
    }
}
