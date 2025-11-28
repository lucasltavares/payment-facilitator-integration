<?php

namespace App\Services;

use App\Models\PixPayment;
use App\Models\Withdrawal;

interface PaymentFacilitatorInterface
{
    /**
     * Create a PIX payment
     *
     * @param array $data Payment data (amount, pix_key, description, etc.)
     * @return PixPayment
     */
    public function createPixPayment(array $data): PixPayment;

    /**
     * Create a withdrawal
     *
     * @param array $data Withdrawal data (amount, pix_key, description, etc.)
     * @return Withdrawal
     */
    public function withdrawValue(array $data): Withdrawal;

    /**
     * Check payment status from external provider
     *
     * @param PixPayment $pixPayment
     * @return PixPayment
     */
    public function checkPixPaymentStatus(PixPayment $pixPayment): PixPayment;

    /**
     * Check withdrawal status from external provider
     *
     * @param Withdrawal $withdrawal
     * @return Withdrawal
     */
    public function checkWithdrawalStatus(Withdrawal $withdrawal): Withdrawal;
}
