<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactSubmission extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUSES = [
        'new' => 'Mới',
        'in_progress' => 'Đang xử lý',
        'resolved' => 'Đã xử lý',
        'spam' => 'Spam',
        'archived' => 'Lưu trữ',
    ];

    protected $fillable = [
        'name', 'phone', 'email', 'company', 'service_id', 'service_interest',
        'message', 'status', 'assigned_to', 'ip_address', 'user_agent', 'consent',
    ];

    protected $casts = [
        'consent' => 'boolean',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(ContactNote::class)->latest();
    }
}
