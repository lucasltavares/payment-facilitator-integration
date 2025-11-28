<?php

namespace App\Models;

use App\Enums\PixPaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PixPayment extends Model
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
        'qr_code',
        'qr_code_image',
        'expires_at',
        'paid_at',
        'status',
        'metadata',
        'error_message',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expires_at' => 'datetime',
        'paid_at' => 'datetime',
        'status' => PixPaymentStatus::class,
        'metadata' => 'array',
    ];

    /**
     * Get the user that owns the payment
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
     * Scope: Get payments by status
     */
    public function scopeWithStatus($query, PixPaymentStatus $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Get successful payments
     */
    public function scopeSuccessful($query)
    {
        return $query->whereIn('status', [PixPaymentStatus::PAID, PixPaymentStatus::SUCCESS]);
    }

    /**
     * Scope: Get failed payments
     */
    public function scopeFailed($query)
    {
        return $query->whereIn('status', [
            PixPaymentStatus::FAILED,
            PixPaymentStatus::CANCELLED,
            PixPaymentStatus::EXPIRED,
        ]);
    }

    /**
     * Scope: Get pending payments
     */
    public function scopePending($query)
    {
        return $query->whereIn('status', [PixPaymentStatus::PENDING, PixPaymentStatus::PROCESSING]);
    }

    /**
     * Check if payment is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast() && $this->status !== PixPaymentStatus::PAID;
    }
}

