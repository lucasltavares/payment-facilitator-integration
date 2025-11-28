<?php

namespace App\Http\Controllers;

use App\Enums\PixPaymentStatus;
use App\Models\PixPayment;
use App\Services\PaymentFacilitatorService;
use App\Models\PaymentFacilitator;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class PixPaymentController extends Controller
{
    /**
     * Create a new PIX payment
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'sometimes|string|size:3',
            'description' => 'sometimes|string|max:255',
            'pix_key' => 'required|string',
            'pix_key_type' => 'sometimes|string|in:CPF,CNPJ,EMAIL,PHONE,RANDOM',
            'expires_at' => 'sometimes|date|after:now',
            'metadata' => 'sometimes|array',
            'payment_facilitator_id' => 'sometimes|exists:payment_facilitators,id',
        ]);

        $validated['user_id'] = $request->user()->id;

        // Get payment facilitator (default or specified)
        $facilitator = $validated['payment_facilitator_id'] 
            ? PaymentFacilitator::findOrFail($validated['payment_facilitator_id'])
            : PaymentFacilitator::where('is_default', true)->where('is_active', true)->firstOrFail();

        $service = new PaymentFacilitatorService($facilitator);
        $pixPayment = $service->createPixPayment($validated);

        return response()->json([
            'message' => 'PIX payment created successfully',
            'data' => $pixPayment,
        ], 201);
    }

    /**
     * Get a specific PIX payment
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $pixPayment = PixPayment::where('user_id', $request->user()->id)
            ->findOrFail($id);

        return response()->json([
            'data' => $pixPayment,
        ]);
    }

    /**
     * List PIX payments with filters
     */
    public function index(Request $request): JsonResponse
    {
        $query = PixPayment::where('user_id', $request->user()->id);

        // Filter by status
        if ($request->has('status')) {
            $status = PixPaymentStatus::tryFrom($request->input('status'));
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

        $payments = $query->latest()->paginate($request->input('per_page', 15));

        return response()->json($payments);
    }

    /**
     * Check payment status
     */
    public function checkStatus(Request $request, int $id): JsonResponse
    {
        $pixPayment = PixPayment::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $facilitator = $pixPayment->paymentFacilitator 
            ?? PaymentFacilitator::where('is_default', true)->where('is_active', true)->firstOrFail();

        $service = new PaymentFacilitatorService($facilitator);
        $pixPayment = $service->checkPixPaymentStatus($pixPayment);

        return response()->json([
            'data' => $pixPayment,
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
        }, PixPaymentStatus::cases());

        return response()->json([
            'statuses' => $statuses,
        ]);
    }
}

