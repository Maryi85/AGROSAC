<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Loan extends Model
{
    protected $fillable = [
        'tool_id',
        'user_id',
        'quantity',
        'out_at',
        'due_at',
        'returned_at',
        'condition_return',
        'status',
        'request_notes',
        'admin_notes',
        'approved_by',
        'approved_at',
        'returned_by',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'out_at' => 'datetime',
        'due_at' => 'datetime',
        'returned_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function tool(): BelongsTo
    {
        return $this->belongsTo(Tool::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function returnedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'returned_by');
    }

    // Métodos helper para estados
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isOut(): bool
    {
        return $this->status === 'out';
    }

    public function isReturned(): bool
    {
        return $this->status === 'returned';
    }

    public function isReturnedByWorker(): bool
    {
        return $this->status === 'returned_by_worker';
    }

    public function isLost(): bool
    {
        return $this->status === 'lost';
    }

    public function isDamaged(): bool
    {
        return $this->status === 'damaged';
    }
}
