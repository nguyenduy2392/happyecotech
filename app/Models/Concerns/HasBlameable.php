<?php

namespace App\Models\Concerns;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * Tự động gán created_by / updated_by theo user đang đăng nhập.
 * Chỉ dùng cho model có sẵn hai cột này.
 */
trait HasBlameable
{
    public static function bootHasBlameable(): void
    {
        static::creating(function ($model) {
            $id = Auth::id();
            if ($id) {
                if (empty($model->created_by)) {
                    $model->created_by = $id;
                }
                if (empty($model->updated_by)) {
                    $model->updated_by = $id;
                }
            }
        });

        static::updating(function ($model) {
            if ($id = Auth::id()) {
                $model->updated_by = $id;
            }
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
