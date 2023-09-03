<?php

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
        Schema::create('school_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id')->nullable();
            $table->foreign('school_id')
                ->references('id')
                ->on('schools')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->unsignedBigInteger('seller_code_id')->nullable();
            $table->foreign('seller_code_id')
                ->references('id')
                ->on('seller_codes')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->double('paid_amount')->default(0);
            $table->double('discount')->default(0);
            $table->enum('status' , ['not_active','active' , 'finished'])->default('not_active');
            $table->string('transfer_photo')->nullable();
            $table->string('invoice_id')->nullable();
            $table->enum('payment_type' , ['bank' , 'online'])->nullable();
            $table->enum('payment' , ['true' , 'false'])->nullable();
            $table->date('paid_at')->nullable();
            $table->date('end_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_subscriptions');
    }
};
