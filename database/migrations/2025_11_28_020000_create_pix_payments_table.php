<?php

use App\Enums\PixPaymentStatus;
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
        Schema::create('pix_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_facilitator_id')->nullable()->constrained()->onDelete('set null');
            $table->string('external_id')->unique()->nullable()->comment('External payment gateway ID');
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('BRL');
            $table->text('description')->nullable();
            $table->string('pix_key');
            $table->string('pix_key_type')->comment('CPF, CNPJ, EMAIL, PHONE, RANDOM');
            $table->text('qr_code')->nullable();
            $table->text('qr_code_image')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('status')->default(PixPaymentStatus::PENDING->value);
            $table->json('metadata')->nullable();
            $table->text('error_message')->nullable();
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
        Schema::dropIfExists('pix_payments');
    }
};

