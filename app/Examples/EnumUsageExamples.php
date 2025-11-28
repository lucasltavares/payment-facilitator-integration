<?php

namespace App\Examples;

use App\Enums\PixPaymentStatus;
use App\Enums\WithdrawalStatus;
use App\Models\PixPayment;
use App\Models\Withdrawal;

/**
 * Examples of how to use the PIX Payment and Withdrawal status enums
 * 
 * This file demonstrates common patterns for working with the status enums.
 * You can delete this file if you don't need it - it's just for reference.
 */
class EnumUsageExamples
{
    /**
     * Example: Creating a payment with a status
     */
    public function createPaymentWithStatus()
    {
        $payment = PixPayment::create([
            'user_id' => 1,
            'amount' => 100.00,
            'pix_key' => 'user@example.com',
            'pix_key_type' => 'EMAIL',
            'status' => PixPaymentStatus::PENDING, // Using enum
        ]);

        return $payment;
    }

    /**
     * Example: Updating payment status
     */
    public function updatePaymentStatus(PixPayment $payment)
    {
        // Update to paid status
        $payment->update([
            'status' => PixPaymentStatus::PAID,
            'paid_at' => now(),
        ]);

        // Or using the enum directly
        $payment->status = PixPaymentStatus::SUCCESS;
        $payment->save();
    }

    /**
     * Example: Checking status conditions
     */
    public function checkPaymentStatus(PixPayment $payment)
    {
        // Check if payment is successful
        if ($payment->status->isSuccess()) {
            // Payment is paid or successful
        }

        // Check if payment failed
        if ($payment->status->isFailure()) {
            // Payment failed, cancelled, or expired
        }

        // Check if payment is in final state
        if ($payment->status->isFinal()) {
            // Payment won't change status anymore
        }

        // Get human-readable label
        $label = $payment->status->label(); // "Paid", "Failed", etc.

        // Get color for UI
        $color = $payment->status->color(); // "green", "red", etc.
    }

    /**
     * Example: Querying by status
     */
    public function queryByStatus()
    {
        // Get all pending payments
        $pendingPayments = PixPayment::where('status', PixPaymentStatus::PENDING)->get();

        // Using scope methods
        $successfulPayments = PixPayment::successful()->get();
        $failedPayments = PixPayment::failed()->get();
        $pendingPayments = PixPayment::pending()->get();

        // Get payments with specific status
        $paidPayments = PixPayment::withStatus(PixPaymentStatus::PAID)->get();
    }

    /**
     * Example: Working with withdrawal statuses
     */
    public function withdrawalExamples()
    {
        // Create withdrawal
        $withdrawal = Withdrawal::create([
            'user_id' => 1,
            'amount' => 500.00,
            'pix_key' => '12345678900',
            'pix_key_type' => 'CPF',
            'status' => WithdrawalStatus::PENDING,
        ]);

        // Update status
        $withdrawal->update([
            'status' => WithdrawalStatus::PROCESSING,
        ]);

        // Check status
        if ($withdrawal->status->isSuccess()) {
            // Withdrawal processed, successful, or paid
        }

        // Query withdrawals
        $processedWithdrawals = Withdrawal::withStatus(WithdrawalStatus::PROCESSED)->get();
        $successfulWithdrawals = Withdrawal::successful()->get();
    }

    /**
     * Example: Getting all available statuses
     */
    public function getAllStatuses()
    {
        // Get all PIX payment statuses
        $pixStatuses = PixPaymentStatus::cases();
        foreach ($pixStatuses as $status) {
            echo $status->value . ' - ' . $status->label() . "\n";
        }

        // Get all withdrawal statuses
        $withdrawalStatuses = WithdrawalStatus::cases();
        foreach ($withdrawalStatuses as $status) {
            echo $status->value . ' - ' . $status->label() . "\n";
        }

        // Get just the values
        $values = PixPaymentStatus::values(); // ['pending', 'processing', 'paid', ...]
        $names = PixPaymentStatus::names(); // ['PENDING', 'PROCESSING', 'PAID', ...]
    }

    /**
     * Example: Converting string to enum
     */
    public function convertStringToEnum()
    {
        $statusString = 'paid';

        // Safe conversion (returns null if invalid)
        $status = PixPaymentStatus::tryFrom($statusString);
        if ($status) {
            // Valid status
        }

        // Throws exception if invalid
        $status = PixPaymentStatus::from($statusString);
    }

    /**
     * Example: Status transitions
     */
    public function statusTransitions()
    {
        $payment = PixPayment::find(1);

        // Typical flow: PENDING -> PROCESSING -> PAID/SUCCESS
        $payment->update(['status' => PixPaymentStatus::PENDING]);
        $payment->update(['status' => PixPaymentStatus::PROCESSING]);
        $payment->update(['status' => PixPaymentStatus::PAID]);

        // Or if failed: PENDING -> PROCESSING -> FAILED
        $payment->update(['status' => PixPaymentStatus::PENDING]);
        $payment->update(['status' => PixPaymentStatus::PROCESSING]);
        $payment->update(['status' => PixPaymentStatus::FAILED]);
    }
}

