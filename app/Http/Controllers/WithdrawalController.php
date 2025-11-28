<?php

namespace App\Http\Controllers;

use App\Enums\WithdrawalStatus;
use App\Models\Withdrawal;
use App\Services\PaymentFacilitatorService;
use App\Models\PaymentFacilitator;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WithdrawalController extends Controller
{
    /**
     * Create a new withdrawal
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'sometimes|string|size:3',
            'description' => 'sometimes|string|max:255',
            'pix_key' => 'required|string',
            'pix_key_type' => 'sometimes|string|in:CPF,CNPJ,EMAIL,PHONE,RANDOM',
            'metadata' => 'sometimes|array',
            'payment_facilitator_id' => 'sometimes|exists:payment_facilitators,id',
        ]);

        $validated['user_id'] = $request->user()->id;

        // Get payment facilitator (default or specified)
        $facilitator = $validated['payment_facilitator_id'] 
            ? PaymentFacilitator::findOrFail($validated['payment_facilitator_id'])
            : PaymentFacilitator::where('is_default', true)->where('is_active', true)->firstOrFail();

        $service = new PaymentFacilitatorService($facilitator);
        $withdrawal = $service->withdrawValue($validated);

        return response()->json([
            'message' => 'Withdrawal created successfully',
            'data' => $withdrawal,
        ], 201);
    }

    /**
     * Get a specific withdrawal
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $withdrawal = Withdrawal::where('user_id', $request->user()->id)
            ->findOrFail($id);

        return response()->json([
            'data' => $withdrawal,
        ]);
    }

    /**
     * List withdrawals with filters
     */
    public function index(Request $request): JsonResponse
    {
        $query = Withdrawal::where('user_id', $request->user()->id);

        // Filter by status
        if ($request->has('status')) {
            $status = WithdrawalStatus::tryFrom($request->input('status'));
            if ($status) {
                $query->where('status', $status);
            }
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->input('from_date'));
        }
        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->input('to_date'));
        }

        $withdrawals = $query->latest()->paginate($request->input('per_page', 15));

        return response()->json($withdrawals);
    }

    /**
     * Check withdrawal status
     */
    public function checkStatus(Request $request, int $id): JsonResponse
    {
        $withdrawal = Withdrawal::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $facilitator = $withdrawal->paymentFacilitator 
            ?? PaymentFacilitator::where('is_default', true)->where('is_active', true)->firstOrFail();

        $service = new PaymentFacilitatorService($facilitator);
        $withdrawal = $service->checkWithdrawalStatus($withdrawal);

        return response()->json([
            'data' => $withdrawal,
        ]);
    }

    /**
     * Get available statuses
     */
    public function statuses(): JsonResponse
    {
        $statuses = array_map(function ($status) {
            return [
                'value' => $status->value,
                'label' => $status->label(),
                'color' => $status->color(),
                'is_final' => $status->isFinal(),
                'is_success' => $status->isSuccess(),
                'is_failure' => $status->isFailure(),
            ];
        }, WithdrawalStatus::cases());

        return response()->json([
            'statuses' => $statuses,
        ]);
    }
}

