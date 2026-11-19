<?php

use App\Http\Middleware\CachePublicHtml;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            $changed = false;
            DB::table('page_blocks')
                ->where('block_type', 'cta')
                ->whereIn('page_id', DB::table('pages')->select('id')->whereIn('slug', ['home', 'gioi-thieu'])->whereNull('deleted_at'))
                ->lockForUpdate()
                ->chunkById(100, function ($blocks) use (&$changed) {
                    foreach ($blocks as $block) {
                        $data = $block->data === null ? [] : json_decode($block->data, true);
                        if (! is_array($data) || array_key_exists('show_form', $data)) {
                            continue;
                        }
                        DB::table('page_blocks')->where('id', $block->id)->update([
                            'data' => json_encode($data + ['show_form' => true], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
                        ]);
                        $changed = true;
                    }
                });

            if ($changed) {
                DB::afterCommit(fn () => CachePublicHtml::invalidate());
            }
        });
    }

    public function down(): void
    {
        // Keep the editable form setting on rollback.
    }
};
