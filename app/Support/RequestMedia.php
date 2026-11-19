<?php

namespace App\Support;

use App\Models\MediaAsset;

class RequestMedia
{
    private array $media = [];

    public function preload(iterable $ids): void
    {
        $missing = collect($ids)->filter(fn ($id) => $this->validId($id))->unique()
            ->reject(fn ($id) => array_key_exists($id, $this->media))->values();
        if ($missing->isEmpty()) {
            return;
        }

        $models = MediaAsset::whereKey($missing->all())->get()->keyBy('id');
        foreach ($missing as $id) {
            $this->media[$id] = $models->get($id);
        }
    }

    public function find(mixed $id): ?MediaAsset
    {
        if ($id instanceof MediaAsset) {
            return $id;
        }
        if (! $this->validId($id)) {
            return null;
        }

        $this->preload([$id]);

        return $this->media[$id];
    }

    private function validId(mixed $id): bool
    {
        return (is_int($id) || is_string($id)) && filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) !== false;
    }
}
