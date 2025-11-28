<?php

namespace App\Enums;

enum PixPaymentStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case PAID = 'paid';
    case SUCCESS = 'success';
    case FAILED = 'failed';
    case CANCELLED = 'cancelled';
    case EXPIRED = 'expired';
    case REFUNDED = 'refunded';

    /**
     * Get all status values as array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get all status names as array
     */
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    /**
     * Check if status is a final/completed status
     */
    public function isFinal(): bool
    {
        return in_array($this, [
            self::PAID,
            self::SUCCESS,
            self::FAILED,
            self::CANCELLED,
            self::EXPIRED,
            self::REFUNDED,
        ]);
    }

    /**
     * Check if status indicates success
     */
    public function isSuccess(): bool
    {
        return in_array($this, [self::PAID, self::SUCCESS]);
    }

    /**
     * Check if status indicates failure
     */
    public function isFailure(): bool
    {
        return in_array($this, [self::FAILED, self::CANCELLED, self::EXPIRED]);
    }

    /**
     * Get human-readable label
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::PROCESSING => 'Processing',
            self::PAID => 'Paid',
            self::SUCCESS => 'Success',
            self::FAILED => 'Failed',
            self::CANCELLED => 'Cancelled',
            self::EXPIRED => 'Expired',
            self::REFUNDED => 'Refunded',
        };
    }

    /**
     * Get color class for UI (optional, for frontend)
     */
    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'yellow',
            self::PROCESSING => 'blue',
            self::PAID, self::SUCCESS => 'green',
            self::FAILED, self::CANCELLED, self::EXPIRED => 'red',
            self::REFUNDED => 'orange',
        };
    }
}

