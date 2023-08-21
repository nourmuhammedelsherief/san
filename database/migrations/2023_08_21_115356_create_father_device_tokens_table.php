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
        Schema::create('father_device_tokens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('father_id');
            $table->foreign('father_id')
                ->references('id')
                ->on('fathers')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->string('device_token')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('father_device_tokens');
    }
};
