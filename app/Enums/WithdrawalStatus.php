<?php

namespace App\Enums;

enum WithdrawalStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case PROCESSED = 'processed';
    case SUCCESS = 'success';
    case PAID = 'paid';
    case FAILED = 'failed';
    case CANCELLED = 'cancelled';
    case REJECTED = 'rejected';

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
            self::PROCESSED,
            self::SUCCESS,
            self::PAID,
            self::FAILED,
            self::CANCELLED,
            self::REJECTED,
        ]);
    }

    /**
     * Check if status indicates success
     */
    public function isSuccess(): bool
    {
        return in_array($this, [self::PROCESSED, self::SUCCESS, self::PAID]);
    }

    /**
     * Check if status indicates failure
     */
    public function isFailure(): bool
    {
        return in_array($this, [self::FAILED, self::CANCELLED, self::REJECTED]);
    }

    /**
     * Get human-readable label
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::PROCESSING => 'Processing',
            self::PROCESSED => 'Processed',
            self::SUCCESS => 'Success',
            self::PAID => 'Paid',
            self::FAILED => 'Failed',
            self::CANCELLED => 'Cancelled',
            self::REJECTED => 'Rejected',
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
            self::PROCESSED, self::SUCCESS, self::PAID => 'green',
            self::FAILED, self::CANCELLED, self::REJECTED => 'red',
        };
    }
}

