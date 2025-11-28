<?php

use App\Enums\WithdrawalStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_facilitator_id')->nullable()->constrained()->onDelete('set null');
            $table->string('external_id')->unique()->nullable()->comment('External withdrawal gateway ID');
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('BRL');
            $table->text('description')->nullable();
            $table->string('pix_key');
            $table->string('pix_key_type')->comment('CPF, CNPJ, EMAIL, PHONE, RANDOM');
            $table->string('status')->default(WithdrawalStatus::PENDING->value);
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->json('metadata')->nullable();
            $table->text('error_message')->nullable();
            $table->string('transaction_id')->nullable()->comment('Bank transaction ID');
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['status', 'created_at']);
            $table->index('external_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdrawals');
    }
};

