<?php

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Ghi nhật ký thao tác của người dùng đã đăng nhập trên các model nội dung.
 * Bỏ qua thao tác của khách (seeding, form public) để tránh nhiễu.
 */
class AuditObserver
{
    public function created(Model $model): void
    {
        $this->log('created', $model);
    }

    public function updated(Model $model): void
    {
        // Nhận diện publish/archive để ghi sự kiện rõ nghĩa hơn.
        $event = 'updated';
        if ($model->isDirty('status')) {
            $status = $model->getAttribute('status');
            $event = match ($status) {
                'published' => 'published',
                'archived' => 'archived',
                default => 'updated',
            };
        }

        $this->log($event, $model);
    }

    public function deleted(Model $model): void
    {
        $forceDeleting = method_exists($model, 'isForceDeleting') && $model->isForceDeleting();
        $this->log($forceDeleting ? 'force_deleted' : 'deleted', $model);
    }

    public function restored(Model $model): void
    {
        $this->log('restored', $model);
    }

    private function log(string $event, Model $model): void
    {
        if (! Auth::check()) {
            return;
        }

        AuditLog::record(
            $event,
            $model,
            class_basename($model).' #'.$model->getKey(),
        );
    }
}
