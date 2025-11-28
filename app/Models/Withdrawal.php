<?php

namespace App\Models;

use App\Enums\WithdrawalStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Withdrawal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'payment_facilitator_id',
        'external_id',
        'amount',
        'currency',
        'description',
        'pix_key',
        'pix_key_type',
        'status',
        'processed_at',
        'paid_at',
        'failed_at',
        'metadata',
        'error_message',
        'transaction_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'processed_at' => 'datetime',
        'paid_at' => 'datetime',
        'failed_at' => 'datetime',
        'status' => WithdrawalStatus::class,
        'metadata' => 'array',
    ];

    /**
     * Get the user that owns the withdrawal
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the payment facilitator
     */
    public function paymentFacilitator(): BelongsTo
    {
        return $this->belongsTo(PaymentFacilitator::class);
    }

    /**
     * Scope: Get withdrawals by status
     */
    public function scopeWithStatus($query, WithdrawalStatus $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Get successful withdrawals
     */
    public function scopeSuccessful($query)
    {
        return $query->whereIn('status', [
            WithdrawalStatus::PROCESSED,
            WithdrawalStatus::SUCCESS,
            WithdrawalStatus::PAID,
        ]);
    }

    /**
     * Scope: Get failed withdrawals
     */
    public function scopeFailed($query)
    {
        return $query->whereIn('status', [
            WithdrawalStatus::FAILED,
            WithdrawalStatus::CANCELLED,
            WithdrawalStatus::REJECTED,
        ]);
    }

    /**
     * Scope: Get pending withdrawals
     */
    public function scopePending($query)
    {
        return $query->whereIn('status', [WithdrawalStatus::PENDING, WithdrawalStatus::PROCESSING]);
    }
}

